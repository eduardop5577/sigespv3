<?php
/************************************************************************** 	
* @Modelo para proceso de asignar perfil a los grupos.
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
**********************************************************************
* @fecha modificacion  
* @autor  
* @descripcion  
**************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once('sigesp_dao_sss_registroeventos.php');
require_once('sigesp_dao_sss_registrofallas.php');

class DerechosGrupo extends DaoGenerico
{
	public $mensaje;
	public $evento;
	public $valido    = true;
	public $seguridad = true;
	public $existe    = true;
	public $cadena;
	public $criterio = array();	
	public $codsis;
	public $nomfisico;
	public $derechos;
	public $admin = array();
	private $conexionbd;
	
	public function __construct() {
		parent::__construct ( 'sss_derechos_grupos' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}

	
/*************************************************************************
* @Función que incluye un perfil para un grupo en un sistema
* @parametros:
* @retorno:
* @fecha de creación: 22/08/2008
* @autor: Ing. Gusmary Balza.
*******************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*************************************************************************/	
	function incluirLocal()
	{
		$this->mensaje = 'Incluyo el perfil de menu '.$this->codmenu.' para el grupo '.$this->nomgru.' en el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try 
		{ 		
			$consulta = " INSERT INTO {$this->_table} (codemp,nomgru,codsis,codmenu,visible,enabled,leer,incluir,".
						"             cambiar,eliminar,imprimir,anular,ejecutar,administrativo,ayuda,cancelar,enviarcorreo,descargar) 									".
						" 	   SELECT '{$this->codemp}','{$this->nomgru}',codsis,codmenu,{$this->visible},1,".
						"			  {$this->leer},{$this->incluir},{$this->cambiar},{$this->eliminar},{$this->imprimir},".
						"			  {$this->anular},{$this->ejecutar},{$this->administrativo},{$this->ayuda},{$this->cancelar}, 	".
						"		      {$this->enviarcorreo},{$this->descargar} 					".
						"        FROM sss_sistemas_ventanas ".
					    "       WHERE codsis='{$this->codsis}' ".
						"         AND codmenu={$this->codmenu} ".
			            "         AND hijo=0 ".
						"         AND codmenu NOT IN (SELECT codmenu FROM {$this->_table} ".
						"				   		       WHERE codemp='{$this->codemp}' ". 
						"								 AND nomgru='{$this->nomgru}' ".
						"                        		 AND codsis='{$this->codsis}') ";	
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
	   	{
			$this->valido  = false;				
			$this->mensaje='Error al Incluir el Perfil de menú '.$this->codmenu.' para el grupo '.$this->nomgru.' en el sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
		}  
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}
	
	
/***********************************************************************
* @Función que modifica un perfil de una funcionalidad
* @parametros:
* @retorno:
* @fecha de creación: 22/08/2008
* @autor: Ing. Gusmary Balza.
***********************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************/		
	public function modificarLocal() 
	{
		$this->mensaje = 'Modifico el perfil de menu '.$this->codmenu.' para el grupo '.$this->nomgru.' en el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try 
		{
			$consulta = " UPDATE {$this->_table} SET 								".
						" 	visible={$this->visible},enabled=1,leer={$this->leer}, 	". 
						" 	incluir={$this->incluir},cambiar={$this->cambiar}, 		".
						" 	eliminar={$this->eliminar},imprimir={$this->imprimir}, 	".
						" 	administrativo={$this->administrativo},					".
						" 	anular={$this->anular},ejecutar={$this->ejecutar},		".
						" 	ayuda={$this->ayuda},cancelar={$this->cancelar}, 		".
						"	enviarcorreo={$this->enviarcorreo},descargar={$this->descargar} ".
						" WHERE codemp='{$this->codemp}'";
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
            }
            $consulta.= $cadena;
			$result = $this->conexionbd->Execute($consulta);	
		}
		catch (exception $e) 
		{
			$this->mensaje='Error al Modificar el Perfil de menú '.$this->codmenu.' para el grupo '.$this->nomgru.' en el sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);
	}	
	
		
/*******************************************************************************
* @Función que verifica el perfil de una funcionalidad
* @parametros:
* @retorno:
* @fecha de creación: 22/08/2008
* @autor: Ing. Gusmary Balza.
***********************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*********************************************************************************/		
	public function leerUno()
	{
		try
		{
			$consulta = " SELECT nomgru,codsis,codmenu,visible,leer,incluir,cambiar,eliminar,imprimir, anular,ejecutar,administrativo,ayuda,			".
						" 		 cancelar,enviarcorreo,descargar,1 as valido 	".
						"   FROM {$this->_table} 								".
						"  WHERE codemp='{$this->codemp}' 					    ".
						"    AND enabled=1										".						
						"    AND nomgru='{$this->nomgru}'						";						
			$cadena=" ";
            $total = count((array)$this->criterio);                  
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." "; 			             
            }
            $consulta.= $cadena;
            if (strtoupper($_SESSION['ls_gestor'])=='OCI8PO') 
			{
            	$consulta.= " AND ROWNUM=1";
            }
            else
			{
            	$consulta.= " LIMIT 1";
            }	
			$result = $this->conexionbd->Execute($consulta);
			if ($result->EOF)
			{		
				$this->existe = false;		
			}
			else
			{
				return $result;
			}	
		}
		catch (exception $e)
		{
			$this->valido  = false;
			$this->mensaje='Error al consultar el Perfil '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
	}
		
	
		
/************************************************************************************
* @Función que incluye un perfil a todas las funcionalidades
* @parametros:
* @retorno:
* @fecha de creación: 22/08/2008
* @autor: Ing. Gusmary Balza.
************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*************************************************************************************/		
	public function insertarPermisosGlobales() 
	{
		$this->mensaje = 'Incluyo el perfil para el grupo '.$this->nomgru.' en el sistema '.$this->codsis;
		$concat = $this->conexionbd->concat('codsis','codmenu');
		$this->conexionbd->StartTrans();
		try
		{
			$consulta = " INSERT INTO {$this->_table} (codemp,nomgru,codsis,codmenu,visible,enabled,leer, 				".
			            " 							   incluir,cambiar,eliminar,imprimir,anular,ejecutar,administrativo,			".
			            " 							   ayuda,cancelar,enviarcorreo,descargar) 		".
						" 	   SELECT '{$this->codemp}','{$this->nomgru}', codsis,codmenu,visible,enabled,leer,incluir, ".
						" 		      cambiar,eliminar,imprimir,anular,ejecutar,administrativo,ayuda,cancelar,enviarcorreo,descargar		".
						"        FROM sss_sistemas_ventanas 					       ".
						"       WHERE codsis='{$this->codsis}' 				           ".
						"         AND hijo=0									       ". 
						"         AND {$concat} NOT IN (SELECT {$concat}               ".
						"			   			        FROM {$this->_table}           ".
						"					           WHERE codemp='{$this->codemp}'  ".
						"				 		         AND nomgru='{$this->nomgru}'  ".
						"						         AND codsis='{$this->codsis}') ";	
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
		{
			
			$this->valido  = false;	
			$this->mensaje=' Error al Incluir el Perfil al Grupo '.$this->nomgru.' en el sistema '.' '.$this->conexionbd->ErrorMsg();
		}	
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}
	
	
/*****************************************************************************************
* @Función que elimina el perfil a una o todas las funcionalidades
* @parametros:
* @retorno:
* @fecha de creación: 22/08/2008
* @autor: Ing. Gusmary Balza.
**************************************************************************
* @fecha modificación: 28/10/2008
* @descripción: Se englobaron las funciones de eliminar para varios casos
* @autor: Ing. Gusmary Balza.
******************************************************************************************/			//para proceso asignar usuarios a personal
	public function eliminarTodos()
	{
		$this->mensaje = 'Suspendio  el perfil para el grupo '.$this->nomgru.' en el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try
		{
			$consulta = " UPDATE {$this->_table} ".
                                    "    SET visible=0, ".
                                    "        enabled=0, ".
                                    "        leer=0, ".
                                    "        incluir=0, ".
                                    "        cambiar=0, ".
                                    " 	     eliminar=0, ".
                                    "        imprimir=0, ".
                                    "        anular=0, ".
                                    "        ejecutar=0, ".
                                    " 	     administrativo=0, ".
                                    "        ayuda=0, ".
                                    "        cancelar=0, ".
                                    "	     enviarcorreo=0, ".
                                    "        descargar=0  ".
                                    " WHERE codemp='{$this->codemp}' ";
			$cadena=" ";
			$total = count((array)$this->criterio);
			for ($contador = 0; $contador < $total; $contador++)
			{
				$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
						  $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
			}
			$consulta.= $cadena;
			$result = $this->conexionbd->Execute($consulta);
		}	
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje=' Error al Eliminar el perfil para el usuario '.$this->nomgru.' en el sistema '.$this->codsis.$this->conexionbd->ErrorMsg();
	   	} 
	   	$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);
	}
	
	function modificarDerechos()
	{
		$this->mensaje = 'Incluyo los derechos al Usuario '.$this->nomgru. ' para el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try
		{
			$criterio ="";
			if ($this->codmenu!='')
			{
				$criterio = "   AND codmenu='{$this->codmenu}'";				
			}

			$consulta = " UPDATE {$this->_table} ".
						"	 SET  ".
      					"        visible=(SELECT visible ".
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     enabled=(SELECT enabled ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     leer=(SELECT leer ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     incluir=(SELECT incluir ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     cambiar=(SELECT cambiar ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".							
						"	     eliminar=(SELECT eliminar ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".							
						"	     imprimir=(SELECT imprimir ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".							
						"	     administrativo=(SELECT administrativo ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".							
						"	     anular=(SELECT anular ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".							
						"	     ejecutar=(SELECT ejecutar ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".							
						"	     ayuda=(SELECT ayuda ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".							
						"	     cancelar=(SELECT cancelar ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".							
						"	     enviarcorreo=(SELECT enviarcorreo ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".							
						"	     descargar=(SELECT descargar ".  
			            "                   FROM sss_sistemas_ventanas ".
						"                  WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
      					"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis) ".							
			     		" WHERE {$this->_table}.codemp='$this->codemp' ".
     					"   AND {$this->_table}.codsis='$this->codsis' ".
     					"   AND {$this->_table}.nomgru= '$this->nomgru' ".
						"  ".$criterio;
			$result = $this->conexionbd->Execute($consulta);					
		}	
		catch (exception $e) 
		{
			$this->mensaje='Error al actualizar los derechos al Usuario '.$this->nomgru.' '.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}	
		$this->conexionbd->CompleteTrans();	
		$this->incluirSeguridad('MODIFICAR',$this->valido);	
	}	
	
	
	function incluirDerechos()
	{
		$this->mensaje = 'Incluyo los derechos al Usuario '.$this->nomgru. ' para el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try
		{
			$consulta = "INSERT INTO {$this->_table} (codemp,nomgru,codsis,codmenu, ".
						"            visible,enabled,leer,incluir,cambiar,eliminar,imprimir,anular, ".
						"	         ejecutar,administrativo,ayuda,cancelar,enviarcorreo,descargar) ". 	
						"	SELECT DISTINCT '$this->codemp','$this->nomgru',codsis,codmenu, ".
						"	       visible,enabled,leer,incluir,cambiar,eliminar, imprimir,anular,ejecutar,".
						"          administrativo,ayuda,cancelar,enviarcorreo, descargar ".		 		
 						"	  FROM {$this->_table}  ". 	
 						" 	 WHERE codemp='{$this->codemp}' ". 
						"	   AND nomgru='{$this->nomgru}' ".
						"	   AND codsis='{$this->codsis}' ".
						"      AND visible='1' ".
						"      AND enabled='1' ";
			$result = $this->conexionbd->Execute($consulta);				
		}	
		catch (exception $e) 
		{
			$this->mensaje='Error al actualizar los derechos al Usuario '.$this->nomgru.' '.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}	
		$this->conexionbd->CompleteTrans();	
		$this->incluirSeguridad('INSERTAR',$this->valido);	
	}		

	
/*****************************************************************************************
* @Función que elimina el perfil a una o todas las funcionalidades
* @parametros:
* @retorno:
* @fecha de creación: 22/08/2008
* @autor: Ing. Gusmary Balza.
**************************************************************************
* @fecha modificación: 28/10/2008
* @descripción: Se englobaron las funciones de eliminar para varios casos
* @autor: Ing. Gusmary Balza.
******************************************************************************************/			//para proceso asignar usuarios a personal
	public function eliminarFisicamente()
	{
		$this->mensaje = 'Elimino el perfil para el grupo '.$this->nomgru.' en el sistema '.$this->codsis;
		try
		{
			$consulta = " DELETE FROM {$this->_table} ".
						" WHERE codemp='{$this->codemp}'";
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
            }
            $consulta.= $cadena;
			$result = $this->conexionbd->Execute($consulta);
		}	
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje=' Error al Eliminar el perfil para el usuario '.$this->nomgru.' en el sistema '.$this->codsis.$this->conexionbd->ErrorMsg();
	   	} 
	   	$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('ELIMINAR',$this->valido);
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
************************************************************************************/
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