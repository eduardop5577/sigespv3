<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
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
require_once ($dirsrv."/modelo/servicio/scg/sigesp_srv_scg_icomprobante_contable.php");
require_once ($dirsrv."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobante.php");

class ServicioComprobanteContable implements IComprobanteContable
{
	public  $mensaje; 
	public  $valido; 
	public  $dirsrv;
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
		$this->dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
		$this->daoComprobante = FabricaDao::CrearDAO('N', 'sigesp_cmp');
		$this->daoComprobante->codemp=$this->codemp;
		$this->utilizaprefijo = $this->daoComprobante->utilizaPrefijo('SCG','SCGCMP',$_SESSION['la_logusr']);
		if($this->utilizaprefijo)
		{
			$this->nronuevo=$this->daoComprobante->buscarCodigo('comprobante',true,15,array('procede'=>'SCGCMP'),'SCG','SCGCMP',$_SESSION['la_logusr'],'',$prefijo);
		}
		unset($this->daoComprobante);
	}
	
	public function buscarCtasCont($codemp,$cuenta,$denominacion,$status)
	{
		$cadenaFiltro = "";
		if(!empty($status)){ 
			$cadenaFiltro = "    AND status='".$status."'  ";
		}
		$dencue = ConexionBaseDatos::criterioUpperSIGESP('denominacion', "'%{$denominacion}%'", 'LIKE');
		$cadenasql = " 	SELECT 	trim(sc_cuenta) as sc_cuenta, max(denominacion) as denominacion, ". 
					 " 			max(status) as status ".
					 " 	FROM scg_cuentas ".
					 " 	WHERE codemp='".$codemp."' ".
					 "    AND sc_cuenta like '".$cuenta."%'  ".
					 "    AND {$dencue}  {$cadenaFiltro} ".
				 	 " 	GROUP BY sc_cuenta  ".
				 	 " 	ORDER BY sc_cuenta ASC  ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->buscarCtasCont ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	public function buscarProcedencias()
	{
		$cadenasql = " SELECT procede, desproc FROM sigesp_procedencias ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->buscarProcedencias ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	public function buscarComprobantes($codemp,$comprobante,$procede,$tipo,$provben,$fecdesde,$fechasta,$tipcom)
	{
		$cadenConcat = $this->conexionbd->Concat('ben.apebene',"','",'ben.nombene');
		$cadenainnner1 = $this->conexionbd->Concat('cmp.codemp','cmp.procede','cmp.comprobante','cmp.codban','cmp.ctaban');
		$cadenainnner2 = $this->conexionbd->Concat('codemp','procede','comprobante','codban','ctaban');
		$cadenaFiltro = '';
		$cadenacmp = ConexionBaseDatos::criterioUpperSIGESP('cmp.comprobante', "'%{$comprobante}%'", 'LIKE');
		if(!empty($procede))
		{
			$cadenaFiltro=$cadenaFiltro." AND cmp.procede = '".$procede."' ";
		}
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
		if (!empty($tipcom)) {
			if ($tipcom == 'SPG') {
				$cadenaFiltro .= " AND cmp.procede <> 'SCGCMP'";
			}
			elseif($tipcom == 'SCG')
			{
				$cadenaFiltro .= " AND (cmp.procede <> 'SPGCMP')";
			}
		}
		$cadenasql="SELECT cmp.comprobante,cmp.descripcion,cmp.procede,cmp.fecha,cmp.tipo_comp,cmp.cod_pro,cmp.ced_bene, ".
       		      "		   cmp.tipo_destino,cmp.codban,cmp.ctaban, ".
       			  "		   (CASE WHEN cmp.tipo_destino='P'  ".
             	  "				THEN   ".
               	  "				 (SELECT nompro  ".
                  "				  FROM rpc_proveedor pro ".
                  " 			  WHERE pro.codemp=cmp.codemp AND pro.cod_pro=cmp.cod_pro)  ".
             	  "				 WHEN cmp.tipo_destino='B'  ".
	     		  "				THEN  ".
				  "				 (SELECT {$cadenConcat}  ".
		 		  "				  FROM rpc_beneficiario ben  ".
		 		  "				  WHERE ben.codemp=cmp.codemp AND ben.ced_bene=cmp.ced_bene)  ".
             	  "			    ELSE 'Ninguno' END) as nombre,  ".
             	  "		   (CASE WHEN cmp.procede = 'SCGCMP'  ".
             	  "		        THEN   ".
             	  "		         (SELECT SUM(monto)  ".
             	  "		          FROM scg_dt_cmp  ".
             	  "		          WHERE cmp.codemp=scg_dt_cmp.codemp AND cmp.procede=scg_dt_cmp.procede ".
             	  "                  AND cmp.comprobante=scg_dt_cmp.comprobante AND cmp.fecha=scg_dt_cmp.fecha ".
		          "                  AND cmp.codban=scg_dt_cmp.codban AND cmp.ctaban=scg_dt_cmp.ctaban AND debhab='D' ".
             	  "		          GROUP BY scg_dt_cmp.codemp,scg_dt_cmp.procede,scg_dt_cmp.comprobante,scg_dt_cmp.fecha,  ".
		          "                        scg_dt_cmp.codban,scg_dt_cmp.ctaban) ".
             	  " 		    ELSE  ".
             	  "		          cmp.total END) AS monto  ".
				  "	FROM sigesp_cmp cmp ".
				  "	WHERE cmp.codemp='".$codemp."' AND ".
				  "		  {$cadenacmp} {$cadenaFiltro}  ".
				  "   AND ".$cadenainnner1." IN (SELECT ".$cadenainnner2." FROM scg_dt_cmp )".
		          " GROUP BY cmp.codemp,cmp.comprobante,cmp.descripcion,cmp.procede,cmp.fecha,cmp.total,cmp.tipo_comp,  ".
		          "          cmp.cod_pro,cmp.ced_bene,cmp.tipo_destino,cmp.codban,cmp.ctaban ".
				  "	ORDER BY cmp.fecha,cmp.comprobante,cmp.procede ASC ";
			
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->buscarComprobantes ERROR->'.$this->conexionbd->ErrorMsg();
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
			$this->mensaje .= ' CLASE->SCG MÉTODO->buscarComprobantes ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}

	public function guardarCmpCon($codemp,$objson,$arrevento)
	{
		DaoGenerico::iniciarTrans();  	
		$arrDetCon= $objson->detallesContables;
		$i=0;
		$monto=0;
		$arrcabecera = array();
		$arregloSCG = array();
		$fecha = convertirFechaBd($objson->fecha);
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
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = $objson->codfuefin;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			foreach ($arrDetCon as $detalle)
			{
				$i++;
				$arregloSCG[$i]['codemp']=$codemp;
				$arregloSCG[$i]['procede']= $arrcabecera['procede'];
				$arregloSCG[$i]['comprobante']= $arrcabecera['comprobante'];
				$arregloSCG[$i]['codban']= $arrcabecera['codban'];
				$arregloSCG[$i]['ctaban']= $arrcabecera['ctaban'];
				$arregloSCG[$i]['fecha']= $arrcabecera['fecha'];
				$arregloSCG[$i]['descripcion']= $detalle->descripcion;
				$arregloSCG[$i]['orden']= $i;			
				$arregloSCG[$i]['sc_cuenta'] = $detalle->sc_cuenta;
				$arregloSCG[$i]['procede_doc'] = $detalle->procede_doc;
				$arregloSCG[$i]['documento'] = $detalle->documento;
				$arregloSCG[$i]['debhab'] = $detalle->debhab;
				$montodebhab =formatoNumericoBd($detalle->monto,1);
				$montodebhab = number_format($montodebhab,2,'.','');
				$arregloSCG[$i]['monto'] = $montodebhab;
				if($detalle->debhab=='D')
				{
					$monto=$monto+$montodebhab;
				}
			}
			
			if($objson->evento!='UPDATE')
			{
				$arrcabecera['total'] = number_format($monto,2,'.','');
				$serviciocomprobante = new ServicioComprobante();
                                $serviciocomprobante->prefijo = $objson->prefijo;
				$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,null,$arregloSCG,null,$arrevento,$this->utilizaprefijo);
				$this->mensaje .= $serviciocomprobante->mensaje;
				unset($serviciocomprobante);
			}
			else
			{
				$arrcabecera['total'] = number_format($monto,2,'.','');
				$serviciocomprobante = new ServicioComprobante();
				$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
				$this->mensaje .= $serviciocomprobante->mensaje;
				if($this->valido)
				{
					$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,null,$arregloSCG,null,$arrevento);
					$this->mensaje .= $serviciocomprobante->mensaje;
				}
				unset($serviciocomprobante);
			}
			
			
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$servicioEvento->desevetra=$arrevento['desevetra'];
		
			if (DaoGenerico::completarTrans($this->valido)) 
			{
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
				$this->mensaje.='Registro guardado con &#233;xito'; 		
			}
			else
			{
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
		
	public function eliminarCmpCon($codemp,$objson,$arrevento)
	{
		$i=0;
		$monto=0;
		$arrcabecera = array();
		$arregloSCG = array();
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
	
	public function generarConsecutivo($prefijo)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////
		// 	 Function:  uf_scg_verificar_configuracion
		// 	   Access:  public
		//  Arguments:  $as_codemp->código de la empresa
		//              $as_logusr->código del usuario
		//	  Returns:  Boolean
		//Description:  Este método verifica si existe una configuración definida para la numeración de los comprobantes contables
		////////////////////////////////////////////////////////////////////////////////////////////////////
		$comprobante='';
		$cadenasql= "SELECT estcompscg, sigesp_prefijos.prefijo ".
                            "  FROM sigesp_prefijos  ".
                            " INNER JOIN sigesp_dt_prefijos".
                            "    ON sigesp_prefijos.codemp  = '".$this->codemp."'  ".
                            "   AND sigesp_prefijos.codsis  = 'SCG' ".
                            "   AND sigesp_prefijos.procede = 'SCGCMP' ".
                            "   AND sigesp_prefijos.prefijo = '".$prefijo."' ".
                            "   AND TRIM(sigesp_dt_prefijos.codusu)  = '".$this->logusr."' ".
                            "   AND sigesp_prefijos.codemp  = sigesp_dt_prefijos.codemp  ".
                            "   AND sigesp_prefijos.id  = sigesp_dt_prefijos.id ".
                            "   AND sigesp_prefijos.codsis  = sigesp_dt_prefijos.codsis ".
                            "   AND sigesp_prefijos.procede = sigesp_dt_prefijos.procede ".
                            "   AND sigesp_prefijos.prefijo = sigesp_dt_prefijos.prefijo ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->generarConsecutivo ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{  
		    if (!$resultado->EOF)
                    {
				if ($resultado->fields['estcompscg']=='1')
				{
					$this->daoComprobante = FabricaDao::CrearDAO("N", "sigesp_cmp");
					$this->daoComprobante->codemp =$this->codemp;
					$comprobante = $this->daoComprobante->buscarCodigo("comprobante",true,15,array('procede'=>'SCGCMP'),'SCG','SCGCMP',$this->logusr,'',$prefijo);
					unset($this->daoComprobante);
				}
				else
				{
					$this->daoComprobante = FabricaDao::CrearDAO("N", "sigesp_cmp");
					$this->daoComprobante->codemp =$this->codemp;
					$comprobante = $this->daoComprobante->buscarCodigo("comprobante",true,15,array('procede'=>'SCGCMP'),'SCG','SCGCMP',$this->logusr,'',$prefijo);
					unset($this->daoComprobante);
				}
                    }
                    else
                    {
                                $this->daoComprobante = FabricaDao::CrearDAO("N", "sigesp_cmp");
				$this->daoComprobante->codemp =$this->codemp;
				$comprobante = $this->daoComprobante->buscarCodigo("comprobante",true,15,array('procede'=>'SCGCMP'),'SCG','SCGCMP',$this->logusr,'',$prefijo);
				unset($this->daoComprobante);			
                    }
		}
		return $comprobante;
	} // end function uf_scg_verificar_configuracion

	public function buscarPrefijosUsuarios()
	{
		$cadenasql = "SELECT prefijo ".
                             "  FROM sigesp_dt_prefijos ".
                             " WHERE codemp  = '".$this->codemp."'  ".
                             "   AND codsis  = 'SCG' ".
                             "   AND procede = 'SCGCMP' ".
                             "   AND TRIM(codusu)  = '".$this->logusr."' ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->->buscarPrefijosUsuarios ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
        
	public function guardarCmpConlot($codemp,$objson,$arrevento)
	{
		DaoGenerico::iniciarTrans();
		$arrComprobante= $objson->detallesComprobantes;
		$mensajesComprobantes = '';
		foreach ($arrComprobante as $comprobante)
		{
			$arrcabecera = array();
			$fecha=ereg_replace("[^A-Za-z0-9]", "", trim($comprobante->fecha));
			$fechacmp = convertirFechaBd($comprobante->fecha);
			if(($this->valido)&&($fechacmp<>''))
			{
				if(!validarFechaPeriodo($fechacmp))
				{
					$this->mensaje .=  'Verifique que el periodo, y el mes de la empresa est&#233;n abiertos';
					$this->valido = false;	
				}
			}
			if(($this->valido)&&($fechacmp<>''))
			{
                                $arrcabecera['codemp'] = $codemp;
				$arrcabecera['procede'] ='SCGCMP';
				$arrcabecera['comprobante'] = fillComprobante($comprobante->comprobante);
				$arrcabecera['codban'] = '---';
				$arrcabecera['ctaban'] = '-------------------------';
				$arrcabecera['fecha'] = $fechacmp;
				$arrcabecera['descripcion'] = $comprobante->descripcion;
				$arrcabecera['tipo_comp'] = 1;
				$arrcabecera['tipo_destino'] = '-';
				$arrcabecera['cod_pro'] = '----------';
				$arrcabecera['ced_bene'] = '----------';
				$arrcabecera['numpolcon'] = 0;
				$arrcabecera['esttrfcmp'] = 0;
				$arrcabecera['estrenfon'] = 0;
				$arrcabecera['codfuefin'] = '--';
				$arrcabecera['codusu'] = $_SESSION['la_logusr'];
				$monto=0;
				$arregloSCG = array();
				$nombrearchivo = $this->dirsrv.'/vista/scg/txt/'.$_SESSION['la_logusr'].'.txt';
				if (file_exists("$nombrearchivo"))
				{
					$archivo=@file("$nombrearchivo");
					$total=count((array)$archivo);
					$contador=1;
					$montodebhab = O;
					$arrCuentas = array(); 
					for($i=0;($i<$total);$i++)
					{
						$contable=explode("|",$archivo[$i]);
						$fecha2=ereg_replace("[^A-Za-z0-9]", "", trim($contable[0]));
						if ($fecha == $fecha2)
						{
							$montodebhab = str_replace(',','.',$contable[9]);
							$montodebhab = doubleval($montodebhab);
							$montodebhab = number_format($montodebhab,2,'.','');
							$debhab='H';
							if (trim($contable[7]) == '40')
							{
								$debhab='D';
								$monto=$monto+$montodebhab;
							}
							$sc_cuenta=trim($contable[8]);
							if(!in_array($sc_cuenta,$arrCuentas,true))
							{
								$arregloSCG[$contador]['codemp']=$codemp;
								$arregloSCG[$contador]['procede']= $arrcabecera['procede'];
								$arregloSCG[$contador]['comprobante']= $arrcabecera['comprobante'];
								$arregloSCG[$contador]['codban']= $arrcabecera['codban'];
								$arregloSCG[$contador]['ctaban']= $arrcabecera['ctaban'];
								$arregloSCG[$contador]['fecha']= $arrcabecera['fecha'];
								$arregloSCG[$contador]['descripcion']= $arrcabecera['descripcion'];
								$arregloSCG[$contador]['procede_doc'] = $arrcabecera['procede'];
								$arregloSCG[$contador]['documento'] = $arrcabecera['comprobante'];
								$arregloSCG[$contador]['orden']= $contador;			
								$arregloSCG[$contador]['sc_cuenta'] = trim($contable[8]);
								$arregloSCG[$contador]['debhab'] =$debhab;
								$arregloSCG[$contador]['monto'] = $montodebhab;
								$arrCuentas[$contador]= trim($contable[8]);
								$contador++;
							}
							else
							{
								$clave = array_search($sc_cuenta,$arrCuentas);
								if ($debhab==$arregloSCG[$clave]['debhab'])
								{
									$arregloSCG[$clave]['monto'] = $arregloSCG[$clave]['monto'] + $montodebhab;
								}
								else 
								{
									$arregloSCG[$clave]['monto'] = $arregloSCG[$clave]['monto'] - $montodebhab;
								}
								if ($arregloSCG[$clave]['monto']<0)
								{
									if($arregloSCG[$clave]['debhab']=='D')
									{
										$arregloSCG[$clave]['debhab']='H';
										$arregloSCG[$clave]['monto'] = abs($arregloSCG[$clave]['monto']);
									}
									else
									{
										$arregloSCG[$clave]['debhab']='D';
										$arregloSCG[$clave]['monto'] = abs($arregloSCG[$clave]['monto']);
									}
									
								}
							}
						}
					}
				}
				if(($contador==0)||($monto==0))
				{
					$this->mensaje .=  'Verifique los detalles del comprobante '.$arrcabecera['comprobante']." - ".$arrcabecera['fecha'];
					$this->valido = false;	
				}
				else
				{
					$arrcabecera['total'] = number_format($monto,2,'.','');
					$serviciocomprobante = new ServicioComprobante();
					$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,null,$arregloSCG,null,$arrevento,$this->utilizaprefijo);
					$this->mensaje .= $serviciocomprobante->mensaje;
					if ($this->valido)
					{
						$mensajesComprobantes .= $arrcabecera['comprobante']."-".$arrcabecera['fecha'].", ";
					}
					unset($serviciocomprobante);
				}				
			}
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Insertó los comprobantes '.$mensajesComprobantes." en lote.";
	
		if (DaoGenerico::completarTrans($this->valido)) 
		{
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento();
			$this->mensaje.='Registro guardado con &#233;xito'; 		
		}
		else
		{
			$servicioEvento->tipoevento=false;
			$servicioEvento->desevetra=$arrevento['desevetra'];
			$servicioEvento->incluirEvento();
			$this->valido=false;
		}
		//liberando variables y retornando el resultado de la operacion
		unset($this->daoRegistroEvento);
		return $this->valido;
	}

}
?>