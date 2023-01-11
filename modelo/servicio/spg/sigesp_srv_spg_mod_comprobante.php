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
require_once ($dirsrv.'/modelo/servicio/spg/sigesp_srv_spg_imod_comprobante.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');


class ServicioModComprobante implements IModComprobante 
{
	private $daoComprobante;
	public  $mensaje; 
	public  $valido;
	public  $conexionBaseDatos; 
		
	public function __construct($prefijo='') 
	{
		$this->daoComprobante = null;
		$this->mensaje = '';
		$this->valido = true;
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$this->bloanu = $_SESSION['la_empresa']['bloanu'];
                $this->codemp = $_SESSION['la_empresa']['codemp'];
                $this->logusr = $_SESSION["la_logusr"];
                $this->prefijo=$prefijo;
	}
	
	public function buscarComprobantes($codemp,$comprobante,$procede,$fecdesde,$fechasta)
	{
		$logusr = $_SESSION["la_logusr"];
		$cadena1Concat = $this->conexionBaseDatos->Concat("'$codemp'","'SPG'","'$logusr'",'spg_dtmp_cmp.codestpro1',
		                                           'spg_dtmp_cmp.codestpro2','spg_dtmp_cmp.codestpro3','spg_dtmp_cmp.codestpro4',
		                                           'spg_dtmp_cmp.codestpro5','spg_dtmp_cmp.estcla');
		$cadena2Concat = $this->conexionBaseDatos->Concat('codemp','codsis','codusu','codintper');
		$cadena = "AND $cadena1Concat IN (SELECT $cadena2Concat FROM sss_permisos_internos WHERE codusu='".$logusr."' AND codsis='SPG' AND enabled=1) "; 
		if(!empty($comprobante)){
			$cadenaFiltro=$cadenaFiltro." AND sigesp_cmp_md.comprobante like '".$comprobante."'  ";
		}
		if(((!empty($fecdesde))&&($fecdesde!="01/01/1900"))&&((!empty($fechasta))&&($fechasta!="01/01/1900")))
		{
			$fecdesde=convertirFechaBd($fecdesde);
			$fechasta=convertirFechaBd($fechasta);
			$cadenaFiltro=$cadenaFiltro." AND sigesp_cmp_md.fecha>='".$fecdesde."' AND sigesp_cmp_md.fecha<='".$fechasta."'  ";
		}	
		$cadenasql=" SELECT distinct  sigesp_cmp_md.* ,spg_ministerio_ua.denuac ".
		        "    FROM sigesp_cmp_md, spg_dtmp_cmp, spg_ministerio_ua  ".
		        "    WHERE sigesp_cmp_md.codemp='".$codemp."' $cadenaFiltro ".
		        "      AND sigesp_cmp_md.procede='".$procede."' ".
				"      AND sigesp_cmp_md.codemp = spg_dtmp_cmp.codemp ".
			    "      AND sigesp_cmp_md.fecha = spg_dtmp_cmp.fecha ".
   				"      AND sigesp_cmp_md.procede = spg_dtmp_cmp.procede ".
				"      AND sigesp_cmp_md.comprobante = spg_dtmp_cmp.comprobante ".
				"      AND spg_ministerio_ua.codemp=sigesp_cmp_md.codemp ".
				"      AND spg_ministerio_ua.coduac=sigesp_cmp_md.coduac ";
		$cadenasql=$cadenasql.$cadena;
		$resultado = $this->conexionBaseDatos->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarComprobantes ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	public function buscarUnidadAdministrativa($codemp,$codigo,$denominacion)
	{
		$cadenaFiltro = "";
		$cadenasql = "";
		if(!empty($codigo)){
			$cadenaFiltro = $cadenaFiltro." AND coduac like '%".$codigo."%' ";
		}
		if(!empty($denominacion)){
			$cadenaFiltro = $cadenaFiltro. " AND UPPER(denuac) like '%".strtoupper($denominacion)."%' ";
		}
		$cadenasql = " SELECT coduac,denuac ".
	              "    FROM spg_ministerio_ua  ".
		          "    WHERE codemp='".$codemp."'  ".
		          "      AND coduac <>'-----'  ".
				  "    ORDER BY coduac ASC";
		$resultado = $this->conexionBaseDatos->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarUnidadAdministrativa ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	public function buscarUnidadesEjecutoras($codemp,$codigo,$denominacion)
	{
		$logusr = $_SESSION["la_logusr"];
		$gestor = $_SESSION["ls_gestor"];
		$sql_seguridad = "";
		$cadenauno = $this->conexionBaseDatos->Concat($codemp,"'SPG'",$logusr,'spg_dt_unidadadministrativa.codestpro1',
		                                              'spg_dt_unidadadministrativa.codestpro2','spg_dt_unidadadministrativa.codestpro3',
		                                              'spg_dt_unidadadministrativa.codestpro4','spg_dt_unidadadministrativa.codestpro5',
		                                              'spg_dt_unidadadministrativa.estcla');
		$cadenados = $this->conexionBaseDatos->Concat('codemp','codsis','codusu','codintper');
		$sql_seguridad = $sql_seguridad." AND $cadenauno IN (SELECT distinct $cadenados FROM sss_permisos_internos WHERE codusu = '".$logusr."' AND codsis = 'SPG' AND enabled=1) ";
		$sql=" SELECT distinct spg_unidadadministrativa.coduniadm, spg_unidadadministrativa.denuniadm ".
			 " FROM spg_unidadadministrativa, spg_dt_unidadadministrativa ".
			 " WHERE spg_unidadadministrativa.codemp='".$codemp."' AND spg_unidadadministrativa.coduniadm like '%".$codigo."%' AND spg_unidadadministrativa.denuniadm like '%".$denominacion."%' "; 
		$resultado = $this->conexionBaseDatos->Execute($sql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->cargarDetallePresupuestario ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	
	public function cargarDetallePresupuestario($codemp,$procede,$comprobante,$fecha)
	{
		$fecha=convertirFechaBd($fecha);
		switch($_SESSION["la_empresa"]["estmodest"]){
			case "1": // Modalidad por Proyecto
				$codest1 = "SUBSTR(DT.codestpro1,length(DT.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(DT.codestpro2,length(DT.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(DT.codestpro3,length(DT.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
				break;
			case "2": // Modalidad por Programatica
				$codest1 = "SUBSTR(DT.codestpro1,length(DT.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
				$codest2 = "SUBSTR(DT.codestpro2,length(DT.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
				$codest3 = "SUBSTR(DT.codestpro3,length(DT.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
				$codest4 = "SUBSTR(DT.codestpro4,length(DT.codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
				$codest5 = "SUBSTR(DT.codestpro5,length(DT.codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
				$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
				break;
		}
		$cadenasql="SELECT DISTINCT DT.codestpro1 as codest1,DT.codestpro2 as codest2,DT.codestpro3 as codest3, ".
	   	        "          DT.codestpro4 as codest4,DT.codestpro5 as codest5,DT.estcla as estcla,DT.spg_cuenta as spg_cuenta, ".
		        "          C.denominacion as dencuenta, DT.procede_doc as procede_doc, P.desproc as desproc, ".
		        "		   DT.documento as documento, DT.operacion as operacion, DT.descripcion as descripcion, DT.codfuefin as codfuefin, ".
		        "          DT.monto as monto, DT.orden as orden, OP.denominacion as denominacion, {$cadenaEstructura} as codestpro  ".
	            "   FROM spg_dtmp_cmp DT, spg_cuentas C, sigesp_procedencias P, spg_operaciones OP ".
	            "   WHERE DT.procede=P.procede  ".
		        "     AND DT.codemp=C.codemp ".
		        "     AND DT.spg_cuenta=C.spg_cuenta  ".
		        "     AND OP.operacion = DT.operacion  ".
		        "     AND (DT.codestpro1=C.codestpro1  AND DT.codestpro2=C.codestpro2 AND ".
				"         DT.codestpro3=C.codestpro3  AND DT.codestpro4=C.codestpro4   AND DT.codestpro5=C.codestpro5 AND ".
				"         DT.estcla=C.estcla)  ".
		        "     AND DT.codemp='".$codemp."'  ".  
		        "     AND DT.procede='".$procede."' ".
				"     AND DT.comprobante='".$comprobante."'  ".
		        "     AND DT.fecha='".$fecha."' ".
	            "   ORDER BY DT.codestpro1,DT.codestpro2,DT.codestpro3,DT.codestpro4,DT.codestpro5,DT.estcla,DT.spg_cuenta,DT.codfuefin,  ".
	            "            dencuenta,procede_doc,desproc,documento,operacion,descripcion,monto,orden,denominacion  ";
		$resultado = $this->conexionBaseDatos->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->cargarDetallePresupuestario ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
	public function validarComprobanteAprobado($codemp,$comprobante,$procede)
	{	
		$aprobado=false;
		$cadenasql = " SELECT estapro ".
	                 " FROM sigesp_cmp_md ".
		   	         " WHERE codemp = '".$codemp."' ". 
			         "   AND comprobante = '".$comprobante."' ". 
			         "   AND procede = '".$procede."' ". 
				     " ORDER BY comprobante";
		$resultado = $this->conexionBaseDatos->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarCuentaPresupuestaria ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		if(!$resultado->EOF)
		{
			$estatus=$resultado->fields["estapro"];
			if($estatus=="1")
			{
				$aprobado=true;
			}
		}
		return $aprobado;
	}
	
	
	public function guardar($codemp,$objson,$arrevento)
	{
		DaoGenerico::iniciarTrans();  		
		$arrDetPre = $objson->detallesPresupuestario;
		$i=1;
		$arrcabecera = array();
		$arregloSCG = array();
		$arregloSPG = array();
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
			$arrcabecera['codemp'] = $codemp;
			$arrcabecera['procede'] = $objson->procede;
			$arrcabecera['comprobante'] = fillComprobante($objson->comprobante);
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $objson->descripcion;
			$arrcabecera['tipo_comp'] = 2;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['estapro'] = 0;
			$arrcabecera['coduac'] = $objson->coduac;
			$arrcabecera['codtipmodpre'] = '----';
			$arrcabecera['total'] = $objson->monto;
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$aprobado=$this->validarComprobanteAprobado($codemp,$objson->comprobante,$objson->procede);
			if($aprobado)
			{
				$this->mensaje .=  'El comprobante esta aprobado y no se puede modificar';
				$this->valido = false;	
			}
			
			if($this->valido)
			{

				foreach ($arrDetPre as $detalle)
				{
					$arregloSPG[$i]['codemp']=$codemp;
					$arregloSPG[$i]['procede']= $arrcabecera['procede'];
					$arregloSPG[$i]['comprobante']= $arrcabecera['comprobante'];
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
					$arregloSPG[$i]['codfuefin'] = $detalle->codfuefin;
					$arregloSPG[$i]['monto'] = formatoNumericoBd($detalle->monto,1);
					$i++;
				}
				if($i==1)
				{
					$this->mensaje .=  'El Comprobante no tiene detalles, no se puede procesar.';
					$this->valido = false;	
				}
				else
				{
					if($objson->evento!='UPDATE')
					{
						$this->valido = $this->guardarComprobante($arrcabecera,$arregloSPG,$arrevento,$objson->evento);
					}
					else
					{
						$this->valido = $this->eliminarComprobante($arrcabecera,$arrevento);
						if($this->valido)
						{
							$this->valido = $this->guardarComprobante($arrcabecera,$arregloSPG,$arrevento,$objson->evento);
						}
					}
				}	
			}		
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra=$arrevento['desevetra'];

		//completando la transaccion retorna 1 si no hay errores
		if (DaoGenerico::completarTrans($this->valido)) 
		{
			$servicioEvento->tipoevento=true;
			$servicioEvento->incluirEvento();
			$this->mensaje .= 'Registro guardado con &#233;xito'; 		
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
	
	public function eliminarLocal($codemp,$objson,$arrevento)
	{
		$i=0;
		$monto=0;
		$arrcabecera = array();
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
			$arrcabecera['codemp'] = $codemp;
			$arrcabecera['procede'] = $objson->procede;
			$arrcabecera['comprobante'] = fillComprobante($objson->comprobante);
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $objson->descripcion;
			$arrcabecera['tipo_comp'] = 2;
			$arrcabecera['tipo_destino'] = '-';
			$arrcabecera['cod_pro'] = '----------';
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['estapro'] = 0;
			$arrcabecera['coduac'] = $objson->coduac;
			$arrcabecera['codtipmodpre'] = '----';
			$arrcabecera['total'] = formatoNumericoBd($objson->totalcuerec);
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			
			
			
			$this->valido = $this->eliminarComprobante($arrcabecera,$arrevento);
			
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$servicioEvento->desevetra=$arrevento['desevetra'];
			//completando la transaccion retorna 1 si no hay errores
			if(DaoGenerico::completarTrans($this->valido))
			{
				$servicioEvento->incluirEvento();
				$this->mensaje.='Registro eliminado exitosamente'; 		
			}
			else
			{
				$servicioEvento->tipoevento=false;
				$servicioEvento->desevetra=$this->mensaje;
				$servicioEvento->incluirEvento();
				$this->valido=false;
			}
			unset($servicioEvento);
			//liberando variables y retornando el resultado de la operacion
			unset($this->daoRegistroEvento);
		}
		return $this->valido;
	}
	
	public function existeComprobante($codemp,$procede,$comprobante,$fecha) 
	{
		$existe = false;
		$cadenaSql = "SELECT comprobante ".
					 "	FROM sigesp_cmp_md ".
					 " WHERE codemp='{$codemp}' ".
					 "   AND procede='{$procede}' ".
					 "   AND comprobante='{$comprobante}' ".
					 "   AND fecha='{$fecha}' ";
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
		return $existe;
	}

	public function existeProcedencia($procede) 
	{
		$existe = false;
		$cadenaSql = "SELECT procede ".
				     "  FROM sigesp_procedencias ".
				     " WHERE procede='{$procede}'";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->_numOfRows > 0) {
				$existe = true;
			}
		}
		return $existe;
	}

	public function validarComprobante($arrdetallespg)
	{
		$validar=true;
		if((is_null($this->daoComprobante->comprobante)) or (empty($this->daoComprobante->comprobante)))
		{
			$this->mensaje .= 'El Comprobante no puede tener valor nulo o vac&#237;o.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;			
			$this->valido = false;	
		}
		if((is_null($this->daoComprobante->procede)) or (empty($this->daoComprobante->procede)))
		{
			$this->mensaje = 'La procedencia no puede tener valor nulo o vacio .'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;
			$this->valido = false;	
		} 	  
		if((is_null($this->daoComprobante->descripcion)) or (empty($this->daoComprobante->descripcion)))
		{
			$this->mensaje .= 'La descripci&#243;n no puede tener valor nulo o vac&#237;o.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;
			$this->valido = false;	
		} 	
		if((is_null($this->daoComprobante->tipo_destino)) or (empty($this->daoComprobante->tipo_destino)))
		{ 
			$this->mensaje .= 'El Tipo (Beneficiario o Proveedor) no puede tener valor nulo o vac&#237;o.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;
			$this->valido = false;	
		} 	
		if((is_null($this->daoComprobante->cod_pro)) or ($this->daoComprobante->cod_pro=='') or (is_null($this->daoComprobante->ced_bene)) or ($this->daoComprobante->ced_bene==''))
		{
			$this->mensaje .=  'El Beneficiario o Proveedor no puede tener valor nulo o vac&#237;o.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;
			$this->valido = false;	
		}
		if(!($this->existeProcedencia($this->daoComprobante->procede)))
		{ 
			$this->mensaje .=  'El Procede '.$this->daoComprobante->procede.' no Existe.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;
			$this->valido = false;	
		}
		if($validar)
		{
			if (!(validarFechaMes($this->daoComprobante->fecha)))
			{
				$this->mensaje .=  'El Mes '.obtenerNombreMes(substr($this->daoComprobante->fecha,5,2)).' no esta abierto.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;
				$this->valido = false;	
			}
			else
			{
				if(!validarFechaPeriodo($this->daoComprobante->fecha))
				{
					$this->mensaje .=  'La fecha '.substr($this->daoComprobante->fecha,5,2).' Est&#225; fuera del periodo.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;
					$this->valido = false;	
				}
			}
		}
		$totalSPG=count((array)$arrdetallespg);
		if($totalSPG<=0)
		{
			$this->mensaje .=  'El comprobante '.$this->daoComprobante->comprobante.' No tiene detalles.'.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;
			$this->valido = false;	
		}
		return $this->valido;
	}

	public function cargarDetallesComprobante($tipoevento='',$fechaanula='',$procedeanula='',$conceptoanula='')
	{
		$arrdetallespg=null;
		//CARGAMOS LOS DETALLES PRESUPUESARIOS DE GASTO
		$cadenaSql="SELECT codemp,procede,comprobante,estcla,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta,".
				   "       procede_doc,documento,operacion,codfuefin,fecha,descripcion,monto,orden ".
				   "  FROM spg_dtmp_cmp ".
				   " WHERE codemp='".$this->daoComprobante->codemp."' ".
				   "   AND procede='".$this->daoComprobante->procede."' ".
				   "   AND comprobante='".$this->daoComprobante->comprobante."' ".
				   "   AND fecha='".$this->daoComprobante->fecha."'".
				   " ORDER BY orden ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while(!$dataSet->EOF)
			{
				$i++;
				$arrdetallespg[$i]['codemp']=$dataSet->fields['codemp'];
				$arrdetallespg[$i]['procede']= $dataSet->fields['procede'];
				$arrdetallespg[$i]['comprobante']= $dataSet->fields['comprobante'];
				$arrdetallespg[$i]['estcla']=$dataSet->fields['estcla'];
				$arrdetallespg[$i]['codestpro1']=$dataSet->fields['codestpro1'];
				$arrdetallespg[$i]['codestpro2']=$dataSet->fields['codestpro2'];
				$arrdetallespg[$i]['codestpro3']=$dataSet->fields['codestpro3'];
				$arrdetallespg[$i]['codestpro4']=$dataSet->fields['codestpro4'];
				$arrdetallespg[$i]['codestpro5']=$dataSet->fields['codestpro5'];
				$arrdetallespg[$i]['spg_cuenta']=$dataSet->fields['spg_cuenta'];
				$arrdetallespg[$i]['procede_doc']= $dataSet->fields['procede_doc'];
				$arrdetallespg[$i]['documento']= $dataSet->fields['documento'];
				$arrdetallespg[$i]['operacion']= $dataSet->fields['operacion'];
				$arrdetallespg[$i]['codfuefin']=$dataSet->fields['codfuefin'];
				$arrdetallespg[$i]['fecha']= $dataSet->fields['fecha'];
				$arrdetallespg[$i]['descripcion']= $dataSet->fields['descripcion'];
				$arrdetallespg[$i]['monto']=$dataSet->fields['monto'];
				$arrdetallespg[$i]['orden']= $dataSet->fields['orden'];
				$dataSet->MoveNext();
			}
		}
		unset($dataSet);
		$arrResultado['Spg']=$arrdetallespg;
		return $arrResultado;
	}
	
	public function existeCierreSPG()
	{
		$existe = false;
		$cadenaSql = "SELECT estciespg ".
					 "	FROM sigesp_empresa ".
					 " WHERE codemp='".$_SESSION['la_empresa']['codemp']."' ".
					 "   AND estciespg=1";
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
				$this->mensaje .= '  -> Ya se realizo el cierre presupuestario de gasto no se pueden registrar movimientos de este tipo';
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $existe;
	}	
	
	public function existeCuenta() 
	{
		$existe = false;
		$cadenaSql = "SELECT status ".
					 "	FROM spg_cuentas ".
					 " WHERE codemp='".$this->daoDetalleSpg->codemp."' ".
					 "   AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
					 "   AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
					 "   AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
					 "   AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
					 "   AND codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
					 "   AND estcla='".$this->daoDetalleSpg->estcla."' ".
					 "   AND trim(spg_cuenta)='".$this->daoDetalleSpg->spg_cuenta."' ";
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
				$status = $dataSet->fields['status']; 
				if($status==='C')
				{
					$existe = true;
				}
				else
				{
					$this->mensaje .= '  -> La cuenta '.$this->daoDetalleSpg->spg_cuenta.'::'.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.' No es de movimiento';
					$this->valido = false;
				}
			}
			else
			{
				$this->mensaje .= '  -> La cuenta '.$this->daoDetalleSpg->spg_cuenta.'::'.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.' No existe en la estructura ';
				$this->valido = false;
			}
		}
		unset($dataSet);
		return $existe;
	}
	
	public function existeCuentaFuenteFinanciamiento() 
	{
		$existe = true;
		$cadenaSql = "SELECT codemp ".
					 "	FROM spg_cuenta_fuentefinanciamiento ".
					 " WHERE codemp='".$this->daoDetalleSpg->codemp."' ".
					 "   AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
					 "   AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
					 "   AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
					 "   AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
					 "   AND codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
					 "   AND estcla='".$this->daoDetalleSpg->estcla."' ".
					 "   AND codfuefin='".$this->daoDetalleSpg->codfuefin."' ".
					 "   AND trim(spg_cuenta)='".$this->daoDetalleSpg->spg_cuenta."' ";
		$dataSet  = $this->conexionBaseDatos->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($dataSet->EOF)
			{
				$this->mensaje .= '  -> La cuenta '.$this->daoDetalleSpg->spg_cuenta.'::'.formatoprogramatica($this->daoDetalleSpg->codestpro1.$this->daoDetalleSpg->codestpro2.$this->daoDetalleSpg->codestpro3.$this->daoDetalleSpg->codestpro4.$this->daoDetalleSpg->codestpro5).'::'.$this->daoDetalleSpg->estcla.' No esta asociada a la fuente de financiamiento '.$this->daoDetalleSpg->codfuefin;
				$this->valido = false;
				$existe = false;
			}
		}
		unset($dataSet);
		return $existe;
	}
	
