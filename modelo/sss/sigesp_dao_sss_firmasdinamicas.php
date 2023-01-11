<?php
/***********************************************************************************
* @Clase para Manejar las firmas dinámicas
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
**********************************************************************
* @fecha modificacion  
* @autor  
* @descripcion  
***********************************************************************************/

$dirsrv = "";
$dirsrv = dirname(__FILE__);
$dirsrv = str_replace("\\","/",$dirsrv);
$dirsrv = str_replace("/modelo/sss","",$dirsrv); 
require_once($dirsrv.'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once($dirsrv."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once('sigesp_dao_sss_registroeventos.php');
require_once('sigesp_dao_sss_registrofallas.php');

class FirmasDinamicas extends DaoGenerico
{
	public $valido=true;
	public $existe=true;
	public $codemp;
	public $mensaje;
	public $cadena;
	public $criterio;	
	public $codsis;
	public $nomfisico;
    public $codfir;
	var $firmas = array();
	var $firmaseliminar = array();
	private $conexionbd;

	public function __construct()
        {
		parent::__construct ( 'sss_firmantesdinamicos' );
		$this->conexionbd = $this->obtenerConexionBd(); 
                $this->codemp=$_SESSION["la_empresa"]["codemp"];   
                $this->codfir = '0001';
                //$this->conexionbd->debug = true;
	}
        
	public function buscarCodigoFirmas()
	{
		try 
		{ 
			$consulta="SELECT MAX(codfir)AS codfir ".
                                  "  FROM sss_firmantesdinamicos ".
				  " WHERE codemp = '{$this->codemp}' ";
			$result = $this->conexionbd->Execute($consulta);
			if (!$result->EOF)
			{		
				$this->codfir = intval($result->fields['codfir']) + 1;
                                $this->codfir = str_pad($this->codfir, 4, "0",0);
			}
			$result->Close(); 
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
	}		

	public function buscarControlNumero($codsis)
	{
		try 
		{ 
			$consulta="SELECT prefijo AS codigo, prefijo as nombre ".
                                  "  FROM sigesp_prefijos ".
				  " WHERE codemp = '{$this->codemp}' ".
                                  "   AND estact = 1 ".
                                  "   AND codsis = '{$codsis}' ";
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
                return $result;
	}	
        
	public function buscarUnidadEjecutora()
	{
		try 
		{ 
			$consulta="SELECT coduniadm AS codigo, denuniadm as nombre ".
                                  "  FROM spg_unidadadministrativa ".
				  " WHERE codemp = '{$this->codemp}' ";
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
                return $result;
	}		
        
	public function buscarUsuario()
	{
		try 
		{ 
			$consulta="SELECT codusu AS codigo, nomusu as nombre ".
                                  "  FROM sss_usuarios ".
				  " WHERE codemp = '{$this->codemp}' ";
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
                return $result;
	}		

	public function buscarTipoSep()
	{
		try 
		{ 
			$consulta="SELECT codtipsol AS codigo, dentipsol as nombre ".
                                  "  FROM sep_tiposolicitud ".
				  " WHERE codemp = '{$this->codemp}' ";
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
                return $result;
	}		

        public function verificarCodigo()
	{
		try 
		{ 
			$consulta="SELECT codfir ".
                                  "  FROM {$this->_table} ".
                                  " WHERE codemp = '{$this->codemp}' ".
                                  "   AND codfir = '{$this->codfir}' ";
			$result = $this->conexionbd->Execute($consulta);
			if ($result->EOF)
			{		
				$this->existe = false;		
			}
			$result->Close(); 
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al consultar el Sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
	}

	public function guardarFirmas()
	{
		$this->mensaje='Incluyo la Firma '.$this->codfir;
		$this->conexionbd->StartTrans();
		try 
		{ 
                    if(!$this->verificarCodigo())
                    {
                    
                        $consulta = " INSERT INTO {$this->_table} ".
                                    "	(codemp, codfir, denfir, tiprepfir, tipclafir, nrofir) ".
                                    " 	values ('{$this->codemp}','{$this->codfir}','{$this->denfir}','{$this->tiprepfir}','{$this->tipclafir}',{$this->nrofir})";
			$result = $this->conexionbd->Execute($consulta);
			$total=	count((array)$this->firmas);
			for ($contador=0; $contador < $total; $contador++)
			{	
                                $this->daodtfirmas = FabricaDao::CrearDAO('N','sss_dt_firmantesdinamicos');
                                $this->daodtfirmas->setData($this->firmas[$contador]);
                                $this->daodtfirmas->codemp = $this->codemp;
                                if(!$this->daodtfirmas->incluir(false,'',false,0,true))
                                {
                                    $this->valido = false;
                                    $this->mensaje = "Error al insertar los detalles";
                                    break;                                    
                                }
                                unset($this->daodtfirmas);
			}
                    }
                    else
                    {
                        $this->valido = false;
                        $this->mensaje = "Ya la firma existe";
                    }
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Incluir la firma '.$this->codfir.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans($this->valido);
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}	

	public function actualizarFirmas()
	{
		$this->mensaje='Modifico la firma '.$this->codfir;
		$this->conexionbd->StartTrans();
		try 
		{ 
			$consulta = "UPDATE {$this->_table} ".
                                    "  SET denfir = '{$this->denfir}', ".
                                    "      tiprepfir = '{$this->tiprepfir}', ".
                                    "      tipclafir = '{$this->tipclafir}', ".
                                    "      nrofir = '{$this->nrofir}'".
                                    " WHERE codemp = '{$this->codemp}' ".
                                    "   AND codfir = '{$this->codfir}' ";
			$result = $this->conexionbd->Execute($consulta);
                        
			$consulta = "DELETE ".
                                    "  FROM sss_dt_firmantesdinamicos ".
                                    " WHERE codemp = '{$this->codemp}' ".
                                    "   AND codfir = '{$this->codfir}' ";
			$result = $this->conexionbd->Execute($consulta);

            $total=	count((array)$this->firmas);
			for ($contador=0; $contador < $total; $contador++)
			{	
                                $this->daodtfirmas = FabricaDao::CrearDAO('N','sss_dt_firmantesdinamicos');
                                $this->daodtfirmas->setData($this->firmas[$contador]);
                                $this->daodtfirmas->codemp = $this->codemp;
                                if(!$this->daodtfirmas->incluir(false,'',false,0,true))
                                {
                                    $this->valido = false;
                                    $this->mensaje = "Error al insertar los detalles";
                                    break;                                    
                                }
                                unset($this->daodtfirmas);
			}
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Modificar la firma '.$this->codfir.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);	
	}	

	public function eliminarFirmas()
	{
		$this->mensaje='Elimino la firma '.$this->codfir;
		$this->conexionbd->StartTrans(); 
		try 
		{ 
			$consulta = "DELETE ".
                                    "  FROM sss_dt_firmantesdinamicos ".
                                    " WHERE codemp = '{$this->codemp}' ".
                                    "   AND codfir = '{$this->codfir}' ";
			$result = $this->conexionbd->Execute($consulta);
                        
			$consulta = "DELETE ".
                                    "  FROM sss_firmantesdinamicos ".
                                    " WHERE codemp = '{$this->codemp}' ".
                                    "   AND codfir = '{$this->codfir}' ";
			$result = $this->conexionbd->Execute($consulta);
		} 
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje=' Error al Eliminar la firma '.$this->codfir.' '.$this->conexionbd->ErrorMsg();
	   	} 
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('ELIMINAR',$this->valido);
		
	}

	public function obtenerDetalles()
	{
		try 
		{ 
                    
                    $consulta = "SELECT tipclafir ".
				"  FROM sss_firmantesdinamicos ".
				" WHERE codemp = '{$this->codemp}' ".
                                "   AND codfir = '{$this->codfir}' ";
		    $result = $this->conexionbd->Execute($consulta);
                    if (!$result->EOF)
                    {
                        $tipclafir = $result->fields["tipclafir"];   
                    }
                    $nombre = "";
                    if ($tipclafir == "001")
                    {
                        $nombre = ", (codcla) AS nombre ";
                    }
                    if ($tipclafir == "002")
                    {
                        $nombre = ",(SELECT denuniadm  ".
                                  "  FROM spg_unidadadministrativa ".
				  " WHERE codemp = sss_dt_firmantesdinamicos.codemp ".
                                  "   AND coduniadm = sss_dt_firmantesdinamicos.codcla) AS nombre ";
                    }
                    if ($tipclafir == "003")
                    {
                        $nombre = ",(SELECT nomusu||' '||apeusu ".
                                  "  FROM sss_usuarios ".
				  " WHERE codemp = sss_dt_firmantesdinamicos.codemp ".
                                  "   AND codusu = sss_dt_firmantesdinamicos.codcla) AS nombre ";
                    }
                    if ($tipclafir == "004")
                    {
                        $nombre = ",(SELECT dentipsol".
                                  "  FROM sep_tiposolicitud ".
				  " WHERE codemp = sss_dt_firmantesdinamicos.codemp ".
                                  "   AND codtipsol = sss_dt_firmantesdinamicos.codcla) AS nombre ";
                    }
                    if ($tipclafir == "005")
                    {
                        $nombre = ",(CASE WHEN codcla='001' THEN 'Bienes'".
                                  "  ELSE 'Servicios' ".
				  "  END) AS nombre ";
                    }
                    $consulta = " SELECT codemp, codfir, codcla, tipclafir, fir1, fir2, fir3, fir4, fir5, 1 as valido ".
                                " ".$nombre." ".
                                "  FROM sss_dt_firmantesdinamicos ".
                                " WHERE sss_dt_firmantesdinamicos.codemp = '{$this->codemp}' ".
                                "   AND sss_dt_firmantesdinamicos.codfir = '{$this->codfir}' ";
                    $result = $this->conexionbd->Execute($consulta);
                    return $result;
		}
		catch (exception $e) 
		{ 
                    $this->valido  = false;	
                    $this->mensaje='Error al consultar los detalles de las firmas '.$consulta.' '.$this->conexionbd->ErrorMsg();
                    $this->incluirSeguridad('CONSULTAR',$this->valido);
		}
	}

	public function leer() 
 	{		
		try 
		{ 

                    $consulta = "SELECT codemp, codfir, denfir, tiprepfir, tipclafir, nrofir, 1 as valido ".
				"  FROM {$this->_table} ".
				" WHERE codemp = '{$this->codemp}' ";
			if ($this->criterio!='')
			{
				$consulta .= " AND {$this->criterio} like '{$this->cadena}%'";
		  	}
		  	$consulta.= "ORDER BY codfir";
		  	$result = $this->conexionbd->Execute($consulta);
			return $result; 
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar la firma '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
	   	} 
	}	
	
	function incluirSeguridad($evento,$tipotransaccion)
	{
		if($tipotransaccion)
		{
			$objEvento = new RegistroEventos();
			$tiponotificacion = 'NOTIFICACION';
		}
		else
		{
			$objEvento = new RegistroFallas();
			$tiponotificacion = 'ERROR';
		}
		// Registro del Evento
		$objEvento->codemp = $this->codemp;
		$objEvento->codsis = 'SSS';
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $evento;
		$objEvento->desevetra = $this->mensaje;
		$objEvento->incluirEvento();
		// Envío de Notificación
		$objEvento->objNotificacion->codemp=$this->codemp;
		$objEvento->objNotificacion->sistema='SSS';
		$objEvento->objNotificacion->tipo=$tiponotificacion;
		$objEvento->objNotificacion->titulo='Firmas Dinamicas';
		$objEvento->objNotificacion->usuario=$_SESSION['la_logusr'];
		$objEvento->objNotificacion->operacion=$this->mensaje;
		$objEvento->objNotificacion->enviarNotificacion();
		unset($objEvento);
	}	
}	
?>