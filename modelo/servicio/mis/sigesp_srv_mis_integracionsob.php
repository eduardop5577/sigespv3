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
require_once ($dirsrv.'/shared/class_folder/class_sigesp_int_spg.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobantespg.php');
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobante.php');
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_iintegracionsob.php");
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_recepcion.php");

class ServicioIntegracionSOB implements IIntegracionSOB {

	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos; 
	private $servicioComprobante;
	
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->codemp = $_SESSION['la_empresa']['codemp'];
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
	}
  
	public function buscarSobAsignacion($codasi,$codobr,$fecasi,$cod_pro,$estatus)
	{
		$parametrosBusqueda = '';		
		if($fecasi != '')
		{
			$fecasi = convertirFechaBd($fecasi);
			$parametrosBusqueda .= " AND sob_asignacion.fecasi ='{$fecasi}'";
		}
		if($codasi != '')
		 {
			$parametrosBusqueda .= " AND sob_asignacion.codasi like '%{$codasi}%'";
		}
		if($codobr != '')
		 {
			$parametrosBusqueda .= " AND sob_asignacion.codobr like '%{$codobr}%'";
		}		
		if($cod_pro != '')
		 {
			$parametrosBusqueda .= " AND sob_asignacion.cod_pro like '%{$cod_pro}%'";
		}
		if($estatus=='0')
		{
			$parametrosBusqueda .= " AND (sob_asignacion.estasi=1 OR sob_asignacion.estasi=6 )";
		}
		if($estatus!='0')
		{
			$parametrosBusqueda .= " AND codasi NOT IN (SELECT codasi FROM sob_contrato) ";
		}
		$cadenaSQL = "SELECT sob_asignacion.codasi,sob_asignacion.fecasi, sob_asignacion.obsasi, ".
		            "        sob_asignacion.fechaconta,sob_asignacion.fechaanula,sob_asignacion.estasi, ".
		            "        sob_asignacion.montotasi, sob_obra.desobr, rpc_proveedor.nompro ".
				    "  FROM sob_asignacion, sob_obra , rpc_proveedor".
				    " WHERE sob_asignacion.codemp = '{$this->codemp}'". 
				    "	AND sob_asignacion.estapr = 1  ".
					" 	AND sob_asignacion.estspgscg = ".$estatus."  {$parametrosBusqueda}".
					"	AND sob_asignacion.codemp = sob_obra.codemp".
					"	AND sob_asignacion.codobr = sob_obra.codobr".
					"	AND sob_asignacion.codemp = rpc_proveedor.codemp".
					"	AND sob_asignacion.cod_pro = rpc_proveedor.cod_pro";
		
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarSobAsignacion ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	}
	
	public function buscarDetallePresupuesto($codasi,$codcon)
	{
		if(empty($codcon))
		{
			switch($_SESSION["la_empresa"]["estmodest"])
			{
				case "1": // Modalidad por Proyecto
					$codest1 = "SUBSTR(sob_cuentasasignacion.codestpro1,length(sob_cuentasasignacion.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(sob_cuentasasignacion.codestpro2,length(sob_cuentasasignacion.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(sob_cuentasasignacion.codestpro3,length(sob_cuentasasignacion.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
					break;
					
				case "2": // Modalidad por Programatica
					$codest1 = "SUBSTR(sob_cuentasasignacion.codestpro1,length(sob_cuentasasignacion.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(sob_cuentasasignacion.codestpro2,length(sob_cuentasasignacion.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(sob_cuentasasignacion.codestpro3,length(sob_cuentasasignacion.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$codest4 = "SUBSTR(sob_cuentasasignacion.codestpro4,length(sob_cuentasasignacion.codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
					$codest5 = "SUBSTR(sob_cuentasasignacion.codestpro5,length(sob_cuentasasignacion.codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
					break;
			}
			$cadenasql = "SELECT {$cadenaEstructura} AS estructura , sob_cuentasasignacion.estcla, sob_cuentasasignacion.spg_cuenta, '' AS operacion, monto, 0 AS disponibilidad, ".
			             "  	 sob_cuentasasignacion.codestpro1, sob_cuentasasignacion.codestpro2, sob_cuentasasignacion.codestpro3,".
						 "       sob_cuentasasignacion.codestpro4, sob_cuentasasignacion.codestpro5, spg_cuentas.denominacion  ". 
			             "  FROM sob_cuentasasignacion ". 
						 " INNER JOIN spg_cuentas ". 
						 "    ON sob_cuentasasignacion.codemp = spg_cuentas.codemp ". 
						 "   AND sob_cuentasasignacion.codestpro1 = spg_cuentas.codestpro1 ". 
						 "   AND sob_cuentasasignacion.codestpro2 = spg_cuentas.codestpro2 ". 
						 "   AND sob_cuentasasignacion.codestpro3 = spg_cuentas.codestpro3 ". 
						 "   AND sob_cuentasasignacion.codestpro4 = spg_cuentas.codestpro4 ". 
						 "   AND sob_cuentasasignacion.codestpro5 = spg_cuentas.codestpro5 ". 
						 "   AND sob_cuentasasignacion.estcla = spg_cuentas.estcla ". 
						 "   AND sob_cuentasasignacion.spg_cuenta = spg_cuentas.spg_cuenta ". 
						 " WHERE sob_cuentasasignacion.codemp = '".$this->codemp."' ". 
			             "   AND codasi = '".$codasi."'";
		}
		else
		{
			switch($_SESSION["la_empresa"]["estmodest"])
			{
				case "1": // Modalidad por Proyecto
					$codest1 = "SUBSTR(sob_cuentavariacion.codestpro1,length(sob_cuentavariacion.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(sob_cuentavariacion.codestpro2,length(sob_cuentavariacion.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(sob_cuentavariacion.codestpro3,length(sob_cuentavariacion.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
					break;
					
				case "2": // Modalidad por Programatica
					$codest1 = "SUBSTR(sob_cuentavariacion.codestpro1,length(sob_cuentavariacion.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
					$codest2 = "SUBSTR(sob_cuentavariacion.codestpro2,length(sob_cuentavariacion.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
					$codest3 = "SUBSTR(sob_cuentavariacion.codestpro3,length(sob_cuentavariacion.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
					$codest4 = "SUBSTR(sob_cuentavariacion.codestpro4,length(sob_cuentavariacion.codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
					$codest5 = "SUBSTR(sob_cuentavariacion.codestpro5,length(sob_cuentavariacion.codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
					$cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
					break;
			}
			$cadenasql = "SELECT {$cadenaEstructura} AS estructura , sob_cuentavariacion.estcla, sob_cuentavariacion.spg_cuenta,'' AS operacion, monto, 0 AS disponibilidad,  ".
			             "       sob_cuentavariacion.codestpro1, sob_cuentavariacion.codestpro2, sob_cuentavariacion.codestpro3, ".
						 "        sob_cuentavariacion.codestpro4, sob_cuentavariacion.codestpro5, spg_cuentas.denominacion ".
						 "  FROM sob_cuentavariacion ".
						 " INNER JOIN spg_cuentas ". 
						 "    ON sob_cuentavariacion.codemp = spg_cuentas.codemp ". 
						 "   AND sob_cuentavariacion.codestpro1 = spg_cuentas.codestpro1 ". 
						 "   AND sob_cuentavariacion.codestpro2 = spg_cuentas.codestpro2 ". 
						 "   AND sob_cuentavariacion.codestpro3 = spg_cuentas.codestpro3 ". 
						 "   AND sob_cuentavariacion.codestpro4 = spg_cuentas.codestpro4 ". 
						 "   AND sob_cuentavariacion.codestpro5 = spg_cuentas.codestpro5 ". 
						 "   AND sob_cuentavariacion.estcla = spg_cuentas.estcla ". 
						 "   AND sob_cuentavariacion.spg_cuenta = spg_cuentas.spg_cuenta ". 
						 " WHERE sob_cuentavariacion.codemp='".$this->codemp."'".
						 " 	AND codvar='".$codasi."' ".
						 " 	AND codcon='".$codcon."' ";
		}
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarDetallePresupuesto ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	}
	
	public function buscarInformacionDetalle($codasi,$codcon)
	{
		$arrDisponible = array();
		$j = 0;
		$dataCuentas = $this->buscarDetallePresupuesto($codasi,$codcon);
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
	
	public function buscarDetalleContable($codant,$codcon,$tabla1,$campo,$tabla2,$table3,$codpro)
	{
		$cuenta_proveedor="rpc_proveedor.sc_cuenta";
		$arreglo=array();
		$i=0;
		$arregloscg=array();
		if ($tabla1=='sob_cuentaanticipo')
		{
			if ($_SESSION['la_empresa']['estantspg']=='1')
			{
				$arreglo1=$this->buscarInformacionDetalleAntVal($codant,$codcon,$tabla1,$campo);
				$total=count((array)$arreglo1);
				for($j=0;$j<$total;$j++)
				{
					$arreglo[$i]['cuenta']=$arreglo1[$j]['cuenta'];
					$arreglo[$i]['denominacion']=$arreglo1[$j]['denominacion'];
					$arreglo[$i]['debe']=$arreglo1[$j]['debe'];
					$arreglo[$i]['haber']=$arreglo1[$j]['haber'];
					$arreglo[$i]['debhab']=$arreglo1[$j]['debhab'];
					$i++;
				}
				$cadenasql="SELECT de.sc_cuenta,an.montotret, scg_cuentas.denominacion ". 
						   "  FROM $table3 an ".
						   " INNER JOIN sigesp_deducciones de ON an.codemp=de.codemp AND an.codded=de.codded ". 
						   " INNER JOIN scg_cuentas ". 
						   "    ON sigesp_deducciones.codemp = scg_cuentas.codemp ". 
						   "   AND sigesp_deducciones.sc_cuenta = scg_cuentas.sc_cuenta ". 
				           " WHERE an.codemp='".$this->codemp."' ".
						   "    AND $campo='".$codant."'".
						   "    AND codcon='".$codcon."'";
				$data = $this->conexionBaseDatos->Execute ( $cadenasql );
				if ($data===false)
				{
					$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarDetalleContable ERROR->'.$this->conexionBaseDatos->ErrorMsg();
					$this->valido = false;
				}
				else
				{
					while(!$data->EOF)
					{
						$arreglo[$i]['cuenta'] = $data->fields['sc_cuenta'];
						$arreglo[$i]['debe'] = '0,00';
						$arreglo[$i]['debhab']='H';
						$arreglo[$i]['haber'] = $data->fields['montotret'];
						$arreglo[$i]['denominacion'] = $data->fields['denominacion'];
						$arreglo[$i]['estasicon']='A';
						$i++;
						$data->MoveNext();
					}
					unset($data);
				}
				$totalscg=count((array)$arreglo);
				$totaldebe=0;
				$totalhaber=0;
				for($k=0;$k<$totalscg;$k++)
				{
					$debhab = $arreglo[$k]['debhab'];
					switch($debhab)
					{
						case "D":
							$debe=$arreglo[$k]['debe'];
							$totaldebe=$totaldebe+$debe;
							break;
						case "H":
							$haber=$arreglo[$k]['haber'];
							$totalhaber=$totalhaber+$haber;
							break;
					}
				}
				$resta=$totaldebe-$totalhaber;
				if($resta!=0)
				{
					$cadenasql="SELECT rpc_proveedor.sc_cuenta,sc_ctaant, scg_cuentas.denominacion ". 
							   "  FROM rpc_proveedor ". 
							   " INNER JOIN scg_cuentas ". 
							   "    ON rpc_proveedor.codemp = scg_cuentas.codemp ". 
							   "   AND rpc_proveedor.sc_cuenta = scg_cuentas.sc_cuenta ". 
							   " WHERE rpc_proveedor.codemp='".$this->codemp."' ".
							   "   AND cod_pro='".$codpro."'";
					$data = $this->conexionBaseDatos->Execute ( $cadenasql );
					if ($data===false)
					{
						$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarDetalleContable ERROR->'.$this->conexionBaseDatos->ErrorMsg();
						$this->valido = false;
					}
					else
					{
						if($data->fields['sc_cuenta']!='')
						{
							$arreglo[$i]['cuenta'] = $data->fields['sc_cuenta'];
							$arreglo[$i]['debe'] = '0,00';
							$arreglo[$i]['debhab']='H';
							$arreglo[$i]['haber'] = $resta;
							$arreglo[$i]['estasicon']='A';
							$arreglo[$i]['denominacion'] = $data->fields['denominacion'];
						}
					}
				}
			}
			else
			{
				$montoretencion = 0;
				$monto = 0;
				$cuentaproveedor = '';
				if($_SESSION["la_empresa"]["conrecdoc"]=="1")
				{
					$cuenta_proveedor="rpc_proveedor.sc_cuentarecdoc";
				}
				$cadenasql="SELECT sob_anticipo.sc_cuenta AS cuentaanticipo, ".$cuenta_proveedor." AS cuentaproveedor, rpc_proveedor.cod_pro, rpc_proveedor.nompro, ".
						"		   sob_anticipo.monto, scg_cuentas.denominacion ".
						" FROM sob_anticipo, sob_contrato, sob_asignacion, rpc_proveedor, scg_cuentas  ".
						" WHERE sob_anticipo.codemp='".$this->codemp."' ".
						"   AND sob_anticipo.codant='".$codant."'".
						"   AND sob_anticipo.codcon='".$codcon."'".
						"   AND sob_anticipo.codemp=sob_contrato.codemp ".
						"   AND sob_anticipo.codcon=sob_contrato.codcon ".
						"   AND sob_asignacion.codemp=sob_contrato.codemp ".
						"   AND sob_asignacion.codasi=sob_contrato.codasi ".
				   		"   AND sob_anticipo.codemp = scg_cuentas.codemp ". 
				   		"   AND trim(sob_anticipo.sc_cuenta) = trim(scg_cuentas.sc_cuenta) ". 
				        "   AND rpc_proveedor.codemp=sob_asignacion.codemp ".
						"   AND rpc_proveedor.cod_pro=sob_asignacion.cod_pro ";
				$data = $this->conexionBaseDatos->Execute ( $cadenasql );
				if ($data===false)
				{
					$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarDetalleContable ERROR->'.$this->conexionBaseDatos->ErrorMsg();
					$this->valido = false;
				}
				else
				{
					while(!$data->EOF)
					{
						$arreglo[$i]['cuenta'] = $data->fields['cuentaanticipo'];
						$arreglo[$i]['debe'] = number_format($data->fields['monto'],2,',','.');
						$arreglo[$i]['haber'] = '0,00';
						$arreglo[$i]['debhab']='D';
						$arreglo[$i]['estasicon']='A';
						$arreglo[$i]['denominacion'] = $data->fields['denominacion'];
						$i++;
						$cuentaproveedor = $data->fields['cuentaproveedor'];
						$monto = $data->fields['monto'];
						$data->MoveNext();
					}
				}
				unset($data);
				$cadenasql="SELECT sob_retencionanticipo.codded, SUM(sob_retencionanticipo.montotret) as monto, ".
						   "       MAX(sigesp_deducciones.sc_cuenta) as sc_cuenta, MAX(scg_cuentas.denominacion) AS  denominacion ".
						   "  FROM sob_retencionanticipo, sigesp_deducciones,scg_cuentas ".  
						   " WHERE sob_retencionanticipo.codemp='".$this->codemp."'".
						   "   AND sob_retencionanticipo.codant='".$codant."'".
						   "   AND trim(sob_retencionanticipo.codcon)=trim('".$codcon."')".
						   "   AND sob_retencionanticipo.montotret > 0".
						   "   AND sigesp_deducciones.codemp = scg_cuentas.codemp ". 
						   "   AND trim(sigesp_deducciones.sc_cuenta) = trim(scg_cuentas.sc_cuenta) ". 
						   "   AND sob_retencionanticipo.codemp = sigesp_deducciones.codemp ".
						   "   AND sob_retencionanticipo.codded = sigesp_deducciones.codded ".
						   "   GROUP BY sob_retencionanticipo.codded";
				$data = $this->conexionBaseDatos->Execute ($cadenasql);
				if ($data===false)
				{
					$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarInformacionDetalleAnt ERROR->'.$this->conexionBaseDatos->ErrorMsg();
					$this->valido = false;
				}
				else
				{
					while(!$data->EOF)
					{
						$arreglo[$i]['cuenta'] = $data->fields['sc_cuenta'];
						$arreglo[$i]['haber'] = number_format($data->fields['monto'],2,',','.');
						$arreglo[$i]['debe'] = '0,00';
						$arreglo[$i]['debhab']='H';
						$arreglo[$i]['estasicon']='A';
						$arreglo[$i]['denominacion'] = $data->fields['denominacion'];
						$montoretencion = $montoretencion+$data->fields['monto'];
						$i++;
						$data->MoveNext();
					}
				}
				$denominacion=$this->buscarDenominacionscg($cuentaproveedor);
				$arreglo[$i]['cuenta'] = $cuentaproveedor;
				$arreglo[$i]['denominacion'] = $denominacion;
				$arreglo[$i]['haber'] = number_format($monto - $montoretencion,2,',','.');
				$arreglo[$i]['debe'] = '0,00';
				$arreglo[$i]['debhab']='H';
			}
		}
		if ($tabla1=='sob_cuentavaluacion')
		{
			$arreglo=array();
			$i=0;
			$total=0;
			$cadenasql="SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, monto ".
					   "  FROM sob_cuentavaluacion ".
					   " WHERE codemp='".$this->codemp."'".
					   "   AND codval='".$codant."' ".
					   "   AND codcon='".$codcon."' ";
			$data = $this->conexionBaseDatos->Execute ($cadenasql);
			if ($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarInformacionDetalleAnt ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			else
			{
				while(!$data->EOF)
				{
					$codestpro=$data->fields["codestpro1"].$data->fields["codestpro2"].$data->fields["codestpro3"].$data->fields["codestpro4"].$data->fields["codestpro5"];
					$arrRespuesta = $this->buscarCuentaContable($data->fields["spg_cuenta"],$data->fields["estcla"],$codestpro);
					$arreglo[$i]['cuenta']=$arrRespuesta['sc_cuenta'];
					$arreglo[$i]['denominacion']=$arrRespuesta['sc_denominacion'];
					$arreglo[$i]['debe']=$data->fields["monto"];
					$arreglo[$i]['debhab']='D';
					$arreglo[$i]['haber'] = '0,00';
					$arreglo[$i]['estasicon']='A';
					$total = $total + $data->fields["monto"];
					$i++;
					$data->MoveNext();
				}
			}
			unset($data);
			$cadenasql="SELECT * ".
					   "  FROM sob_cargovaluacion ".  
					   " WHERE codemp='".$this->codemp."'  ".  
					   "   AND codval='".$codant."'  ".
					   "   AND codcon='".$codcon."'";
			$data = $this->conexionBaseDatos->Execute ($cadenasql);
			if ($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarInformacionDetalleAnt ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			else
			{
				while(!$data->EOF)
				{
					$codestpro=$data->fields["codestprog"];						
					$arrRespuesta = $this->buscarCuentaContable($data->fields["spg_cuenta"],$data->fields["estcla"],$codestpro);
					$arreglo[$i]['cuenta']=$arrRespuesta['sc_cuenta'];
					$arreglo[$i]['denominacion']=$arrRespuesta['sc_denominacion'];
					$arreglo[$i]['debe']=$data->fields["monto"];
					$arreglo[$i]['debhab']='D';
					$arreglo[$i]['haber'] = '0,00';
					$arreglo[$i]['estasicon']='A';
					$total = $total + $data->fields["monto"];
					$i++;
					$data->MoveNext();
				}
			}
			unset($data);
			$cadenasql="SELECT sob_retencionvaluacioncontrato.codded, SUM(sob_retencionvaluacioncontrato.montotret) as monto, MAX(sigesp_deducciones.sc_cuenta) as sc_cuenta, ".
					   "       MAX(scg_cuentas.denominacion) AS denominacion". 
				       "  FROM sob_retencionvaluacioncontrato, sigesp_deducciones, scg_cuentas ". 
				       " WHERE sob_retencionvaluacioncontrato.codemp='".$this->codemp."'".
				       "   AND sob_retencionvaluacioncontrato.codval='".$codant."'".
				       "   AND sob_retencionvaluacioncontrato.codcon='".$codcon."'".
				       "   AND sob_retencionvaluacioncontrato.montotret > 0".
				       "   AND sigesp_deducciones.codemp = scg_cuentas.codemp ". 
				       "   AND sigesp_deducciones.sc_cuenta = scg_cuentas.sc_cuenta ". 
			           "   AND sob_retencionvaluacioncontrato.codemp = sigesp_deducciones.codemp ".
					   "   AND sob_retencionvaluacioncontrato.codded = sigesp_deducciones.codded ".
				       "   GROUP BY sob_retencionvaluacioncontrato.codded";
			$data = $this->conexionBaseDatos->Execute ($cadenasql);
			if ($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarInformacionDetalleAnt ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			else
			{
				while(!$data->EOF)
				{
					$arreglo[$i]['cuenta']=$data->fields["sc_cuenta"];
					$arreglo[$i]['debe']='0,00';
					$arreglo[$i]['debhab']='H';
					$arreglo[$i]['haber'] = $data->fields["monto"];
					$arreglo[$i]['estasicon']='A';
					$arreglo[$i]['denominacion']=$data->fields["denominacion"];
					$total = $total - $data->fields["monto"];
					$i++;
					$data->MoveNext();
				}
			}
			unset($data);
			$cadenasql="SELECT sob_valuacion.amoval, rpc_proveedor.sc_ctaant, rpc_proveedor.sc_cuenta, scg_cuentas.denominacion  ".
				       "  FROM sob_valuacion, sob_contrato, sob_asignacion, rpc_proveedor, scg_cuentas ".
				       " WHERE sob_valuacion.codemp='".$this->codemp."'".
				       "   AND sob_valuacion.codval='".$codant."'".
				       "   AND sob_valuacion.codcon='".$codcon."'".
					   "   AND sob_valuacion.codemp = sob_contrato.codemp ".
					   "   AND sob_valuacion.codcon = sob_contrato.codcon ".
				       "   AND rpc_proveedor.codemp = scg_cuentas.codemp ". 
				       "   AND rpc_proveedor.sc_cuenta = scg_cuentas.sc_cuenta ". 
					   "   AND sob_contrato.codemp = sob_asignacion.codemp ".
					   "   AND sob_contrato.codasi = sob_asignacion.codasi ".
					   "   AND sob_asignacion.codemp = rpc_proveedor.codemp ".
					   "   AND sob_asignacion.cod_pro = rpc_proveedor.cod_pro ";
			$data = $this->conexionBaseDatos->Execute ($cadenasql);
			if ($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarInformacionDetalleAnt ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			else
			{
				while(!$data->EOF)
				{
					if ($data->fields["amoval"]>0)
					{
						$arreglo[$i]['cuenta']=$data->fields["sc_ctaant"];
						$arreglo[$i]['denominacion']=$data->fields["denominacion"];
						$arreglo[$i]['debe']='0,00';
						$arreglo[$i]['debhab']='H';
						$arreglo[$i]['haber'] = $data->fields["amoval"];
						$arreglo[$i]['estasicon']='M';
						$total = $total - $data->fields["amoval"];
						$i++;
					}
					$arreglo[$i]['cuenta']=$data->fields["sc_cuenta"];
					$arreglo[$i]['denominacion']=$data->fields["denominacion"];
					$arreglo[$i]['debe']='0,00';
					$arreglo[$i]['debhab']='H';
					$arreglo[$i]['estasicon']='A';
					$arreglo[$i]['haber'] =$total;
					$i++;
					$data->MoveNext();
				}
			}
		}
		return $arreglo;
	}
	public function buscarDenominacionscg($cuentascg)
	{
        $denominacion="";
		$cadenasql="SELECT denominacion ".
                " FROM scg_cuentas".
                " WHERE codemp='".$this->codemp."' ".
				"   AND sc_cuenta='".$cuentascg."' ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarDenominacionscg ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
			   $denominacion = $data->fields['denominacion'];
			}
		}
		unset($data);
		return $denominacion;
	}
	
	
	public function agruparCuentasContable($arrContable){
		$arrAgrupado = array();
		$arrCuenta = array();
		$j = 0;
		foreach ($arrContable as $movimiento) {
			$arrCuenta[$j] = $movimiento['cuenta'].$movimiento['debhab'];
			$j++;
		}
		$arrCuenta = array_unique($arrCuenta);
		$k = 0;
		foreach ($arrCuenta as $cuenta) {
			$primero = true;
			$monto = 0;
			foreach ($arrContable as $movcuenta) {
				if ($cuenta == $movcuenta['cuenta'].$movcuenta['debhab']) {
					if ($primero) {
						$arrAgrupado[$k]['cuenta'] = $movcuenta['cuenta'];
						$arrAgrupado[$k]['estasicon'] = $movcuenta['estasicon'];
						$arrAgrupado[$k]['debhab'] = $movcuenta['debhab'];
						$primero = false;
					}
					
					if ($movcuenta['debhab'] == 'D') {
						$monto = $monto + $movcuenta['debe'];
					}
					else {
						$monto = $monto + $movcuenta['haber'];
					}
					
				}
			}
			
			if ($arrAgrupado[$k]['debhab'] == 'D') {
				$arrAgrupado[$k]['debe']  = $monto;
				$arrAgrupado[$k]['haber'] = '0,00';
			}
			else {
				$arrAgrupado[$k]['debe']  = '0,00';
				$arrAgrupado[$k]['haber'] = $monto;
			}
			
			$k++;
		}
		
		return $arrAgrupado;		
	}
	
	public function buscarInformacionDetalleAntVal($codigo,$codcon,$tabla1,$campo)
	{
		$arreglo=array();
		$this->valido=true;
		if ($_SESSION['la_empresa']['estantspg']=='1')
		{
			$i=0;
			$total=0;
			$cadenasql="SELECT ".$tabla1.".codestpro1, ".$tabla1.".codestpro2, ".$tabla1.".codestpro3, ".$tabla1.".codestpro4 ".
			           "       ".$tabla1.".codestpro5, ".$tabla1.".spg_cuenta, ".$tabla1.".estcla, ".$tabla1.".monto, spg_cuentas.denominacion ".
				       "  FROM ".$tabla1." ".  
					   " INNER JOIN spg_cuentas ". 
					   "    ON ".$tabla1.".codemp = spg_cuentas.codemp ". 
					   "   AND ".$tabla1.".codestpro1 = spg_cuentas.codestpro1 ". 
					   "   AND ".$tabla1.".codestpro2 = spg_cuentas.codestpro2 ". 
					   "   AND ".$tabla1.".codestpro3 = spg_cuentas.codestpro3 ". 
					   "   AND ".$tabla1.".codestpro4 = spg_cuentas.codestpro4 ". 
					   "   AND ".$tabla1.".codestpro5 = spg_cuentas.codestpro5 ". 
					   "   AND ".$tabla1.".estcla = spg_cuentas.estcla ". 
					   "   AND ".$tabla1.".spg_cuenta = spg_cuentas.spg_cuenta ". 
					   " WHERE ".$tabla1.".codemp='".$this->codemp."'  ".  
				       "   AND $campo='".$codigo."'  ".
				       "   AND codcon='".$codcon."'";
			$data = $this->conexionBaseDatos->Execute ($cadenasql);
			if ($data===false)
			{
				$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarInformacionDetalleAnt ERROR->'.$this->conexionBaseDatos->ErrorMsg();
				$this->valido = false;
			}
			else
			{
				$len1=$_SESSION["la_empresa"]["loncodestpro1"];
				$len2=$_SESSION["la_empresa"]["loncodestpro2"];
				$len3=$_SESSION["la_empresa"]["loncodestpro3"];
				$len4=$_SESSION["la_empresa"]["loncodestpro4"];
				$len5=$_SESSION["la_empresa"]["loncodestpro5"];
				
				while(!$data->EOF)
				{
					$codest1=substr($data->fields["codestpro1"],(25-$len1),$len1);
					$codest2=substr($data->fields["codestpro2"],(25-$len2),$len2);
					$codest3=substr($data->fields["codestpro3"],(25-$len3),$len3);
					$codest4=substr($data->fields["codestpro4"],(25-$len4),$len4);
					$codest5=substr($data->fields["codestpro5"],(25-$len5),$len5);	
					$arreglo[$i]['spg_cuenta'] = $data->fields["spg_cuenta"];
					$total=$total+$data->fields["monto"];
					$monto=$data->fields["monto"];
					$programatica=$codest1.$codest2.$codest3.$codest4.$codest5;
					$disponibilidad=$this->disponibilidad($this->codemp,$data,$int_spg,'','','');
					$arreglo[$i]['disponibilidad'] = $disponibilidad;
					$estcla=$data->fields["estcla"];
					switch($estcla)
					{
						case "A":
							$estatus="Accion";
							break;
						case "P":
							$estatus="Proyecto";
							break;
					}
					$arreglo[$i]['estructura']=$programatica;
					$arreglo[$i]['estcla']=$estatus;
					$arreglo[$i]['denspg']=$data->fields["denominacion"];
					$arreglo[$i]['monto']=$monto;
					$arreglo[$i]['total']=$total;
					$codestpro=$data->fields["codestpro1"].$data->fields["codestpro2"].$data->fields["codestpro3"].$data->fields["codestpro4"].$data->fields["codestpro5"];
					$arrRespuesta = $this->buscarCuentaContable($data->fields["spg_cuenta"],$estcla,$codestpro);
					$arreglo[$i]['cuenta']=$arrRespuesta['sc_cuenta'];
					$arreglo[$i]['denominacion']=$arrRespuesta['sc_denominacion'];
					$arreglo[$i]['debe']=$monto;
					$arreglo[$i]['debhab']='D';
					$arreglo[$i]['haber'] = '0,00';
					$i++;
					$data->MoveNext();
				}
			}
			if ($tabla1 =='sob_cuentavaluacion')
			{
				$codestpro = $this->conexionBaseDatos->Concat('spg_cuentas.codestpro1','spg_cuentas.codestpro2','spg_cuentas.codestpro3','spg_cuentas.codestpro4','spg_cuentas.codestpro5');
				$cadenasql="SELECT codestprog, spg_cuentas.spg_cuenta, monto, spg_cuentas.denominacion  ".
						   "  FROM sob_cargovaluacion ".  
						   " INNER JOIN spg_cuentas ". 
						   "    ON sob_cargovaluacion.codemp = spg_cuentas.codemp ". 
						   "   AND sob_cargovaluacion.codestpro = ".$codestpro." ". 
						   "   AND sob_cargovaluacion.estcla = spg_cuentas.estcla ". 
						   "   AND sob_cargovaluacion.spg_cuenta = spg_cuentas.spg_cuenta ". 
						   " WHERE sob_cargovaluacion.codemp='".$this->codemp."'  ".  
						   "   AND $campo='".$codigo."'  ".
						   "   AND codcon='".$codcon."'";
				$data = $this->conexionBaseDatos->Execute ($cadenasql);
				if ($data===false)
				{
					$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarInformacionDetalleAnt ERROR->'.$this->conexionBaseDatos->ErrorMsg();
					$this->valido = false;
				}
				else
				{
					$len1=$_SESSION["la_empresa"]["loncodestpro1"];
					$len2=$_SESSION["la_empresa"]["loncodestpro2"];
					$len3=$_SESSION["la_empresa"]["loncodestpro3"];
					$len4=$_SESSION["la_empresa"]["loncodestpro4"];
					$len5=$_SESSION["la_empresa"]["loncodestpro5"];
					
					while(!$data->EOF)
					{
						$codestpro=$data->fields["codestprog"];						
						$codest1=substr(substr($codestpro,0,25),(25-$len1),$len1);
						$codest2=substr(substr($codestpro,25,25),(25-$len2),$len2);
						$codest3=substr(substr($codestpro,50,25),(25-$len3),$len3);
						$codest4=substr(substr($codestpro,75,25),(25-$len4),$len4);
						$codest5=substr(substr($codestpro,100,25),(25-$len5),$len5);	
						$arreglo[$i]['spg_cuenta'] = $data->fields["spg_cuenta"];
						$total=$total+$data->fields["monto"];
						$monto=$data->fields["monto"];
						$programatica=$codest1.$codest2.$codest3.$codest4.$codest5;
						$disponibilidad=$this->disponibilidad($this->codemp,$data,$int_spg,'','','');
						$arreglo[$i]['disponibilidad'] = $disponibilidad;
						$estcla=$data->fields["estcla"];
						switch($estcla)
						{
							case "A":
								$estatus="Accion";
								break;
							case "P":
								$estatus="Proyecto";
								break;
						}
						$arreglo[$i]['estructura']=$programatica;
						$arreglo[$i]['denspg']=$data->fields["denominacions"];
						$arreglo[$i]['estcla']=$estatus;
						$arreglo[$i]['monto']=$monto;
						$arreglo[$i]['total']=$total;
						$arrRespuesta = $this->buscarCuentaContable($data->fields["spg_cuenta"],$estcla,$codestpro);
						$arreglo[$i]['cuenta']=$arrRespuesta['sc_cuenta'];
						$arreglo[$i]['denominacion']=$arrRespuesta['sc_denominacion'];
						$arreglo[$i]['debe']=$monto;
						$arreglo[$i]['debhab']='D';
						$arreglo[$i]['haber'] = '0,00';
						$i++;
						$data->MoveNext();
					}
				}
			}
		}
		return $arreglo;
	}
	
	public function buscarInformacionDetalleValuacion($codigo,$codcon)
	{
		$arreglo=array();
		$this->valido=true;
		$i=0;
		$total=0;
		$cadenasql="SELECT sob_cuentavaluacion.codestpro1, sob_cuentavaluacion.codestpro2, sob_cuentavaluacion.codestpro3, ".
				   "       sob_cuentavaluacion.codestpro4, sob_cuentavaluacion.codestpro5, sob_cuentavaluacion.estcla, ".
				   "       sob_cuentavaluacion.spg_cuenta, monto, spg_cuentas.denominacion ".
				   "  FROM sob_cuentavaluacion ".
				   " INNER JOIN spg_cuentas ". 
				   "    ON sob_cuentavaluacion.codemp = spg_cuentas.codemp ". 
				   "   AND sob_cuentavaluacion.codestpro1 = spg_cuentas.codestpro1 ". 
				   "   AND sob_cuentavaluacion.codestpro2 = spg_cuentas.codestpro2 ". 
				   "   AND sob_cuentavaluacion.codestpro3 = spg_cuentas.codestpro3 ". 
				   "   AND sob_cuentavaluacion.codestpro4 = spg_cuentas.codestpro4 ". 
				   "   AND sob_cuentavaluacion.codestpro5 = spg_cuentas.codestpro5 ". 
				   "   AND sob_cuentavaluacion.estcla = spg_cuentas.estcla ". 
				   "   AND sob_cuentavaluacion.spg_cuenta = spg_cuentas.spg_cuenta ". 
				   " WHERE sob_cuentavaluacion.codemp='".$this->codemp."'".
				   "   AND codval='".$codigo."' ".
				   "   AND codcon='".$codcon."' ";
		$data = $this->conexionBaseDatos->Execute ($cadenasql);
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarInformacionDetalleAnt ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$len1=$_SESSION["la_empresa"]["loncodestpro1"];
			$len2=$_SESSION["la_empresa"]["loncodestpro2"];
			$len3=$_SESSION["la_empresa"]["loncodestpro3"];
			$len4=$_SESSION["la_empresa"]["loncodestpro4"];
			$len5=$_SESSION["la_empresa"]["loncodestpro5"];
			while(!$data->EOF)
			{
				$codest1=substr($data->fields["codestpro1"],(25-$len1),$len1);
				$codest2=substr($data->fields["codestpro2"],(25-$len2),$len2);
				$codest3=substr($data->fields["codestpro3"],(25-$len3),$len3);
				$codest4=substr($data->fields["codestpro4"],(25-$len4),$len4);
				$codest5=substr($data->fields["codestpro5"],(25-$len5),$len5);	
				$total=$total+$data->fields["monto"];
				$monto=$data->fields["monto"];
				$programatica=$codest1.$codest2.$codest3.$codest4.$codest5;
				$disponibilidad=$this->disponibilidad($this->codemp,$data,$int_spg,'','','');
				$estcla=$data->fields["estcla"];
				switch($estcla)
				{
					case "A":
						$estatus="Accion";
						break;
					case "P":
						$estatus="Proyecto";
						break;
				}
				$codestpro=$data->fields["codestpro1"].$data->fields["codestpro2"].$data->fields["codestpro3"].$data->fields["codestpro4"].$data->fields["codestpro5"];
				$arreglo[$i]['spg_cuenta'] = $data->fields["spg_cuenta"];
				$arreglo[$i]['denominacion'] = $data->fields["denominacion"];
				$arreglo[$i]['codestpro']=$codestpro;
				$arreglo[$i]['estructura']=$programatica;
				$arreglo[$i]['estcla']=$estatus;
				$arreglo[$i]['monto']=$monto;
				$arreglo[$i]['total']=$total;
				$arreglo[$i]['disponibilidad'] = $disponibilidad;
				$i++;
				$data->MoveNext();
			}
		}
		$codestpro = $this->conexionBaseDatos->Concat('spg_cuentas.codestpro1','spg_cuentas.codestpro2','spg_cuentas.codestpro3','spg_cuentas.codestpro4','spg_cuentas.codestpro5');
		$cadenasql="SELECT codestprog, sob_cargovaluacion.estcla, sob_cargovaluacion.spg_cuenta, monto, spg_cuentas.denominacion ".
				   "  FROM sob_cargovaluacion ".  
				   " INNER JOIN spg_cuentas ". 
				   "    ON sob_cargovaluacion.codemp = spg_cuentas.codemp ". 
				   "   AND sob_cargovaluacion.codestprog =  ".$codestpro." ".
				   "   AND sob_cargovaluacion.estcla = spg_cuentas.estcla ". 
				   "   AND sob_cargovaluacion.spg_cuenta = spg_cuentas.spg_cuenta ". 
				   " WHERE sob_cargovaluacion.codemp='".$this->codemp."'  ".  
				   "   AND codval='".$codigo."'  ".
				   "   AND codcon='".$codcon."'";
		$data = $this->conexionBaseDatos->Execute ($cadenasql);
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarInformacionDetalleAnt ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$len1=$_SESSION["la_empresa"]["loncodestpro1"];
			$len2=$_SESSION["la_empresa"]["loncodestpro2"];
			$len3=$_SESSION["la_empresa"]["loncodestpro3"];
			$len4=$_SESSION["la_empresa"]["loncodestpro4"];
			$len5=$_SESSION["la_empresa"]["loncodestpro5"];
			while(!$data->EOF)
			{
				$codestpro=$data->fields["codestprog"];						
				$codest1=substr(substr($codestpro,0,25),(25-$len1),$len1);
				$codest2=substr(substr($codestpro,25,25),(25-$len2),$len2);
				$codest3=substr(substr($codestpro,50,25),(25-$len3),$len3);
				$codest4=substr(substr($codestpro,75,25),(25-$len4),$len4);
				$codest5=substr(substr($codestpro,100,25),(25-$len5),$len5);	
				$total=$total+$data->fields["monto"];
				$monto=$data->fields["monto"];
				$programatica=$codest1.$codest2.$codest3.$codest4.$codest5;
				$disponibilidad=$this->disponibilidad($this->codemp,$data,$int_spg,'','','');
				$estcla=$data->fields["estcla"];
				switch($estcla)
				{
					case "A":
						$estatus="Accion";
						break;
					case "P":
						$estatus="Proyecto";
						break;
				}
				$arreglo[$i]['spg_cuenta'] = $data->fields["spg_cuenta"];
				$arreglo[$i]['denominacion'] = $data->fields["denominacion"];
				$arreglo[$i]['codestpro']=$codestpro;
				$arreglo[$i]['estructura']=$programatica;
				$arreglo[$i]['estcla']=$estatus;
				$arreglo[$i]['monto']=$monto;
				$arreglo[$i]['total']=$total;
				$arreglo[$i]['disponibilidad'] = $disponibilidad;
				$i++;
				$data->MoveNext();
			}
		}
		return $arreglo;
	}
	
	public function validarMontoASI($codasi)
	{
        $montoacumulado=0;
		$cadenasql="SELECT COALESCE(SUM(monto),0) As monto ".
                " FROM sob_cuentasasignacion ".
                " WHERE codemp='".$this->codemp."' ".
				"   AND codasi='".$codasi."' ";
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->validarMontoASI ERROR->'.$this->conexionBaseDatos->ErrorMsg();
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
	
	public function buscarDetallePresupuestario($codasi,$codcon,$arrcabecera,$mensaje)
	{
		$arregloSPG = null;
		if(empty($codcon))
		{
			$cadenasql="SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,codfuefin,spg_cuenta,monto ".
                       "  FROM sob_cuentasasignacion ".
                       " WHERE codemp='".$this->codemp."' ".
				       "   AND codasi='".$codasi."'";
		}
		else
		{
			$cadenasql="SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,codfuefin,spg_cuenta,monto  ".
                       "  FROM sob_cuentavariacion ".
                       " WHERE codemp='".$this->codemp."' ".
				       "   AND codvar='".$codasi."'  ".
		               "   AND codcon='".$codcon."'";
		}
		$data = $this->conexionBaseDatos->Execute ( $cadenasql );
		if ($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarDetallePresupuestario ERROR->'.$this->conexionBaseDatos->ErrorMsg();
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
				$arregloSPG[$i]['mensaje']= $mensaje;
				$data->MoveNext();
			}			
		}
		unset($data);
		return $arregloSPG;
	}
	
	public function procesoContabilizarSOBASI($objson)
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
			$codasi=fillComprobante($objson->arrDetalle[$j]->codasi);
			$arrevento['desevetra'] = "Contabilizar la asignacion {$codasi}, asociado a la empresa {$this->codemp}";
			$this->mensaje='';
			$this->valido=true;
			if ($this->contabilizarSobAsi($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codasi;
				$arrRespuesta[$h]['mensaje'] = 'La asignacion fue contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codasi;
				$arrRespuesta[$h]['mensaje'] = 'La asignacion no fue contabilizada, '.$this->mensaje.' ';
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarSobAsi($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$codasi=fillComprobante($objson->arrDetalle[$j]->codasi);
		// OBTENGO LA ASIGNACION A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codasi='".$codasi."' AND (estasi='1' OR estasi='6')";
		$this->daoSobAsignacion = FabricaDao::CrearDAO('C','sob_asignacion','',$criterio);
		// VERIFICO QUE LA ASIGNACION EXISTA, ESTE EMITIDA Y MODIFICADA
		if($this->daoSobAsignacion->codasi=='')
		{
			$this->mensaje .= 'ERROR -> No existe la Asignacion N�'.$codasi.', en estatus EMITIDA o MODIFICADA.';
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE CONTABILIZACI�N DE LA ASIGNACION SEA MAYOR O IGUAL A LA FECHA DE LA ASIGNACION
		$fecha=convertirFechaBd($objson->feccon);
        if(!compararFecha($this->daoSobAsignacion->fecasi,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Contabilizacion '.$fecha.' es menor que la fecha de Emision '.$this->daoSobAsignacion->fecasi.' de la Asignacion N� '.$codasi;
			$this->valido = false;			
		}
        if(trim($this->daoSobAsignacion->puncueasi==''))
		{
			$this->daoSobAsignacion->puncueasi='-';
		}
        if(trim($this->daoSobAsignacion->obsasi==''))
		{
			$this->mensaje .= 'ERROR -> La Asignacion Nro '.$codasi.', No tiene Observacion';
			$this->valido = false;			
		}
		// VERIFICO QUE LOS MONTOS PRESUPUESTARIOS CUADREN. 
		$montogasto=$this->validarMontoASI($codasi);
		if($_SESSION['la_empresa']['confiva']=='P') // si el iva es presupuestario
		{
			if(number_format($this->daoSobAsignacion->montotasi,2,'.','')!=number_format($montogasto,2,'.',''))
			{
				$this->mensaje .= ' ERROR -> El monto de la asignacion '.$codasi.' no esta cuadrado con el resumen presupuestario';
				$this->valido = false;			
			}       
		}
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->codemp;
			$arrcabecera['procede'] = 'SOBASI';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSobAsignacion->codasi);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $this->daoSobAsignacion->obsasi;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $this->daoSobAsignacion->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoSobAsignacion->montotasi,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallespg=$this->buscarDetallePresupuestario($codasi,'',$arrcabecera,'R');
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,null,null,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSobAsignacion->estspgscg='1';
				$this->daoSobAsignacion->estasi='5';
				$this->daoSobAsignacion->fechaconta=$fecha;
				$this->daoSobAsignacion->fechaanula='1900-01-01';
				$this->valido = $this->daoSobAsignacion->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSobAsignacion->ErrorMsg;
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
	
	public function procesoRevContabilizarSOBASI($objson)
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
			$codasi=fillComprobante($objson->arrDetalle[$j]->codasi);
			$arrevento['desevetra'] = "Reversar la asignacion {$codasi}, asociado a la empresa {$this->codemp}";
			if ($this->RevcontabilizarSobAsi($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codasi;
				$arrRespuesta[$h]['mensaje'] = 'La asignacion fue reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codasi;
				$arrRespuesta[$h]['mensaje'] = "La asignacion no fue reversada, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function RevcontabilizarSobAsi($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$codasi=fillComprobante($objson->arrDetalle[$j]->codasi);
		// OBTENGO LA ASIGNACION A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codasi='".$codasi."' AND  estspgscg='1' ";
		$this->daoSobAsignacion = FabricaDao::CrearDAO('C','sob_asignacion','',$criterio);
		// VERIFICO QUE LA ASIGNACION EXISTA
		if($this->daoSobAsignacion->codasi=='')
		{
			$this->mensaje .= 'ERROR -> No existe la Asignacion N�'.$codasi.', en estatus CONTABILIZADA';
			$this->valido = false;			
		}		
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSobAsignacion->codemp;
			$arrcabecera['procede'] = 'SOBASI';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSobAsignacion->codasi);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSobAsignacion->fechaconta;
			$arrcabecera['descripcion'] = $this->daoSobAsignacion->obsasi;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $this->daoSobAsignacion->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoSobAsignacion->montotasi,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSobAsignacion->estspgscg='0';
				$this->daoSobAsignacion->estasi='6';
				$this->daoSobAsignacion->fechaconta='1900-01-01';
				$this->daoSobAsignacion->fechaanula='1900-01-01';
				$this->valido = $this->daoSobAsignacion->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSobAsignacion->ErrorMsg;
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
	
	public function procesoAnularSOBASI($objson)
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
			$codasi=fillComprobante($objson->arrDetalle[$j]->codasi);
			$arrevento['desevetra'] = "Anular la asignacion {$codasi}, asociado a la empresa             	{$this->codemp}";
			if ($this->AnularSobAsi($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codasi;
				$arrRespuesta[$h]['mensaje'] = 'La asignacion fue anulada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codasi;
				$arrRespuesta[$h]['mensaje'] = "La asignacion no fue anulada, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function AnularSobAsi($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$codasi = fillComprobante($objson->arrDetalle[$j]->codasi);
		$conanu = $objson->arrDetalle[$j]->codanu;
		// OBTENGO LA ASIGNACION A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codasi='".$codasi."' AND  estspgscg='1' ";
		$this->daoSobAsignacion = FabricaDao::CrearDAO('C','sob_asignacion','',$criterio);
		// VERIFICO QUE LA ASIGNACION EXISTA
		if($this->daoSobAsignacion->numsol=='')
		{
			$this->mensaje .= 'ERROR -> No existe la Asignacion N�'.$codasi.', en estatus CONTABILIZADA';
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE ANULACI�N DE LA ASIGNACION SEA MAYOR O IGUAL A LA FECHA DE CONTABILIZACI�N
		$fecha=convertirFechaBd($objson->fechaanula);
        if(!compararFecha($this->daoSobAsignacion->fechaconta,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Anulacion '.$fecha.' es menor que la fecha de Contabilizacion '.$this->daoSobAsignacion->fechaconta.' de la Asignacion N� '.$codasi;
			$this->valido = false;			
		}
		// VERIFICO QUE LA FECHA DE ANULACI�N DE LA ASIGNACION SEA MAYOR O IGUAL A LA FECHA DE LA SOLICITUD
		$fecha=convertirFechaBd($objson->fechaanula);
        if(!compararFecha($this->daoSobAsignacion->fecasi,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Anulacion '.$fecha.' es menor que la fecha de Emision '.$this->daoSobAsignacion->fecasi.' de la Asignacion N� '.$codasi;
			$this->valido = false;			
		}
		// VERIFICO QUE LOS MONTOS PRESUPUESTARIOS CUADREN. 
		$montogasto=$this->validarMontoASI($codasi);
		if(trim($_SESSION['la_empresa']['confiva'])=='P') // si el iva es presupuestario
		{
			if(number_format($this->daoSobAsignacion->montotasi,2,'.','')!=number_format($montogasto,2,'.',''))
			{
				$this->mensaje .= 'ERROR -> El monto de la asignacion '.$codasi.' no esta cuadrado con el resumen presupuestario';
				$this->valido = false;			
			}       
		}
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSobAsignacion->codemp;
			$arrcabecera['procede'] = 'SOBASI';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSobAsignacion->codasi);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSobAsignacion->fechaconta;
			$arrcabecera['descripcion'] = $this->daoSobAsignacion->obsasi;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $this->daoSobAsignacion->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(($this->daoSobAsignacion->montotasi),2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->anularComprobante($arrcabecera,$fecha,'SOBRAS',$conanu,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSobAsignacion->estspgscg='2';
				$this->daoSobAsignacion->estasi='5';
				$this->daoSobAsignacion->fechaanula=$fecha;
				$this->valido = $this->daoSobAsignacion->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSobAsignacion->ErrorMsg;
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
	
	public function procesoRevAnularSOBASI($objson)
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
			$codasi=fillComprobante($objson->arrDetalle[$j]->codasi);
			$arrevento['desevetra'] = "Reversar la anulacion de la asignacion {$codasi}, asociado a la empresa {$this->codemp}";
			if ($this->RevanularSobAsi($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codasi;
				$arrRespuesta[$h]['mensaje'] = 'La asignacion fue reversada su anulacion exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codasi;
				$arrRespuesta[$h]['mensaje'] = "La asignacion no fue reversada su anulacion, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function RevanularSobAsi($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$codasi=fillComprobante($objson->arrDetalle[$j]->codasi);
		// OBTENGO LA ASIGNACION A REVERSAR ANULACION
		$criterio="codemp = '".$this->codemp."' AND codasi='".$codasi."' AND estspgscg='2' ";
		$this->daoSobAsignacion = FabricaDao::CrearDAO('C','sob_asignacion','',$criterio);
		// VERIFICO QUE LA ASIGNACION EXISTA
		if($this->daoSobAsignacion->codasi=='')
		{
			$this->mensaje .= 'ERROR -> No existe la asignacion N�'.$codasi.', en estatus Anulada';
			$this->valido = false;			
		}		
		// VERIFICO QUE EL ESTATUS DE LA ASIGNACION SEA ANULADA
		if($this->daoSobAsignacion->estspgscg!='2') 
		{
			$this->mensaje .= 'ERROR -> La  asignacion '.$codasi.' debe estar en estatus ANLUADA para su Reverso.';
			$this->valido = false;			
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSobAsignacion->codemp;
			$arrcabecera['procede'] = 'SOBRAS';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSobAsignacion->codasi);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSobAsignacion->fechaanula;
			$arrcabecera['descripcion'] = $this->daoSobAsignacion->obsasi;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $this->daoSobAsignacion->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoSobAsignacion->montotasi,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSobAsignacion->estspgscg='1';
				$this->daoSobAsignacion->estasi='5';
				$this->daoSobAsignacion->fechaconta='';
				$this->daoSobAsignacion->fechaanula='1900-01-01';
				$this->valido = $this->daoSobAsignacion->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSobAsignacion->ErrorMsg;
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
	
	public function buscarSobContrato($codasi,$codcon,$feccon,$fecinicon,$estatus)
	{
		$parametrosBusqueda = '';
		
		if(!empty($codasi))
		{
			$parametrosBusqueda .= " AND codasi like '%".$codasi."%'";
		}
		if(!empty($codcon))
		{
			$parametrosBusqueda .= " AND codcon like '%".$codcon."%'";
		}
		if(!empty($feccon))
		{
			$feccon = convertirFechaBd($feccon);
			$parametrosBusqueda .= " AND feccon = '".$feccon."'";
		}
		if(!empty($fecinicon))
		{
			$fecinicon = convertirFechaBd($fecinicon);
			$parametrosBusqueda .= " AND fecinicon = '".$fecinicon."'";
		}
		if($estatus==1)
		{
			$parametrosBusqueda .=" AND sob_contrato.codcon NOT IN (SELECT sob_anticipo.codcon FROM sob_anticipo WHERE sob_contrato.codcon=sob_anticipo.codcon)".
								  " AND sob_contrato.codcon NOT IN (SELECT sob_valuacion.codcon FROM sob_valuacion WHERE sob_contrato.codcon=sob_valuacion.codcon)";
		}
		$cadenaSql="SELECT sob_contrato.codcon, sob_contrato.codasi, sob_contrato.feccon as fechacontrato, ".
		           "       sob_contrato.obscon, sob_contrato.fechaconta, sob_contrato.fechaanula, ".
				   "       sob_asignacion.fechaconta AS fechacontaasig, rpc_proveedor.nompro, ".
		           "       sob_asignacion.cod_pro, sob_asignacion.montotasi, sob_obra.desobr, sob_asignacion.obsasi ".
                   "  FROM sob_contrato, sob_asignacion, sob_obra, rpc_proveedor ".
                   " WHERE sob_contrato.codemp='".$this->codemp."'".
				   "   AND sob_contrato.estapr = 1".
				   "   AND sob_contrato.estspgscg=".$estatus."  {$parametrosBusqueda}".
		           "   AND sob_contrato.codasi=sob_asignacion.codasi".
				   "   AND sob_obra.codobr=sob_asignacion.codobr".
		           "   AND sob_asignacion.codemp=rpc_proveedor.codemp".
				   "   AND sob_asignacion.cod_pro=rpc_proveedor.cod_pro";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarSobContrato ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	}
	
	public function verificarConfig($sistema,$seccion,$variable)
	{
		$value = 0;
		$ls_sql="SELECT value".
                "  FROM sigesp_config ".
				" WHERE codemp='".$this->codemp."'".
				"	AND codsis='".$sistema."'".
				"	AND seccion='".$seccion."'".
				"	AND entry='".$variable."'";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->verificarConfig ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else{
			$value = $data->fields['value'];
		}
		return $value;	
	}
	
	public function procesoContabilizarSobCon($objson)
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
			$codcon=fillComprobante($objson->arrDetalle[$j]->codcon);
			$arrevento['desevetra'] = "Contabilizacion del contrato {$codcon}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarSobCon($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codcon;
				$arrRespuesta[$h]['mensaje'] = 'El contrato fue contabilizado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codcon;
				$arrRespuesta[$h]['mensaje'] = "El contrato no fue contabilizado, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarSobCon($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$codcon = fillComprobante($objson->arrDetalle[$j]->codcon);
		$codasi = fillComprobante($objson->arrDetalle[$j]->codasi);
		
		// OBTENGO EL CONTRATO A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codcon='".$codcon."' AND codasi='".$codasi."' ";
		$this->daoSobContrato = FabricaDao::CrearDAO('C','sob_contrato','',$criterio);
		// VERIFICO QUE EL CONTRATO EXISTA, ESTE EMITIDA Y MODIFICADA
		if($this->daoSobContrato->codcon=='')
		{
			$this->mensaje .= 'ERROR -> No existe el Contrato N�'.$codcon;
			$this->valido = false;			
		}		
		// VERIFICO QUE LA FECHA DE CONTABILIZACI�N DEL CONTRATO SEA MAYOR O IGUAL A LA FECHA DE EMISION DEL CONTRATO
		if(!empty($objson->feccon))
		{
			$fecha=convertirFechaBd($objson->feccon);
		}
		else
		{
			$fecha = $this->daoSobContrato->feccon;
		}
		$fechacontaasig=convertirFechaBd($objson->arrDetalle[$j]->fechacontaasig); 
		if(!compararFecha($this->daoSobContrato->feccon,$fecha))
		{
			$this->mensaje .= 'ERROR -> La Fecha de Contabilizacion '.$fecha.' es menor que la fecha de Emision '.$this->daoSobContrato->feccon.' del contrato N� '.$codcon;
			$this->valido = false;			
		}
		if(!compararFecha($fechacontaasig,$fecha)){
			$this->mensaje .= 'ERROR -> La Fecha de Contabilizacion '.$fecha.' es menor que la fecha de Contabilizacion '.$fechacontaasig.' del contrato N� '.$codcon;
			$this->valido = false;	
		}
		// VERIFICO QUE LOS MONTOS PRESUPUESTARIOS CUADREN. 
		$montogasto=$this->validarMontoASI($codasi);
		if(number_format($this->daoSobContrato->monto,2,'.','')!=number_format($montogasto,2,'.',''))
		{
			$this->mensaje .= ' ERROR -> La Asignacion N� '.$codasi.' del Contrato N� '.$codcon.'no esta cuadrado con el resumen presupuestario';
			$this->valido = false;			
		} 
		if($this->valido)
		{
			$this->valido = $this->revPrecompromiso_asignacion_contrato($codcon,$codasi,$fecha,$arrevento);
		}
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSobContrato->codemp;
			$arrcabecera['procede'] = 'SOBCON';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSobContrato->codcon);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $this->daoSobContrato->obscon;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $objson->arrDetalle[$j]->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoSobContrato->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$arrdetallespg=$this->buscarDetallePresupuestario($this->daoSobContrato->codasi,'',$arrcabecera,'O');
		}
		if ($this->valido)
		{
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,null,null,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSobContrato->estspgscg='1';
				$this->daoSobContrato->fechaconta=$fecha;
				$this->daoSobContrato->fechaanula='1900-01-01';
				$this->valido = $this->daoSobContrato->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSobContrato->ErrorMsg;
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
	
	public function revPrecompromiso_asignacion_contrato($codcon,$codasi,$fecha,$arrevento)
	{
		// OBTENGO LA ASIGNACION ASOCIADA AL CONTRATO A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codasi = '".$codasi."' ";
		$this->daoAsignacion = FabricaDao::CrearDAO('C','sob_asignacion','',$criterio);
		// VERIFICO QUE LA ASIGNACION EXISTA
		if($this->daoAsignacion->codasi=='')
		{
			$this->mensaje .= 'ERROR -> No existe la Asignacion N�'.$codasi.', asociada al Contrato N� '.$codcon;
			$this->valido = false;			
		}		
		$descripcion = $this->daoAsignacion->obsasi;
		if(empty($descripcion)){
			$descripcion = "ninguno";
		}
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoAsignacion->codemp;
			$arrcabecera['procede'] = 'SOBASI';
			$arrcabecera['comprobante'] = fillComprobante($this->daoAsignacion->codasi);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoAsignacion->fechaconta;
			$arrcabecera['descripcion'] = $descripcion;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $this->daoAsignacion->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(($this->daoAsignacion->montotasi),2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->anularComprobante($arrcabecera,$fecha,'SOBRPC',' ',$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if($this->valido)
			{
				$this->daoAsignacion->fechaanula=$fecha;
				$this->valido = $this->daoAsignacion->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoAsignacion->ErrorMsg;
				}								
			}	
		}
		$servicioEvento = new ServicioEvento();
		$servicioEvento->evento=$arrevento['evento'];
		$servicioEvento->tipoevento=$this->valido; 
		$servicioEvento->codemp=$arrevento['codemp'];
		$servicioEvento->codsis=$arrevento['codsis'];
		$servicioEvento->nomfisico=$arrevento['nomfisico'];
		$servicioEvento->desevetra='Reverso el Precompromiso de la Asignacion '.$codasi.'para contabilizar el Contrato '.$codcon;		
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
	
	public function EliminarRevAsignacion($codasi,$arrevento)
	{
		// OBTENGO LA ASIGNACION ASOCIADA AL CONTRATO A REVERSAR LA CONTABILIZACION
		$criterio="codemp = '".$this->codemp."' AND codasi='".$codasi."' ";
		$this->daoSobAsignacion = FabricaDao::CrearDAO('C','sob_asignacion','',$criterio);
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSobAsignacion->codemp;
			$arrcabecera['procede'] = 'SOBRPC';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSobAsignacion->codasi);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSobAsignacion->fechaconta;
			$arrcabecera['descripcion'] = $this->daoSobAsignacion->obsasi;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $this->daoSobAsignacion->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoSobAsignacion->montotasi,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->daoSobAsignacion->fechaanula='1900-01-01';
				$this->valido = $this->daoSobAsignacion->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSobAsignacion->ErrorMsg;
				}				
			}
		}
		unset($serviciocomprobante);
		return $this->valido;
	}
	
	public function procesoRevContabilizarSobCon($objson)
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
			$codcon=fillComprobante($objson->arrDetalle[$j]->codcon);
			$arrevento['desevetra'] = "Reverso el contrato {$codcon}, asociado a la empresa {$this->codemp}";
			if ($this->RevcontabilizarSobCon($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codcon;
				$arrRespuesta[$h]['mensaje'] = 'El contrato fue reversado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codcon;
				$arrRespuesta[$h]['mensaje'] = "El contrato no fue reversado, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
		public function RevcontabilizarSobCon($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$codcon = fillComprobante($objson->arrDetalle[$j]->codcon);
		// OBTENGO EL CONTRATO A REVERSAR LA CONTABILIZACION
		$criterio="codemp = '".$this->codemp."' AND codcon='".$codcon."' AND  estspgscg='1' ";
		$this->daoSobContrato = FabricaDao::CrearDAO('C','sob_contrato','',$criterio);
		// VERIFICO QUE EL CONTRATO EXISTA
		if($this->daoSobContrato->codcon=='')
		{
			$this->mensaje .= 'ERROR -> No existe el Contrato No.'.$codcon.', en estatus CONTABILIZADO';
			$this->valido = false;			
		}
		$datacont=$this->buscarValuaciones($codcon,"");
		if(!$datacont->EOF)
		{
			$this->mensaje .= 'ERROR -> El Contrato No.'.$codcon.', Tiene Valuaciones Asignadas';
			$this->valido = false;			
		}
		$dataant=$this->buscarAnticipos($codcon,"");
		if(!$dataant->EOF)
		{
			$this->mensaje .= 'ERROR -> El Contrato No.'.$codcon.', Tiene Anticipos Asignados';
			$this->valido = false;			
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSobContrato->codemp;
			$arrcabecera['procede'] = 'SOBCON';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSobContrato->codcon);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSobContrato->fechaconta;
			$arrcabecera['descripcion'] = $this->daoSobContrato->obscon;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $objson->arrDetalle[$j]->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoSobContrato->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido = $this->EliminarRevAsignacion($this->daoSobContrato->codasi,$arrevento);
			}
			if($this->valido)
			{
				$this->daoSobContrato->estspgscg='0';
				$this->daoSobContrato->fechaconta='1900-01-01';
				$this->valido = $this->daoSobContrato->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSobContrato->ErrorMsg;
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

	public function procesoAnularSobCon($objson)
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
			$codcon=fillComprobante($objson->arrDetalle[$j]->codcon);
			$arrevento['desevetra'] = "Anulacion del contrato {$codcon}, asociado a la empresa {$this->codemp}";
			if ($this->AnularSobCon($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codcon;
				$arrRespuesta[$h]['mensaje'] = 'El contrato fue anulado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codcon;
				$arrRespuesta[$h]['mensaje'] = "El contrato no fue anulado, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function AnularSobCon($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$codcon = fillComprobante($objson->arrDetalle[$j]->codcon);
		$conanu = $objson->arrDetalle[$j]->codanu;
		$fecha = convertirFechaBd($objson->fechaanula);
		// OBTENGO EL CONTRATO ANULAR
		$criterio="codemp = '".$this->codemp."' AND codcon='".$codcon."' AND  estspgscg='1' ";
		$this->daoSobContrato = FabricaDao::CrearDAO('C','sob_contrato','',$criterio);
		// VERIFICO QUE EL CONTRATO EXISTA
		if($this->daoSobContrato->codcon=='')
		{
			$this->mensaje .= 'ERROR -> No existe el Contrato N�'.$codcon.', en estatus CONTABILIZADO';
			$this->valido = false;			
		}
		$datacont=$this->buscarValuaciones($codcon,"");
		if(!$datacont->EOF)
		{
			$this->mensaje .= 'ERROR -> El Contrato No.'.$codcon.', Tiene Valuaciones Asignadas';
			$this->valido = false;			
		}
		$dataant=$this->buscarAnticipos($codcon,"");
		if(!$dataant->EOF)
		{
			$this->mensaje .= 'ERROR -> El Contrato No.'.$codcon.', Tiene Anticipos Asignados';
			$this->valido = false;			
		}
		if ($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSobContrato->codemp;
			$arrcabecera['procede'] = 'SOBCON';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSobContrato->codcon);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSobContrato->fechaconta;
			$arrcabecera['descripcion'] = $this->daoSobContrato->obscon;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $objson->arrDetalle[$j]->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format(($this->daoSobContrato->monto),2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->anularComprobante($arrcabecera,$fecha,'SOBACO',$conanu,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido = $this->EliminarRevAsignacion($codasi,arrevento);
			}
			if($this->valido)
			{
				$this->daoSobContrato->estspgscg='2';
				$this->daoSobContrato->fechaanula=$fecha;
				$this->valido = $this->daoSobContrato->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSobContrato->ErrorMsg;
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
	
	public function procesoRevAnularSobCon($objson)
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
			$codcon=fillComprobante($objson->arrDetalle[$j]->codcon);
			$arrevento['desevetra'] = "Reversar la Anulacion del contrato {$codcon}, asociado a la empresa {$this->codemp}";
			if ($this->RevanularSobCon($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codcon;
				$arrRespuesta[$h]['mensaje'] = 'El contrato fue reversado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codcon;
				$arrRespuesta[$h]['mensaje'] = "El contrato no fue reversado, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
		
	public function RevanularSobCon($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$codcon = fillComprobante($objson->arrDetalle[$j]->codcon);
		$codasi = fillComprobante($objson->arrDetalle[$j]->codasi);
		
		// OBTENGO EL CONTRATO A REVERSAR ANULACION
		$criterio="codemp = '".$this->codemp."' AND codcon='".$codcon."' AND estspgscg='2' ";
		$this->daoSobContrato = FabricaDao::CrearDAO('C','sob_contrato','',$criterio);
		// VERIFICO QUE EL CONTRATO EXISTA
		if($this->daoSobContrato->codcon=='')
		{
			$this->mensaje .= 'ERROR -> No existe el contrato N�'.$codcon.', en estatus Anulada';
			$this->valido = false;			
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSobContrato->codemp;
			$arrcabecera['procede'] = 'SOBACO';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSobContrato->codcon);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSobContrato->fechaanula;
			$arrcabecera['descripcion'] = $this->daoSobContrato->obscon;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $objson->arrDetalle[$j]->cod_pro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = number_format($this->daoSobContrato->monto,2,'.','');
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido)
			{
				$this->valido = $this->revPrecompromiso_asignacion_contrato($codcon,$codasi,$this->daoSobContrato->fechaconta,$arrevento);
			}
			if($this->valido)
			{
				$this->daoSobContrato->estspgscg='1';
				$this->daoSobContrato->fechaconta='';
				$this->daoSobContrato->fechaanula='1900-01-01';
				$this->valido = $this->daoSobContrato->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSobContrato->ErrorMsg;
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
	
	public function buscarTipoDocumento($tipodocumento)
	{
		$criterio='';
		if ($tipodocumento=='ANTICIPO')
		{
			if($_SESSION["la_empresa"]["estantspg"]=='0')
			{
				$criterio='   AND (estpre = 3 OR estpre = 4 ) ';
			}
			else
			{
				$criterio='   AND estpre = 2 ';
			}
		}
		if ($tipodocumento=='VALUACION')
		{
			$criterio='   AND estpre = 1 AND tipodocanti= 0';
		}
		
		$ls_sql="SELECT codtipdoc,dentipdoc  ".
				"FROM cxp_documento  ".
				"WHERE estcon = 1 ".$criterio.
				"ORDER BY codtipdoc ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);	
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarTipoDocumento ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	}
	
	public function buscarSobAnticipo($codcon,$codant,$feccon,$fecant,$estatus)
	{
		$criterio="";
		if(!empty($codant))
		{
			$criterio=$criterio." AND sob_anticipo.codant like '%".$codant."%'";
		}
		if(!empty($codcon))
		{
			$criterio=$criterio." AND sob_anticipo.codcon like '%".$codcon."%'";
		}
		if(!empty($feccon))
		{
			$feccon=convertirFechaBd($feccon);
			$criterio=$criterio." AND sob_contrato.feccon = '".$feccon."'";
		}
		if(!empty($fecant))
		{
			$fecant=convertirFechaBd($fecant);
			$criterio=$criterio." AND sob_anticipo.fecant = '".$fecant."'";
		}
		$cadenaSql="SELECT sob_anticipo.codcon,sob_anticipo.codant,sob_anticipo.fecant,sob_anticipo.fechaconta, sob_anticipo.fechaanula,".
				   "       sob_anticipo.monto as monto,sob_contrato.fechaconta AS fechacontacontrato,sob_asignacion.cod_pro, rpc_proveedor.nompro".
                   "  FROM sob_anticipo, sob_contrato, sob_asignacion, rpc_proveedor".
                   " WHERE sob_anticipo.codemp='".$this->codemp."'".
				   "   AND sob_anticipo.estapr    = 1".
				   "   AND sob_anticipo.estspgscg =".$estatus."".
				   "   AND sob_contrato.estspgscg = 1 ".
				   $criterio.
				   "   AND sob_anticipo.codemp    =sob_contrato.codemp".
				   "   AND sob_anticipo.codcon    =sob_contrato.codcon".
				   "   AND sob_asignacion.codemp  =sob_contrato.codemp".
				   "   AND sob_asignacion.codasi  =sob_contrato.codasi".
				   "   AND rpc_proveedor.codemp   =sob_asignacion.codemp".
				   "   AND rpc_proveedor.cod_pro  =sob_asignacion.cod_pro".
				   " ORDER BY sob_anticipo.codcon,sob_anticipo.codant";	
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= 'CLASE->INTEGRADOR SOB M�TODO->buscarSobAnticipo ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	}
	
	public function buscarCargo($codcar)
	{
		$porcar="";
		$cadenaSql="SELECT porcar". 
				   "  FROM sigesp_cargos". 
				   " WHERE codemp='".$this->codemp."'".
				   "   AND codcar='".$codcar."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarCargo ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if($data->fields['porcar']!='')
			{
				$porcar = $data->fields['porcar'];
			}
		}
		return $porcar;
	}
	
	public function buscarCuentaContable($spgcuenta,$estcla,$codestpro)
	{
		$sc_cuenta="";
		$sc_denominacion="";
		$cadenaSql="SELECT spg_cuentas.sc_cuenta, scg_cuentas.denominacion ". 
				   "  FROM spg_cuentas ". 
				   " INNER JOIN scg_cuentas ". 
				   "    ON spg_cuentas.codemp = scg_cuentas.codemp ". 
				   "   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta ". 
		           " WHERE spg_cuentas.codemp='".$this->codemp."'".
				   "   AND spg_cuentas.spg_cuenta='".$spgcuenta."'".
				   "   AND spg_cuentas.estcla='".$estcla."'".
				   "   AND spg_cuentas.codestpro1='".substr($codestpro,0,25)."'".
				   "   AND spg_cuentas.codestpro2='".substr($codestpro,25,25)."'".
				   "   AND spg_cuentas.codestpro3='".substr($codestpro,50,25)."'".
				   "   AND spg_cuentas.codestpro4='".substr($codestpro,75,25)."'".
				   "   AND spg_cuentas.codestpro5='".substr($codestpro,100,25)."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarCargo ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if($data->fields['sc_cuenta']!='')
			{
				$sc_cuenta = $data->fields['sc_cuenta'];
				$sc_denominacion = $data->fields['denominacion'];
			}
			else
			{
				$this->mensaje .= 'ERROR-> Cuenta Contable Inv�lida';
				$this->valido = false;		
			}
		}
		$arrRespuesta['sc_cuenta'] = $sc_cuenta;
		$arrRespuesta['sc_denominacion'] = $sc_denominacion; 		
		return $arrRespuesta;
	}
	
	public function obtenerCargos($codant,$codcon,$numrecdoc,$codtipdoc,$ced_bene,$cod_pro,$tabla,$campo,$procede)
	{
		$arrcargos = array();
		$cadenaSql="SELECT codcar,basimp,monto,formula,codestprog,spg_cuenta,estcla, '--' AS codfuefin". 
		           "  FROM $tabla". 
		           " WHERE codemp='".$this->codemp."'".
		           "   AND $campo='".$codant."'".
		           "   AND codcon='".$codcon."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->obtenerCargos ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while(!$data->EOF)
			{
				$i++;
				$codestpro = $data->fields['codestprog'];
				$arrcargos[$i]['codemp'] = $this->codemp;
				$arrcargos[$i]['numrecdoc'] = $numrecdoc; 
				$arrcargos[$i]['codtipdoc'] = $codtipdoc; 
				$arrcargos[$i]['ced_bene'] = $ced_bene;
				$arrcargos[$i]['cod_pro'] = $cod_pro; 
				$arrcargos[$i]['codcar'] = $data->fields['codcar']; 
				$arrcargos[$i]['procede_doc'] = $procede;
				$arrcargos[$i]['numdoccom'] = $codcon;
				$arrcargos[$i]['monobjret'] = $data->fields['basimp'];
				$arrcargos[$i]['monret'] = $data->fields['monto'];
				$arrcargos[$i]['codestpro1'] = substr($codestpro,0,25);
				$arrcargos[$i]['codestpro2'] = substr($codestpro,25,25);
				$arrcargos[$i]['codestpro3'] = substr($codestpro,50,25);
				$arrcargos[$i]['codestpro4'] = substr($codestpro,75,25);
				$arrcargos[$i]['codestpro5'] = substr($codestpro,100,25); 
				$arrcargos[$i]['spg_cuenta'] = $data->fields['spg_cuenta'];
				$arrcargos[$i]['porcar'] = $this->buscarCargo($data->fields['codcar']);  
				$arrcargos[$i]['formula'] = $data->fields['formula'];
				$arrcargos[$i]['estcla'] = $data->fields['estcla'];
				$arrcargos[$i]['codfuefin'] = $data->fields['codfuefin'];
				$arrcargos[$i]['codestpro'] = $data->fields['codestprog'];
				$arrcargos[$i]['listo'] = 'N';
				$data->MoveNext();
			}
		}
		return $arrcargos;
	}
	
	public function buscarPorDed($codded)
	{
		$arreglo[0]['porded']="";
		$arreglo[0]['sc_cuenta']="";
		$cadenaSql="SELECT porded,sc_cuenta ". 
		           "  FROM sigesp_deducciones ". 
		           " WHERE codemp='".$this->codemp."'".
		           "   AND codded='".$codded."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarPorDed ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if($data->fields['porded']!='')
			{
				$arreglo[0]['porded'] = $data->fields['porded'];
				$arreglo[0]['sc_cuenta'] = $data->fields['sc_cuenta'];
			}
		}
		return $arreglo;
	}
	
	public function buscarScgCuenCont($sc_cuenta)
	{
		$status="";
		$denominacion="";
		$arreglo=array();
		$existe=false;
		$cadenaSql="SELECT sc_cuenta, status, denominacion".
				   "  FROM scg_cuentas  ".
				   " WHERE codemp='".$this->codemp."'".
				   "   AND trim(sc_cuenta)='".trim($sc_cuenta)."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarScgCuenCont ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if($data->fields['sc_cuenta']!='')
			{
				$arreglo['sc_cuenta']=$data->fields['sc_cuenta'];
				$arreglo['denominacion']=$data->fields['denominacion'];
				$arreglo['status']=$data->fields['status'];
			}
		}
		return $arreglo;
	}
		
	public function obtenerDeducciones($codant,$codcon,$numrecdoc,$codtipdoc,$ced_bene,$cod_pro,$tabla,$campo,$procede)
	{
		$arrdeducciones = array();
		$cadenaSql="SELECT codded,monret,montotret  ". 
				   "  FROM $tabla ". 
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND $campo='".$codant."' ".
				   "   AND codcon='".$codcon."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->obtenerDeducciones ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while(!$data->EOF)
			{
				$i++;
				$arrdeducciones[$i]['codemp'] = $this->codemp;
				$arrdeducciones[$i]['numrecdoc'] = $numrecdoc;
				$arrdeducciones[$i]['codtipdoc'] = $codtipdoc;
				$arrdeducciones[$i]['ced_bene'] = $ced_bene;
				$arrdeducciones[$i]['cod_pro'] = $cod_pro;
				$arrdeducciones[$i]['codded'] = $data->fields['codded'];
				$arrdeducciones[$i]['procede_doc'] = $procede;
				$arrdeducciones[$i]['numdoccom'] = $codcon;
				$arrdeducciones[$i]['monobjret'] = $data->fields['monret'];
				$arrdeducciones[$i]['monret'] = $data->fields['montotret'];
				$arreglo = $this->buscarPorDed($data->fields['codded']);
				$arrdeducciones[$i]['sc_cuenta'] = $arreglo[0]['sc_cuenta'];
				$arrdeducciones[$i]['porded'] = $arreglo[0]['porded'];
				$arrdeducciones[$i]['estcmp'] = '0';
				$arrdeducciones[$i]['listo'] = 'N';
				$data->MoveNext();
			}
		}
		return $arrdeducciones;
	}
	
	public function buscarCuentaProveedor($codpro)
	{
		$arreglo=array();
		$cadenaSql="SELECT sc_cuenta,sc_ctaant ". 
				   "  FROM rpc_proveedor ". 
				   " WHERE codemp='".$this->codemp."' ".
				   "   AND cod_pro='".$codpro."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarCuentaProveedor ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if($data->fields['sc_cuenta']!='')
			{
				$arreglo['sc_cuenta'] = $data->fields['sc_cuenta'];
				$arreglo['sc_ctaant'] = $data->fields['sc_ctaant'];
			}
			else
			{
				$this->mensaje.=' Falta por configurar la cuenta contable del proveedor ';
			}			
		}	
		return $arreglo;
	}
	
	public function procesoContabilizarSobAnt($objson)
	{
		$arrRespuesta= array();
		$nSol = count((array)$objson->arrDetalle);
		$arreglo = $objson->arrDetalle;
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
			$codant = $objson->arrDetalle[$j]->codant;
			$arrevento['desevetra'] = "Contabilizacion del anticipo {$codant}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarSobAnt($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codant;
				$arrRespuesta[$h]['mensaje'] = 'El anticipo fue contabilizado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codant;
				$arrRespuesta[$h]['mensaje'] = "El anticipo no fue contabilizado, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}

	public function contabilizarSobAnt($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$arrcabecera=array();
		$arregloCar=array();
		$arrDetalleSPG=array();
		$arregloDed=array();
		$arrDetalleSCG=array();
		$this->valido=true;
		$fecha = convertirFechaBd($objson->feccon);
		$estcomobr=$_SESSION["la_empresa"]["estcomobr"];  	
		$codant = $objson->arrDetalle[$j]->codant;
		$codcon = fillComprobante($objson->arrDetalle[$j]->codcon);
		$codpro = $objson->arrDetalle[$j]->cod_pro;
		$procede = 'SOBANT';
		$cont_scg=1;
		$cont_spg=1;
		// OBTENGO EL CONTRATO A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codant='".$codant."' AND codcon='".$codcon."'  ";
		$this->daoSobAnticipo = FabricaDao::CrearDAO('C','sob_anticipo','',$criterio);
		$monto = $this->daoSobAnticipo->monto;
		if($_SESSION["la_empresa"]["estantspg"]=='1')
		{
			$numref=$this->daoSobAnticipo->numref;
			$montoiva=$this->daoSobAnticipo->montoiva;
			$montoret=$this->daoSobAnticipo->montoret;
			$monto = ($monto+$montoiva)-$montoret;
		}
		else
		{
			$numref='ANTICIPOCONTAB';
			$montoret=$this->daoSobAnticipo->montoret;
			$montoiva='0';
			$monto = $monto - $montoret;
		}
		
			$arrcabecera['codemp'] = $this->daoSobAnticipo->codemp;
			$arrcabecera['numrecdoc'] = $this->daoSobAnticipo->numrecdoc;
			$arrcabecera['codtipdoc'] = $objson->codtipdoc;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['cod_pro'] = $codpro;
			$arrcabecera['dencondoc'] = $this->buscarDenominacion($codcon);
			$arrcabecera['fecemidoc'] = $this->daoSobAnticipo->fecfac;
			$arrcabecera['fecregdoc'] = $fecha;
			$arrcabecera['fecvendoc'] = $this->daoSobAnticipo->fecfac;
			$arrcabecera['montotdoc'] = $monto;
			$arrcabecera['mondeddoc'] = $montoret;
			$arrcabecera['moncardoc'] = $montoiva;
			$arrcabecera['tipproben'] = 'P';
			$arrcabecera['numref'] = $numref;
			$arrcabecera['estprodoc'] = 'R';
			$arrcabecera['procede'] = $procede;
			$arrcabecera['estlibcom'] = 0;
			$arrcabecera['estaprord'] = 0;
			$arrcabecera['fecaprord'] = '1900-01-01';
			$arrcabecera['usuaprord'] = '';
			$arrcabecera['estimpmun'] = 0;  
			$arrcabecera['codcla'] = '--';
			$arrcabecera['repcajchi'] = 0;
			if($this->valido)
			{
				if($_SESSION["la_empresa"]["estantspg"]=='1')
				{
					$arreglospg =  $this->buscarSpgRecDoc($codcon,$codant,'sob_cuentaanticipo','codant');
					$totalspg = count((array)$arreglospg);
					if($this->valido && $totalspg>0)
					{
						for($i=1;$i<=$totalspg;$i++)
						{
							$arrDetalleSPG[$cont_spg]['codemp'] = $arrcabecera['codemp'];
							$arrDetalleSPG[$cont_spg]['numrecdoc'] = $arrcabecera['numrecdoc'];
							$arrDetalleSPG[$cont_spg]['codtipdoc'] = $arrcabecera['codtipdoc'];
							$arrDetalleSPG[$cont_spg]['ced_bene'] = $arrcabecera['ced_bene'];
							$arrDetalleSPG[$cont_spg]['cod_pro'] = $arrcabecera['cod_pro'];
							$arrDetalleSPG[$cont_spg]['procede_doc'] = 'SOBCON';
							$arrDetalleSPG[$cont_spg]['spg_cuenta'] = $arreglospg[$i]['spg_cuenta'];
							$arrDetalleSPG[$cont_spg]['codestpro'] = $arreglospg[$i]['codestpro'];
							$arrDetalleSPG[$cont_spg]['estcla'] = $arreglospg[$i]['estcla'];
							$arrDetalleSPG[$cont_spg]['monto'] = $arreglospg[$i]['monto'];
							$arrDetalleSPG[$cont_spg]['numdoccom'] = $codcon;
							$arrDetalleSPG[$cont_spg]['codfuefin'] = '--';
							$cont_spg++;
							
							$arrDetalleSCG[$cont_scg]['codemp'] = $arrcabecera['codemp'];
							$arrDetalleSCG[$cont_scg]['numrecdoc'] = $arrcabecera['numrecdoc'];
							$arrDetalleSCG[$cont_scg]['codtipdoc'] = $arrcabecera['codtipdoc'];
							$arrDetalleSCG[$cont_scg]['ced_bene'] = $arrcabecera['ced_bene'];
							$arrDetalleSCG[$cont_scg]['cod_pro'] = $arrcabecera['cod_pro'];
							$arrDetalleSCG[$cont_scg]['procede_doc'] = 'SOBCON';
							$arrDetalleSCG[$cont_scg]['sc_cuenta'] = $arreglospg[$i]['sc_cuenta'];
							$arrDetalleSCG[$cont_scg]['monto'] = $arreglospg[$i]['monto'];
							$arrDetalleSCG[$cont_scg]['debhab'] = 'D';
							$arrDetalleSCG[$cont_scg]['numdoccom'] = $codcon;
							$arrDetalleSCG[$cont_scg]['estasicon'] = 'A';
							$cont_scg++;
						}
					}
					$arregloCar = $this->obtenerCargos($codant,$codcon,$this->daoSobAnticipo->numrecdoc,$objson->codtipdoc,'----------',$codpro,'sob_cargoanticipo','codant','SOBANT');
					$totalcar=count((array)$arregloCar);
					$montotal=0;
					$monbasimp=0;
					if($this->valido && $totalcar>0)
					{
						for($i=1 ; $i<=$totalcar; $i++)
						{
							$l=$i+1;
							if($arregloCar[$i]['listo']=='N')
							{
								$arrDetalleSPG[$cont_spg]['codemp'] = $arrcabecera['codemp'];
								$arrDetalleSPG[$cont_spg]['numrecdoc'] = $arrcabecera['numrecdoc'];
								$arrDetalleSPG[$cont_spg]['codtipdoc'] = $arrcabecera['codtipdoc'];
								$arrDetalleSPG[$cont_spg]['ced_bene'] = $arrcabecera['ced_bene'];
								$arrDetalleSPG[$cont_spg]['cod_pro'] = $arrcabecera['cod_pro'];
								$arrDetalleSPG[$cont_spg]['procede_doc'] = 'SOBCON';
								$arrDetalleSPG[$cont_spg]['numdoccom'] = $codcon;
								$arrDetalleSPG[$cont_spg]['spg_cuenta'] = $arregloCar[$i]['spg_cuenta'];
								$arrDetalleSPG[$cont_spg]['codestpro'] = $arregloCar[$i]['codestpro'];
								$arrDetalleSPG[$cont_spg]['estcla'] = $arregloCar[$i]['estcla'];
							    $arrDetalleSPG[$cont_spg]['codfuefin'] = '--';
								$montotal = $arregloCar[$i]['monret'];
								$monbasimp = $arregloCar[$i]['monobjret'];
								if($l<$total)
								{
									while($l<$total)
									{
										if($arregloCar[$i]['spg_cuenta']==$arregloCar[$l]['spg_cuenta'] && $arregloCar[$i]['codestpro']==$arregloCar[$l]['codestpro']&& $arregloCar[$i]['estcla']==$arregloCar[$l]['estcla'] && $arregloCar[$l]['listo']=='N')
										{
											$montotal=$montotal+$arregloCar[$l]['monret'];
											$monbasimp=$monbasimp+$arregloCar[$l]['monobjret'];
											$arregloCar[$l]['listo']=='S';
										}
										$l++;
									}
								}
								$arregloCar[$i]['listo']=='S';
								$arrDetalleSPG[$cont_spg]['monto'] = $montotal;
								$arrDetalleSPG[$cont_spg]['basimp'] = $monbasimp;
								
								$arrDetalleSCG[$cont_scg]['codemp'] = $arrcabecera['codemp'];
								$arrDetalleSCG[$cont_scg]['numrecdoc'] = $arrcabecera['numrecdoc'];
								$arrDetalleSCG[$cont_scg]['codtipdoc'] = $arrcabecera['codtipdoc'];
								$arrDetalleSCG[$cont_scg]['ced_bene'] = $arrcabecera['ced_bene'];
								$arrDetalleSCG[$cont_scg]['cod_pro'] = $arrcabecera['cod_pro'];
								$arrDetalleSCG[$cont_scg]['procede_doc'] = 'SOBCON';
								$arrRespuesta=$this->buscarCuentaContable($arregloCar[$i]['spg_cuenta'],$arregloCar[$i]['estcla'],$arregloCar[$i]['codestpro']);
								$arrDetalleSCG[$cont_scg]['sc_cuenta'] = $arrRespuesta['sc_cuenta'];
								$arrDetalleSCG[$cont_scg]['debhab'] = 'D';
								$arrDetalleSCG[$cont_scg]['numdoccom'] = $codcon;
								$arrDetalleSCG[$cont_scg]['monto'] = $montotal;
								$arrDetalleSCG[$cont_scg]['estasicon'] = 'A';
								$cont_scg++;
								$cont_spg++;
							}
						}
					}
					$arregloDed = $this->obtenerDeducciones($codant,$codcon,$this->daoSobAnticipo->numrecdoc,$objson->codtipdoc,'----------',$codpro,'sob_retencionanticipo','codant','SOBANT');
					$totaldec=count((array)$arregloDed);
					$montotal=0;
					$monbasimp=0;
					if($this->valido && $totaldec>0)
					{
						for($i=1 ; $i<=$totaldec; $i++)
						{
							$l=$i+1;
							if($arregloDed[$i]['listo']=='N')
							{
								$arrDetalleSCG[$cont_scg]['sc_cuenta'] = $arregloDed[$i]['sc_cuenta'];
								$montotal = $arregloDed[$i]['monret'];
								while($l<$totaldec)
								{
									if($arregloDed[$i]['sc_cuenta']==$arregloDed[$l]['sc_cuenta'] && $arregloDed[$l]['listo']=='N')
									{
										$montotal=$montotal+$arregloDed[$l]['monret'];
										$arregloDed[$l]['listo']=='S';
									}
									$l++;
								}
								$arregloDed[$i]['listo']=='S';
								$arrDetalleSCG[$cont_scg]['codemp'] = $arrcabecera['codemp'];
								$arrDetalleSCG[$cont_scg]['numrecdoc'] = $arrcabecera['numrecdoc'];
								$arrDetalleSCG[$cont_scg]['codtipdoc'] = $arrcabecera['codtipdoc'];
								$arrDetalleSCG[$cont_scg]['ced_bene'] = $arrcabecera['ced_bene'];
								$arrDetalleSCG[$cont_scg]['cod_pro'] = $arrcabecera['cod_pro'];
								$arrDetalleSCG[$cont_scg]['procede_doc'] = 'SOBCON';
								$arrDetalleSCG[$cont_scg]['monto'] = $montotal;
								$arrDetalleSCG[$cont_scg]['debhab'] = 'H';
								$arrDetalleSCG[$cont_scg]['numdoccom'] = $codcon;
								$arrDetalleSCG[$cont_scg]['estasicon'] = 'A';
								$cont_scg++;
							}
						}
					}
					$totalscg=count((array)$arrDetalleSCG);
					$montohaber=0;
					$montodebe=0;
					if($totalscg>0)
					{
						for($g=1;$g<=$totalscg;$g++)
						{
							if($arrDetalleSCG[$g]['debhab']=='D')
							{
								$montodebe=$montodebe+$arrDetalleSCG[$g]['monto'];
							}
							else
							{
								$montohaber=$montohaber+$arrDetalleSCG[$g]['monto'];
							}	
						}
						$resta=$montodebe-$montohaber;
						$arreglocuepro=$this->buscarCuentaProveedor($codpro);
						$arrDetalleSCG[$cont_scg]['codemp'] = $arrcabecera['codemp'];
						$arrDetalleSCG[$cont_scg]['numrecdoc'] = $arrcabecera['numrecdoc'];
						$arrDetalleSCG[$cont_scg]['codtipdoc'] = $arrcabecera['codtipdoc'];
						$arrDetalleSCG[$cont_scg]['ced_bene'] = $arrcabecera['ced_bene'];
						$arrDetalleSCG[$cont_scg]['cod_pro'] = $arrcabecera['cod_pro'];
						$arrDetalleSCG[$cont_scg]['procede_doc'] = 'SOBCON';
						$arrDetalleSCG[$cont_scg]['numdoccom'] = $codcon;
						$arrDetalleSCG[$cont_scg]['sc_cuenta'] = $arreglocuepro['sc_cuenta'];
						$arrDetalleSCG[$cont_scg]['monto'] = $resta;
						$arrDetalleSCG[$cont_scg]['debhab'] = 'H';
						$arrDetalleSCG[$cont_scg]['estasicon'] = 'A';
						$cont_scg++;
					}
					$totalscg=count((array)$arrDetalleSCG);
					$montohaber=0;
					$montodebe=0;
					if($totalscg>0){
						for($g=1;$g<=$totalscg;$g++)
						{
							if($arrDetalleSCG[$g]['debhab']=='D')
							{
								$montodebe=$montodebe+$arrDetalleSCG[$g]['monto'];
							}
							else
							{
								$montohaber=$montohaber+$arrDetalleSCG[$g]['monto'];
							}	
						}
						$resta=$montodebe-$montohaber;
						if($resta!=0)
						{
							$this->valido=false;
							$this->mensaje.=' El detalle contable no cuadra ';
						}
					}
				}
				else
				{
					$arregloscg = $this->buscarDetalleContable($codant,$codcon,'sob_cuentaanticipo','codant','sob_cargoanticipo','sob_retencionanticipo',$codpro);
					$totalarr = count((array)$arregloscg);
					for($i=0;$i<$totalarr;$i++)
					{
						if ($arregloscg[$i]['debhab']=='D')
						{
							$monto = $arregloscg[$i]['debe'];
						}
						else
						{
							$monto = $arregloscg[$i]['haber'];
						}
						$monto=str_replace('.','',$monto);
						$monto=str_replace(',','.',$monto);
						$arrDetalleSCG[$cont_scg]['codemp']=$arrcabecera['codemp'];
						$arrDetalleSCG[$cont_scg]['numrecdoc']=$arrcabecera['numrecdoc'];
						$arrDetalleSCG[$cont_scg]['codtipdoc']=$arrcabecera['codtipdoc'];
						$arrDetalleSCG[$cont_scg]['ced_bene']=$arrcabecera['ced_bene'];
						$arrDetalleSCG[$cont_scg]['cod_pro']=$arrcabecera['cod_pro'];
						$arrDetalleSCG[$cont_scg]['procede_doc'] = 'SOBANT';
						$arrDetalleSCG[$cont_scg]['sc_cuenta'] = trim($arregloscg[$i]['cuenta']);
						$arrDetalleSCG[$cont_scg]['monto'] =$monto;
						$arrDetalleSCG[$cont_scg]['debhab'] = $arregloscg[$i]['debhab'];
						$arrDetalleSCG[$cont_scg]['numdoccom']=$codcon;
						if($arregloscg[$i]['debhab']=="D")
						{
							$arrDetalleSCG[$cont_scg]['estasicon']='M';
						}
						else
						{
							$arrDetalleSCG[$cont_scg]['estasicon']='A';
						}
						$cont_scg++;
					}
					$arregloDed = $this->obtenerDeducciones($codant,$codcon,$this->daoSobAnticipo->numrecdoc,$objson->codtipdoc,'----------',$codpro,'sob_retencionanticipo','codant','SOBANT');
					$totaldec=count((array)$arregloDed);
					$montotal=0;
					$monbasimp=0;
					if($this->valido && $totaldec>0)
					{
						for($i=1 ; $i<=$totaldec; $i++)
						{
							$l=$i+1;
							if($arregloDed[$i]['listo']=='N')
							{
								$montotal = $arregloDed[$i]['monret'];
								while($l<$totaldec)
								{
									if($arregloDed[$i]['sc_cuenta']==$arregloDed[$l]['sc_cuenta'] && $arregloDed[$l]['listo']=='N')
									{
										$montotal=$montotal+$arregloDed[$l]['monret'];
										$arregloDed[$l]['listo']=='S';
									}
									$l++;
								}
								$arregloDed[$i]['listo']=='S';
							}
						}
					}
				}
			}
			if($this->valido)
			{
				$serviciorecepcion = new ServicioRecepcion();
				$this->valido = $serviciorecepcion->guardarRecepcion($arrcabecera,$arrDetalleSPG,$arrDetalleSCG,$arregloCar,$arregloDed,$arrevento);
				$this->mensaje .= $serviciorecepcion->mensaje;
				unset($serviciorecepcion);
			}
		
		if($this->valido)
		{
			$this->daoSobAnticipo->estant='5';
			$this->daoSobAnticipo->estspgscg='1';
			$this->daoSobAnticipo->fechaconta=$fecha;
			$this->daoSobAnticipo->fechaanula='1900-01-01';
			$this->valido = $this->daoSobAnticipo->modificar();
			if(!$this->valido)
			{
				$this->mensaje .= $this->daoSobAnticipo->ErrorMsg;
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
	
	public function buscarDenominacion($codcon)
	{
		$obscon = '';
		$cadenaSQL="SELECT sob_contrato.obscon ". 
				   "  FROM sob_contrato ". 
				   " WHERE sob_contrato.codemp='".$this->codemp."' ".
				   "   AND sob_contrato.codcon='".$codcon."' ";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarDenominacion ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$obscon = $data->fields['obscon'];
		}
		return $obscon;
	}
	public function procesoRevContabilizarSobAnt($objson)
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
			$codant=$objson->arrDetalle[$j]->codant;
			$arrevento['desevetra'] = "Reversar el anticipo {$codant}, asociado a la empresa {$this->codemp}";
			if ($this->RevcontabilizarSobAnt($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codant;
				$arrRespuesta[$h]['mensaje'] = 'El anticipo fue reversado exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codant;
				$arrRespuesta[$h]['mensaje'] = "El anticipo no fue reversado, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function RevcontabilizarSobAnt($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$codant = $objson->arrDetalle[$j]->codant;
		$codcon = fillComprobante($objson->arrDetalle[$j]->codcon);
		$codpro = $objson->arrDetalle[$j]->cod_pro;
		$arrcabecera=array();
		$i=0;
		// OBTENGO EL ANTICIPO A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codant='".$codant."' AND codcon='".$codcon."' AND  estspgscg='1'";
		$this->daoSobAnticipo = FabricaDao::CrearDAO('C','sob_anticipo','',$criterio);
		// VERIFICO QUE EL ANTICIPO EXISTA
		if($this->daoSobAnticipo->codant=='')
		{
			$this->mensaje .= 'ERROR -> No existe el Anticipo N�'.$codant.', del Contrato N�'.$codcon;
			$this->valido = false;			
		}	
		$estcomobr=$_SESSION["la_empresa"]["estcomobr"];
		
			$arrcabecera['codemp'] = $this->daoSobAnticipo->codemp;
			$arrcabecera['numrecdoc'] = $this->daoSobAnticipo->numrecdoc;
			//$arrcabecera['codtipdoc'] = $objson->codtipdoc;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['cod_pro'] = $codpro;
			$arrcabecera['dencondoc'] = "ANTICIPO CONTRATO ".$codcon;
			$arrcabecera['fecemidoc'] = $this->daoSobAnticipo->fechaconta;
			$arrcabecera['fecregdoc'] = $this->daoSobAnticipo->fechaconta;
			$arrcabecera['fecvendoc'] = $this->daoSobAnticipo->fechaconta;
			$arrcabecera['montotdoc'] = $this->daoSobAnticipo->monto;
			$arrcabecera['mondeddoc'] = $this->daoSobAnticipo->montoret;
			$arrcabecera['moncardoc'] = $this->daoSobAnticipo->montoiva;
			$arrcabecera['tipproben'] = 'P';
			$arrcabecera['numref'] = $this->daoSobAnticipo->numref;
			$arrcabecera['estprodoc'] = 'R';
			$arrcabecera['procede'] = 'SOBANT';
			$arrcabecera['estlibcom'] = 0;
			$arrcabecera['estaprord'] = 0;
			$arrcabecera['fecaprord'] = '1900-01-01';
			$arrcabecera['usuaprord'] = '';
			$arrcabecera['estimpmun'] = 0;  
			$arrcabecera['codcla'] = '--';
			$arrcabecera['repcajchi'] = 0;
			$serviciorecepcion = new ServicioRecepcion();
			$this->valido = $serviciorecepcion->eliminarRecepcion($arrcabecera,$arrevento);
			$this->mensaje .= $serviciorecepcion->mensaje;
			unset($serviciorecepcion);
		
		if($this->valido)
		{
			$this->daoSobAnticipo->estant='1';
			$this->daoSobAnticipo->estspgscg='0';
			$this->daoSobAnticipo->fechaconta='1900-01-01';
			$this->daoSobAnticipo->fechaanula='1900-01-01';
			$this->valido = $this->daoSobAnticipo->modificar();
			if(!$this->valido)
			{
				$this->mensaje .= $this->daoSobAnticipo->ErrorMsg;
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

	public function buscarSobValuacion($codval,$codcon,$feccon,$fecval,$estatus)
	{
		$criterio="";
		$estgenrd="1";
		if(!empty($codval))
		{
			$criterio = $criterio." AND sob_valuacion.codval like '%".$codval."%' ";
		}
		if(!empty($codobr))
		{
			$criterio = $criterio." AND sob_valuacion.codcon like '%".$codcon."%'  ";
		}
		if(!empty($feccon))
		{
			$feccon = convertirFechaBd($feccon);
			$criterio = $criterio." AND sob_contrato.feccon = '".$feccon."'  ";
		}
		if(!empty($fecval))
		{
			$fecval = convertirFechaBd($fecval);
			$criterio = $criterio." AND sob_valuacion.fecha = '".$fecval."' ";
		}
		if($estatus=='0')
		{
			$estgenrd="0";
		}
		$cadenaSQL="SELECT sob_valuacion.*,sob_contrato.fechaconta as fechacontacontrato, sob_asignacion.cod_pro, ".
				"		  (SELECT nompro FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp = sob_asignacion.codemp ".
				"            AND rpc_proveedor.cod_pro = sob_asignacion.cod_pro) as nompro ".
                "FROM sob_valuacion, sob_contrato , sob_asignacion ".
                "WHERE sob_valuacion.codemp='".$this->codemp."' ".
				"	AND sob_valuacion.estapr = 1 ".
				"   AND sob_valuacion.estspgscg=".$estatus." ".
				"   AND sob_contrato.estapr = 1 ".
				"   AND sob_contrato.estspgscg=1 ".
				"   AND sob_valuacion.estgenrd='".$estgenrd."' ".
				$criterio.
				"   AND sob_valuacion.codemp = sob_contrato.codemp ".
				"   AND sob_valuacion.codcon = sob_contrato.codcon ".
				"	AND sob_contrato.codemp = sob_asignacion.codemp ".
				"	AND sob_contrato.codasi = sob_asignacion.codasi ";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false){
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarSobValuacion ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	}
	
	public function buscarContratista($codcon)
	{
		$codpro = '';
		$cadenaSQL="SELECT sob_asignacion.cod_pro ". 
				   "  FROM sob_contrato,sob_asignacion ". 
				   " WHERE sob_contrato.codemp='".$this->codemp."' ".
				   "   AND sob_contrato.codcon='".$codcon."' ".
				   "   AND sob_contrato.codemp=sob_asignacion.codemp ".
				   "   AND sob_contrato.codasi=sob_asignacion.codasi ";
		$data = $this->conexionBaseDatos->Execute($cadenaSQL);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarContratista ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$codpro = $data->fields['cod_pro'];
		}
		return $codpro;
	}
	
	public function buscarValuaciones($codcon,$codval)
	{
		$criterio="";
		if(!empty($codval))
		{
			$criterio = $criterio." AND codval like '%".$codval."%' ";
		}
		$sql = "SELECT * ".
			"   FROM sob_valuacion".
			"   WHERE codemp='".$this->codemp."'".
			"     AND codcon='".$codcon."'".$criterio;
		$data = $this->conexionBaseDatos->Execute($sql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarValuaciones ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	}
	
	public function buscarAnticipos($codcon,$codant)
	{
		$criterio="";
		if(!empty($codant))
		{
			$criterio = $criterio." AND codant like '%".$codant."%' ";
		}
		$sql = "SELECT * ".
			"   FROM sob_anticipo".
			"   WHERE codemp='".$this->codemp."'".
			"     AND codant='".$codant."'".$criterio;
		$data = $this->conexionBaseDatos->Execute($sql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarAnticipos ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	}
	
	public function buscarCuentasPresupuestarias($codcon,$codval,$tabla,$campo)
	{
		$this->valido=true;
		$cadenaSql="SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,spg_cuenta,monto".
				   "  FROM $tabla".
				   " WHERE $tabla.codemp='".$this->codemp."'".
				   "   AND $tabla.codcon='".$codcon."'".
				   "   AND $tabla.$campo='".$codval."'";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarCuentasPresupuestarias ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}	
		return $data;
	}

	public function buscarSpgRecDoc($codcon,$codval,$tabla,$campo)
	{
		$this->valido=true;
		$arreglo=array();
		$data=$this->buscarCuentasPresupuestarias($codcon,$codval,$tabla,$campo);
		while((!$data->EOF)&&($this->valido))
		{
			$i++;
			$codestpro=$data->fields["codestpro1"].$data->fields["codestpro2"].$data->fields["codestpro3"].$data->fields["codestpro4"].$data->fields["codestpro5"];
			$arreglo[$i]['estcla']=$data->fields["estcla"];
			$arreglo[$i]['spg_cuenta']=$data->fields["spg_cuenta"];
			$arreglo[$i]['monto']=$data->fields["monto"];
			$arreglo[$i]['codestpro']=$codestpro;
			$arrRespuesta = $this->buscarCuentaContable($data->fields["spg_cuenta"],$data->fields["estcla"],$codestpro);
			$arreglo[$i]['sc_cuenta']= $arrRespuesta['sc_cuenta'];
			$data->MoveNext();
		}
		return $arreglo;
	}
	
	public function procesoContabilizarSobVal($objson)
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
			$codval=$objson->arrDetalle[$j]->codval;
			$arrevento['desevetra'] = "Contabilizacion la valuacion {$codval}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarSobVal($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codval;
				$arrRespuesta[$h]['mensaje'] = 'La valuacion fue contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codval;
				$arrRespuesta[$h]['mensaje'] = "La valuacion no fue contabilizada, {$this->mensaje} ";
			}
			$h++;
			$this->valido=true;
			$this->mensaje="";
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarSobVal($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();
		$arrcabecera=array();
		$arregloCar=array();
		$arrDetalleSPG=array();
		$arregloDed=array();
		$arrDetalleSCG=array();
		$cont_scg=1;
		$cont_spg=1;
		$this->valido=true;
		$this->mensaje='';
		$fecha = convertirFechaBd($objson->feccon);
		$codtipdoc = $objson->codtipdoc;
		$codval = $objson->arrDetalle[$j]->codval;
		$codcon = fillComprobante($objson->arrDetalle[$j]->codcon);
		$codpro = $this->buscarContratista($codcon);
		// OBTENGO LA VALUACION A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codval='".$codval."' AND codcon='".$codcon."'  ";
		$this->daoSobValuacion = FabricaDao::CrearDAO('C','sob_valuacion','',$criterio);
		$monto = $this->daoSobValuacion->montotval;
		$basimp = $this->daoSobValuacion->basimpval;
		$subtot = $this->daoSobValuacion->subtot;
		$totreten = $this->daoSobValuacion->totreten;
		$totcar =(($monto+$totreten)-$basimp);
		$numref = $this->daoSobValuacion->numref;
		$fecrecdoc = $this->daoSobValuacion->fecrecdoc;
		$monamoval = $this->daoSobValuacion->amoval;
		$obsval=$this->daoSobValuacion->obsval;
		$fecval = $this->daoSobValuacion->fecha;
		$validofecha = compararFecha($fecval,$fecha);
		$montototdoc = $this->daoSobValuacion->montotval;
		if(!$validofecha)
		{
			$this->mensaje .= " La Fecha de contabilizacion no puede ser menor a la fecha de la valuacion ";
			$this->valido = false;
		}
		if($this->valido)
		{
			$arrcabecera['codemp'] = $this->daoSobValuacion->codemp;
			$arrcabecera['numrecdoc'] = $this->daoSobValuacion->numrecdoc;
			$arrcabecera['codtipdoc'] = $codtipdoc;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['cod_pro'] = $codpro;
			$arrcabecera['dencondoc'] = $obsval;
			$arrcabecera['fecemidoc'] = $fecrecdoc;
			$arrcabecera['fecregdoc'] = $fecha;
			$arrcabecera['fecvendoc'] = $fecha;
			$arrcabecera['montotdoc'] = $montototdoc;
			$arrcabecera['mondeddoc'] = $totreten;
			$arrcabecera['moncardoc'] = $totcar;
			$arrcabecera['tipproben'] = 'P';
			$arrcabecera['numref'] = $numref;
			$arrcabecera['estprodoc'] = 'R';
			$arrcabecera['procede'] = 'SOBVAL';
			$arrcabecera['estlibcom'] = 1;
			$arrcabecera['estaprord'] = 0;
			$arrcabecera['fecaprord'] = '1900-01-01';
			$arrcabecera['usuaprord'] = '';
			$arrcabecera['estimpmun'] = '0';
			$arrcabecera['codcla'] = '--';
			$arrcabecera['repcajchi'] = 0;
			if($_SESSION["la_empresa"]["estcomobr"]=='0')
			{
				$arreglospg =  $this->buscarInformacionDetalleValuacion($codval,$codcon);
				$totalarr = count((array)$arreglospg);
				for($i=0;$i<$totalarr;$i++)
				{
					if($arreglospg[$i]['estcla']=='Accion')
					{
						$estcla='A';
					}
					else
					{
						$estcla='P';
					}
					$arrDetalleSPG[$cont_spg]['codemp']=$arrcabecera['codemp'];
					$arrDetalleSPG[$cont_spg]['numrecdoc']=$arrcabecera['numrecdoc'];
					$arrDetalleSPG[$cont_spg]['codtipdoc']=$arrcabecera['codtipdoc'];
					$arrDetalleSPG[$cont_spg]['ced_bene']=$arrcabecera['ced_bene'];
					$arrDetalleSPG[$cont_spg]['cod_pro']=$arrcabecera['cod_pro'];
					$arrDetalleSPG[$cont_spg]['procede_doc'] = 'SOBCON';
					$arrDetalleSPG[$cont_spg]['spg_cuenta'] = $arreglospg[$i]['spg_cuenta'];
					$arrDetalleSPG[$cont_spg]['codestpro'] = $arreglospg[$i]['codestpro'];
					$arrDetalleSPG[$cont_spg]['codfuefin'] = '--';
					$arrDetalleSPG[$cont_spg]['estcla'] = $estcla;
					$arrDetalleSPG[$cont_spg]['monto'] = $arreglospg[$i]['monto'];
					$arrDetalleSPG[$cont_spg]['numdoccom']=$codcon;
					$cont_spg++;
				}
				if($this->valido)
				{
					$arregloscg = $this->buscarDetalleContable($codval,$codcon,'sob_cuentavaluacion','codval','sob_cargovaluacion','sob_retencionvaluacioncontrato',$codpro);
					$arregloscg = $this->agruparCuentasContable($arregloscg);
					$totalarr = count((array)$arregloscg);
					for($i=0;$i<$totalarr;$i++)
					{
						if ($arregloscg[$i]['debhab']=='D')
						{
							$monto = $arregloscg[$i]['debe'];
						}
						else
						{
							$monto = $arregloscg[$i]['haber'];
						}
						$arrDetalleSCG[$cont_scg]['codemp']=$arrcabecera['codemp'];
						$arrDetalleSCG[$cont_scg]['numrecdoc']=$arrcabecera['numrecdoc'];
						$arrDetalleSCG[$cont_scg]['codtipdoc']=$arrcabecera['codtipdoc'];
						$arrDetalleSCG[$cont_scg]['ced_bene']=$arrcabecera['ced_bene'];
						$arrDetalleSCG[$cont_scg]['cod_pro']=$arrcabecera['cod_pro'];
						$arrDetalleSCG[$cont_scg]['procede_doc'] = 'SOBCON';
						$arrDetalleSCG[$cont_scg]['sc_cuenta'] = $arregloscg[$i]['cuenta'];
						$arrDetalleSCG[$cont_scg]['monto'] = $monto; 
						$arrDetalleSCG[$cont_scg]['debhab'] = $arregloscg[$i]['debhab'];
						$arrDetalleSCG[$cont_scg]['numdoccom']=$codcon;
						$arrDetalleSCG[$cont_scg]['estasicon']=$arregloscg[$i]['estasicon'];
						$cont_scg++;
					}
				}
				if($this->valido)
				{
					$arregloCar = $this->obtenerCargos($codval,$codcon,$this->daoSobValuacion->numrecdoc,$objson->codtipdoc,'----------',$codpro,'sob_cargovaluacion','codval','SOBCON');
					$total=count((array)$arregloCar);
					$montotal=0;
					$monbasimp=0;
					for($i=1;$i<=$total;$i++)
					{
						$l=$i+1;
						if($arregloCar[$i]['listo']=='N')
						{
							$montotal = $arregloCar[$i]['monret'];
							$monbasimp = $arregloCar[$i]['monobjret'];
							if($l<$total)
							{
								while($l<$total)
								{
									if($arregloCar[$i]['spg_cuenta']==$arregloCar[$l]['spg_cuenta'] && $arregloCar[$i]['codestpro']==$arregloCar[$l]['codestpro']&& $arregloCar[$i]['estcla']==$arregloCar[$l]['estcla'] && $arregloCar[$l]['listo']=='N')
									{
										$montotal=$montotal+$arregloCar[$l]['monret'];
										$monbasimp=$monbasimp+$arregloCar[$l]['monobjret'];
										$arregloCar[$l]['listo']=='S';
									}
								$l++;
								}
							}
							$arregloCar[$i]['listo']='S';
						}
					}
				}
				if($this->valido)
				{
					$arregloDed = $this->obtenerDeducciones($codval,$codcon,$this->daoSobValuacion->numrecdoc,$objson->codtipdoc,'----------',$codpro,'sob_retencionvaluacioncontrato','codval','SOBCON');
					$totalded=count((array)$arregloDed);
					$montotal=0;
					$monbasimp=0;
					for($i=1;$i<=$totalded; $i++)
					{
						$l=$i+1;
						if($arregloDed[$i]['listo']=='N')
						{
							$montotal = $arregloDed[$i]['monret'];
							if($l<$totalded)
							{
								while($l<$totalded)
								{
									if($arregloDed[$i]['sc_cuenta']==$arregloDed[$l]['sc_cuenta'] && $arregloDed[$l]['listo']=='N')
									{
										$montotal=$montotal+$arregloDed[$l]['monret'];
										$arregloDed[$l]['listo']=='S';
									}
									$l++;
								}
							}
							$arregloDed[$i]['listo']='S';
						}
					}
				}
				if($this->valido)
				{
					$serviciorecepcion = new ServicioRecepcion();
					$this->valido = $serviciorecepcion->guardarRecepcion($arrcabecera,$arrDetalleSPG,$arrDetalleSCG,$arregloCar,$arregloDed,$arrevento);
					$this->mensaje .= $serviciorecepcion->mensaje;
					unset($serviciorecepcion);
				}
			}
		}
		if($this->valido)
		{
			$this->daoSobValuacion->estspgscg='1';
			$this->daoSobValuacion->estgenrd='1';
			$this->daoSobValuacion->estval='5';
			$this->valido = $this->daoSobValuacion->modificar();
			if(!$this->valido)
			{
				$this->mensaje .= $this->daoSobValuacion->ErrorMsg;
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
	
	public function buscarNumSol($comprobante,$cod_pro,$ced_bene,$codcon)
	{
		$numsol="";
		$ls_sql="SELECT numsol".
				"FROM cxp_dt_solicitudes".
				"WHERE codemp='".$this->codemp."'".
				"  AND TRIM(numrecdoc)='".$comprobante."'".
				"  AND cod_pro='".$cod_pro."'".
				"  AND ced_bene='".$ced_bene."'";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarNumSol ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				$this->valido = false;
				$this->mensaje .= 'La Valuacion del Contrato '.$codcon.' tiene una Recepcion de Documentos Asociada por lo tanto no puede ser reversada.';			
			}
			else
			{
				$this->valido=true;
			}
		}
		return $this->valido;
	}
	
	public function procesoRevContabilizarSobVal($objson)
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
			$codval=fillComprobante($objson->arrDetalle[$j]->codval);
			$arrevento['desevetra'] = "Reversar la valuacion {$codval}, asociado a la empresa {$this->codemp}";
			if ($this->RevcontabilizarSobVal($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codval;
				$arrRespuesta[$h]['mensaje'] = 'La valuacion fue reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codval;
				$arrRespuesta[$h]['mensaje'] = "La valuacion no fue reversada, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function RevcontabilizarSobVal($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  
		$codval = $objson->arrDetalle[$j]->codval;
		$codcon = $objson->arrDetalle[$j]->codcon;
		$arrcabecera=array();
		// OBTENGO LA VALUACION A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codval='".$codval."' AND codcon='".$codcon."' AND  estspgscg='1' ";
		$this->daoSobValuacion = FabricaDao::CrearDAO('C','sob_valuacion','',$criterio);
		// VERIFICO QUE LA VALUACION EXISTA
		$estcomobr=$_SESSION["la_empresa"]["estcomobr"];
		$codpro=$this->buscarContratista($codcon);
		if($estcomobr==0)
		{
			if($this->daoSobValuacion->numrecdoc!='')
			{
				$monto = formatoNumericoBd($this->daoSobValuacion->montotval);
				$basimp = $this->daoSobValuacion->basimpval;
				$subtot = $this->daoSobValuacion->subtot;
				$totreten = $this->daoSobValuacion->totreten;
				$totcar =(($monto+$totreten)-$basimp);
				$monamoval = formatoNumericoBd($this->daoSobValuacion->amoval,1);
				$montototdoc = $monto-$monamoval;
				$arrcabecera['codemp'] = $this->daoSobValuacion->codemp;
				$arrcabecera['numrecdoc'] = $this->daoSobValuacion->numrecdoc;
				//$arrcabecera['codtipdoc'] = $objson->codtipdoc;
				$arrcabecera['ced_bene'] = '----------';
				$arrcabecera['cod_pro'] = $codpro;
				$arrcabecera['dencondoc'] = "VALUACION CONTRATO ".$codcon;
				$arrcabecera['fecemidoc'] = $this->daoSobValuacion->fechaconta;
				$arrcabecera['fecregdoc'] = $this->daoSobValuacion->fechaconta;
				$arrcabecera['fecvendoc'] = $this->daoSobValuacion->fechaconta;
				$arrcabecera['montotdoc'] = $montototdoc;
				$arrcabecera['mondeddoc'] = $totreten;
				$arrcabecera['moncardoc'] = $totcar;
				$arrcabecera['tipproben'] = 'P';
				$arrcabecera['numref'] = $this->daoSobValuacion->numref;
				$arrcabecera['estprodoc'] = 'R';
				$arrcabecera['procede'] = 'SOBRPC';
				$arrcabecera['estlibcom'] = 1;
				$arrcabecera['estaprord'] = 0;
				$arrcabecera['fecaprord'] = '1900-01-01';
				$arrcabecera['usuaprord'] = '';
				$arrcabecera['estimpmun'] = '0';
				$arrcabecera['codcla'] = '--';
				$arrcabecera['repcajchi'] = 0;
				$serviciorecepcion = new ServicioRecepcion();
				$this->valido = $serviciorecepcion->eliminarRecepcion($arrcabecera,$arrevento);
				$this->mensaje .= $serviciorecepcion->mensaje;
				unset($serviciorecepcion);
				if($this->valido)
				{
					$this->daoSobValuacion->estspgscg='0';
					$this->daoSobValuacion->estval='1';
					$this->daoSobValuacion->estgenrd='0';
					$this->daoSobValuacion->fechaconta='1900-01-01';
					$this->daoSobValuacion->fechaanula='1900-01-01';
					$this->valido = $this->daoSobValuacion->modificar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoSobValuacion->ErrorMsg;
					}				
				}	
			}
		}
		else
		{
			if($this->buscarNumSol($this->daoSobAnticipo->numrecdoc,$codpro,'----------',$codcon))
			{
				$this->daoSobValuacion->estspgscg='0';
			    $this->daoSobValuacion->estgenrd='0';
			    $this->daoSobValuacion->sobval='6';
				$this->daoSobValuacion->fechaconta='1900-01-01';
				$this->daoSobValuacion->fechaanula='1900-01-01';
				$this->valido = $this->daoSobValuacion->modificar();
				if(!$this->valido)
				{
					$this->mensaje .= $this->daoSobValuacion->ErrorMsg;
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
	
	public function buscarSobVariacion($codvar,$codcon,$feccon,$fecvar,$estatus)
	{
		$criterio="";
		if(!empty($codvar))
		{
			$criterio=$criterio." AND sob_variacioncontrato.codvar like '%".$codvar."%'";
		}
		if(!empty($codcon))
		{
			$criterio=$criterio." AND sob_variacioncontrato.codcon like '%".$codcon."%'";
		}
		if(!empty($feccon))
		{
			$feccon=convertirFechaBd($feccon);
			$criterio=$criterio." AND sob_contrato.feccon = '".$feccon."'";
		}
		if(!empty($fecvar))
		{
			$fecvar=convertirFechaBd($fecvar);
			$criterio=$criterio." AND sob_variacioncontrato.fecvar = '".$fecvar."'";
		}
		$cadenaSql="SELECT sob_variacioncontrato.codcon,sob_variacioncontrato.codvar,sob_variacioncontrato.fecvar, sob_variacioncontrato.motvar,".
				   "       sob_variacioncontrato.fechaconta,sob_variacioncontrato.fechaanula,sob_contrato.fechaconta as fechacontacontrato,".
				   "       sob_asignacion.cod_pro,rpc_proveedor.nompro,sob_contrato.obscon ".
                   "  FROM sob_variacioncontrato,sob_contrato,sob_asignacion,rpc_proveedor".
                   " WHERE sob_variacioncontrato.codemp='".$this->codemp."'".
				   "   AND sob_variacioncontrato.estapr = '1'".
				   "   AND sob_variacioncontrato.estspgscg=".$estatus."".
				   $criterio.
				   "   AND sob_variacioncontrato.codemp = sob_contrato.codemp".
				   "   AND sob_variacioncontrato.codcon = sob_contrato.codcon".
				   "   AND sob_asignacion.codemp = sob_contrato.codemp".
				   "   AND sob_asignacion.codasi = sob_contrato.codasi".
				   "   AND sob_asignacion.codemp = rpc_proveedor.codemp".
				   "   AND sob_asignacion.cod_pro = rpc_proveedor.cod_pro";
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarSobVariacion ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	}
	
	function buscarTipoVariacion($codcon,$codvar)
	{
		$tipvar="";
		$ls_sql="SELECT sob_variacioncontrato.tipvar  ".
                "FROM sob_variacioncontrato  ".
                "WHERE sob_variacioncontrato.codemp='".$this->codemp."' ".
				"  AND sob_variacioncontrato.estvar=1 ".
				"  AND sob_variacioncontrato.estapr='1' ".
				"  AND sob_variacioncontrato.codcon='".$codcon."' ".
				"  AND sob_variacioncontrato.codvar='".$codvar."' ";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->buscarTipoVariacion ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
            $tipvar=$data->fields['tipvar'];
		}
		return $tipvar;
	}
	
	function sumaTotalCuentasGasVar($codcon,$codvar)
	{
		$monto=0;
		$ls_sql="SELECT COALESCE(SUM(monto),0) As monto  ".
                "FROM sob_cuentavariacion  ".
                "WHERE codemp='".$this->codemp."' ".
				"  AND codcon='".$codcon."'";
				"  AND codvar='".$codvar."'";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->INTEGRADOR SOB M�TODO->sumaTotalCuentasGasVar ERROR->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$monto=$data->fields['monto'];
		}
		return $monto;
	}
	
	public function procesoContabilizarSobVar($objson)
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
			$codvar=$objson->arrDetalle[$j]->codvar;
			$arrevento['desevetra'] = "Contabilizacion la variacion {$codvar}, asociado a la empresa {$this->codemp}";
			if ($this->contabilizarSobVar($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codvar;
				$arrRespuesta[$h]['mensaje'] = 'La variacion fue contabilizada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codvar;
				$arrRespuesta[$h]['mensaje'] = "La variacion no fue contabilizada, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function contabilizarSobVar($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$codvar=$objson->arrDetalle[$j]->codvar;
		$codcon=fillComprobante($objson->arrDetalle[$j]->codcon);
		$codpro=$objson->arrDetalle[$j]->cod_pro;
		// OBTENGO LA VARIACION A CONTABILIZAR
		$criterio="codemp = '".$this->codemp."' AND codvar='".$codvar."' AND codcon='".$codcon."'";
		$this->daoSobVariacion = FabricaDao::CrearDAO('C','sob_variacioncontrato','',$criterio);
		// VERIFICO QUE LA VARIACION EXISTA, ESTE EMITIDA Y MODIFICADA
		if($this->daoSobVariacion->codvar=='')
		{
			$this->mensaje .= 'ERROR -> No existe la Variacion N�'.$codvar.', del Contrato N�'.$codcon;
			$this->valido = false;			
		}
		if($this->buscarTipoVariacion($codcon,$codvar)!='3')
		{
			$suma_gasto=$this->sumaTotalCuentasGasVar($codcon,$codvar);
			$monto_asi=$suma_gasto;
			if($suma_gasto!=$monto_asi){
				$this->mensaje .= 'ERROR -> La Variacion del Contrato N�'.$codcon.', no esta cuadrando con el resumen presupuestario';
				$this->valido = false;	
			}
			$feccon=convertirFechaBd($objson->arrDetalle[$j]->fechacontacontrato);
			$fecvar=convertirFechaBd($objson->arrDetalle[$j]->fecvar);
			if(!compararFecha($feccon,$fecvar))
			{
				$this->mensaje .= 'ERROR -> La Fecha de Contabilizacion es menor que la fecha de Emision del Contrato'.$codcon;
				$this->valido = false;	
			}
			if ($this->valido)
			{
				$arrcabecera['codemp'] = $this->daoSobVariacion->codemp;
				$arrcabecera['procede'] = 'SOBVAR';
				$arrcabecera['comprobante'] = fillComprobante($this->daoSobVariacion->codvar);
				$arrcabecera['codban'] = '---';
				$arrcabecera['ctaban'] = '-------------------------';
				$arrcabecera['fecha'] = $fecvar;
				$arrcabecera['descripcion'] = $this->daoSobVariacion->motvar;
				$arrcabecera['tipo_comp'] = 1;
				$arrcabecera['tipo_destino'] = 'P';
				$arrcabecera['cod_pro'] = $codpro;
				$arrcabecera['ced_bene'] = '----------';
				$arrcabecera['total'] = $this->daoSobVariacion->monto;
				$arrcabecera['numpolcon'] = 0;
				$arrcabecera['esttrfcmp'] = 0;
				$arrcabecera['estrenfon'] = 0;
				$arrcabecera['codfuefin'] = '--';
				$arrcabecera['codusu'] = $_SESSION['la_logusr'];
				$arrdetallespg=$this->buscarDetallePresupuestario($this->daoSobVariacion->codvar,$this->daoSobVariacion->codcon,$arrcabecera,'O');
				if ($this->valido)
				{
					$serviciocomprobante = new ServicioComprobante();
					$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arrdetallespg,null,null,$arrevento);
					$this->mensaje .= $serviciocomprobante->mensaje;
					unset($serviciocomprobante);
				}
			}
		}
		if($this->valido)
		{
			$this->daoSobVariacion->estspgscg='1';
			$this->daoSobVariacion->estvar='5';
			$this->daoSobVariacion->fechaconta=convertirFechaBd($detalle->fecvar);
			$this->daoSobVariacion->fechaanula='1900-01-01';
			$this->valido = $this->daoSobVariacion->modificar();
			if(!$this->valido)
			{
				$this->mensaje .= $this->daoSobVariacion->ErrorMsg;
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
	
	
	
	function actualizarMontoContrato($codcon,$codvar,$operacion)
	{
		$this->valido=true;
		$ls_sql="SELECT sob_variacioncontrato.tipvar,sob_variacioncontrato.monto AS montovar,sob_contrato.monto  AS montocon  ".
                "FROM sob_variacioncontrato,sob_contrato ".
                "WHERE sob_variacioncontrato.codemp='".$this->codemp."' ".
				"  AND sob_variacioncontrato.codcon='".$codcon."'".
				"  AND sob_variacioncontrato.codvar='".$codvar."'".
				"  AND sob_variacioncontrato.codemp=sob_contrato.codemp".
				"  AND sob_variacioncontrato.codcon=sob_contrato.codcon";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false)
		{
            $this->mensaje.='CLASE->Integracion SOB M�TODO->actualizarMontoContrato ERROR->';			
			$this->valido=false;
		}
		else
		{                 
			if($data->EOF)
		    {
				$tipvar=$data->fields["tipvar"];
				$montovar=$data->fields["montovar"];
				$montocon=$data->fields["montocon"];
				if($operacion=="1")
				{
					if($tipvar=="0")
					{
						$monreal=$montocon-$montovar;
					}
					else
					{
						$monreal=$montocon-$montovar;
					}
				}
				else
				{
					$monreal=0;
				}
				$ls_sql="UPDATE sob_contrato  ".
						"SET monreacon=".$monreal."  ".
						"WHERE codemp='".$this->codemp."' ".
						"  AND codcon='".$codcon."'";		
				$data = $this->conexionBaseDatos->Execute($ls_sql);
				if($data===false)
				{
					$this->valido=false;
					$this->mensaje.='CLASE->Integracion SOB M�TODO->actualizarMontoContrato ERROR->';	
				}
			} 
			if($this->valido)
			{
				$this->valido=$this->actualizarPartidasCuentas($codcon,$codvar,$operacion);
			}
		}
		return $this->valido; 
	}// end function uf_procesar_detalles_gastos_variacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function actualizarPartidasCuentas($codcon,$codvar,$operacion)
	{
		$lb_valido=true;	
		$ls_sql="SELECT sob_variacionpartida.codpar,sob_variacioncontrato.tipvar,sob_variacionpartida.cantidad_nueva, "."		sob_variacionpartida.precio_nuevo,sob_variacionpartida.precio_anterior,sob_asignacionpartidaobra.canvarpar,sob_asignacionpartidaobra.codasi ".
                "  FROM sob_variacionpartida,sob_contrato,sob_variacioncontrato,sob_asignacionpartidaobra ".
                " WHERE sob_variacioncontrato.codemp='".$this->codemp."' ".
				"  AND sob_variacioncontrato.codcon='".$codcon."' ".
				"  AND sob_variacioncontrato.codvar='".$codvar."' ".
				"  AND sob_variacioncontrato.codemp=sob_variacionpartida.codemp".
				"  AND sob_variacioncontrato.codvar=sob_variacionpartida.codvar".
				"  AND sob_variacioncontrato.codcon=sob_variacionpartida.codcon".
				"  AND sob_variacioncontrato.codcon=sob_variacionpartida.codcon".
				"  AND sob_variacioncontrato.codemp=sob_contrato.codemp".
				"  AND sob_variacioncontrato.codcon=sob_contrato.codcon".
				"  AND sob_contrato.codemp=sob_asignacionpartidaobra.codemp".
				"  AND sob_contrato.codasi=sob_asignacionpartidaobra.codasi".
				"  AND sob_asignacionpartidaobra.codemp=sob_variacionpartida.codemp".
				"  AND sob_asignacionpartidaobra.codpar=sob_variacionpartida.codpar";
		$data = $this->conexionBaseDatos->Execute($ls_sql);
		if($data===false)
		{
			$this->valido=false;
			$this->mensaje.='CLASE->Integracion SOB M�TODO->actualizarPartidasCuentas ERROR->';	
		}
		else
		{                 
			while(!$data->EOF)
		    {
				$tipvar=$data->fields["tipvar"];
				$cantidad_nueva=$data->fields["cantidad_nueva"];
				$precio_nuevo=$data->fields["precio_nuevo"];
				$precio_anterior=$data->fields["precio_anterior"];
				$canvarpar=$data->fields["canvarpar"];
				$codpar=$data->fields["codpar"];
				$codasi=$data->fields["codasi"];
				if($operacion=="1")
				{
					if($tipvar=="0")
					{
						$cantidad_nueva=($cantidad_nueva*-1);
					}
				}
				else
				{
					$precio_nuevo=$precio_anterior;
					if($tipvar!="0")
					{
						$cantidad_nueva=($cantidad_nueva*-1);
					}
				}
				$canvarpar=$canvarpar+$cantidad_nueva;
				$ls_sql="UPDATE sob_asignacionpartidaobra  ".
						"SET preparasi=".$precio_nuevo.", canvarpar=".$canvarpar."  ".
						"WHERE codemp='".$this->codemp."'".
						"   AND codasi='".$codasi."'".
						"   AND codpar='".$codpar."'";		
				$data = $this->conexionBaseDatos->Execute($ls_sql);
		    	if($data===false)
				{
					$this->valido=false;
					$this->mensaje.='CLASE->Integracion SOB M�TODO->actualizarPartidasCuentas ERROR->';	
				}
			} 
		}
		return $this->valido; 
	}// end function uf_procesar_detalles_gastos_variacion
	
	public function procesoRevContabilizarSobVar($objson)
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
			$codvar=$objson->arrDetalle[$j]->codvar;
			$arrevento['desevetra'] = "Reversar la variacion {$codvar}, asociado a la empresa {$this->codemp}";
			if ($this->RevcontabilizarSobVar($objson,$arrevento,$j)) 
			{
				$nOk++;
				$arrRespuesta[$h]['estatus'] = 1;
				$arrRespuesta[$h]['documento'] = $codvar;
				$arrRespuesta[$h]['mensaje'] = 'La variacion fue reversada exitosamente';
			}
			else 
			{
				$nEr++;
				$arrRespuesta[$h]['estatus'] = 0;
				$arrRespuesta[$h]['documento'] = $codvar;
				$arrRespuesta[$h]['mensaje'] = "La variacion no fue reversada, {$this->mensaje} ";
			}
			$h++;
		}
		$detalleResultado = generarJsonArreglo($arrRespuesta);
		return $nSol.'|'.$nOk.'|'.$nEr.'|'.$detalleResultado;
	}
	
	public function RevcontabilizarSobVar($objson,$arrevento,$j)
	{
		DaoGenerico::iniciarTrans();  	
		$codvar=$objson->arrDetalle[$j]->codvar;
		$codcon=fillComprobante($objson->arrDetalle[$j]->codcon);
		$codpro=$objson->arrDetalle[$j]->cod_pro;
		// OBTENGO LA VARIACION A REVERSAR
		$criterio="codemp = '".$this->codemp."' AND codvar='".$codvar."' AND codcon='".$codcon."' AND estspgscg='1' ";
		$this->daoSobVariacion = FabricaDao::CrearDAO('C','sob_variacioncontrato','',$criterio);
		// VERIFICO QUE LA VARIACION EXISTA
		if($this->daoSobVariacion->codvar=='')
		{
			$this->mensaje .= 'ERROR -> No existe la Variacion N�'.$codvar.', del Contrato N�'.$codcon.' en estatus CONTABILIZADO';
			$this->valido = false;			
		}
		if($this->buscarTipoVariacion($codcon,$codvar)!='3')
		{
			$arrcabecera['codemp'] = $this->daoSobVariacion->codemp;
			$arrcabecera['procede'] = 'SOBVAR';
			$arrcabecera['comprobante'] = fillComprobante($this->daoSobVariacion->codvar);
			$arrcabecera['codban'] = '---';
			$arrcabecera['ctaban'] = '-------------------------';
			$arrcabecera['fecha'] = $this->daoSobVariacion->fechaconta;
			$arrcabecera['descripcion'] = $this->daoSobVariacion->motvar;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = 'P';
			$arrcabecera['cod_pro'] = $codpro;
			$arrcabecera['ced_bene'] = '----------';
			$arrcabecera['total'] = $this->daoSobVariacion->monto;
			$arrcabecera['numpolcon'] = 0;
			$arrcabecera['esttrfcmp'] = 0;
			$arrcabecera['estrenfon'] = 0;
			$arrcabecera['codfuefin'] = '--';
			$arrcabecera['codusu'] = $_SESSION['la_logusr'];
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->eliminarComprobante($arrcabecera,$arrevento);
			$this->mensaje .= $serviciocomprobante->mensaje;
			if($this->valido){
				$this->valido=$this->actualizarMontoContrato($codcon,$codvar,'0');
			}
		}
		if($this->valido)
		{
			$this->daoSobVariacion->estspgscg='0';
			$this->daoSobVariacion->estvar='1';
			$this->daoSobVariacion->fechaconta=$this->daoSobVariacion->fechaconta;
			$this->daoSobVariacion->fechaanula='1900-01-01';
			$this->valido = $this->daoSobVariacion->modificar();
			if(!$this->valido)
			{
				$this->mensaje .= $this->daoSobVariacion->ErrorMsg;
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
	
	function disponibilidad($codemp,$data2,$int_spg,$spg_cuenta='',$codestpro='',$estcla='')
    { 
    	$this->servicioComprobante = new ServicioComprobanteSPG();
		$disponible=true;
    	$disponibilidad=0;
    	$nivel=0;
    	$empresa=$_SESSION["la_empresa"];
		$vali_nivel=$empresa["vali_nivel"];
		if ($spg_cuenta=='' && $codestpro=='')
		{
			$cuenta=$data2->fields["spg_cuenta"];
		}
		else
		{
	    	$cuenta=$spg_cuenta;
		}
		$arrdetallespg['codemp']     = $codemp;
		$arrdetallespg['codestpro1'] = $data2->fields['codestpro1'];
		$arrdetallespg['codestpro2'] = $data2->fields['codestpro2'];
		$arrdetallespg['codestpro3'] = $data2->fields['codestpro3'];
		$arrdetallespg['codestpro4'] = $data2->fields['codestpro4'];
		$arrdetallespg['codestpro5'] = $data2->fields['codestpro5'];
		$arrdetallespg['estcla']     = $data2->fields['estcla'];
		$arrdetallespg['spg_cuenta'] = $data2->fields['spg_cuenta'];
		$this->servicioComprobante->setDaoDetalleSpg($arrdetallespg);
		if($vali_nivel==5)
		{
			$formpre=str_replace("-","",$empresa["formpre"]);
			$vali_nivel=$this->servicioComprobante->obtenerNivel($cuenta);
		}
		if($_SESSION["la_empresa"]["estvaldis"]==0)
		{
			$vali_nivel=0;
		}
		$nivel=$this->servicioComprobante->obtenerNivel($cuenta);
		if ($nivel <= $vali_nivel)
		{
			$status="";
			$asignado=0;
			$aumento=0;
			$disminucion=0;
			$precomprometido=0;
			$comprometido=0;
			$causado=0;
			$pagado=0;
			$this->servicioComprobante->saldoSelect('ACTUAL');
			$disponibilidad=((($this->servicioComprobante->asignado) + ($this->servicioComprobante->aumento)) - (($this->servicioComprobante->disminucion) + ($this->servicioComprobante->comprometido) + ($this->servicioComprobante->precomprometido)));
			if(round($data2->fields['monto'],2) >= round($disponibilidad,2))
			{
				$disponible=false;
			}
			if($disponible)
			{
				$status="";
				$asignado=0;
				$aumento=0;
				$disminucion=0;
				$precomprometido=0;
				$comprometido=0;
				$causado=0;
				$pagado=0;
				$this->servicioComprobante->saldoSelect('COMPROBANTE');
				$disponibilidad=((($this->servicioComprobante->asignado) + ($this->servicioComprobante->aumento)) - (($this->servicioComprobante->disminucion) + ($this->servicioComprobante->comprometido) + ($this->servicioComprobante->precomprometido)));
				if(round($data2->fields['monto'],2) >= round($disponibilidad,2))
				{
					$disponible=false;
				}
			}				
		} 	
		unset($this->servicioComprobante);
		return $disponible;
	} // end function uf_show_error_disponible
}
?>