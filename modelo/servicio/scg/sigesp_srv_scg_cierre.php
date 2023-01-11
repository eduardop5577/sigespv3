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
require_once ($dirsrv."/modelo/servicio/scg/sigesp_srv_scg_icierre.php");
require_once ($dirsrv."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_comprobante.php");

class ServicioCierreSCG implements ICierreSCG
{
	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos; 
	private $daoCierre;
	
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->daoCierre = null;
		$this->conexionbd  = ConexionBaseDatos::getInstanciaConexion();
	}
	
	public function verificarEstatusCierreSemestral()
	{
		$arreglo=array();
		$this->valido=true;
		$cadenasql="SELECT estciesem,ciesem1,ciesem2,estciescg  ".
				   "  FROM sigesp_empresa ".
				   " WHERE codemp='".$_SESSION['la_empresa']['codemp']."' ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->verificarEstatusCierreSemestral ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$resultado->EOF)
			{
				$arreglo['estciesem']=$resultado->fields['estciesem'];
				$arreglo['ciesem1']=$resultado->fields['ciesem1'];
				$arreglo['ciesem2']=$resultado->fields['ciesem2'];	
				$arreglo['estciescg']=$resultado->fields['estciescg'];	
			}
			else
			{
				$this->mensaje .= ' ERROR-> Problema al cargar la configuracion del Cierre.';
				$this->valido = false;
			}
		}
		return $arreglo;
	}	
	
	public function verificarCierre()
	{
		$arreglo=array();
		$year=0;  
		$comprobante="";
		$documento="";
		$procede="";
		$fecha="";
		$fecha_cierre="";
		$periodo="";
		$procede = "SCGCIE";
		$arrCierre = $this->verificarEstatusCierreSemestral();
		$ctaresultadod=$_SESSION['la_empresa']["c_resultad"];
		$ctaresultadon=$_SESSION['la_empresa']["c_resultan"];
		if(($ctaresultadod==null)||(trim($ctaresultadod)==""))
		{
			$this->mensaje.="No se definio la cuenta de resultado !!!";
			$this->valido=false;
		}		
		if(($ctaresultadon==null)||(trim($ctaresultadon)==""))
		{
			$this->mensaje.="No se definio la cuenta de resultado anterior !!!";
			$this->valido=false;
		}
		$year=intval(substr($_SESSION['la_empresa']["periodo"],0,4));
		$comprobante="CIERRE-".strval($year);
		$comprobante=str_pad($comprobante,15,"0",0);
		$descripcion="CIERRE DEL EJERCICIO";
		$fecha_cierre = obtenerFechaCierre();
		if($arrCierre['estciesem']==1)
		{
			$descripcion="CIERRE SEMESTRAL";
			if ($arrCierre['ciesem2']==1)
			{
				$comprobante = 'CIERRE3112'.$year;
				$fecha_cierre = '31/12/'.$year;
			}
			else if ($arrCierre['ciesem2']==0)
			{
				$comprobante = 'CIERRE3006'.$year;
				$fecha_cierre = '30/06/'.$year;
			}
		}
		$this->valido=validarFechaPeriodo(convertirFechaBd($fecha_cierre));
		if($this->valido)
		{
			$codemp=$_SESSION['la_empresa']["codemp"];
			$codban="---";
			$ctaban="-------------------------";
			$this->valido=true; 
			$comprobante=fillComprobante($comprobante);
			$serviciocomprobante = new ServicioComprobante();
			$this->valido = $serviciocomprobante->existeComprobante($codemp,$procede,$comprobante,$codban,$ctaban);
			$this->mensaje .= $serviciocomprobante->mensaje;
			unset($serviciocomprobante);
			if($this->valido)
			{
				$arreglo['procede']=$procede;
				$arreglo['comprobante']=$comprobante;
				$arreglo['codban']=$codban;
				$arreglo['ctaban']=$ctaban;
				$arreglo['tipo_destino']="-";
				$arreglo['cod_prov']="----------";
				$arreglo['ced_ben']="----------";
				$arreglo['descripcion']=$descripcion;
				$arreglo['fecha']=$fecha_cierre;
				$arreglo['periodoI']=false;
				$arreglo['periodoII']=false;
				if($arrCierre['estciesem']==1)
				{
					if ($arrCierre['ciesem2']==1)
					{
						$this->mensaje.='El cierre semestral del Periodo II fue ejecutado con Anteriodad';
						$arreglo['periodoII']=true;
						$arreglo['periodoI']=true;
					}
					else if ($arrCierre['ciesem2']==0)
					{
						$this->mensaje.='El cierre semestral del Periodo I fue ejecutado con Anteriodad';
						$arreglo['periodoI']=true;
					}
				}
				else
				{
					$this->mensaje.='El cierre fue ejecutado con Anteriodad';
				}
				$arrDetalle=$this->cargarDetalleComprobante($codemp,$procede,$comprobante,$fecha_cierre);
				$total=count((array)$arrDetalle);
				$montodebe=0;
				$montohaber=0;
				for($i=0;$i<$total;$i++)
				{
					if($arrDetalle[$i]['debhab']=='D')
					{
						$montodebe=number_format($montodebe+$arrDetalle[$i]['montosf'],2,'.','');
					}
					else
					{
						$montohaber=number_format($montohaber+$arrDetalle[$i]['montosf'],2,'.','');
					}
				}
				$arreglo['montodebe']=$montodebe;
				$arreglo['montohaber']=$montohaber;
			}
		}	
		else
		{
			$this->mensaje.=' No puede Ejecutar el Cierre Contable. Debe verificar que el mes este abierto !!!';			
		}
		$arreglo['valido']=$this->valido;
		$arreglo['mensaje']=$this->mensaje;
		return $arreglo;
	}
	
	public function cargarDetalleComprobante($codemp,$procede,$comprobante,$fecha)
	{
		$arreglo=array();
		$monto_debe=0;
		$monto_haber=0;
		$monto=0;
		$fecha=convertirFechaBd($fecha);
		$cadenasql="SELECT sigesp_cmp.procede,sigesp_cmp.comprobante,sigesp_cmp.fecha,scg_dt_cmp.descripcion,  ".
		        "          scg_dt_cmp.sc_cuenta,scg_dt_cmp.debhab,scg_dt_cmp.monto,scg_dt_cmp.documento,scg_cuentas.status ". 
				"   FROM sigesp_cmp ".
				"   INNER JOIN scg_dt_cmp USING(codemp,procede,comprobante,fecha,codban,ctaban), scg_cuentas  ".
				"   WHERE sigesp_cmp.codemp='".$codemp."' ".
				"	  AND sigesp_cmp.procede='".$procede."' ".
				"     AND sigesp_cmp.comprobante='".$comprobante."' ".
				"     AND sigesp_cmp.fecha='".$fecha."' ".
				"     AND scg_dt_cmp.codemp=scg_cuentas.codemp ".
				"     AND scg_dt_cmp.sc_cuenta=scg_cuentas.sc_cuenta ".
				"     AND scg_cuentas.status='C'
				    ORDER BY scg_dt_cmp.sc_cuenta, scg_dt_cmp.debhab ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->cargarComprobante ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			$i=0;
			while(!$resultado->EOF) 
			{   
				$arreglo[$i]['sc_cuenta']=$resultado->fields['sc_cuenta'];
				$arreglo[$i]['descripcion']=$resultado->fields['descripcion'];
				$arreglo[$i]['procede_doc']=$resultado->fields['procede'];
				$arreglo[$i]['documento']=$resultado->fields['documento'];
				$arreglo[$i]['debhab']=$resultado->fields['debhab'];
				$arreglo[$i]['monto']=number_format($resultado->fields['monto'],2,',','.');
				$arreglo[$i]['montosf']=number_format($resultado->fields['monto'],2,'.','');
				$i++;
				$resultado->MoveNext();
			} 
		}	
		return $arreglo;
	}
	
	//Este método verifica si existe o no la cuenta contable y ademas retorna la denominacion 
	//y estatus de la cuenta
	public function existeCuenta($codemp,$sc_cuenta)
	{
		$this->valido=true;
		$arreglo=array();
		$cadenasql="SELECT sc_cuenta, status, denominacion ".
				"   FROM scg_cuentas ".
				"   WHERE codemp='".$codemp."' ".
				"     AND trim(sc_cuenta)='".trim($sc_cuenta)."' ";
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->existeCuenta ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$resultado->EOF)
			{
				 $arreglo['sc_cuenta']=$resultado->fields["sc_cuenta"];
				 $arreglo['denominacion']=$resultado->fields["denominacion"];
				 $arreglo['status']=$resultado->fields['status'];
			}
			else
			{
				$this->mensaje .= " ERROR-> La cuenta Contable ".$sc_cuenta." no existe";
				$this->valido = false;
			}
		}		
		return $arreglo;
	}
	
	//Funcion que retorna el saldo de una cuenta
	function buscarSaldo($cuenta,$fecha)
	{
		$total_debe=0;
		$total_haber=0;
		$saldo=0; 
		$fecha_ini_cierre=$_SESSION['la_empresa']["periodo"];
		$arreglo=$this->verificarEstatusCierreSemestral();
		if($arreglo["ciesem1"]==1)
		{
			$fecha_ini_cierre='2013/07/01';
		}
		$fecha_ini_cierre=convertirFechaBd($fecha_ini_cierre);
		$anno_anterior = intval(substr($fecha_ini_cierre,0,4)-1);
		$fecha_fin_cierre= convertirFechaBd($fecha);
		$anno_actual   = substr($fecha_fin_cierre,0,4);
		$mes_cierre =substr($fecha_fin_cierre,5,2);
		$fecha_ini_cierre  = ultimoDiaMes($mes_cierre,$anno_anterior);
		$fecha_ini_cierre= convertirFechaBd($fecha_ini_cierre);
		// consula sql para movimientos del debe
		$cadenasql="SELECT SUM( monto ) As ntotal ".
			    "   FROM scg_dt_cmp".
			    "   WHERE codemp='".$_SESSION['la_empresa']["codemp"]."' ".
				"     AND sc_cuenta='".$cuenta."' ".
				"     AND fecha >='".$fecha_ini_cierre."' ".
				"     AND fecha <='".$fecha_fin_cierre."' ".
				"     AND debhab='D'";
		$data = $this->conexionbd->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->buscarSaldo ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				if($data->fields["ntotal"]!=0)
				{
					$total_debe=number_format($data->fields["ntotal"],2,'.','');
				}
			}
		}
		unset($data);		
		// consula sql para movimientos del haber
		$cadenasql="SELECT SUM( monto ) As ntotal ".
			    "   FROM scg_dt_cmp ".
			    "   WHERE codemp='".$_SESSION['la_empresa']["codemp"]."' ".
				"     AND sc_cuenta='".$cuenta."' ".
				"     AND fecha >='".$fecha_ini_cierre."' ".
				"     AND fecha <='".$fecha_fin_cierre."' ".
				"     AND debhab='H'";
		$data = $this->conexionbd->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->buscarSaldo ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			if(!$data->EOF)
			{
				if($data->fields["ntotal"]!=0)
				{
					$total_haber=number_format($data->fields["ntotal"],2,'.','');
				}
			}
	    }
		$saldo=number_format($total_debe - $total_haber,2,'.','');
		return $saldo;
	} 
	
	//Funcion que busca las cuentas de gasto o de ingreso dependiendo de el tc
	function buscarCuentasGastoIngresoCosto($codemp,$tc)
	{
		$cadenasql="SELECT sc_cuenta ".
				"   FROM scg_cuentas ".
				"   WHERE codemp='".$codemp."' ".
				"     AND sc_cuenta LIKE '".$tc."' ".
				"     AND status='C'".
				"   ORDER BY sc_cuenta";
		$data = $this->conexionbd->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->buscarCuentasGastoIngresoCosto ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $data;
	} 
	
	public function buscarDetalles($codemp,$procede,$arrcabecera)
	{
		$arrDetalleContables=array();
		$i=1;
		$year=intval(substr($_SESSION['la_empresa']["periodo"],0,4));
		$fecha=$year.'/12/31';
		$descripcion=trim(strval($year));
		$descripcion="CIERRE DEL EJERCICIO  ". $descripcion;
		$arreglo=$this->verificarEstatusCierreSemestral();
		if($arreglo["estciesem"]==1 && $arreglo["ciesem1"]==0)
		{
			$fecha=$year.'/06/30';
		}
		// TRASLADO DE RESULTADOS
		$cuenta=$_SESSION['la_empresa']["c_resultad"];
		$arrCuenta=$this->existeCuenta($codemp,$cuenta);
		if(!$this->valido)
		{
			$this->mensaje.="La cuenta ".$cuenta." no existe.";
			$this->valido = false;
		} 
		if($arrCuenta['status']!="C")
		{
			$this->mensaje.="La cuenta ".$cuenta." no es de movimiento.".$status;
			$this->valido = false;
		}
		if($this->valido)
		{
			$saldo=$this->buscarSaldo($cuenta,$fecha);
			$monto_actual=$saldo;
			if ($saldo<0)
			{
				$monto_actual=number_format($saldo*(-1),2,'.','');
			}
			if($this->valido && $saldo!=0)
			{
				if($saldo>0)
				{
					$operacion="H";
				}   
				else	
				{
					$operacion="D";
				}
				$arrDetalleContables[$i]['codemp']=$arrcabecera['codemp'];
				$arrDetalleContables[$i]['comprobante']=$arrcabecera['comprobante'];
				$arrDetalleContables[$i]['procede']=$arrcabecera['procede'];
				$arrDetalleContables[$i]['codban']=$arrcabecera['codban'];
				$arrDetalleContables[$i]['ctaban']=$arrcabecera['ctaban'];
				$arrDetalleContables[$i]['fecha']=$arrcabecera['fecha'];
				$arrDetalleContables[$i]['descripcion']='TRASLADO DE RESULTADOS';
				$arrDetalleContables[$i]['orden']=$i;
				$arrDetalleContables[$i]['sc_cuenta']=$cuenta;
				$arrDetalleContables[$i]['procede_doc']=$procede;
				$arrDetalleContables[$i]['documento']='000000000000001';
				$arrDetalleContables[$i]['debhab']=$operacion;
				$arrDetalleContables[$i]['monto']=$monto_actual;
				$i++;
			}
			// TRASLADO DE RESULTADOS ANTERIORES
			$cuenta = $_SESSION['la_empresa']["c_resultan"];
			$arrCuenta=$this->existeCuenta($codemp,$cuenta);
			if($this->valido)
			{
				if($saldo!=0)
				{
					if ($saldo>0)
					{
						$operacion = "D";
					}   
					else
					{
						$operacion = "H";
					} 
					$arrDetalleContables[$i]['codemp']=$arrcabecera['codemp'];
					$arrDetalleContables[$i]['comprobante']=$arrcabecera['comprobante'];
					$arrDetalleContables[$i]['procede']=$arrcabecera['procede'];
					$arrDetalleContables[$i]['codban']=$arrcabecera['codban'];
					$arrDetalleContables[$i]['ctaban']=$arrcabecera['ctaban'];
					$arrDetalleContables[$i]['fecha']=$arrcabecera['fecha'];
					$arrDetalleContables[$i]['descripcion']='TRASLADO DE RESULTADOS ANTERIORES';
					$arrDetalleContables[$i]['orden']=$i;
					$arrDetalleContables[$i]['sc_cuenta']=$cuenta;
					$arrDetalleContables[$i]['procede_doc']=$procede;
					$arrDetalleContables[$i]['documento']='000000000000001';
					$arrDetalleContables[$i]['debhab']=$operacion;
					$arrDetalleContables[$i]['monto']=$monto_actual;
					$i++;
				}
			}
			else
			{
				$this->mensaje.="No existe la Cuenta de Resultados Anteriores en el Plan de Cuentas";	
				$this->valido=false; 
			}
		}
		else
		{
			$this->mensaje.="No existe la Cuenta de Resultados en el Plan de Cuentas";
			$this->valido=false;
		}
		// CIERRE DE LAS CUENTAS DE GASTOS
		$tc=trim($_SESSION['la_empresa']["gasto"])."%";
		$cuentaGasto=$this->buscarCuentasGastoIngresoCosto($codemp,$tc);
		$saldo_acumulado=0;
		if($this->valido && !$cuentaGasto->EOF)
		{
			while(!$cuentaGasto->EOF)
			{
				$saldo=0;	
				$cuenta = $cuentaGasto->fields['sc_cuenta'];
				$saldo=$this->buscarSaldo($cuenta,$fecha);
				if($this->valido && $saldo!=0)
				{
					$saldo_acumulado = number_format($saldo_acumulado + $saldo,2,'.','');
					$monto_actual=$saldo;
					if ($saldo<0)
					{
						$monto_actual=number_format($saldo*(-1),2,'.','');
					}
					if ($saldo!=0)
					{ 
						if ($saldo>0)
						{
							$operacion = "H";
						}   
						else	
						{
							$operacion = "D";
						} 	
						$arrDetalleContables[$i]['codemp']=$arrcabecera['codemp'];
						$arrDetalleContables[$i]['comprobante']=$arrcabecera['comprobante'];
						$arrDetalleContables[$i]['procede']=$arrcabecera['procede'];
						$arrDetalleContables[$i]['codban']=$arrcabecera['codban'];
						$arrDetalleContables[$i]['ctaban']=$arrcabecera['ctaban'];
						$arrDetalleContables[$i]['fecha']=$arrcabecera['fecha'];
						$arrDetalleContables[$i]['descripcion']=$descripcion;
						$arrDetalleContables[$i]['orden']=$i;
						$arrDetalleContables[$i]['sc_cuenta']=$cuenta;
						$arrDetalleContables[$i]['procede_doc']=$procede;
						$arrDetalleContables[$i]['documento']='000000000000002';
						$arrDetalleContables[$i]['debhab']=$operacion;
						$arrDetalleContables[$i]['monto']=$monto_actual;
						$i++;
					}
				}
				$cuentaGasto->MoveNext();
			}
		}
		// ASIENTO DE CUADRE DE LOS GASTOS
		$cuenta=$_SESSION['la_empresa']["c_resultad"];
		$arrCuenta=$this->existeCuenta($codemp,$cuenta);
		$saldo=$saldo_acumulado;
		if($this->valido)
		{
			if($saldo!=0)
			{
				$monto_actual=$saldo;
				if ($saldo<0)
				{
					$monto_actual=number_format($saldo*(-1),2,'.','');
				}
				if ($saldo>0)
				{
					$operacion = "D";
				}   
				else
				{
					$operacion = "H";
				}  
				$arrDetalleContables[$i]['codemp']=$arrcabecera['codemp'];
				$arrDetalleContables[$i]['comprobante']=$arrcabecera['comprobante'];
				$arrDetalleContables[$i]['procede']=$arrcabecera['procede'];
				$arrDetalleContables[$i]['codban']=$arrcabecera['codban'];
				$arrDetalleContables[$i]['ctaban']=$arrcabecera['ctaban'];
				$arrDetalleContables[$i]['fecha']=$arrcabecera['fecha'];
				$arrDetalleContables[$i]['descripcion']=$descripcion;
				$arrDetalleContables[$i]['orden']=$i;
				$arrDetalleContables[$i]['sc_cuenta']=$cuenta;
				$arrDetalleContables[$i]['procede_doc']=$procede;
				$arrDetalleContables[$i]['documento']='000000000000002';
				$arrDetalleContables[$i]['debhab']=$operacion;
				$arrDetalleContables[$i]['monto']=$monto_actual;
				$i++;  	
			}
		}
		else
		{
			$this->mensaje.="No existe la Cuenta de Resultados en el Plan de Cuentas";
			$this->valido=false;
		}
		// CIERRE DE LAS CUENTAS DE COSTOS CASO SIGESP
		if ($_SESSION['la_empresa']['estcossig']==1)
		{
			$cuenta="";
			$tc="5%";
			$cuentaCosto=$this->buscarCuentasGastoIngresoCosto($codemp,$tc);
			$saldo_acumulado=0;
			if(!$cuentaCosto->EOF)
			{
				while(!$cuentaCosto->EOF)
				{
					$saldo=0;	
					$cuenta = $cuentaCosto->fields["sc_cuenta"];
					$saldo=$this->buscarSaldo($cuenta,$fecha);
					$saldo_acumulado = ($saldo_acumulado + $saldo);
					$monto_actual = abs($saldo);
					if($saldo!=0 && $this->valido)
					{ 
						if($saldo>0)
						{
							$operacion = "H";
						}   
						else	
						{
							$operacion = "D";
						} 	
						$arrDetalleContables[$i]['codemp']=$arrcabecera['codemp'];
						$arrDetalleContables[$i]['comprobante']=$arrcabecera['comprobante'];
						$arrDetalleContables[$i]['procede']=$arrcabecera['procede'];
						$arrDetalleContables[$i]['codban']=$arrcabecera['codban'];
						$arrDetalleContables[$i]['ctaban']=$arrcabecera['ctaban'];
						$arrDetalleContables[$i]['fecha']=$arrcabecera['fecha'];
						$arrDetalleContables[$i]['descripcion']=$descripcion;
						$arrDetalleContables[$i]['orden']=$i;
						$arrDetalleContables[$i]['sc_cuenta']=$cuenta;
						$arrDetalleContables[$i]['procede_doc']=$procede;
						$arrDetalleContables[$i]['documento']='000000000000004';
						$arrDetalleContables[$i]['debhab']=$operacion;
						$arrDetalleContables[$i]['monto']=$monto_actual;
						$i++;
					}
					$cuentaCosto->MoveNext();
				}
				// ASIENTO DE CUADRE DE LOS COSTOS CASO SIGESP
				$cuenta=$_SESSION['la_empresa']["c_resultad"];
				$arrCuenta=$this->existeCuenta($codemp,$cuenta);
				$saldo=$saldo_acumulado;
				if($this->valido)
				{
					if($saldo!=0)
					{
						$monto_actual = abs($saldo);
						if($saldo>0)
						{
							$operacion = "D";
						}   
						else
						{
							$operacion = "H";
						}    
						$arrDetalleContables[$i]['codemp']=$arrcabecera['codemp'];
						$arrDetalleContables[$i]['comprobante']=$arrcabecera['comprobante'];
						$arrDetalleContables[$i]['procede']=$arrcabecera['procede'];
						$arrDetalleContables[$i]['codban']=$arrcabecera['codban'];
						$arrDetalleContables[$i]['ctaban']=$arrcabecera['ctaban'];
						$arrDetalleContables[$i]['fecha']=$arrcabecera['fecha'];
						$arrDetalleContables[$i]['descripcion']=$descripcion;
						$arrDetalleContables[$i]['orden']=$i;
						$arrDetalleContables[$i]['sc_cuenta']=$cuenta;
						$arrDetalleContables[$i]['procede_doc']=$procede;
						$arrDetalleContables[$i]['documento']='000000000000004';
						$arrDetalleContables[$i]['debhab']=$operacion;
						$arrDetalleContables[$i]['monto']=$monto_actual;
						$i++;	  	
					}
				}
				else
				{
					$this->mensaje.="No existe la Cuenta de Resultados en el Plan de Cuentas";
					$this->valido=false;
				}
			}
		} 
		// CIERRE DE LAS CUENTAS DE INGRESOS
		$tc=trim($_SESSION['la_empresa']["ingreso"])."%";
		$cuentaIngreso=$this->buscarCuentasGastoIngresoCosto($codemp,$tc);
		$saldo_acumulado=0;
		if(!$cuentaIngreso->EOF)
		{
			while(!$cuentaIngreso->EOF) 
			{   
				$cuenta=$cuentaIngreso->fields["sc_cuenta"];
				$saldo=0;	
				$saldo=$this->buscarSaldo($cuenta,$fecha);
				if($this->valido && $saldo!=0)
				{
					$saldo_acumulado =number_format( ($saldo_acumulado + $saldo),2,'.','');
					$monto_actual=$saldo;
					if ($saldo<0)
					{
						$monto_actual=number_format($saldo*(-1),2,'.','');
					}
					if($saldo>0)
					{
						$operacion = "H";
					}   
					else
					{
						$operacion = "D";
					}
					$arrDetalleContables[$i]['codemp']=$arrcabecera['codemp'];
					$arrDetalleContables[$i]['comprobante']=$arrcabecera['comprobante'];
					$arrDetalleContables[$i]['procede']=$arrcabecera['procede'];
					$arrDetalleContables[$i]['codban']=$arrcabecera['codban'];
					$arrDetalleContables[$i]['ctaban']=$arrcabecera['ctaban'];
					$arrDetalleContables[$i]['fecha']=$arrcabecera['fecha'];
					$arrDetalleContables[$i]['descripcion']=$descripcion;
					$arrDetalleContables[$i]['orden']=$i;
					$arrDetalleContables[$i]['sc_cuenta']=$cuenta;
					$arrDetalleContables[$i]['procede_doc']=$procede;
					$arrDetalleContables[$i]['documento']='000000000000003';
					$arrDetalleContables[$i]['debhab']=$operacion;
					$arrDetalleContables[$i]['monto']=$monto_actual;
					$i++;
				}
				$cuentaIngreso->MoveNext();			
			} //fin del while
		}
		// ASIENTO DE CUADRE DE LOS INGRESOS
		$cuenta=$_SESSION['la_empresa']["c_resultad"];
		$arrCuenta=$this->existeCuenta($codemp,$cuenta);
		$saldo=$saldo_acumulado;
		if($this->valido)
		{
			if($saldo!=0)
			{
				$monto_actual=$saldo;
				if ($saldo<0)
				{
					$monto_actual=number_format($saldo*(-1),2,'.','');
				}
				if ($saldo>0)
				{
					$operacion = "D";
				}   
				else	
				{
					$operacion = "H";
				}	
				$arrDetalleContables[$i]['codemp']=$arrcabecera['codemp'];
				$arrDetalleContables[$i]['comprobante']=$arrcabecera['comprobante'];
				$arrDetalleContables[$i]['procede']=$arrcabecera['procede'];
				$arrDetalleContables[$i]['codban']=$arrcabecera['codban'];
				$arrDetalleContables[$i]['ctaban']=$arrcabecera['ctaban'];
				$arrDetalleContables[$i]['fecha']=$arrcabecera['fecha'];
				$arrDetalleContables[$i]['descripcion']=$descripcion;
				$arrDetalleContables[$i]['orden']=$i;
				$arrDetalleContables[$i]['sc_cuenta']=$cuenta;
				$arrDetalleContables[$i]['procede_doc']=$procede;
				$arrDetalleContables[$i]['documento']='000000000000003';
				$arrDetalleContables[$i]['debhab']=$operacion;
				$arrDetalleContables[$i]['monto']=$monto_actual;
				$i++;	
			} 
		}
		else
		{
			$this->mensaje.="No existe la Cuenta de Resultados en el Plan de Cuentas";
			$this->valido=false;
		}
		return $arrDetalleContables;
	}
	
	public function modificarEmpresa($campo,$valor)
	{
		$cadenasql="UPDATE sigesp_empresa SET $campo=$valor ".
			    "   WHERE codemp='".$_SESSION['la_empresa']["codemp"]."' ";
		$data = $this->conexionbd->Execute($cadenasql);
		if($data===false)
		{
			$this->mensaje .= ' CLASE->SCG MÉTODO->buscarSaldo ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $this->valido;
	}

	public function guardarCierreEjercicio($codemp,$objson,$arrevento)
	{
      	$year=0;  
		$comprobante="";
		$documento="";
		$procede="";
		$fecha="";
		$fecha_cierre="";
		$arreglo=array();
		$year=intval(substr($_SESSION['la_empresa']["periodo"],0,4));
		$ctaresultadod=$_SESSION['la_empresa']["c_resultad"];
		$ctaresultadon=$_SESSION['la_empresa']["c_resultan"];
		DaoGenerico::iniciarTrans();
		if(($ctaresultadod==null)||(trim($ctaresultadod)==""))
		{
			$this->mensaje.="No se definio la cuenta de resultado !!!";
			$this->valido=false;
		}		
		if(($ctaresultadon==null)||(trim($ctaresultadon)==""))
		{
			$this->mensaje.="No se definio la cuenta de resultado anterior !!!";
			$this->valido=false;
		}
		if($this->valido)
		{
			$comprobante="CIERRE-".strval($year);
			$comprobante=str_pad($comprobante,15,"0",0);
			$fecha_cierre = obtenerFechaCierre();
			$descripcion="CIERRE DEL EJERCICIO";
			$arreglo=$this->verificarEstatusCierreSemestral();
			if($arreglo['estciesem']==1)
			{
				$descripcion="CIERRE SEMESTRAL";
				if ($arreglo['ciesem1']==0)
				{
					$comprobante = 'CIERRE3006'.$year;
					$fecha_cierre = '30/06/'.$year;
				}
				else if ($arreglo['ciesem1']==1)
				{
					$comprobante = 'CIERRE3112'.$year;
					$fecha_cierre = '31/12/'.$year;
					if($_SESSION['la_empresa']['estciespg']==0)
					{
						$this->mensaje.=' No puede Ejecutar el Cierre Contable. Debe Procesar el Cierre Presupuestario. Contacte al Administrador del Sistema !!!';
						$this->valido=false;
					}
					else if($_SESSION['la_empresa']['estciespi']==0)
					{
						$this->mensaje.=' No puede Ejecutar el Cierre Contable. Debe Procesar el Cierre Presupuestario. Contacte al Administrador del Sistema !!!';
						$this->valido=false;
					}
				}
			}
			else
			{
				if($_SESSION['la_empresa']['estciespg']==0)
				{
					$this->mensaje.=' No puede Ejecutar el Cierre Contable. Debe Procesar el Cierre Presupuestario. Contacte al Administrador del Sistema !!!';
					$this->valido=false;
				}
				else if($_SESSION['la_empresa']['estciespi']==0)
				{
					$this->mensaje.=' No puede Ejecutar el Cierre Contable. Debe Procesar el Cierre Presupuestario. Contacte al Administrador del Sistema !!!';
					$this->valido=false;
				}
			}
		}
		if($this->valido)
		{
			$this->valido=validarFechaPeriodo($fecha_cierre);
			if($this->valido)
			{
				$arrcabecera['codemp'] = $codemp;
				$arrcabecera['procede'] = $objson->procede;
				$arrcabecera['comprobante'] = fillComprobante($comprobante);
				$arrcabecera['codban'] = $objson->codban;
				$arrcabecera['ctaban'] = $objson->ctaban;
				$arrcabecera['fecha'] = convertirFechaBd($fecha_cierre);
				$arrcabecera['descripcion'] = $descripcion;
				$arrcabecera['tipo_comp'] = 1;
				$arrcabecera['tipo_destino'] = $objson->tipo_destino;
				$arrcabecera['cod_pro'] = $objson->cod_pro;
				$arrcabecera['ced_bene'] = $objson->ced_bene;
				$arrcabecera['numpolcon'] = 0;
				$arrcabecera['esttrfcmp'] = 0;
				$arrcabecera['estrenfon'] = 0;
				$arrcabecera['codfuefin'] = '--';
				$arrcabecera['codusu'] = $_SESSION['la_logusr'];
				$arrdetallescg=$this->buscarDetalles($codemp,$objson->procede,$arrcabecera);
				if($this->valido)
				{
					$total=count((array)$arrdetallescg);
					$monto=0;
					for($i=1;$i<=$total;$i++)
					{
						if($arrdetallescg[$i]['debhab']=='D')
						{
							$monto_scg=number_format($arrdetallescg[$i]['monto'],2,'.','');
							$monto=number_format($monto+$monto_scg,2,'.','');
						}
					}
					$arrcabecera['total'] = number_format($monto,2,'.','');
					$serviciocomprobante = new ServicioComprobante();
					$this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,null,$arrdetallescg,null,$arrevento);
					$this->mensaje .= $serviciocomprobante->mensaje;
					unset($serviciocomprobante);
				}
				if($this->valido)
				{
					if($arreglo['estciesem']==1)
					{
						if($arreglo['ciesem1']==0)
						{
							$criterio=" codemp = '".$codemp."' ";
							$this->daoCierre = FabricaDao::CrearDAO('C','sigesp_empresa','',$criterio);
							$this->daoCierre->ciesem1='1';
							$this->valido = $this->daoCierre->modificar();
							if(!$this->valido)
							{
								$this->mensaje .= $this->daoCierre->ErrorMsg;
							}
						}
						else if($arreglo['ciesem1']==1)
						{
							$criterio=" codemp = '".$codemp."' ";
							$this->daoCierre = FabricaDao::CrearDAO('C','sigesp_empresa','',$criterio);
							$this->daoCierre->ciesem2='1';
							$this->daoCierre->estciescg='1';
							$this->valido = $this->daoCierre->modificar();
							if(!$this->valido)
							{
								$this->mensaje .= $this->daoCierre->ErrorMsg;
							}
						}
					}
					else
					{
						$criterio=" codemp = '".$codemp."' ";
						$this->daoCierre = FabricaDao::CrearDAO('C','sigesp_empresa','',$criterio);
						$this->daoCierre->estciescg='1';
						$this->valido = $this->daoCierre->modificar();
						if(!$this->valido)
						{
							$this->mensaje .= $this->daoCierre->ErrorMsg;
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
				if($this->valido)
				{
					if(DaoGenerico::completarTrans())
					{
						$servicioEvento->tipoevento=true;
						$servicioEvento->incluirEvento();
						$this->mensaje.=' -> Registro guardado con &#233;xito'; 		
					}
					else
					{
						$servicioEvento->tipoevento=false;
						$servicioEvento->desevetra=$arrevento['desevetra'];
						$servicioEvento->incluirEvento();
						$this->valido=false;
					}
				}
				//liberando variables y retornando el resultado de la operacion
				unset($this->daoRegistroEvento);
			}
			else
			{
					$this->mensaje.=' No puede Ejecutar el Cierre Contable. Debe verificar que el mes este abierto !!!';			
			}
		}
		return $this->valido;
	}
	
	public function eliminarCierreEjercicio($codemp,$objson,$arrevento)
	{
		$i=0;
		$monto=0;
		$arrcabecera = array();
		$arregloSCG = array();
		$arreglo = array();
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
			$arrcabecera['codban'] = $objson->codban;
			$arrcabecera['ctaban'] = $objson->ctaban;
			$arrcabecera['fecha'] = $fecha;
			$arrcabecera['descripcion'] = $objson->descripcion;
			$arrcabecera['tipo_comp'] = 1;
			$arrcabecera['tipo_destino'] = $objson->tipo_destino;
			$arrcabecera['cod_pro'] = $objson->cod_pro;
			$arrcabecera['ced_bene'] = $objson->ced_bene;
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
			if($this->valido)
			{
				$arreglo=$this->verificarEstatusCierreSemestral();
				if($arreglo['estciesem']==1)
				{
					if($arreglo['ciesem2']==1)
					{
						$criterio=" codemp = '".$codemp."' ";
						$this->daoCierre = FabricaDao::CrearDAO('C','sigesp_empresa','',$criterio);
						$this->daoCierre->ciesem2='0';
						$this->daoCierre->estciescg='0';
						$this->valido = $this->daoCierre->modificar();
						if(!$this->valido)
						{
							$this->mensaje .= $this->daoCierre->ErrorMsg;
						}
					}
					else if($arreglo['ciesem2']==0)
					{
						$criterio=" codemp = '".$codemp."' ";
						$this->daoCierre = FabricaDao::CrearDAO('C','sigesp_empresa','',$criterio);
						$this->daoCierre->ciesem1='0';
						$this->valido = $this->daoCierre->modificar();
						if(!$this->valido)
						{
							$this->mensaje .= $this->daoCierre->ErrorMsg;
						}
					}
				}
				else
				{
					$criterio=" codemp = '".$codemp."' ";
					$this->daoCierre = FabricaDao::CrearDAO('C','sigesp_empresa','',$criterio);
					$this->daoCierre->estciescg='0';
					$this->valido = $this->daoCierre->modificar();
					if(!$this->valido)
					{
						$this->mensaje .= $this->daoCierre->ErrorMsg;
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
			if(DaoGenerico::completarTrans($this->valido))
			{
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
				$this->mensaje.=' -> Registro eliminado exitosamente'; 		
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
}
?>