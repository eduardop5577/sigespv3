<?php
/***********************************************************************************
* @Modelo para la apertura del ejercicio contable.
* @fecha de modificacion: 01/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
 ***********************************************************************************/
 
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_registroeventos.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_registrofallas.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_conexion.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/shared/class_folder/class_sigesp_int.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/shared/class_folder/class_sigesp_int_int.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/shared/class_folder/class_sigesp_int_spg.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/shared/class_folder/class_sigesp_int_scg.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/shared/class_folder/class_sigesp_int_spi.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/shared/class_folder/class_fecha.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/shared/class_folder/class_mensajes.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/shared/class_folder/sigesp_include.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/shared/class_folder/class_funciones.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/shared/class_folder/sigesp_c_seguridad.php');

class AperturaEjercicio extends DaoGenerico
{
	var $_table = 'scg_cuentas';
	public $mensaje;
	public $valido = true;
	public $existe;
	public $criterio;
		
	public $servidor;
	public $usuario;
	public $clave;
	public $basedatos;
	public $gestor;
	public $puerto;
	public $tipoconexionbd = 'DEFECTO';	
	public $archivo;
	public $resultapertura;

	public function __construct() {
		parent::__construct ( 'scg_cuentas' );
		$this->conexionbd = $this->obtenerConexionBd(); 
		$this->objlibcon = new ConexionBaseDatos();
	}
	

/***********************************************************************************
* @Función para actualizar las cuentas contables.
* @parametros:
* @retorno:
* @fecha de creación: 15/12/2008.
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	public function procesarAperturaEjercicio()
	{
		$conexionbdorigen = $this->objlibcon->conectarBD($_SESSION['sigesp_servidor_apr'], $_SESSION['sigesp_usuario_apr'], $_SESSION['sigesp_clave_apr'],
									   $_SESSION['sigesp_basedatos_apr'], $_SESSION['sigesp_gestor_apr'], $_SESSION['sigesp_puerto_apr']);
		
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		escribirArchivo($this->archivo,'                            APERTURA DEL EJERCICIO CONTABLE');
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		$this->mensaje = 'Proceso la apertura del ejercicio contable';
		$anno     = (substr($this->periodo,0,4)-1);
		$fecdesde = $anno.'-01-01';
		$fechasta = $anno.'-12-31';
		$this->conexionbd->StartTrans();
		$this->valido = true;				
		try
		{						
			$this->ls_activo=trim($_SESSION["la_empresa"]["activo"]);
			$this->ls_activo_h=trim($_SESSION["la_empresa"]["activo_h"]);
			$this->ls_pasivo=trim($_SESSION["la_empresa"]["pasivo"]);
			$this->ls_pasivo_h=trim($_SESSION["la_empresa"]["pasivo_h"]);
			$this->ls_resultado=trim($_SESSION["la_empresa"]["resultado"]);
			$this->ls_resultado_h=trim($_SESSION["la_empresa"]["resultado_h"]);
			$this->ls_capital=trim($_SESSION["la_empresa"]["capital"]);
			$this->ls_orden_d=trim($_SESSION["la_empresa"]["orden_d"]);
			$this->ls_orden_h=trim($_SESSION["la_empresa"]["orden_h"]);
                        
			$this->ls_ingreso_f=trim($_SESSION["la_empresa"]["ingreso_f"]);
			$this->ls_gastos_f =trim($_SESSION["la_empresa"]["gasto_f"]);
			$this->ls_scforden_d=trim($_SESSION["la_empresa"]["scforden_d"]);
			$this->ls_scforden_h=trim($_SESSION["la_empresa"]["scforden_h"]);
			$this->ls_activo_t=trim($_SESSION["la_empresa"]["activo_t"]);
			$this->ls_pasivo_t=trim($_SESSION["la_empresa"]["pasivo_t"]);
			$this->ls_resultado_t=trim($_SESSION["la_empresa"]["resultado_t"]);
			$this->ls_ingreso=trim($_SESSION["la_empresa"]["ingreso"]);
			$this->ls_gastos =trim($_SESSION["la_empresa"]["gasto"]);
			$this->ls_resultado_actual = trim($_SESSION["la_empresa"]["c_resultad"]);			
			$this->ls_resultado_anterior = trim($_SESSION["la_empresa"]["c_resultan"]);	
					
			$consulta="SELECT scg_cuentas.sc_cuenta, coalesce(saldo.T_Debe,0) as total_debe, ".
					  "       coalesce(saldo.T_Haber,0) as total_haber  ".
					  "  FROM scg_cuentas ".
					  "  LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
					  "                          coalesce(sum(haber_mes),0) as T_Haber ".
					  "                     FROM scg_saldos ".
					  "                    WHERE codemp='".$this->codemp."' ".
					  "                      AND fecsal<='".$fechasta."' ".
					  "                    GROUP BY codemp,sc_cuenta) saldo ".
					  "    ON scg_cuentas.codemp=saldo.codemp ".
			          "   AND scg_cuentas.sc_cuenta=saldo.sc_cuenta ".
					  " WHERE scg_cuentas.codemp='".$this->codemp."' ".
					  "   AND (scg_cuentas.sc_cuenta like '".$this->ls_activo."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_pasivo."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_resultado."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_activo_h."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_pasivo_h."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_resultado_h."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_capital."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_ingreso_f."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_gastos_f."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_scforden_d."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_scforden_h."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_activo_t."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_pasivo_t."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_resultado_t."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_orden_d."%' ".
					  "    OR scg_cuentas.sc_cuenta like '".$this->ls_orden_h."%') ".
					  "   AND scg_cuentas.status='C'".
					  " ORDER BY trim(scg_cuentas.sc_cuenta) ";  
			escribirArchivo($this->archivo,$consulta);
			$resultSCG = $conexionbdorigen->Execute($consulta);
			if ($resultSCG===false)
			{
				$this->mensaje = 'Error al Seleccionar los saldos del origen.';
				escribirArchivo($this->archivo,'* Error al Seleccionar los saldos del origen '.$conexionbdorigen->ErrorMsg());
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				$this->valido = false; 
			}
			else
			{	
				$this->monto_saldo_actual = 0;
				$anno=$anno-1;
				$this->monto_saldo_actual=$this->obtenerSaldo($conexionbdorigen,$this->ls_resultado_actual,$anno.'-12-31',$fechasta,$this->monto_saldo_actual);
				$this->arrSaldosContables = array('sccuenta'=>array(),'saldoant'=>array(),'debe'=>array(),'haber'=>array(),'saldoact'=>array());
				$i = 0;			
				$lb_saldoant=false;
				while (!$resultSCG->EOF) 
				{
					if (trim($resultSCG->fields['sc_cuenta']) != trim($this->ls_resultado_actual))
					{
						$this->arrSaldosContables['sccuenta'][$i] 	= $resultSCG->fields['sc_cuenta'];
						$this->arrSaldosContables['debe'][$i] 		= number_format($resultSCG->fields['total_debe'],2,".","");
						$this->arrSaldosContables['haber'][$i] 		= number_format($resultSCG->fields['total_haber'],2,".","");
						$this->arrSaldosContables['saldoant'][$i] 	= 0;
						if (trim($resultSCG->fields['sc_cuenta']) == trim($this->ls_resultado_anterior))
						{
							$this->arrSaldosContables['saldoant'][$i] 	= number_format($this->monto_saldo_actual,2,".","");
							$lb_saldoant=true;
						}
						$i++;
					}
					$resultSCG->MoveNext();
				}				
				if(!$lb_saldoant)				
				{
						$this->arrSaldosContables['sccuenta'][$i] 	= $this->ls_resultado_anterior;
						$this->arrSaldosContables['denominacion'][$i] = '';	
						$this->arrSaldosContables['debe'][$i] 		= 0;
						$this->arrSaldosContables['haber'][$i] 		= 0;
						$this->arrSaldosContables['saldoant'][$i] 	= number_format($this->monto_saldo_actual,2,".","");
				}
			}
			if ($this->valido)
			{				
				$anno     = (substr($this->periodo,0,4)-1);
				$fecdesde = $anno."-12-31";
				$autoconta = true;
				if ($this->tipo=='B')
				{
					$fuente = $this->ced_ben;
				}
				if ($this->tipo=='P')
				{
					$fuente = $this->cod_prov;
				}
				if ($this->tipo=='-')
				{
					$fuente = '----------';
				}
				$codban = '---';
				$ctaban = '-------------------------';
				
				$this->objInt = new class_sigesp_int_int();
				$this->objInt->es_apertura=true;
				if ($this->valido)
				{
					//Insertar los Saldos Contables Iniciales
					$this->valido = $this->objInt->uf_int_init($this->codemp,$this->procede,$this->comprobante,$fecdesde,$this->descripcion,$this->tipo,$fuente,$autoconta,$codban,$ctaban,$this->tipo_cmp); 
					escribirArchivo($this->archivo,'INSERT INTO sigesp_cmp (codemp ,procede ,comprobante,fecha,codban,ctaban,descripcion,tipo_comp,tipo_destino,cod_pro,ced_bene,total) VALUES ("'.$this->codemp.'","'.$this->procede.'","'.$this->comprobante.'","'.$fecdesde.'","---","-------------------------","APERTURA DE SALDOS","2","-","----------","----------",0);');
				}																
				if ($this->valido)
				{					
					$total = count((array)$this->arrSaldosContables['sccuenta']);					
					$j=0;
					while ($j<$total && $this->valido)
					{							
						$sccuenta 	  = $this->arrSaldosContables['sccuenta'][$j];
						$saldoant     = number_format($this->arrSaldosContables['saldoant'][$j],2,".","");
						$debe		  = number_format($this->arrSaldosContables['debe'][$j],2,".","");
						$haber		  = number_format($this->arrSaldosContables['haber'][$j],2,".","");
						$saldoact	  = ($saldoant+$debe-$haber);
						$saldoact	  = number_format($saldoact,2,".","");
						if ($saldoact!=0)
						{
							$monto = abs($saldoact);		
							if ($saldoact>0)
							{
								$operacion = 'D';
							}
							if ($saldoact<0)
							{
								$operacion = 'H';
							}
							escribirArchivo($this->archivo,'INSERT INTO scg_dt_cmp (codemp ,procede ,comprobante,fecha,codban,ctaban,sc_cuenta,procede_doc,documento,debhab,descripcion,monto,orden) VALUES ("'.$this->codemp.'","'.$this->procede.'","'.$this->comprobante.'","'.$fecdesde.'","---","-------------------------","'.$sccuenta.'","'.$this->procede.'","'.$this->comprobante.'","'.$operacion.'","APERTURA DE SALDOS",'.$monto.',0);');
							$this->valido = $this->objInt->uf_scg_insert_datastore($this->codemp,$sccuenta,$operacion,$monto,
																				   $this->comprobante,$this->procede,
																				   $this->descripcion);													 																	 
						}					
						$j++;						
					}				
				}				
				if ($this->valido)
				{
					$this->valido = $this->objInt->uf_init_end_transaccion_integracion('');
				}				
				$this->objInt->uf_sql_transaction($this->valido);				
			}
			if ($this->valido)
			{
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				escribirArchivo($this->archivo,'*			La Apertura de Contabilidad se Creo con Exito');
				escribirArchivo($this->archivo,'*******************************************************************************************************');		
			}
			else
			{
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				escribirArchivo($this->archivo,'*		'.$this->objInt->is_msg_error);
				escribirArchivo($this->archivo,'*******************************************************************************************************');		
			
			}
		}
		catch (exception $e) 
		{
			$this->valido = false;
			$this->mensaje='Ocurrio un error en la Transferencia. '.$this->conexionbd->ErrorMsg();
			escribirArchivo($this->archivo,'* Ocurrio un error en la Transferencia. ');
			escribirArchivo($this->archivo,'* Error  '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'*		'.$this->objInt->is_msg_error);
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('PROCESAR',$this->valido);
					
	}
		
/***********************************************************************************
* @Función para obtener el periodo de la empresa.
* @parametros:
* @retorno:
* @fecha de creación: 27/05/2009.
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	public function obtenerSaldo($conexionbdorigen,$sc_cuenta,$fecdesde,$fechasta,$monto)
	{
		$monto = 0;
		$consulta="SELECT SUM(monto) As debe, 0 as haber ".
			      "  FROM scg_dt_cmp ".
			      " WHERE codemp='".$this->codemp."' ".
				  "   AND sc_cuenta='".$sc_cuenta."' ".
				  "   AND fecha >='".$fecdesde."' ".
				  "   AND fecha <='".$fechasta."' ".
				  "   AND debhab='D' ".
				  " UNION  ".
				  "SELECT 0 As debe, SUM(monto) as haber ".
			      "  FROM scg_dt_cmp".
			      " WHERE codemp='".$this->codemp."' ".
				  "   AND sc_cuenta='".$sc_cuenta."' ".
				  "   AND fecha >='".$fecdesde."' ".
				  "   AND fecha <='".$fechasta."' ".
				  "   AND debhab='H'";
		$result = $conexionbdorigen->Execute($consulta);
		if ($result===false)
		{
			$this->mensaje = 'Error al Obtener el saldo.';
			escribirArchivo($this->archivo,'* Error al Obtener el saldo. '.$conexionbdorigen->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
			$this->valido = false;
		}
		else
		{
			while (!$result->EOF)
			{
				$debe  = number_format($result->fields['debe'],2,".","");
				$haber = number_format($result->fields['haber'],2,".","");
				$monto = $monto + ($debe-$haber);
				$result->MoveNext();
			}
		}
		$monto= number_format($monto,2,".","");
		return $monto;
	}	
		
		
/***********************************************************************************
* @Función que Incluye el registro de la transacción exitosa
* @parametros: $evento
* @retorno:
* @fecha de creación: 10/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function incluirSeguridad($evento,$tipotransaccion)
	{
		if($tipotransaccion) // Transacción Exitosa
		{
			$objEvento = new RegistroEventos();
		}
		else // Transacción fallida
		{
			$objEvento = new RegistroFallas();
		}
		// Registro del Evento
		$objEvento->codemp = $this->codemp;
		$objEvento->codsis = $this->codsis;
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $evento;
		$objEvento->desevetra = $this->mensaje;
		$objEvento->incluirEvento();
		unset($objEvento);
	}
	
}
?>	