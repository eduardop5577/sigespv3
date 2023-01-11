<?php
/**************************************************************************************
* @Clase para Manejar  la ejecución de los reportes de Seguridad.
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
**********************************************************************
* @fecha modificacion  
* @autor  
* @descripcion  
**************************************************************************************/

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once('sigesp_dao_sss_registroeventos.php');
require_once('sigesp_dao_sss_registrofallas.php');

class Reportes extends DaoGenerico
{
	public $valido = true;
	public $criterio;
	public $criterio2;
	private $conexionbd;

	public function __construct() {
		parent::__construct ( 'sss_derechos_usuarios' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}
	
/***********************************************************************************
* @Función para obtener los permisos para un (o varios) sistemas de uno (o varios) 
* usuarios y/o grupos
* @parametros: 
* @retorno:
* @fecha de creación: 31/10/2008.
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/				
	function leerDerechos() //para reporte de permisos
	{
		//	$this->conexionbd->debug = 1;
		try
		{			
			$consulta = " SELECT codemp,codsis,codmenu,codusu,visible,leer,incluir,".
						"	cambiar,eliminar,imprimir,anular,ejecutar, ".
						" 	administrativo,ayuda,cancelar,enviarcorreo,descargar,1 as valido, ".
						" (SELECT nombre FROM sigesp_empresa 
								WHERE sss_derechos_usuarios.codemp=sigesp_empresa.codemp) as nombre, ".
						" (SELECT nomsis FROM sss_sistemas 
								WHERE sss_derechos_usuarios.codsis=sss_sistemas.codsis) as nomsis, ".
						" (SELECT nomusu FROM sss_usuarios 
								WHERE sss_derechos_usuarios.codusu=sss_usuarios.codusu) as nomusu, ".
						" (SELECT apeusu FROM sss_usuarios 
								WHERE sss_derechos_usuarios.codusu=sss_usuarios.codusu) as apeusu, ".
						" (SELECT nomlogico FROM sss_sistemas_ventanas 
								WHERE sss_derechos_usuarios.codmenu=sss_sistemas_ventanas.codmenu ".
						" AND sss_derechos_usuarios.codsis=sss_sistemas_ventanas.codsis) as nomlogico ".
						" FROM sss_derechos_usuarios ".
						" WHERE sss_derechos_usuarios.codemp='{$this->codemp}' ";
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
 			               
            }
            $consulta.= $cadena;
            
            $consulta .= " UNION ".
            			" SELECT codemp,codsis,codmenu,nomgru,visible,leer,incluir,".
						"	cambiar,eliminar,imprimir,anular,ejecutar, ".
						" 	administrativo,ayuda,cancelar,2 as enviarcorreo,descargar,1 as valido, ".
						" 	(SELECT nombre FROM sigesp_empresa 
								WHERE sss_derechos_grupos.codemp=sigesp_empresa.codemp) as nombre, ".
						" 	(SELECT nomsis FROM sss_sistemas 
								WHERE sss_derechos_grupos.codsis=sss_sistemas.codsis) as nomsis, ".
						" 	(SELECT nomgru FROM sss_grupos 
								WHERE sss_derechos_grupos.nomgru=sss_grupos.nomgru) as nomgru, ".
            			" 	(SELECT nota FROM sss_grupos 
								WHERE sss_derechos_grupos.nomgru=sss_grupos.nomgru) as nota, ".
						" 	(SELECT nomlogico FROM sss_sistemas_ventanas 
								WHERE sss_derechos_grupos.codmenu=sss_sistemas_ventanas.codmenu ".
						" 		AND sss_derechos_grupos.codsis=sss_sistemas_ventanas.codsis) as nomlogico ".
						" FROM sss_derechos_grupos ".
						" WHERE sss_derechos_grupos.codemp='{$this->codemp}' ";
            $cadena2 = "";
            $total2 = count((array)$this->criterio2);
		  	for ($contador = 0; $contador < $total2; $contador++)
			{
            	$cadena2.= $this->criterio2[$contador]['operador']." ".$this->criterio2[$contador]['criterio']." ".
 			               $this->criterio2[$contador]['condicion']." ".$this->criterio2[$contador]['valor']." ";
 			               
            }
            $consulta.= $cadena2;
            $ordenar = "ORDER BY {$this->orden}";
            $consulta.= $ordenar; 
           // echo $consulta;
        	$result = $this->conexionbd->Execute($consulta);
			return $result;
		}	
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al ejecutar el Reporte de Permisos '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('REPORTAR',$this->valido);
	   	} 
	}

	
/***********************************************************************************
* @Función para obtener los registros de eventos y/o fallas para un (o varios) 
* sistemas de uno (o varios) usuarios y/o grupos
* @parametros: 
* @retorno:
* @fecha de creación: 31/10/2008.
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/					
	function leerRegistros()
	{
		//$this->conexionbd->debug = 1;
		$cadenaFechaA = '';
		$cadenaFechaB = '';
		if ($_SESSION['sigesp_gestor']=='POSTGRES') {
			$cadenaFechaA = "AND sss_registro_eventos.fecevetra::text LIKE '".$this->fecha."%'";
			$cadenaFechaB = "AND sss_registro_fallas.fecevetra::text LIKE '".$this->fecha."%'";
		}
		else {
			$cadenaFechaA = "AND CAST(sss_registro_eventos.fecevetra AS CHAR) LIKE '".$this->fecha."%'";
			$cadenaFechaB = "AND CAST(sss_registro_fallas.fecevetra AS CHAR) LIKE '".$this->fecha."%'";
		}
		
		try
		{	
			$consulta = " SELECT codemp,numeve,codusu,codsis,codmenu, ".
						" 	evento,fecevetra,equevetra,desevetra,1 as tipo,1 as valido,".
						" (SELECT nombre FROM sigesp_empresa 
								WHERE sss_registro_eventos.codemp=sigesp_empresa.codemp) as nombre, ".
						" (SELECT nomsis FROM sss_sistemas 
								WHERE sss_registro_eventos.codsis=sss_sistemas.codsis) as nomsis, ".
						" (SELECT nomusu FROM sss_usuarios 
								WHERE sss_registro_eventos.codusu=sss_usuarios.codusu) as nomusu, ".
						" (SELECT apeusu FROM sss_usuarios 
								WHERE sss_registro_eventos.codusu=sss_usuarios.codusu) as apeusu, ".
						" (SELECT nomlogico FROM sss_sistemas_ventanas 
								WHERE sss_registro_eventos.codmenu=sss_sistemas_ventanas.codmenu ".
						" 		AND sss_registro_eventos.codsis=sss_sistemas_ventanas.codsis) as nomlogico ".
						" FROM sss_registro_eventos ".
						" WHERE sss_registro_eventos.codemp='{$this->codemp}' ".$cadenaFechaA;
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
 			               
            }
    		$consulta.= $cadena;
    		
    		$consulta.= " UNION ".
            			" SELECT codemp,numeve,codusu,codsis,codmenu, ".
						" 	evento,fecevetra,equevetra,desevetra,0 as tipo,1 as valido,".
						" 	(SELECT nombre FROM sigesp_empresa 
								WHERE sss_registro_fallas.codemp=sigesp_empresa.codemp) as nombre, ".
						" 	(SELECT nomsis FROM sss_sistemas 
								WHERE sss_registro_fallas.codsis=sss_sistemas.codsis) as nomsis, ".
						" (SELECT nomusu FROM sss_usuarios 
								WHERE sss_registro_fallas.codusu=sss_usuarios.codusu) as nomusu, ".
						" (SELECT apeusu FROM sss_usuarios 
								WHERE sss_registro_fallas.codusu=sss_usuarios.codusu) as apeusu, ".
						" 	(SELECT nomlogico FROM sss_sistemas_ventanas 
								WHERE sss_registro_fallas.codmenu=sss_sistemas_ventanas.codmenu ".
						" 		AND sss_registro_fallas.codsis=sss_sistemas_ventanas.codsis) as nomlogico ".
						" FROM sss_registro_fallas ".
						" WHERE sss_registro_fallas.codemp='{$this->codemp}' ".$cadenaFechaB;
    		
    		$cadena2 = "";
            $total2 = count((array)$this->criterio2);
		  	for ($contador = 0; $contador < $total2; $contador++)
			{
            	$cadena2.= $this->criterio2[$contador]['operador']." ".$this->criterio2[$contador]['criterio']." ".
 			               $this->criterio2[$contador]['condicion']." ".$this->criterio2[$contador]['valor']." ";
 			               
            }
            $consulta.= $cadena2;
           
            $result = $this->conexionbd->Execute($consulta);
            
            return $result;
		}				
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al ejecutar el Reporte de Auditoría '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('REPORTAR',$this->valido);
	   	} 
	}
		
	
/***********************************************************************************
* @Función para obtener los eventos para un (o varios) sistemas de uno (o varios) 
* usuarios
* @parametros: 
* @retorno:
* @fecha de creación: 03/11/2008.
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/					
	function leerEventos()
	{
		//$this->conexionbd->debug = 1;
		$cadenaFecha = '';
		if ($_SESSION['sigesp_gestor']=='POSTGRES') {
			$cadenaFecha = "AND sss_registro_eventos.fecevetra::text LIKE '".$this->fecha."%'";
		}
		else {
			$cadenaFecha = "AND CAST(sss_registro_eventos.fecevetra AS CHAR) LIKE '".$this->fecha."%'";
		}
		
		try
		{	
			$consulta = " SELECT codemp,numeve,codusu,codsis,codmenu, ".
						" 	evento,fecevetra,equevetra,desevetra,1 as tipo,1 as valido,".
						" (SELECT nombre FROM sigesp_empresa 
								WHERE sss_registro_eventos.codemp=sigesp_empresa.codemp) as nombre, ".
						" (SELECT nomsis FROM sss_sistemas 
								WHERE sss_registro_eventos.codsis=sss_sistemas.codsis) as nomsis, ".
						" (SELECT nomusu FROM sss_usuarios 
								WHERE sss_registro_eventos.codusu=sss_usuarios.codusu) as nomusu, ".
						" (SELECT apeusu FROM sss_usuarios 
								WHERE sss_registro_eventos.codusu=sss_usuarios.codusu) as apeusu, ".
						" (SELECT nomlogico FROM sss_sistemas_ventanas 
								WHERE sss_registro_eventos.codmenu=sss_sistemas_ventanas.codmenu ".
						" 		AND sss_registro_eventos.codsis=sss_sistemas_ventanas.codsis) as nomlogico ".
						" FROM sss_registro_eventos ".
						" WHERE sss_registro_eventos.codemp='{$this->codemp}' ".
						" {$cadenaFecha}";
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
 			               
            }
    		$consulta.= $cadena;            
        	$result = $this->conexionbd->Execute($consulta);
			return $result;
		}				
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al ejecutar el Reporte de Auditoría '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('REPORTAR',$this->valido);
	   	} 
	}
		
	
/***********************************************************************************
* @Función para obtener las eventos de fallas para un (o varios) sistemas de 
* un (o varios) usuarios 
* @parametros: 
* @retorno:
* @fecha de creación: 03/11/2008.
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/				
	function leerFallas()
	{		
		//$this->conexionbd->debug = 1;
		$cadenaFecha = '';
		if ($_SESSION['sigesp_gestor']=='POSTGRES') {
			$cadenaFecha = "AND sss_registro_fallas.fecevetra::text LIKE '".$this->fecha."%'";
		}
		else {
			$cadenaFecha = "AND CAST(sss_registro_fallas.fecevetra AS CHAR) LIKE '".$this->fecha."%'";
		}
		try
		{    		
    		$consulta.= " SELECT codemp,numeve,codusu,codsis,codmenu, ".
						" 	evento,fecevetra,equevetra,desevetra,0 as tipo,1 as valido,".
						" 	(SELECT nombre FROM sigesp_empresa 
								WHERE sss_registro_fallas.codemp=sigesp_empresa.codemp) as nombre, ".
						" 	(SELECT nomsis FROM sss_sistemas 
								WHERE sss_registro_fallas.codsis=sss_sistemas.codsis) as nomsis, ".
						" (SELECT nomusu FROM sss_usuarios 
								WHERE sss_registro_fallas.codusu=sss_usuarios.codusu) as nomusu, ".
						" (SELECT apeusu FROM sss_usuarios 
								WHERE sss_registro_fallas.codusu=sss_usuarios.codusu) as apeusu, ".
						" 	(SELECT nomlogico FROM sss_sistemas_ventanas 
								WHERE sss_registro_fallas.codmenu=sss_sistemas_ventanas.codmenu ".
						" 		AND sss_registro_fallas.codsis=sss_sistemas_ventanas.codsis) as nomlogico ".
						" FROM sss_registro_fallas ".
						" WHERE sss_registro_fallas.codemp='{$this->codemp}' ".
						" {$cadenaFecha}";
    		
    		$cadena = "";
            $total = count((array)$this->criterio);
		  	for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
 			               
            }
            $consulta.= $cadena;          
            
        	$result = $this->conexionbd->Execute($consulta);
			return $result;
		}				
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al ejecutar el Reporte de Auditoría '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('REPORTAR',$this->valido);
	   	} 
	}
		
	
	function leerTraspasos()
	{
		//$this->conexionbd->debug = 1;
		try
		{    	
			$consulta = " SELECT codres,codproc,codsis,fecha,bdorigen,bddestino,descripcion,1 as valido ".
						" FROM sigesp_dt_proc_cons ";			
			$cadena = "";
            $total = count((array)$this->criterio);
            for ($contador = 0;  $contador < 3; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
 			               
            }
            $consulta.= $cadena; 
           	$result = $this->conexionbd->Execute($consulta);
          	return $result;
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al ejecutar el Reporte de Traspaso '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('REPORTAR',$this->valido);
	   	} 
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
*************************************************************************************/
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
		$objEvento->codsis = 'SSS';
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $evento;
		$objEvento->desevetra = $this->mensaje;
		$objEvento->incluirEvento();
		unset($objEvento);
	}
		
	
}
?>