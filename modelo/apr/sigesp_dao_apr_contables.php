<?php
/***********************************************************************************
* @Modelo para el actualizar cuentas contables
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
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_sistemaventana.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_conexion.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_funciones.php');

class ActCuentasContables extends DaoGenerico
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
	var $cuenta = array();

	public function __construct() {
		parent::__construct ( 'scg_cuentas' );
		$this->conexionbd = $this->obtenerConexionBd(); 
		$this->objlibcon = new ConexionBaseDatos();
	}

/***********************************************************************************
 * @Función para seleccionar con que conexion a Base de Datos se va a trabajar
 * @parametros:
 * @retorno:
 * @fecha de creación: 06/11/2008.
 * @autor: Ing. Yesenia Moreno de Lang
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/
	public function seleccionarConexion()
	{
		
		if ($this->tipoconexionbd != 'DEFECTO')
		{
			$this->conexionbd = $this->objlibcon->conectarBD($this->servidor, $this->usuario, $this->clave, $this->basedatos, $this->gestor, $this->puerto);
		}
	}
	
	
/***********************************************************************************
 * @Función para cargar las cuentas contables utilizadas
 * @parametros:
 * @retorno:
 * @fecha de creación: 09/12/2008.
 * @autor: Ing. Gusmary Balza.
 ************************************************************************************
 * @fecha modificación:
 * @descripción:
 * @autor:
 ***********************************************************************************/	
	public function cargarCuentas() 
	{
		
		$this->servidor = $_SESSION['sigesp_servidor_apr'];
		$this->usuario 	= $_SESSION['sigesp_usuario_apr'];
		$this->clave 	= $_SESSION['sigesp_clave_apr'];
		$this->basedatos= $_SESSION['sigesp_basedatos_apr'];
		$this->gestor 	= $_SESSION['sigesp_gestor_apr'];
		$this->puerto 	= $_SESSION['sigesp_puerto_apr'];
		$this->tipoconexionbd = 'ALTERNA';
		
		$this->seleccionarConexion();
		
		$consulta = "SELECT c_resultad as sc_cuentaorigen".
					"  FROM sigesp_empresa ".
					" WHERE trim(c_resultad)<>'' ".
					" UNION ".
					"SELECT c_resultan as sc_cuentaorigen".
					"  FROM sigesp_empresa ".
					" WHERE trim(c_resultan)<>'' ".
					" UNION ".
					"SELECT scctaben as sc_cuentaorigen".
					"  FROM sigesp_empresa ".
					" WHERE trim(scctaben)<>'' ".
					" UNION ".
					"SELECT c_financiera as sc_cuentaorigen".
					"  FROM sigesp_empresa ".
					" WHERE trim(c_financiera)<>'' ".
					" UNION ".
					"SELECT c_fiscal as sc_cuentaorigen".
					"  FROM sigesp_empresa ".
					" WHERE trim(c_fiscal)<>'' ".
					" UNION ".
					"SELECT sc_cuenta as sc_cuentaorigen".
					"  FROM rpc_proveedor ".
					" WHERE trim(sc_cuenta)<>'' ".
					" UNION ".
					"SELECT sc_cuenta as sc_cuentaorigen".
					"  FROM rpc_beneficiario ".
					" WHERE trim(sc_cuenta)<>'' ".
					" UNION ".
					"SELECT sc_cuenta as sc_cuentaorigen".
					"  FROM saf_activo ".
					" WHERE trim(sc_cuenta)<>'' ".
					" UNION ".
					"SELECT sc_cuenta as sc_cuentaorigen".
					"  FROM sigesp_deducciones ".
					" WHERE trim(sc_cuenta)<>'' ".
					" UNION ".
					"SELECT sc_cuenta as sc_cuentaorigen".
					"  FROM siv_articulo ".
					" WHERE trim(sc_cuenta)<>'' ".
					" UNION ".
					"SELECT sc_cuenta as sc_cuentaorigen".
					"  FROM scb_ctabanco ".
					" WHERE trim(sc_cuenta)<>'' ".
					" UNION ".
					"SELECT sc_cuenta as sc_cuentaorigen".
					"  FROM scb_colocacion ".
					" WHERE trim(sc_cuenta)<>'' ".
					" UNION ".
					"SELECT sc_cuenta as sc_cuentaorigen".
					"  FROM sno_beneficiario ".
					" WHERE trim(sc_cuenta)<>'' ".
					" UNION ".
					"SELECT cueconnom as sc_cuentaorigen".
					"  FROM sno_nomina ".
					" WHERE trim(cueconnom)<>'' ".
					" UNION ".
					"SELECT cueaboper as sc_cuentaorigen".
					"  FROM sno_personalnomina ".
					" WHERE trim(cueaboper)<>'' ".
					" UNION ".
					"SELECT cueconcon as sc_cuentaorigen".
					"  FROM sno_concepto ".
					" WHERE trim(cueconcon)<>'' ".
					" UNION ".
					"SELECT cueconpatcon as sc_cuentaorigen".
					"  FROM sno_concepto ".
					" WHERE trim(cueconpatcon)<>'' ".
					" UNION ".
					"SELECT cueconnom as sc_cuentaorigen".
					"  FROM sno_hnomina ".
					" WHERE trim(cueconnom)<>'' ".
					" UNION ".
					"SELECT cueaboper as sc_cuentaorigen".
					"  FROM sno_hpersonalnomina ".
					" WHERE trim(cueaboper)<>'' ".
					" UNION ".
					"SELECT cueconcon as sc_cuentaorigen".
					"  FROM sno_hconcepto ".
				    " WHERE trim(cueconcon)<>'' ".
					" UNION ".
					"SELECT cueconpatcon as sc_cuentaorigen".
					"  FROM sno_hconcepto ".
					" WHERE trim(cueconpatcon)<>'' ".		
					" GROUP BY sc_cuentaorigen ".
					" ORDER BY sc_cuentaorigen ";	
		$result = $this->conexionbd->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$cadena = 'Error al Seleccionar las Cuentas Contables.'.''.$this->conexionbd->ErrorMsg();
		}
		else
		{			
			$arreglo = array ();
			$j=0;
			while (!$result->EOF)
			{				
				$this->sccuentaorigen  = validarTexto($result->fields['sc_cuentaorigen'],0,25,'');
				$this->sccuentadestino = '';
				$arreglo[$j]['origen'] = $result->fields['sc_cuentaorigen'];
				$arreglo[$j]['destino']='';
				$resultDestino = $this->cargarCuentaDestino(); 								
				if (TRIM($arreglo[$j]['origen'])==TRIM($resultDestino->fields['scg_cuentaorigen']))
				{
					$arreglo[$j]['destino']=$resultDestino->fields['scg_cuentadestino'];					
				}
				$j++;	
				$result->MoveNext();
			}
		}
		return $arreglo;
	}
	
	
