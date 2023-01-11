<?php
/***********************************************************************************
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

class MovimientoActivos extends DaoGenerico
{
	var $_table = 'siv_movimiento';
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

	public function __construct() {
		parent::__construct ( 'saf_movimiento' );
		$this->conexionbd = $this->obtenerConexionBd(); 
		$this->objlibcon = new ConexionBaseDatos();
	}


	
/***********************************************************************************
* @Función para insertar los movimientos iniciales.
* @parametros:
* @retorno:
* @fecha de creación: 15/12/2008.
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	public function insertarMovimientoInicial()
	{
		$this->conexionbdorigen = $this->objlibcon->conectarBD($_SESSION['sigesp_servidor_apr'], $_SESSION['sigesp_usuario_apr'], $_SESSION['sigesp_clave_apr'],
									         $_SESSION['sigesp_basedatos_apr'], $_SESSION['sigesp_gestor_apr'], $_SESSION['sigesp_puerto_apr']);
	
		$this->mensaje = 'Inserto el movimiento inicial de activos fijos';
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		escribirArchivo($this->archivo,'                            MOVIMIENTO INICIAL DE ACTIVOS FIJOS');
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		$this->conexionbd->StartTrans();
		try
		{	
			$consulta = "SELECT saf_dt_movimiento.codemp,tipcmp,SUM(monact) AS monact,saf_movimiento.coduniadm".
						"  FROM saf_dt_movimiento,saf_movimiento".
						" WHERE saf_dt_movimiento.codemp=saf_movimiento.codemp".
						"   AND saf_dt_movimiento.cmpmov=saf_movimiento.cmpmov".
						"   AND saf_dt_movimiento.codcau=saf_movimiento.codcau".
						"   AND saf_dt_movimiento.estcat=saf_movimiento.estcat".
						"   AND saf_dt_movimiento.feccmp=saf_movimiento.feccmp".
						" GROUP BY saf_dt_movimiento.codemp,tipcmp,saf_movimiento.coduniadm ";
			$result = $this->conexionbdorigen->Execute($consulta);
			if ($result===false)
			{
				$this->mensaje = 'Error al Seleccionar los Movimientos de Resumen';
				escribirArchivo($this->archivo,'* Error al Seleccionar los Movimientos de Resumen. '.$this->conexionbdorigen->ErrorMsg());
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				$this->valido = false;
			}
			else
			{
				if (!$result->EOF)
				{
				   	$periodo = (date("Y")-1);
					$feccmp=$periodo."-12-31";
					$i=0;
					$consulta = "SELECT codact".
								"  FROM saf_activo".
								" WHERE codact='---------------' ";
					$resultact = $this->conexionbd->Execute($consulta);
					if ($resultact->EOF)
					{
						$consulta = "INSERT INTO saf_activo (codemp,codact,denact) VALUES ('0001','---------------','Activo por Defecto');";
						$resultmov = $this->conexionbd->Execute($consulta);
							escribirArchivo($this->archivo,'--> '.$consulta);
						if ($resultmov===false)
						{
							$this->mensaje = 'Error al Insertar Activo por Defecto.';
							escribirArchivo($this->archivo,'* Error al Insertar Activo por Defecto. '.$this->conexionbd->ErrorMsg());
							escribirArchivo($this->archivo,'*******************************************************************************************************');
							$this->valido = false;
						}
					}

					$consulta = "SELECT codact,ideact".
								"  FROM saf_dta".
								" WHERE codact='---------------'".
								"   AND ideact='---------------' ";
					$resultdt = $this->conexionbd->Execute($consulta);
					if ($resultdt->EOF)
					{
						$consulta = "INSERT INTO saf_dta (codemp,codact,ideact,seract,idchapa,estact,estcon) VALUES ('0001','---------------','---------------','0000000000000000000000000','---------------','A',0);";
						$resultmov = $this->conexionbd->Execute($consulta);
						if ($resultmov===false)
						{
							$this->mensaje = 'Error al Insertar dt Activo por Defecto.';
							escribirArchivo($this->archivo,'* Error al Insertar dt Activo por Defecto. '.$this->conexionbd->ErrorMsg());
							escribirArchivo($this->archivo,'*******************************************************************************************************');
							$this->valido = false;
						}
					}


				}
				while (!$result->EOF)
				{
					$i++;
					$comprobante=str_pad($i,15,'0',STR_PAD_LEFT);
					$codemp = validarTexto($result->fields['codemp'],0,4,'');
					$tipcmp = $result->fields['tipcmp'];
					$monact = $result->fields['monact'];
					$coduniadm = $result->fields['coduniadm'];
					if($tipcmp=="INC")
						$codcau="018";
					else
						$codcau="059";
					$consulta = "INSERT INTO saf_movimiento (codemp,cmpmov,codcau,estcat,feccmp,estpromov,numcmp,tipcmp,coduniadm) ".
					 			"     VALUES ('".$codemp."','".$comprobante."','".$codcau."',2,'".$feccmp."',0,'-','".$tipcmp."','".$coduniadm."')";
					$resultmov = $this->conexionbd->Execute($consulta);
					if ($resultmov===false)
					{
						$this->mensaje = 'Error al Insertar el Movimiento Inicial.';
						escribirArchivo($this->archivo,'* Error al Insertar el Movimiento Inicial. '.$this->conexionbd->ErrorMsg());
						escribirArchivo($this->archivo,'*******************************************************************************************************');
						$this->valido = false;
					}

					$consulta = " INSERT INTO saf_dt_movimiento (codemp, cmpmov, codcau, estcat, ".
								"		feccmp, codact, ideact, monact, coduniadm) ".
								" VALUES ('".$codemp."','".$comprobante."','".$codcau."',2, ".
								"		'".$feccmp."','---------------','---------------',".$monact.",'".$coduniadm."')";
					$resultdt = $this->conexionbd->Execute($consulta);
					if ($resultdt===false)
					{
						$this->mensaje = 'Error al Insertar los Detalles del movimiento inicial.';
						escribirArchivo($this->archivo,'* Error al Insertar los Detalles del movimiento inicial. '.$this->conexionbd->ErrorMsg());
						escribirArchivo($this->archivo,'*******************************************************************************************************');
						$this->valido = false;
					}
					$result->MoveNext();
				}
			}
			escribirArchivo($this->archivo,'*******************************************************************************************************');
			escribirArchivo($this->archivo,'El Movimiento Inicial de Activos Fijos se Creo con Exito');
			escribirArchivo($this->archivo,'*******************************************************************************************************');
				
		}
		catch (exception $e)
		{
			$this->valido = false;
			$this->mensaje='Ocurrio un error en la Transferencia. '.$this->conexionbd->ErrorMsg();
			escribirArchivo($this->archivo,'* Ocurrio un error en la Transferencia. ');
			escribirArchivo($this->archivo,'* Error  '.$this->conexionbd->ErrorMsg());
			escribirArchivo($this->archivo,'*******************************************************************************************************');
		}
		$this->conexionbd->CompleteTrans($this->valido);
		$this->incluirSeguridad('PROCESAR',$this->valido);
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