	public function existeReverso($operacion)
	{
		$existe=false;
		$operacion_reverso = "";
		if(trim($operacion) == "AU")
		{
		 	$operacion_reverso = "DI";
		}
		elseif(trim($operacion) == "DI")
		{
		  	$operacion_reverso = "AU";
		}
		$cadenaSql=" SELECT * ".
		        "    FROM spg_dtmp_cmp   ".
				"    WHERE codemp = '".$this->daoDetalleSpg->codemp."' ".
				"      AND procede = '".$this->daoDetalleSpg->procede."' ".
				"      AND operacion = '".$operacion_reverso."' ".
				"      AND comprobante = '".$this->daoDetalleSpg->comprobante."' ".
				"      AND fecha = '".$this->daoDetalleSpg->fecha."'  ".
				"      AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
				"      AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
				"      AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
				"      AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
				"      AND codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
				"      AND estcla='".$this->daoDetalleSpg->estcla."' ".
				"      AND spg_cuenta = '".$this->daoDetalleSpg->spg_cuenta."'".
				"      AND codfuefin = '".$this->daoDetalleSpg->codfuefin."'".
				"      AND procede_doc = '".$this->daoDetalleSpg->procede_doc."'".
				"      AND documento = '".$this->daoDetalleSpg->documento."'";
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
				$existe=true;
			}  
		}//fin del else	
		return 	$existe;	
	}
	
	public function existeMovimiento($tipo_comp)
	{
 	    $existe=false;
 	    $monto=0;
 	    $orden=0;
		if(($this->estmodprog==1)&&($tipo_comp=='2'))
		{
			$mes=substr($cabecera->fecha,5,2);
			$cadenaSql="SELECT SUM(enero+febrero+marzo) as trimestre1, SUM(abril+mayo+junio) as trimestre2,".
					   "       SUM(julio+agosto+septiembre) as trimestre3, SUM(octubre+noviembre+diciembre) as trimestre4,".
					   "       SUM(enero) as enero, SUM(febrero) as febrero, SUM(marzo) as marzo, SUM(abril) as abril, SUM(mayo) as mayo,".
					   "       SUM(junio) as junio, SUM(julio) as julio, SUM(agosto) as agosto, SUM(septiembre) as septiembre,".
					   "       SUM(octubre) as octubre, SUM(noviembre) as noviembre, SUM(diciembre) as diciembre, SUM(orden) AS orden".
					   "  FROM spg_dtmp_mensual, spg_dtmp_cmp, sigesp_cmp_md  ".
					   " WHERE spg_dtmp_mensual.codemp='".$this->daoDetalleSpg->codemp."' ".
					   "   AND spg_dtmp_mensual.spg_cuenta = '".$this->daoDetalleSpg->spg_cuenta."' ".
					   "   AND spg_dtmp_mensual.codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
					   "   AND spg_dtmp_mensual.codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
					   "   AND spg_dtmp_mensual.codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
					   "   AND spg_dtmp_mensual.codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
					   "   AND spg_dtmp_mensual.codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
					   "   AND spg_dtmp_mensual.estcla='".$this->daoDetalleSpg->estcla."' ".
					   "   AND spg_dtmp_mensual.procede='".$this->daoDetalleSpg->procede."' ".
					   "   AND spg_dtmp_mensual.comprobante='".$this->daoDetalleSpg->comprobante."' ".
					   "   AND sigesp_cmp_md.fechaconta = '".$this->daoDetalleSpg->fecha."' ".
					   "   AND spg_dtmp_mensual.procede_doc = '".$this->daoDetalleSpg->procede_doc."' ".
				  	   "   AND spg_dtmp_mensual.documento = '".$this->daoDetalleSpg->documento."' ".
					   "   AND spg_dtmp_mensual.operacion = '".$this->daoDetalleSpg->operacion."' ".
					   "   AND spg_dtmp_cmp.codemp=spg_dtmp_mensual.codemp".
					   "   AND spg_dtmp_cmp.procede=spg_dtmp_mensual.procede".
					   "   AND spg_dtmp_cmp.comprobante=spg_dtmp_mensual.comprobante".
					   "   AND spg_dtmp_cmp.fecha=spg_dtmp_mensual.fecha".
					   "   AND spg_dtmp_cmp.codestpro1=spg_dtmp_mensual.codestpro1".
					   "   AND spg_dtmp_cmp.codestpro2=spg_dtmp_mensual.codestpro2".
					   "   AND spg_dtmp_cmp.codestpro3=spg_dtmp_mensual.codestpro3".
					   "   AND spg_dtmp_cmp.codestpro4=spg_dtmp_mensual.codestpro4".
					   "   AND spg_dtmp_cmp.codestpro5=spg_dtmp_mensual.codestpro5".
					   "   AND spg_dtmp_cmp.estcla=spg_dtmp_mensual.estcla".
					   "   AND spg_dtmp_cmp.spg_cuenta=spg_dtmp_mensual.spg_cuenta".
					   "   AND spg_dtmp_cmp.operacion=spg_dtmp_mensual.operacion".
					   "   AND spg_dtmp_cmp.procede_doc=spg_dtmp_mensual.procede_doc".
					   "   AND spg_dtmp_cmp.documento=spg_dtmp_mensual.documento".
					   "   AND spg_dtmp_cmp.codemp=sigesp_cmp_md.codemp".
					   "   AND spg_dtmp_cmp.procede=sigesp_cmp_md.procede".
					   "   AND spg_dtmp_cmp.comprobante=sigesp_cmp_md.comprobante".
					   "   AND spg_dtmp_cmp.fecha=sigesp_cmp_md.fecha".
					   " GROUP BY spg_dtmp_mensual.codemp,spg_dtmp_mensual.procede,spg_dtmp_mensual.comprobante,spg_dtmp_mensual.fecha,".
					   "          spg_dtmp_mensual.codestpro1,spg_dtmp_mensual.codestpro2,spg_dtmp_mensual.codestpro3,spg_dtmp_mensual.codestpro4,".
					   "          spg_dtmp_mensual.codestpro5,spg_dtmp_mensual.estcla,spg_dtmp_mensual.spg_cuenta,spg_dtmp_mensual.operacion,".
					   "          spg_dtmp_mensual.procede_doc,spg_dtmp_mensual.documento";
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
					$trimestre1 = number_format($dataSet->fields['trimestre1'],2,'.','');
					$trimestre2 = number_format($dataSet->fields['trimestre2'],2,'.','');
					$trimestre3 = number_format($dataSet->fields['trimestre3'],2,'.','');
					$trimestre4 = number_format($dataSet->fields['trimestre4'],2,'.','');
					$enero = number_format($dataSet->fields['enero'],2,'.','');
					$febrero = number_format($dataSet->fields['febrero'],2,'.','');
					$marzo = number_format($dataSet->fields['marzo'],2,'.','');
					$abril = number_format($dataSet->fields['abril'],2,'.','');
					$mayo = number_format($dataSet->fields['mayo'],2,'.','');
					$junio = number_format($dataSet->fields['junio'],2,'.','');
					$julio = number_format($dataSet->fields['julio'],2,'.','');
					$agosto = number_format($dataSet->fields['agosto'],2,'.','');
					$septiembre = number_format($dataSet->fields['septiembre'],2,'.','');
					$octubre = number_format($dataSet->fields['octubre'],2,'.','');
					$noviembre = number_format($dataSet->fields['noviembre'],2,'.','');
					$diciembre = number_format($dataSet->fields['diciembre'],2,'.','');
					$orden=$dataSet->fields['orden'];
					$existe=true;
					switch($mes)
					{
						case'01':
							$monto=$trimestre1;
							if($this->estmodape==0)
							{
								$monto=$enero;
							}
						break;
						case'02':
							$monto=$trimestre1;
							if($this->estmodape==0)
							{
								$monto=$febrero;
							}
						break;
						case'03':
							$monto=$trimestre1;
							if($this->estmodape==0)
							{
								$monto=$marzo;
							}
						break;
						case'04':
							$monto=$trimestre2;
							if($this->estmodape==0)
							{
								$monto=$abril;
							}
						break;
						case'05':
							$monto=$trimestre2;
							if($this->estmodape==0)
							{
								$monto=$mayo;
							}
						break;
						case'06':
							$monto=$trimestre2;
							if($this->estmodape==0)
							{
								$monto=$junio;
							}
						break;
						case'07':
							$monto=$trimestre3;
							if($this->estmodape==0)
							{
								$monto=$julio;
							}
						break;
						case'08':
							$monto=$trimestre3;
							if($this->estmodape==0)
							{
								$monto=$agosto;
							}
						break;
						case'09':
							$monto=$trimestre3;
							if($this->estmodape==0)
							{
								$monto=$septiembre;
							}
						break;
						case'10':
							$monto=$trimestre4;
							if($this->estmodape==0)
							{
								$monto=$octubre;
							}
						break;
						case'11':
							$monto=$trimestre4;
							if($this->estmodape==0)
							{
								$monto=$noviembre;
							}
						break;
						case'12':
							$monto=$trimestre4;
							if($this->estmodape==0)
							{
								$monto=$diciembre;
							}
						break;
					}
				}
			}
			unset($dataSet);
		}
		else
		{
			$cadenaSql="SELECT monto, orden ".
					   "  FROM spg_dtmp_cmp ".		
					   " WHERE codemp='".$this->daoDetalleSpg->codemp."' ".
					   "   AND spg_cuenta = '".$this->daoDetalleSpg->spg_cuenta."' ".
					   "   AND codestpro1='".$this->daoDetalleSpg->codestpro1."' ".
					   "   AND codestpro2='".$this->daoDetalleSpg->codestpro2."' ".
					   "   AND codestpro3='".$this->daoDetalleSpg->codestpro3."' ".
					   "   AND codestpro4='".$this->daoDetalleSpg->codestpro4."' ".
					   "   AND codestpro5='".$this->daoDetalleSpg->codestpro5."' ".
					   "   AND estcla='".$this->daoDetalleSpg->estcla."' ".
					   "   AND codfuefin='".$this->daoDetalleSpg->codfuefin."' ".
					   "   AND procede='".$this->daoDetalleSpg->procede."' ".
					   "   AND comprobante='".$this->daoDetalleSpg->comprobante."' ".
					   "   AND fecha = '".$this->daoDetalleSpg->fecha."' ".
					   "   AND procede_doc = '".$this->daoDetalleSpg->procede_doc."' ".
				  	   "   AND documento = '".$this->daoDetalleSpg->documento."' ".
					   "   AND operacion = '".$this->daoDetalleSpg->operacion."' ";
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
					$orden=$dataSet->fields['orden'];
					$monto=number_format($dataSet->fields['monto'],2,'.','');
					$existe=true;
				}			
			}
			unset($dataSet);
		}
		return $existe;
	}	
	
	public function guardarDetalleSPG($daoComprobante,$arrdetallespg,$arrevento) 
	{
		if(!$this->existeCierreSPG())
		{
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$totalspg = count((array)$arrdetallespg);
			for($i=1;($i<=$totalspg)&&($this->valido);$i++)
			{
				$this->daoDetalleSpg = FabricaDao::CrearDAO('N', 'spg_dtmp_cmp');				
				$this->daoDetalleSpg->codemp=$arrdetallespg[$i]['codemp'];
				$this->daoDetalleSpg->procede=$arrdetallespg[$i]['procede'];
				$this->daoDetalleSpg->comprobante=$daoComprobante->comprobante;
				$this->daoDetalleSpg->estcla=$arrdetallespg[$i]['estcla'];
				$this->daoDetalleSpg->codestpro1=$arrdetallespg[$i]['codestpro1'];
				$this->daoDetalleSpg->codestpro2=$arrdetallespg[$i]['codestpro2'];
				$this->daoDetalleSpg->codestpro3=$arrdetallespg[$i]['codestpro3'];
				$this->daoDetalleSpg->codestpro4=$arrdetallespg[$i]['codestpro4'];
				$this->daoDetalleSpg->codestpro5=$arrdetallespg[$i]['codestpro5'];
				$this->daoDetalleSpg->spg_cuenta=$arrdetallespg[$i]['spg_cuenta'];
				$this->daoDetalleSpg->procede_doc=$arrdetallespg[$i]['procede_doc'];
				$this->daoDetalleSpg->documento=$arrdetallespg[$i]['documento'];
				$this->daoDetalleSpg->operacion=$arrdetallespg[$i]['operacion'];
				$this->daoDetalleSpg->codfuefin=$arrdetallespg[$i]['codfuefin'];
				$this->daoDetalleSpg->fecha=$arrdetallespg[$i]['fecha'];
				$this->daoDetalleSpg->descripcion=$arrdetallespg[$i]['descripcion'];
				$this->daoDetalleSpg->monto=$arrdetallespg[$i]['monto'];
				$this->daoDetalleSpg->orden=$arrdetallespg[$i]['orden'];
				$this->daoDetalleSpg->codcencos   = '---';
				if((is_null($this->daoDetalleSpg->documento)) or (empty($this->daoDetalleSpg->documento)))
				{
					$this->mensaje .= 'El Documento no puede tener valor nulo o vac&#237;o.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleSpg->procede_doc)) or (empty($this->daoDetalleSpg->procede_doc)))
				{
					$this->mensaje .= 'El Procede no puede tener valor nulo o vac&#237;o.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleSpg->descripcion)) or (empty($this->daoDetalleSpg->descripcion)))
				{
					$this->mensaje .= 'La Descripci&#243;n no puede tener valor nulo o vac&#237;o.';			
					$this->valido = false;	
				}
				if(($this->existeCuenta())&&($this->valido))
				{
					if ($this->existeCuentaFuenteFinanciamiento())
					{
						if(!$this->existeReverso($this->daoDetalleSpg->operacion)) //existeReverso
						{
							if(!$this->existeMovimiento($daoComprobante->tipo_comp))
							{
								$this->valido=$this->daoDetalleSpg->incluir();
								if(!$this->valido)
								{
									$this->mensaje .= $this->daoDetalleSpg->ErrorMsg;
								}
								$servicioEvento->tipoevento=$this->valido; 
								if($this->valido)
								{
									$servicioEvento->desevetra='Incluyo detalle presupuestario '.$this->daoDetalleSpg->codestpro1.'::'.
															   $this->daoDetalleSpg->codestpro2.'::'.$this->daoDetalleSpg->codestpro3.'::'.
															   $this->daoDetalleSpg->codestpro4.'::'.$this->daoDetalleSpg->codestpro5.'::'.
															   $this->daoDetalleSpg->spg_cuenta.'::'.$this->daoDetalleSpg->procede_doc.'::'.
															   $this->daoDetalleSpg->documento.'::'.$this->daoDetalleSpg->operacion.'::'.
															   $this->daoDetalleSpg->codfuefin.'::'.$this->daoDetalleSpg->fecha.'::'.
															   $this->daoDetalleSpg->monto.'   del comprobante '.$daoComprobante->codemp.'::'.
															   $daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.$daoComprobante->fecha;			
									$servicioEvento->incluirEvento();
								}
								else
								{
									$this->valido=false;
									$servicioEvento->desevetra=$this->mensaje;
									$servicioEvento->incluirEvento();
								}	
							}
							else
							{
								$this->valido=false;
								$this->mensaje .= ' -> El movimiento Ya existe.';
								$servicioEvento->tipoevento=$this->valido; 
								$servicioEvento->desevetra=$this->mensaje;
								$servicioEvento->incluirEvento();
							}
						}
						else{
							//por aqui
						}
					}
					else
					{
						$this->valido=false;						
						$servicioEvento->tipoevento=$this->valido; 
						$servicioEvento->desevetra=$this->mensaje;
						$servicioEvento->incluirEvento();
					}
				}
				else
				{
					$this->valido=false;
					$servicioEvento->tipoevento=$this->valido; 
					$servicioEvento->desevetra=$this->mensaje;
					$servicioEvento->incluirEvento();
				}
			}
			unset($servicioEvento);
		}
		return $this->valido;	
	}
	
	public function eliminarDetalleSPG($daoComprobante,$arrdetallespg,$arrevento) 
	{
		if(!$this->existeCierreSPG())
		{
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrevento['evento'];
			$servicioEvento->codemp=$arrevento['codemp'];
			$servicioEvento->codsis=$arrevento['codsis'];
			$servicioEvento->nomfisico=$arrevento['nomfisico'];
			$totalspg = count((array)$arrdetallespg);
			for($i=1;($i<=$totalspg)&&($this->valido);$i++)
			{
				$criterio="     codemp  = '".$arrdetallespg[$i]['codemp']."'".
				          " AND procede = '".$arrdetallespg[$i]['procede']."' ".
						  " AND comprobante = '".$arrdetallespg[$i]['comprobante']."' ".
						  " AND estcla = '".$arrdetallespg[$i]['estcla']."' ".
						  " AND codestpro1 = '".$arrdetallespg[$i]['codestpro1']."' ".
						  " AND codestpro2 = '".$arrdetallespg[$i]['codestpro2']."' ".
						  " AND codestpro3 = '".$arrdetallespg[$i]['codestpro3']."' ".
						  " AND codestpro4 = '".$arrdetallespg[$i]['codestpro4']."' ".
						  " AND codestpro5 = '".$arrdetallespg[$i]['codestpro5']."' ".
						  " AND spg_cuenta = '".$arrdetallespg[$i]['spg_cuenta']."' ".
						  " AND procede_doc = '".$arrdetallespg[$i]['procede_doc']."' ".
						  " AND documento = '".$arrdetallespg[$i]['documento']."' ".
						  " AND operacion = '".$arrdetallespg[$i]['operacion']."' ".
						  " AND codfuefin = '".$arrdetallespg[$i]['codfuefin']."' ".
						  " AND orden = '".$arrdetallespg[$i]['orden']."' ";					  
				$this->daoDetalleSpg = FabricaDao::CrearDAO('C','spg_dtmp_cmp','',$criterio);
				if((is_null($this->daoDetalleSpg->documento)) or (empty($this->daoDetalleSpg->documento)))
				{
					$this->mensaje .= 'El Documento no puede tener valor nulo o vac&#237;o.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleSpg->procede_doc)) or (empty($this->daoDetalleSpg->procede_doc)))
				{
					$this->mensaje .= 'El Procede no puede tener valor nulo o vac&#237;o.';			
					$this->valido = false;	
				}
				if((is_null($this->daoDetalleSpg->descripcion)) or (empty($this->daoDetalleSpg->descripcion)))
				{
					$this->mensaje .= 'La Descripci&#243;n no puede tener valor nulo o vac&#237;o.';			
					$this->valido = false;	
				}
				if(($this->existeCuenta())&&($this->valido))
				{
					if ($this->existeCuentaFuenteFinanciamiento())
					{
						if($this->existeMovimiento($daoComprobante->tipo_comp))
						{
							$this->valido=$this->daoDetalleSpg->eliminar('','',true);
							if(!$this->valido)
							{
								$this->mensaje .= $this->daoDetalleSpg->ErrorMsg;
							}
							$servicioEvento->tipoevento=$this->valido; 
							if($this->valido)
							{
								$servicioEvento->desevetra='Elimino detalle presupuestario '.$this->daoDetalleSpg->codestpro1.'::'.
														   $this->daoDetalleSpg->codestpro2.'::'.$this->daoDetalleSpg->codestpro3.'::'.
														   $this->daoDetalleSpg->codestpro4.'::'.$this->daoDetalleSpg->codestpro5.'::'.
														   $this->daoDetalleSpg->spg_cuenta.'::'.$this->daoDetalleSpg->procede_doc.'::'.
														   $this->daoDetalleSpg->documento.'::'.$this->daoDetalleSpg->operacion.'::'.
														   $this->daoDetalleSpg->codfuefin.'::'.$this->daoDetalleSpg->fecha.'::'.
														   $this->daoDetalleSpg->monto.'   del comprobante '.$daoComprobante->codemp.'::'.
														   $daoComprobante->procede.'::'.$daoComprobante->comprobante.'::'.
														   $daoComprobante->codban.'::'.$daoComprobante->ctaban;			
								$servicioEvento->incluirEvento();
							}
							else
							{
								$this->valido=false;
								$servicioEvento->desevetra=$this->mensaje;
								$servicioEvento->incluirEvento();
							}
						}
						else
						{
							$this->valido=false;
							$servicioEvento->tipoevento=$this->valido; 
							$servicioEvento->desevetra=$this->mensaje;
							$servicioEvento->incluirEvento();
						}
					}
					else
					{
						$this->valido=false;						
						$servicioEvento->tipoevento=$this->valido; 
						$servicioEvento->desevetra=$this->mensaje;
						$servicioEvento->incluirEvento();
					}
				}
				else
				{
					$this->valido=false;
					$servicioEvento->tipoevento=$this->valido; 
					$servicioEvento->desevetra=$this->mensaje;
					$servicioEvento->incluirEvento();
				}
			}
			unset($servicioEvento);
		}
		return $this->valido;	
	}
	
	public function guardarComprobante($arrcabecera,$arrdetallespg,$arrevento,$evento) 
	{
		$this->daoComprobante = FabricaDao::CrearDAO('N', 'sigesp_cmp_md');
		$this->daoComprobante->codemp      = $arrcabecera['codemp'];
		$this->daoComprobante->procede     = $arrcabecera['procede'];
		$this->daoComprobante->comprobante = fillComprobante($arrcabecera['comprobante']);
		$this->daoComprobante->fecha       = $arrcabecera['fecha'];
		$this->daoComprobante->descripcion = $arrcabecera['descripcion'];
		$this->daoComprobante->tipo_comp   = $arrcabecera['tipo_comp'];
		$this->daoComprobante->tipo_destino= $arrcabecera['tipo_destino'];
		$this->daoComprobante->cod_pro     = $arrcabecera['cod_pro'];
		$this->daoComprobante->ced_bene    = $arrcabecera['ced_bene'];
		$this->daoComprobante->total       = $arrcabecera['total'];
		$this->daoComprobante->numpolcon   = $arrcabecera['numpolcon'];
		$this->daoComprobante->esttrfcmp   = $arrcabecera['esttrfcmp'];
		$this->daoComprobante->estrenfon   = $arrcabecera['estrenfon'];
		$this->daoComprobante->estapro     = $arrcabecera['estapro'];
		$this->daoComprobante->coduac      = $arrcabecera['coduac'];
		$this->daoComprobante->codtipmodpre= $arrcabecera['codtipmodpre'];
		$this->daoComprobante->codfuefin   = $arrcabecera['codfuefin'];
		$this->daoComprobante->codusu      = $arrcabecera['codusu'];
		$this->daoComprobante->codcencos   = '---';
		$utilizaprefijo = $this->daoComprobante->utilizaPrefijo('SPG',$arrcabecera['procede'],$_SESSION['la_logusr']);
		if(($utilizaprefijo)&&($evento=='INSERT'))
		{
			$nronuevo=$this->daoComprobante->buscarCodigo('comprobante',true,15,array('procede'=>$arrcabecera['procede']),'SPG',$arrcabecera['procede'],$arrcabecera['codusu'],'',$this->prefijo);
			if($nronuevo!=$arrcabecera['comprobante'])
			{
				$this->daoComprobante->comprobante = fillComprobante($nronuevo);
				$this->mensaje .= " Le fue asignado el numero de comprobante ".$nronuevo.", ";
			}
			else
			{
				$nronuevo=$this->daoComprobante->buscarCodigo('comprobante',true,15,array('procede'=>$arrcabecera['procede']),'SPG',$arrcabecera['procede'],$arrcabecera['codusu'],'',$this->prefijo);
				if($nronuevo!=$arrcabecera['comprobante'])
				{
					$this->daoComprobante->comprobante = fillComprobante($nronuevo);
					$this->mensaje .= " Le fue asignado el numero de comprobante ".nronuevo.", ";
				}
			}
		}
		
		if($this->validarComprobante($arrdetallespg))
		{
			if(($utilizaprefijo)&&($evento=='INSERT'))
			{
				$respuesta = $this->daoComprobante->incluir(true,"comprobante",true,15,false,array('procede'=>$arrcabecera['procede']),'SPG',$arrcabecera['procede'],$arrcabecera['codusu']);
				if ($respuesta === true)
				{
					$this->valido = true;
				}
				else
				{
					if (($respuesta !== false)&&($this->daoComprobante->errorDuplicate))
					{
						$this->guardarComprobante($arrcabecera,$arrdetallespg,$arrevento);
					}
					else
					{
						$this->valido = false;
					}
				}
			}
			else
			{
				$this->valido = $this->daoComprobante->incluir();
			}
			if($this->valido)
			{
				if((count((array)$arrdetallespg)>0)&&($this->valido))
				{
					// incluir detalles de Presupuesto de Gasto
					$this->valido=$this->guardarDetalleSPG($this->daoComprobante,$arrdetallespg,$arrevento);			
				}
			}
			else
			{
				$this->mensaje .= $this->daoComprobante->ErrorMsg;
			}
		}
		else
		{
			$this->valido = false;
		}
		
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Incluyo el comprobante '.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;			
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

	public function eliminarComprobante($arrcabecera,$arrevento)
	{
		$criterio="codemp = '".$arrcabecera['codemp']."' AND procede='".$arrcabecera['procede']."' AND comprobante='".fillComprobante($arrcabecera['comprobante'])."' AND fecha='".$arrcabecera['fecha']."' ";
		$this->daoComprobante = FabricaDao::CrearDAO('C','sigesp_cmp_md','',$criterio);
		if($this->existeComprobante($this->daoComprobante->codemp,$this->daoComprobante->procede,$this->daoComprobante->comprobante,$this->daoComprobante->fecha))
		{	
			$_SESSION['fechacomprobante']=$this->daoComprobante->fecha;
			$arrResultado=$this->cargarDetallesComprobante();
			$arrdetallespg=$arrResultado['Spg'];
			if(($this->validarComprobante($arrdetallespg))&&($this->valido))
			{
				if((count((array)$arrdetallespg)>0)&&($this->valido))
				{
					// eliminar detalles de Presupuesto de Gasto
					$this->valido=$this->eliminarDetalleSPG($this->daoComprobante,$arrdetallespg,$arrevento);
				}
				if ($this->valido)
				{
					$this->valido = $this->daoComprobante->eliminar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoComprobante->ErrorMsg;
					}
				}
			}
			else
			{
				$this->valido = false;
			}
		}
		else
		{
			$this->mensaje .= 'El Comprobante '.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha.' no existe.';			
			$this->valido = false;	
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Elimino el comprobante '.$this->daoComprobante->codemp.'::'.$this->daoComprobante->procede.'::'.$this->daoComprobante->comprobante.'::'.$this->daoComprobante->fecha;			
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
	
	public function generarConsecutivo($codemp, $logusr, $procede, $prefijo)
	{
		$comprobante='';
		$this->daoComprobante = FabricaDao::CrearDAO("N", "sigesp_cmp_md");
		$this->daoComprobante->codemp =$codemp;
		$comprobante = $this->daoComprobante->buscarCodigo("comprobante",true,15,array('procede'=>$procede),'SPG',$procede,$logusr,'',$prefijo);
		unset($this->daoComprobante);
		return $comprobante;
	}

	public function existeNumeroComprobante($codemp, $procede, $numcom)
	{
		$existe = false;
		$cadenasql="SELECT comprobante ".
				   "  FROM sigesp_cmp_md ".
				   " WHERE codemp = '{$codemp}'  ".
		 		   "   AND procede = '{$procede}'  ".
				   "   AND comprobante = '{$numcom}'";
		$resultado = $this->conexionBaseDatos->Execute($cadenasql);
		if($resultado === false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->existeNumeroComprobante ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if ($resultado->_numOfRows > 0)
			{
				$existe = true;
				$this->mensaje .= ' El numero del comprobante '.$numcom.' ya fue usado';
			}
		}
		return $existe;
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

	public function buscarPrefijosUsuarios($procede)
	{
		$cadenasql = "SELECT prefijo ".
                             "  FROM sigesp_dt_prefijos ".
                             " WHERE codemp  = '".$this->codemp."'  ".
                             "   AND codsis  = 'SPG' ".
                             "   AND procede = '".$procede."' ".
                             "   AND TRIM(codusu)  = '".$this->logusr."' ";
		$resultado = $this->conexionBaseDatos->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->->buscarPrefijosUsuarios ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $resultado;
	}
        
}
?>