/***********************************************************************************
* @Función para cargar las cuentas actuales.(nuevas)
* @parametros:
* @retorno:
* @fecha de creación: 10/12/2008.
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function cargarCuentaDestino() 
	{
		$this->servidor = $_SESSION['sigesp_servidor'];
		$this->usuario 	= $_SESSION['sigesp_usuario'];
		$this->clave 	= $_SESSION['sigesp_clave'];
		$this->basedatos= $_SESSION['sigesp_basedatos'];
		$this->gestor 	= $_SESSION['sigesp_gestor'];
		$this->puerto 	= $_SESSION['sigesp_puerto'];
		$this->tipoconexionbd = 'ALTERNA';
				
		$this->seleccionarConexion();
		
		$consulta = "SELECT scg_cuentaorigen,scg_cuentadestino ".
					"  FROM apr_contable ".
					" WHERE scg_cuentaorigen='{$this->sccuentaorigen}' ";
		
		$result = $this->conexionbd->Execute($consulta);
		if ($result===false)
		{
			$this->valido = false;
			$cadena = 'Error en la base de datos destino'.''.$this->conexionbd->ErrorMsg();
			$this->mensaje = '';
		}
		else
		{			
			return $result;		
		}
	}
	
/***********************************************************************************
* @Función para insertar las cuenta anterior y actual.
* @parametros:
* @retorno:
* @fecha de creación: 10/12/2008.
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	public function incluirCuentas()
	{

		$total = count((array)$this->cuenta);
		for ($i=0; ($i <= $total) && ($this->valido); $i++)
		{
			$sccuentaant = trim($this->cuenta[$i]->sccuentaant);
			$sccuentaact = trim($this->cuenta[$i]->sccuentaact);			
			if($sccuentaact!='')
			{
				$this->consulta = " INSERT INTO apr_contable (scg_cuentaorigen, scg_cuentadestino)
							  		VALUES ('".$sccuentaant."','".$sccuentaact."') ";
				$result = $this->conexionbd->Execute($this->consulta);
				if ($result===false)
				{
					$this->valido = false;
					$cadena = 'Error en la base de datos destino.'.''.$this->conexionbd->ErrorMsg();
					$this->mensaje = 'Error en la base de datos destino.'.''.$this->conexionbd->ErrorMsg();
				}
				else
				{
					$this->mensaje = 'Asocio la cuenta Contable Origen '.$sccuentaact.' con la Cuenta Contable Destino '.$sccuentaact;
					$this->incluirSeguridad('INSERTAR',$this->valido);
				}
			}
		}
		return $this->valido;		
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