<?php
/***********************************************************************************
* @Modelo para el movimiento inicial de existencias de inventario.
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

class MovimientoInventario extends DaoGenerico
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
		parent::__construct ( 'siv_movimiento' );
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
	
		$this->mensaje = 'Inserto el movimiento inicial de existencias de inventario';
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		escribirArchivo($this->archivo,'                            MOVIMIENTO INICIAL DE EXISTENCIAS DE INVENTARIO');
		escribirArchivo($this->archivo,'*******************************************************************************************************');
		$this->conexionbd->StartTrans();
		try
		{	
			$consulta = "SELECT codemp, codart, codalm, SUM(existencia) AS existencia, ".
					   	"       (SELECT ultcosart ".
						"		   FROM siv_articulo ".
                        "         WHERE siv_articuloalmacen.codemp=siv_articulo.codemp ".
					   	"           AND siv_articuloalmacen.codart=siv_articulo.codart) AS ultcosart ".
                        "  FROM siv_articuloalmacen ".
					   	" WHERE existencia > 0 ".
                        " GROUP BY codemp,codart,codalm ";
			$result = $this->conexionbdorigen->Execute($consulta);
			if ($result===false)
			{
				$this->mensaje = 'Error al Seleccionar los Artículos por Almacen.';
				escribirArchivo($this->archivo,'* Error al Seleccionar los Artículos por Almacen. '.$this->conexionbdorigen->ErrorMsg());
				escribirArchivo($this->archivo,'*******************************************************************************************************');
				$this->valido = false;
			}
			else
			{
				if (!$result->EOF)
				{
					$consulta="SELECT nummov".
							  "  FROM siv_movimiento".
							  " ORDER BY nummov DESC";
					$resultcomp = $this->conexionbd->Execute($consulta);
					if (!$resultcomp->EOF)
					{
						$comprobante = $resultcomp->fields['nummov'];
						settype($comprobante,'int');
						$comprobante=$comprobante + 1;
						$comprobante=str_pad($comprobante, 15, "0", STR_PAD_LEFT);
					}
					else
					{
						$comprobante = '000000000000001';
					}
					
					$this->periodo = $_SESSION['la_empresa']['periodo'];
					$solicitante = 'Apertura';
	
					$consulta = "INSERT INTO siv_movimiento (nummov,fecmov,nomsol,codusu) ".
					 			"     VALUES ('".$comprobante."','".$this->periodo."','".$solicitante."','".$this->codusu."')";
					$resultmov = $this->conexionbd->Execute($consulta);
					if ($resultmov===false)
					{
						$this->mensaje = 'Error al Insertar el Movimiento Inicial.';
						escribirArchivo($this->archivo,'* Error al Insertar el Movimiento Inicial. '.$this->conexionbd->ErrorMsg());
						escribirArchivo($this->archivo,'*******************************************************************************************************');
						$this->valido = false;
					}
				}
				while (!$result->EOF)
				{
					$codemp = validarTexto($result->fields['codemp'],0,4,'');
					$nummov = $comprobante;
					$fecmov = $this->periodo;
					$codart = validarTexto($result->fields['codart'],0,20,'');
					$codalm = validarTexto($result->fields['codalm'],0,10,'');
					$opeinv = 'ENT';
					$codprodoc = 'APR';
					$numdoc = $comprobante;
					$canart = $result->fields['existencia'];  
					$cosart = $result->fields['ultcosart'];
					$promov = 'APE';
					$numdocori = $comprobante;
					$candesart = $result->fields['existencia'];
					$fecdesart = $this->periodo;
					$cosart = $result->fields['ultcosart'];
					if ($canart>0)
					{
						$consulta = " INSERT INTO siv_dt_movimiento (codemp, nummov, fecmov, codart, ".
									"		codalm, opeinv, codprodoc, numdoc, canart, cosart, ".
									"		promov, numdocori, candesart, fecdesart) ".
									" VALUES ('".$codemp."','".$nummov."','".$fecmov."','".$codart."', ".
									"		'".$codalm."','".$opeinv."','".$codprodoc."','".$numdoc."', ".
									"		".$canart.",".$cosart.",'".$promov."','".$numdocori."', ".
									"		".$candesart.",'".$fecdesart."')";
						$resultdt = $this->conexionbd->Execute($consulta);
						if ($resultdt===false)
						{
							$this->mensaje = 'Error al Insertar los Detalles del movimiento inicial.';
							escribirArchivo($this->archivo,'* Error al Insertar los Detalles del movimiento inicial. '.$this->conexionbd->ErrorMsg());
							escribirArchivo($this->archivo,'*******************************************************************************************************');
							$this->valido = false;
						}
						else
						{
							$consulta="SELECT existencia".
									  "  FROM siv_articuloalmacen".
									  " WHERE codemp='".$codemp."'".
									  "   AND codart='".$codart."'".
									  "   AND codalm='".$codalm."'";
							$resultaxa = $this->conexionbd->Execute($consulta);
							if (!$resultaxa->EOF)
							{
								$existencia = $resultcomp->fields['existencia'];
								settype($existencia,'int');
								$existencia=$existencia + $canart;
								$consulta = " UPDATE siv_articuloalmacen SET existencia=".$existencia."".
										    " WHERE codemp='".$codemp."'".
										    "   AND codart='".$codart."'".
										    "   AND codalm='".$codalm."'";
								$resultdt = $this->conexionbd->Execute($consulta);	
								if ($resultdt===false)
								{
									$this->mensaje = 'Error al Insertar los Artículos por almacén.';
									escribirArchivo($this->archivo,'* Error al Insertar los Artículos por almacén. '.$this->conexionbd->ErrorMsg());
									escribirArchivo($this->archivo,'*******************************************************************************************************');
									$this->valido = false;
								}
							}
							else
							{
								$consulta = " INSERT INTO siv_articuloalmacen (codemp, codart, codalm, existencia) ".
											" VALUES ('".$codemp."','".$codart."','".$codalm."',".$canart.") ";
								$resultdt = $this->conexionbd->Execute($consulta);	
								if ($resultdt===false)
								{
									$this->mensaje = 'Error al Insertar los Artículos por almacén.';
									escribirArchivo($this->archivo,'* Error al Insertar los Artículos por almacén. '.$this->conexionbd->ErrorMsg());
									escribirArchivo($this->archivo,'*******************************************************************************************************');
									$this->valido = false;
								}
							}
						}
					}
					$result->MoveNext();
				}
			}
			escribirArchivo($this->archivo,'*******************************************************************************************************');
			escribirArchivo($this->archivo,'El Movimiento Inicial de Inventario se Creo con Exito');
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