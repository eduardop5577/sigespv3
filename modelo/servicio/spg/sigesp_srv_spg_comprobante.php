<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
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
require_once ($dirsrv."/modelo/servicio/spg/sigesp_srv_spg_icomprobante.php");
require_once ($dirsrv."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobante.php");

class ServicioComprobantePresupuestarioGasto implements IComprobantePresupuestarioGasto
{
	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos; 
	private $daoComprobante;
	
	public function __construct($prefijo='')
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->daoComprobante = null;
		$this->conexionbd  = ConexionBaseDatos::getInstanciaConexion();
		$this->codemp = $_SESSION['la_empresa']['codemp'];
		$this->logusr = $_SESSION["la_logusr"];
		$this->daoComprobante = FabricaDao::CrearDAO('N', 'sigesp_cmp');
		$this->daoComprobante->codemp=$this->codemp;
		$this->utilizaprefijo = $this->daoComprobante->utilizaPrefijo('SPG','SPGCMP',$_SESSION['la_logusr']);
		if($this->utilizaprefijo)
		{
			$this->nronuevo=$this->daoComprobante->buscarCodigo('comprobante',true,15,array('procede'=>'SPGCMP'),'SPG','SPGCMP',$_SESSION['la_logusr'],'',$prefijo);
		}
		unset($this->daoComprobante);
	}
	
	public function buscarFuenteFinanciamiento($codemp)
	{
		$cadenasql = " SELECT codfuefin,denfuefin ".
   					 " FROM sigesp_fuentefinanciamiento ".
		             " WHERE codemp='{$codemp}' ".
		             " GROUP BY codfuefin,denfuefin ".
		             " ORDER BY codfuefin,denfuefin ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarFuenteFinanciamiento ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	public function buscarCuentaPresupuestaria($codemp,$codigo,$denominacion)
	{	
		$cadenasql = " SELECT DISTINCT trim(spg_cuenta) as spg_cuenta,max(denominacion) as denominacion, max(status) as status ".
	                 " FROM spg_cuentas ".
		   	         " WHERE codemp = '".$codemp."' ". 
			         "   AND spg_cuenta like '%{$codigo}%'  ". 
				     "   AND UPPER(denominacion) like '%{$denominacion}%' ".
			         " GROUP BY spg_cuenta ".
				     " ORDER BY spg_cuenta";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarCuentaPresupuestaria ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	public function buscarMoneda($codigo,$denominacion)
	{
		$cadenasql = " SELECT codmon,denmon FROM sigesp_moneda ".
				     " WHERE codmon like '%".$codigo."%'  ".
				     "   AND UPPER(denmon) like '%".strtoupper($denominacion)."%' ".
			         "   AND codmon<>'---'";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarMoneda ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	public function buscarComprobantes($codemp,$comprobante,$procede,$tipo,$provben,$fecdesde,$fechasta,$filtro,$numconcom)
	{
		$cadenConcat = $this->conexionbd->Concat('ben.apebene',"','",'ben.nombene');
		$cadenConcat1 = $this->conexionbd->Concat('cmp.codemp','cmp.procede','cmp.comprobante','cmp.codban','cmp.ctaban');
		$cadenConcat2 = $this->conexionbd->Concat('detcmp.codemp','detcmp.procede','detcmp.comprobante','detcmp.codban','detcmp.ctaban');
		$cadenaFiltro = '';
		$tablaAdicional = '';
		$tipocmp="	   AND tipo_comp=1 ";
		$cadenacmp = ConexionBaseDatos::criterioUpperSIGESP('cmp.comprobante', "'%{$comprobante}%'", 'LIKE');
		if(($tipo=="P")&&(!empty($provben)))
		{
			$cadenaFiltro=$cadenaFiltro." AND cmp.cod_pro like '".$provben."' ";
		}
		if(($tipo=="B")&&(!empty($provben)))
		{
			$cadenaFiltro=$cadenaFiltro." AND cmp.ced_bene like'".$provben."' ";
		}
		if(((!empty($fecdesde))&&($fecdesde!="01/01/1900"))&&((!empty($fechasta))&&($fechasta!="01/01/1900")))
		{
			$fecdesde=convertirFechaBd($fecdesde);
			$fechasta=convertirFechaBd($fechasta);
			$cadenaFiltro=$cadenaFiltro." AND cmp.fecha>='".$fecdesde."' AND cmp.fecha<='".$fechasta."'";
		}	
		if(!empty($procede))
		{
			$cadenaFiltro=$cadenaFiltro." AND cmp.procede='".$procede."'";
		}	
		if(!empty($numconcom))
		{
			$cadenaFiltro=$cadenaFiltro." AND cmp.numconcom like'%".$numconcom."%'";
		}	
		if ($filtro =='EJECUCION_COMPROMISO')
		{
			$tablaAdicional = ', spg_dt_cmp, spg_operaciones';
			$cadenaFiltro=$cadenaFiltro." AND spg_dt_cmp.codemp=cmp.codemp ".
										" AND spg_dt_cmp.comprobante=cmp.comprobante ".
										" AND spg_dt_cmp.codban=cmp.codban ".
										" AND spg_dt_cmp.ctaban=cmp.ctaban ".
										" AND spg_dt_cmp.procede=cmp.procede ".
										" AND spg_dt_cmp.operacion=spg_operaciones.operacion ".
										" AND (spg_operaciones.operacion='CS' OR spg_operaciones.operacion='CG' OR spg_operaciones.operacion='CCP')";
		}
		$cadenasql="SELECT cmp.comprobante,cmp.descripcion,cmp.procede,cmp.fecha,cmp.tipo_comp,cmp.cod_pro,cmp.ced_bene, ".
       		      "		   cmp.tipo_destino,cmp.codban,cmp.ctaban,cmp.total, cmp.total as monto,cmp.numconcom, ".
       			  "		   (CASE WHEN cmp.tipo_destino='P'  ".
             	  "				THEN   ".
               	  "				(SELECT nompro  ".
                  "				 FROM rpc_proveedor pro ".
                  " 			 WHERE pro.codemp=cmp.codemp AND pro.cod_pro=cmp.cod_pro)  ".
             	  "				WHEN cmp.tipo_destino='B'  ".
	     		  "				THEN  ".
				  "				(SELECT {$cadenConcat}  ".
		 		  "				 FROM rpc_beneficiario ben  ".
		 		  "				 WHERE ben.codemp=cmp.codemp AND ben.ced_bene=cmp.ced_bene)  ".
             	  "			    ELSE 'Ninguno' END) as nombre  ".
				  "	FROM sigesp_cmp cmp ".$tablaAdicional.
				  "	WHERE cmp.codemp='".$codemp."'  ".
			  	  $tipocmp.
				  "	   AND ($cadenConcat1  IN  (SELECT $cadenConcat2 FROM spg_dt_cmp detcmp) ) AND ".
				  "		  {$cadenacmp} {$cadenaFiltro}  ".
		          " GROUP BY cmp.codemp,cmp.comprobante,cmp.descripcion,cmp.procede,cmp.fecha,cmp.total,cmp.tipo_comp,  ".
		          "          cmp.cod_pro,cmp.ced_bene,cmp.tipo_destino,cmp.codban,cmp.ctaban ".
				  "	ORDER BY cmp.fecha,cmp.comprobante,cmp.procede ASC";	
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarComprobantes ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	
	public function buscarModificaciones($codemp,$comprobante,$procede,$fecdesde,$fechasta,$estapro)
	{
		$cadenaFiltro = '';
		$cadenacmp = ConexionBaseDatos::criterioUpperSIGESP('sigesp_cmp_md.comprobante', "'%{$comprobante}%'", 'LIKE');
		if(!empty($procede))
		{
			$cadenaFiltro=$cadenaFiltro." AND sigesp_cmp_md.procede = '".$procede."'";
		}
		if(((!empty($fecdesde))&&($fecdesde!="01/01/1900"))&&((!empty($fechasta))&&($fechasta!="01/01/1900")))
		{
			$fecdesde=convertirFechaBd($fecdesde);
			$fechasta=convertirFechaBd($fechasta);
			$cadenaFiltro=$cadenaFiltro." AND sigesp_cmp_md.fecha>='".$fecdesde."' AND sigesp_cmp_md.fecha<='".$fechasta."'";
		}	
		$cadenasql="SELECT sigesp_cmp_md.comprobante,sigesp_cmp_md.descripcion,sigesp_cmp_md.procede,sigesp_cmp_md.fecha ".
				  "	FROM sigesp_cmp_md ".
				  "	WHERE sigesp_cmp_md.codemp='".$codemp."'  ".
				  "	  AND {$cadenacmp} {$cadenaFiltro}  ".
				  "	  AND sigesp_cmp_md.estapro = {$estapro}  ".
		          " GROUP BY sigesp_cmp_md.codemp,sigesp_cmp_md.comprobante,sigesp_cmp_md.descripcion,sigesp_cmp_md.procede,sigesp_cmp_md.fecha ".
				  "	ORDER BY sigesp_cmp_md.fecha,sigesp_cmp_md.comprobante,sigesp_cmp_md.procede ASC";	
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarComprobantes ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	

	public function cargarDetalleContable($codemp,$procede,$comprobante,$fecha,$codban,$ctaban)
	{
		$fecha=convertirFechaBd($fecha);
		$cadenasql="SELECT DISTINCT DT.sc_cuenta as sc_cuenta, C.denominacion as denominacion, DT.procede_doc as procede_doc, ".
				"	       P.desproc as despro, DT.documento as documento, DT.fecha as fecha, DT.debhab as debhab, ".
				" 		   DT.descripcion as descripcion, DT.monto as monto, DT.orden as orden " .
				"   FROM scg_dt_cmp DT, scg_cuentas C, sigesp_procedencias P ".
				"   WHERE DT.codemp='".$codemp."' ".
				"     AND DT.procede='".$procede."' ".
				"     AND DT.comprobante='".$comprobante."' ".
			    "     AND DT.fecha= '".$fecha."' ".
				"     AND DT.codban= '".$codban."' ".
				"     AND DT.ctaban= '".$ctaban."' ".
				"	  AND DT.sc_cuenta=C.sc_cuenta AND DT.procede=P.procede ".
				"   ORDER BY DT.debhab, DT.orden ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->cargarDetalleContable ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	public function cargarDetallePresupuestario($codemp,$procede,$comprobante,$fecha,$codban,$ctaban)
	{
		$fecha=convertirFechaBd($fecha);
		switch($_SESSION["la_empresa"]["estmodest"]){
			case "1": // Modalidad por Proyecto
				$codest1 = "SUBSTR(DT.codestpro1,length(DT.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(DT.codestpro2,length(DT.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(DT.codestpro3,length(DT.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$cadenaEstructura = $this->conexionbd->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
				break;
			case "2": // Modalidad por Programatica
				$codest1 = "SUBSTR(DT.codestpro1,length(DT.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(DT.codestpro2,length(DT.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(DT.codestpro3,length(DT.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$codest4 = "SUBSTR(DT.codestpro4,length(DT.codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
				$codest5 = "SUBSTR(DT.codestpro5,length(DT.codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
				$cadenaEstructura = $this->conexionbd->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
				break;
		}
		$cadenasql="SELECT DISTINCT DT.spg_cuenta as spg_cuenta,DT.descripcion as descripcion,DT.procede_doc as procede_doc,  ".
		        "          DT.codestpro1 as codestpro1,DT.codestpro2 as codestpro2,  ".
				"          DT.codestpro3 as codestpro3,DT.codestpro4 as codestpro4,DT.codestpro5 as codestpro5, ".
		        "          DT.estcla as estcla,DT.documento as documento, trim(DT.operacion) as operacion,  ".
		        "          DT.monto as monto,DT.codfuefin as codfuefin,{$cadenaEstructura} as codestpro" .
				"   FROM spg_dt_cmp DT, spg_cuentas C, sigesp_procedencias P  ".
				"   WHERE DT.codemp='".$codemp."' ".
				"     AND DT.procede='".$procede."' ".
				"     AND DT.comprobante='".$comprobante."' ".
			    "     AND DT.fecha= '".$fecha."' ".
				"     AND DT.codban= '".$codban."' ".
				"     AND DT.ctaban= '".$ctaban."' ".
				"	  AND DT.spg_cuenta=C.spg_cuenta AND DT.procede=P.procede ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->cargarDetallePresupuestario ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}

	public function guardar($codemp,$objson,$arrevento)
	{
		$arrDetCon = $objson->detallesContable;
		$arrDetPre = $objson->detallesPresupuestario;
		$i=0;
		$j=0;
		$arrcabecera = array();
		$arregloSCG = array();
		$arregloSPG = array();
		$fecha = convertirFechaBd($objson->fecha);
		DaoGenerico::iniciarTrans();  		
		
		if($this->valido)
		{
			if(!validarFechaPeriodo($fecha))
			{
				$this->mensaje .=  'Verifique que el periodo, y el mes de la empresa est&#233;n abiertos';
				$this->valido = false;	
			}
		}
		if($this->valido)
		{
			if($objson->tipo_destino=='P')
			{
				$codpro = $objson->cod_pro;
				$cedbene = '----------';
			}
			else if($objson->tipo_destino=='B')
			{
				$cedbene = $objson->cod_pro;
				$codpro = '----------';
			}
			else
			{
				$cedbene = '----------';
				$codpro = '----------';
			}
			if(($this->utilizaprefijo)&&($objson->evento=='INSERT'))
			{
                                if (fillComprobante($objson->comprobante)!=$this->nronuevo)
				{
					$objson->comprobante=$this->nronuevo;
					$this->mensaje .= " Le fue asignado el numero de comprobante ".$this->nronuevo.", ";
				}
			}
			$arrcabecera['codemp'] = $codemp;
			$arrcabecera['procede'] = $objson->procede;
			$arrcabecera['comprobante'] = fillComprobante($objson->comprobante);
			$arrcabecera['codban'] = $objson->codban;
			$arrcabecera['ctaban'] = $objson->ctaban;
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $objson->descripcion;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $objson->tipo_destino;
			$arrcabecera['cod_pro'] = $codpro;
			$arrcabecera['ced_bene'] = $cedbene;
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = $objson->estrenfon;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['total'] = $objson->totalpre;
			$arrcabecera['numconcom'] = $objson->numconcom;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			foreach ($arrDetPre as $detalle)
			{
				$i++;
				$codfuefin='--';
				if(!empty($detalle->codfuefin))
				{
					$codfuefin=$detalle->codfuefin;
				}
				$arregloSPG[$i]['codemp']=$codemp;
				$arregloSPG[$i]['procede']= $arrcabecera['procede'];
				$arregloSPG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSPG[$i]['codban']= $arrcabecera['codban'];
				$arregloSPG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSPG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSPG[$i]['orden']= $i;	
				$arregloSPG[$i]['descripcion']= $detalle->descripcion;		
				$arregloSPG[$i]['spg_cuenta'] = $detalle->spg_cuenta;
				$arregloSPG[$i]['procede_doc'] = $detalle->procede_doc;
				$arregloSPG[$i]['documento'] = $detalle->documento;
				$arregloSPG[$i]['operacion'] = $detalle->operacion;
				$arregloSPG[$i]['estcla'] = $detalle->estcla;
				$arregloSPG[$i]['codestpro1'] = $detalle->codestpro1;
				$arregloSPG[$i]['codestpro2'] = $detalle->codestpro2;
				$arregloSPG[$i]['codestpro3'] = $detalle->codestpro3;
				$arregloSPG[$i]['codestpro4'] = $detalle->codestpro4;
				$arregloSPG[$i]['codestpro5'] = $detalle->codestpro5;
				$arregloSPG[$i]['codfuefin'] = $codfuefin;
				$arregloSPG[$i]['monto'] = formatoNumericoBd($detalle->monto,1);
			}
			$numconcom="000000000000000";
			if(($arregloSPG[1]['operacion']=='CS')||($arregloSPG[1]['operacion']=='CG')||($arregloSPG[1]['operacion']=='CCP'))
			{
				$this->daoComprobante = FabricaDao::CrearDAO("N", "sigesp_cmp");
				$this->daoComprobante->codemp=$codemp;
				$numconcom=$this->daoComprobante->buscarCodigo('numconcom',true,15,'','MIS','NUMCON',$_SESSION['la_logusr'],'nroinicom','');
			}
			if(($arrcabecera['numconcom']=="")||(($arrcabecera['numconcom']=="000000000000000")))
			{
				$arrcabecera['numconcom'] = $numconcom;
			}
			if(count($arrDetCon)>0)
			{
				foreach ($arrDetCon as $detalle)
				{
					$j++;
					$arregloSCG[$j]['codemp']=$codemp;
					$arregloSCG[$j]['procede']= $arrcabecera['procede'];
					$arregloSCG[$j]['comprobante']= $arrcabecera['comprobante'];
					$arregloSCG[$j]['codban']= $arrcabecera['codban'];
					$arregloSCG[$j]['ctaban']= $arrcabecera['ctaban'];
					$arregloSCG[$j]['fecha']= $arrcabecera['fecha'];
					$arregloSCG[$j]['orden']= $j;
					$arregloSCG[$j]['descripcion']= $detalle->descripcion;			
					$arregloSCG[$j]['sc_cuenta'] = $detalle->sc_cuenta;
					$arregloSCG[$j]['procede_doc'] = $detalle->procede_doc;
					$arregloSCG[$j]['documento'] = $detalle->documento;
					$arregloSCG[$j]['debhab'] = $detalle->debhab;
					$arregloSCG[$j]['monto'] = formatoNumericoBd($detalle->monto,1);
				}
				if($objson->evento!='UPDATE')
				{
                                        $serviciocomprobante = new ServicioComprobante();
                                        $serviciocomprobante->prefijo = $objson->prefijo;
					$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arregloSPG,$arregloSCG,null,$arrevento,$this->utilizaprefijo);
					$this->mensaje .= $serviciocomprobante->mensaje;
					unset($serviciocomprobante);
				}
				else
				{
					$serviciocomprobante = new ServicioComprobante();
					$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
					$this->mensaje .= $serviciocomprobante->mensaje;
					if($this->valido)
					{
						$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arregloSPG,$arregloSCG,null,$arrevento);
						$this->mensaje .= $serviciocomprobante->mensaje;
					}
					unset($serviciocomprobante);
				}
			}
			else
			{
				if($objson->evento!='UPDATE')
				{

					$serviciocomprobante = new ServicioComprobante();
                                        $serviciocomprobante->prefijo = $objson->prefijo;                                        
					$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arregloSPG,null,null,$arrevento,$this->utilizaprefijo);
					$this->mensaje .= $serviciocomprobante->mensaje;
					unset($serviciocomprobante);
				}
				else
				{
					$serviciocomprobante = new ServicioComprobante();
					$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
					$this->mensaje .= $serviciocomprobante->mensaje;
					if($this->valido)
					{
						$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arregloSPG,null,null,$arrevento);
						$this->mensaje .= $serviciocomprobante->mensaje;
					}
					unset($serviciocomprobante);
				}
			}
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$servicioEvento->desevetra=$arrevento['desevetra'];
			//completando la transaccion retorna 1 si no hay errores
			//$this->valido=false;
			if (DaoGenerico::completarTrans($this->valido)) 
			{
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
				$this->mensaje .= 'Registro guardado con &#233;xito'; 		
			}
			else{
				$servicioEvento->tipoevento=false;
				$servicioEvento->desevetra=$arrevento['desevetra'];
				$servicioEvento->incluirEvento();
				$this->valido=false;
			}
			 
			//liberando variables y retornando el resultado de la operacion
			unset($this->daoRegistroEvento);
		}
		return $this->valido;
	}
	
	public function eliminarLocal($codemp,$objson,$arrevento)
	{
		$i=0;
		$monto=0;
		$arrcabecera = array();
		$fecha = convertirFechaBd($objson->fecha);
		DaoGenerico::iniciarTrans();
		
		if($this->valido){
			if(!validarFechaPeriodo($fecha)){
				$this->mensaje .=  'Verifique que el periodo, y el mes de la empresa est&#233;n abiertos';
				$this->valido = false;	
			}
		}
		if($this->valido){
			if($objson->tipo_destino=='P'){
				$codpro = $objson->cod_pro;
				$cedbene = '----------';
			}
			else if($objson->tipo_destino=='B'){
				$cedbene = $objson->cod_pro;
				$codpro = '----------';
			}
			else{
				$cedbene = '----------';
				$codpro = '----------';
			}
			$arrcabecera['codemp'] = $codemp;
			$arrcabecera['procede'] = $objson->procede;
			$arrcabecera['comprobante'] = fillComprobante($objson->comprobante);
			$arrcabecera['codban'] = $objson->codban;
			$arrcabecera['ctaban'] = $objson->ctaban;
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $objson->descripcion;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $objson->tipo_destino;
			$arrcabecera['cod_pro'] = $codpro;
			$arrcabecera['ced_bene'] = $cedbene;
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $objson->confuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrcabecera['total'] = number_format(0,2,'.','');
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$servicioEvento->desevetra=$arrevento['desevetra'];
	
			//completando la transaccion retorna 1 si no hay errores
			if(DaoGenerico::completarTrans($this->valido)) {
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
				$this->mensaje.='Registro eliminado exitosamente'; 		
			}
			else{
				$servicioEvento->tipoevento=false;
				$servicioEvento->desevetra=$arrevento['desevetra'];
				$servicioEvento->incluirEvento();
				$this->valido=false;
			}
			 
			//liberando variables y retornando el resultado de la operacion
			unset($this->daoRegistroEvento);
		}
		return $this->valido;
	}
	
	public function generarConsecutivo($codemp, $logusr, $procede, $prefijo)
	{
		$comprobante='';
		$this->daoComprobante = FabricaDao::CrearDAO("N", "sigesp_cmp");
		$this->daoComprobante->codemp =$codemp;
		$comprobante = $this->daoComprobante->buscarCodigo("comprobante",true,15,array('procede'=>$procede),'SPG', $procede, $logusr, '', $prefijo);
		unset($this->daoComprobante);
		return $comprobante;
	}

	public function verificarPrefijo($codemp, $procede)
	{
		$existeControl=1;
		$criterio="codemp = '".$codemp."' AND procede='".$procede."' ";
		$this->daoControl = FabricaDao::CrearDAO('C','sigesp_prefijos','',$criterio);
		if($this->daoControl->codemp=='')
		{
			$existeControl=0;
		}
		unset($this->daoControl);
		return $existeControl;
	}

	public function buscarPrefijosUsuarios()
	{
		$cadenasql = "SELECT prefijo ".
                             "  FROM sigesp_dt_prefijos ".
                             " WHERE codemp  = '".$this->codemp."'  ".
                             "   AND codsis  = 'SPG' ".
                             "   AND procede = 'SPGCMP' ".
                             "   AND TRIM(codusu)  = '".$this->logusr."' ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->->buscarPrefijosUsuarios ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
         
}
?>