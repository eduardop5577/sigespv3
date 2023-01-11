<?php
/***********************************************************************************
* @Clase para Manejar  para la definición de Derechos Usuarios.
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* **************************
* @fecha modificacion  
* @autor  
* @descripcion  
***********************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once('sigesp_dao_sss_registroeventos.php');
require_once('sigesp_dao_sss_registrofallas.php');

class DerechosUsuario extends DaoGenerico
{
	public $valido = true;
	public $seguridad = true;
	public $mensaje;
	public $existe    = true;
	public $cadena;
	public $criterio = array();
	public $codusu;
	public $codsis;
	public $nomfisico;
	public $derechos;
	public $codusuori='';
	public $admin = array();
	public $servidor;
	public $usuario;
	public $clave;
	public $basedatos;
	public $gestor;
	public $puerto;
	public $tipoconexionbd = 'DEFECTO';
	private $conexionbd;
	
	public function __construct()
	{
		parent::__construct ( 'sss_derechos_usuarios' );
		$this->conexionbd = $this->obtenerConexionBd(); 
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
			$this->conexionbd = conectarBD($this->servidor, $this->usuario, $this->clave, $this->basedatos, $this->gestor, $this->puerto);
		}
	}
	
	
/***********************************************************************************
* @Función que Inserta los permisos a todos los sistemas
* @parametros: 
* @retorno: 
* @fecha de creación: 11/09/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	public function insertarPermisosGlobales() 
	{
		$this->mensaje = 'Incluyo el perfil para el usuario '.$this->codusu.' en el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();		
		try
		{			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codmenu,visible,enabled,leer, 				".
			            " 							   incluir,cambiar,eliminar,imprimir,anular,ejecutar,administrativo,			".
			            " 							   ayuda,cancelar,enviarcorreo,descargar) 		".
						" 	   SELECT '{$this->codemp}','{$this->codusu}', codsis,codmenu,visible,enabled,leer,incluir, ".
						" 		      cambiar,eliminar,imprimir,anular,ejecutar,administrativo,ayuda,cancelar,enviarcorreo,descargar		".
						"        FROM sss_sistemas_ventanas 					       ".
						"       WHERE codsis='{$this->codsis}' 				           ".
						"         AND hijo=0									       ". 
						"         AND codmenu NOT IN (SELECT codmenu                   ".
						"			   			        FROM {$this->_table}           ".
						"					           WHERE codemp='{$this->codemp}'  ".
						"				 		         AND codusu='{$this->codusu}'  ".
						"						         AND codsis='{$this->codsis}') ";	
			$result = $this->conexionbd->Execute($consulta);
		}	
		catch (exception $e) 
	   	{
			$this->valido  = false;				
			$this->mensaje='Error al Incluir el Perfil para todos los menus para el Usuario '.$this->codusu.' en el sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
		}  
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}
	
	
/***********************************************************************************
* @Función que busca las opciones de menu
* @parametros: 
* @retorno: 
* @fecha de creación: 11/09/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function obtenerEscritorioUsuario()
	{
		try
		{
			$consulta = "SELECT sss_sistemas.codsis, MAX(sss_sistemas.nomsis) AS nomsis, ".
						"       count(sss_usuarios.codusu) As Total, MAX(sss_sistemas.tipsis) AS tipsis, ".
						"		MAX(sss_sistemas.imgsis) AS imgsis, MAX(sss_sistemas.accsis) AS accsis, ".
						"       MAX(sss_sistemas.ordsis) AS ordsis, 1 as valido ".
						"  FROM $this->_table ".
						" INNER JOIN sss_sistemas ".
						"    ON $this->_table.codemp = '$this->codemp' ". 
						"   AND $this->_table.codusu = '$this->codusu' ".
						"   AND $this->_table.enabled = '1' ".
						"   AND sss_sistemas.estsis = '1' ".
						"   AND $this->_table.codsis = sss_sistemas.codsis ".
						" INNER JOIN sss_usuarios ".
						"    ON $this->_table.codemp = '$this->codemp' ". 
						"   AND $this->_table.codusu = '$this->codusu' ".
						"   AND $this->_table.enabled = '1' ".
						"   AND sss_usuarios.estatus=1 ".
						"   AND $this->_table.codemp = sss_usuarios.codemp ".
						"   AND $this->_table.codusu = sss_usuarios.codusu ".
						" GROUP BY sss_sistemas.codsis   ".
						" ORDER BY tipsis, ordsis  ";
			$result = $this->conexionbd->Execute($consulta); 
			return $result;
		}
		catch (exception $e) 
	   	{
			$this->valido  = false;				
			$this->mensaje='Error al consultar el escritorio del usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}	
	}

	
/***********************************************************************************
* @Función que busca el sistema y el usuario válido
* @parametros: 
* @retorno: 
* @fecha de creación: 07/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	public function obtenerSistemaUsuario()
	{
		try
		{
			$consultafecha = "";//$this->conexionbd->OffsetDate(0, $this->conexionbd->sysTimeStamp);
			//$consultafecha = $this->conexionbd->SQLDate('d/m/Y h:i A', $consultafecha);
			$consultainactividad = (time()- $_SESSION['session_activa']) + 10;
			$consultainactividad = date('i', $consultainactividad);
			
			$consulta = "SELECT sss_sistemas.nomsis, sss_usuarios.nomusu, sss_usuarios.apeusu, ".
						"       '".$consultafecha."' AS fecha, (".$consultainactividad.") AS inactivo, ".
						"		1 as valido".
						"  FROM $this->_table ".
						" INNER JOIN sss_sistemas ".
						"    ON $this->_table.codemp = '$this->codemp' ". 
						"   AND $this->_table.codusu = '$this->codusu' ".
						"   AND $this->_table.codsis = '$this->codsis' ".
						"   AND sss_sistemas.estsis = '1' ".
						"   AND $this->_table.codsis = sss_sistemas.codsis ".
						" INNER JOIN sss_usuarios ".
						"    ON $this->_table.codemp = '$this->codemp' ". 
						"   AND $this->_table.codusu = '$this->codusu' ".
						"   AND $this->_table.codsis = '$this->codsis' ".
						"   AND sss_usuarios.estatus=1 ".
						"   AND $this->_table.codemp = sss_usuarios.codemp ".
						"   AND $this->_table.codusu = sss_usuarios.codusu ";
			$result = $this->conexionbd->SelectLimit($consulta,1); 		
			return $result;
		}
		catch (exception $e) 
	   	{
			$this->valido  = false;				
			$this->mensaje='Error al consultar el sistema '.$this->codsis.' y el usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}		
	}

	
/****************************************************************************************
* @Función que incluye un perfil
* @parametros:
* @retorno:
* @fecha de creación: 22/08/2008
* @autor: Ing. Gusmary Balza.
*************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
********************************************************************************************/	
	function incluirLocal()
	{
		$this->seleccionarConexion();
		$this->mensaje='Incluyo el perfil de menu '.$this->codmenu.' para el usuario '.$this->codusu.' en el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try 
		{ 		
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codmenu,visible,enabled,leer,incluir,".
						"             cambiar,eliminar,imprimir,anular,ejecutar,administrativo,ayuda,cancelar,enviarcorreo,descargar) 									".
						" 	   SELECT '{$this->codemp}','{$this->codusu}',codsis,codmenu,{$this->visible},1,".
						"			  {$this->leer},{$this->incluir},{$this->cambiar},{$this->eliminar},{$this->imprimir},".
						"			  {$this->anular},{$this->ejecutar},{$this->administrativo},{$this->ayuda},{$this->cancelar}, 	".
						"		      {$this->enviarcorreo},{$this->descargar} 					".
						"        FROM sss_sistemas_ventanas ".
					    "       WHERE codsis='{$this->codsis}' ".
						"         AND codmenu={$this->codmenu} ".
			            "         AND hijo=0 ".
						"         AND codmenu NOT IN (SELECT codmenu FROM {$this->_table} ".
						"				   		       WHERE codemp='{$this->codemp}' ". 
						"								 AND codusu='{$this->codusu}' ".
						"                        		 AND codsis='{$this->codsis}') ";	
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
	   	{
			$this->valido  = false;				
			$this->mensaje='Error al Incluir el Perfil de menú '.$this->codmenu.' para el usuario '.$this->codusu.' en el sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
		}  
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);
	}
	
	
/*****************************************************************************************
* @Función que modifica un perfil de una funcionalidad
* @parametros:
* @retorno:
* @fecha de creación: 22/08/2008
* @autor: Ing. Gusmary Balza.
**************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***************************************************************************************/		
	public function modificarPerfil() 
	{
		$this->seleccionarConexion();
		
		$this->mensaje = 'Modifico el perfil de menu '.$this->codmenu.' para el usuario '.$this->codusu.' en el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try 
		{
			$consulta = " UPDATE {$this->_table} 			".
						"	 SET visible={$this->visible},	".
						"	     enabled={$this->visible},  ".
						"		 leer={$this->leer},        ".
						"		 incluir={$this->incluir},	".
						" 		 cambiar={$this->cambiar},  ".
						"		 eliminar={$this->eliminar},".
						" 		 imprimir={$this->imprimir},".
						"		 administrativo={$this->administrativo},".
						" 		 anular={$this->anular},    ".
						"		 ejecutar={$this->ejecutar},".
						" 		 ayuda={$this->ayuda},      ".
						"		 cancelar={$this->cancelar},".
						"   	 enviarcorreo={$this->enviarcorreo},".
						"		 descargar={$this->descargar} ".
						" WHERE codemp='{$this->codemp}'";
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador <= $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
            }
            $consulta.= $cadena;
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
		{
			$this->mensaje = 'Error al modificar el perfil de menu '.$this->codmenu.' para el Usuario '.$this->codusu.' en el sistema '.$this->codsis.''.$this->conexionbd->ErrorMsg();	
			$this->valido = false;
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);
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
******************************************************************************************/			
	public function eliminarTodos()
	{
		$this->mensaje = 'Suspendio el perfil para el usuario '.$this->codusu.' en el sistema '.$this->codsis;
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
			$this->mensaje=' Error al Eliminar el perfil para el usuario '.$this->codusu.' en el sistema '.$this->codsis.$this->conexionbd->ErrorMsg();
	   	} 
	   	$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);
	}
		
	
/***************************************************************************************
* @Función que busca el perfil de una funcionalidad
* @parametros:
* @retorno:
* @fecha de creación: 22/08/2008
* @autor: Ing. Gusmary Balza.
**************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
******************************************************************************************/		
	public function leerUno()
	{
		$this->seleccionarConexion();
		try
		{
			$consulta = " SELECT codusu,codsis,codmenu,visible,leer,incluir,cambiar,eliminar,imprimir, anular,ejecutar,administrativo,ayuda,			".
						" 		 cancelar,enviarcorreo,descargar,1 as valido 	".
						"   FROM {$this->_table} 								".
						"  WHERE codemp='{$this->codemp}' 					    ".
						"    AND enabled=1										".						
						"    AND codusu='{$this->codusu}'						";						
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
	
	
/*************************************************************************
* @Función que modifica los derechos de usuario 
* @parametros: 
* @retorno:
* @fecha de creación: 20/11/2008
* @autor: Ing. Gusmary Balza
**************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*************************************************************************/			
	function modificarDerechos()
	{
		$this->mensaje = 'Incluyo los derechos al Usuario '.$this->codusu. ' para el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try
		{			
			$criterio='';
			if ($this->codmenu!='')
			{
				$criterio = "   AND {$this->_table}.codmenu='{$this->codmenu}'";				
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
     					"   AND {$this->_table}.codusu= '$this->codusu' ".
						$criterio;
			$result = $this->conexionbd->Execute($consulta);	
		}	
		catch (exception $e) 
		{
			$this->mensaje='Error al actualizar los derechos al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}	
		$this->conexionbd->CompleteTrans();	
		$this->incluirSeguridad('MODIFICAR',$this->valido);	
	}	
	

/*************************************************************************
* @Función que inserta los derechos de usuario 
* @parametros: 
* @retorno:
* @fecha de creación: 20/11/2008
* @autor: Ing. Gusmary Balza
**************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*************************************************************************/			
	function incluirDerechos()
	{
		$this->mensaje = 'Incluyo los derechos al Usuario '.$this->codusu. ' para el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try
		{
			$consulta = "INSERT INTO {$this->_table} (codemp,codusu,codsis,codmenu, ".
						"            visible,enabled,leer,incluir,cambiar,eliminar,imprimir,anular, ".
						"	         ejecutar,administrativo,ayuda,cancelar,enviarcorreo,descargar) ". 	
						"	SELECT DISTINCT '$this->codemp','$this->codusu',codsis,codmenu, ".
						"	       MAX(visible) AS visible, MAX(enabled) AS enabled, MAX(leer) AS leer, ".
						"		   MAX(incluir) AS incluir, MAX(cambiar) AS cambiar, MAX(eliminar) AS eliminar, ".
						"		   MAX(imprimir) AS imprimir, MAX(anular) AS anular, MAX(ejecutar) AS ejecutar,".
						"          MAX(administrativo) AS administrativo, MAX(ayuda) AS ayuda, MAX(cancelar) AS cancelar,".
						"		   MAX(enviarcorreo) AS enviarcorreo, MAX(descargar) AS descargar ".		 		
 						"	  FROM {$this->_table}  ". 	
 						" 	 WHERE codemp='{$this->codemp}' ". 
						"	   AND codusu='{$this->codusu}' ".
						"	   AND codsis='{$this->codsis}' ".
						"      AND visible='1' ".
						"      AND enabled='1' ".
						" GROUP BY codsis, codmenu ";
			$result = $this->conexionbd->Execute($consulta);	
		}	
		catch (exception $e) 
		{
			$this->mensaje='Error al actualizar los derechos al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}	
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);	
	}	


/*************************************************************************
* @Función que inserta los derechos de usuario 
* @parametros: 
* @retorno:
* @fecha de creación: 20/11/2008
* @autor: Ing. Gusmary Balza
**************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*************************************************************************/			
	function incluirDerechosGrupos()
	{
		$this->mensaje = 'Incluyo los derechos del grupo '.$this->nomgru. ' al Usuario '.$this->codusu. ' ';
		$this->conexionbd->StartTrans();
		try
		{			
			$consulta = "INSERT INTO {$this->_table} (codemp,codusu,codsis,codmenu, ".
						"            visible,enabled,leer,incluir,cambiar,eliminar,imprimir,anular, ".
						"	         ejecutar,administrativo,ayuda,cancelar,enviarcorreo,descargar) ". 	
						"	SELECT sss_usuarios_en_grupos.codemp, sss_usuarios_en_grupos.codusu, sss_derechos_grupos.codsis, ".
						"          sss_derechos_grupos.codmenu, sss_derechos_grupos.visible, ".
						"	       sss_derechos_grupos.enabled, sss_derechos_grupos.leer, sss_derechos_grupos.incluir, ".
						"          sss_derechos_grupos.cambiar, sss_derechos_grupos.eliminar, sss_derechos_grupos.imprimir, ".
						"		   sss_derechos_grupos.anular, sss_derechos_grupos.ejecutar, sss_derechos_grupos.administrativo, ".
						"          sss_derechos_grupos.ayuda, sss_derechos_grupos.cancelar, sss_derechos_grupos.enviarcorreo, ".
						"          sss_derechos_grupos.descargar ".		 		
 						"	  FROM sss_usuarios_en_grupos ".
 						"    INNER JOIN sss_derechos_grupos ".
 						"       ON sss_derechos_grupos.codemp = '$this->codemp'".
 						"      AND sss_derechos_grupos.visible = 1 ".
 						"      AND sss_derechos_grupos.enabled = 1 ".
 						"      AND sss_derechos_grupos.nomgru = '$this->nomgru'".
 						"      AND sss_derechos_grupos.codmenu NOT IN (SELECT codmenu ". 
 						"    							                 FROM sss_derechos_usuarios  ".
						"                                               WHERE sss_derechos_usuarios.codemp = '$this->codemp'".
						"                                                 AND sss_derechos_usuarios.codemp =  sss_derechos_grupos.codemp".
						"                                                 AND sss_derechos_usuarios.codmenu =  sss_derechos_grupos.codmenu".
						"                                                 AND sss_derechos_usuarios.codsis =  sss_derechos_grupos.codsis".
						"                                                 AND sss_derechos_usuarios.codemp =  sss_usuarios_en_grupos.codemp".
						"                                                 AND sss_derechos_usuarios.codusu =  sss_usuarios_en_grupos.codusu)".
						"      AND sss_usuarios_en_grupos.codemp = sss_derechos_grupos.codemp ".
						"      AND sss_usuarios_en_grupos.nomgru = sss_derechos_grupos.nomgru ";
			$result = $this->conexionbd->Execute($consulta);	
		}	
		catch (exception $e) 
		{
			$this->mensaje='Error al actualizar los derechos al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}	
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('INSERTAR',$this->valido);	
	}	
	
		
/*************************************************************************
* @Función que elimina los derechos de usuario 
* @parametros: 
* @retorno:
* @fecha de creación: 20/11/2008
* @autor: Ing. Gusmary Balza
**************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*************************************************************************/			
	function eliminarDerechosGrupos()
	{
		$this->mensaje = 'Elimino los derechos del grupo '.$this->nomgru. ' al Usuario '.$this->codusu. ' ';
		$concat = $this->conexionbd->concat('codsis','codmenu');
		$consulta = "UPDATE {$this->_table} ".
					"   SET visible = 0, ".
					"       enabled = 0, ".
					"       leer = 0, ".
					"       incluir = 0, ".
					"       cambiar = 0, ".
					"       eliminar = 0, ".
					"       imprimir = 0, ".
					"       anular = 0, ".
					"	    ejecutar = 0, ".
					"       administrativo = 0, ".
					"       ayuda = 0, ".
					"       cancelar = 0, ".
					"       enviarcorreo = 0,".
					"       descargar = 0 ". 	
					" WHERE codemp='{$this->codemp}' ". 
					"   AND codusu='{$this->codusu}' ".
					"   AND visible='1' ".
					"   AND enabled='1' ".
					"   AND {$concat} IN (SELECT {$concat} ".
					"					    FROM sss_derechos_grupos ".
					"					   WHERE sss_derechos_grupos.codemp = '{$this->codemp}' ".
					"					     AND sss_derechos_grupos.nomgru = '$this->nomgru')";
		$result = $this->conexionbd->Execute($consulta);	
		if ($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}	
		$this->incluirSeguridad('ELIMINAR',$this->valido);	
	}	

/*************************************************************************
* @Función que elimina los derechos de usuario 
* @parametros: 
* @retorno:
* @fecha de creación: 20/11/2008
* @autor: Ing. Gusmary Balza
**************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*************************************************************************/			
	function eliminarTodosDerechosGrupos()
	{
		$this->mensaje = 'Elimino los derechos del grupo '.$this->nomgru. ' a todos los Usuarios';
		$concat = $this->conexionbd->concat('codsis','codmenu');
		$consulta = "UPDATE {$this->_table} ".
					"   SET visible = 0, ".
					"       enabled = 0, ".
					"       leer = 0, ".
					"       incluir = 0, ".
					"       cambiar = 0, ".
					"       eliminar = 0, ".
					"       imprimir = 0, ".
					"       anular = 0, ".
					"	    ejecutar = 0, ".
					"       administrativo = 0, ".
					"       ayuda = 0, ".
					"       cancelar = 0, ".
					"       enviarcorreo = 0,".
					"       descargar = 0 ". 	
					" WHERE codemp='{$this->codemp}' ". 
					"   AND visible='1' ".
					"   AND enabled='1' ".
					"   AND {$concat} IN (SELECT {$concat} ".
					"			FROM sss_derechos_grupos ".
					"		       WHERE sss_derechos_grupos.codemp = '{$this->codemp}' ".
					"			 AND sss_derechos_grupos.nomgru = '$this->nomgru') ".
					"   AND codusu IN (SELECT codusu ".
					"		     FROM sss_usuarios_en_grupos ".
					"		    WHERE sss_usuarios_en_grupos.codemp = '{$this->codemp}' ".
					"		      AND sss_usuarios_en_grupos.nomgru = '$this->nomgru') ";
		$result = $this->conexionbd->Execute($consulta);	
		if ($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}	
		$this->incluirSeguridad('ELIMINAR',$this->valido);	
	}	

	
/*****************************************************************************************
* @Función que modifica un perfil de una funcionalidad
* @parametros:
* @retorno:
* @fecha de creación: 22/08/2008
* @autor: Ing. Gusmary Balza.
**************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***************************************************************************************/		
	public function modificarDerechosGrupos() 
	{
		$this->seleccionarConexion();
		
		$this->mensaje = 'Modifico el perfil de menu '.$this->codmenu.' para los usuarios del grupo '.$this->nomgru.' en el sistema '.$this->codsis;
		$this->conexionbd->StartTrans();
		try 
		{
			$concat = $this->conexionbd->concat('codsis','codmenu');
			$criterio = '';
			if (trim($this->codsis)!='')
			{
				$criterio .= "   AND codsis='{$this->codsis}'";
			}
			
			if ($this->codmenu!='')
			{
				$criterio .= "   AND codmenu='{$this->codmenu}'";				
			}
			else
			{
				$criterio .= "   AND $concat IN (SELECT $concat ".
					     "			   FROM sss_derechos_grupos ".
					     "			  WHERE sss_derechos_grupos.codemp= '$this->codemp'".
					     "			    AND sss_derechos_grupos.nomgru= '$this->nomgru'";
				if (trim($this->codsis)!='')
				{
					$criterio .= "   AND sss_derechos_grupos.codsis= '$this->codsis')";
				}
				else
				{
					$criterio .= "  )";
				}
			}
			$consulta = " UPDATE {$this->_table} 			".
				    "    SET ".
     				    "        visible=(SELECT visible ".
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     enabled=(SELECT enabled ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     leer=(SELECT leer ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     incluir=(SELECT incluir ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     cambiar=(SELECT cambiar ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     eliminar=(SELECT eliminar ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     imprimir=(SELECT imprimir ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     administrativo=(SELECT administrativo ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     anular=(SELECT anular ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     ejecutar=(SELECT ejecutar ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     ayuda=(SELECT ayuda ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     cancelar=(SELECT cancelar ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     enviarcorreo=(SELECT enviarcorreo ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
				    "	     descargar=(SELECT descargar ".  
			            "                   FROM sss_derechos_grupos ".
				    "				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      				    "				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
				    "                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
				    "				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis)  ".
				    " WHERE codemp='{$this->codemp}'".
			            "   AND codusu IN (SELECT codusu ".
			            "				     FROM sss_usuarios_en_grupos ".
				    "					WHERE sss_usuarios_en_grupos.codemp= '$this->codemp'".
				    "					  AND sss_usuarios_en_grupos.nomgru= '$this->nomgru')".
				    $criterio;
			$result = $this->conexionbd->Execute($consulta);
		}
		catch (exception $e) 
		{
			$this->mensaje = 'Error al modificar el perfil de menu '.$this->codmenu.' para el Usuario '.$this->codusu.' en el sistema '.$this->codsis.''.$this->conexionbd->ErrorMsg();	
			$this->valido = false;
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);
	}
	
/***********************************************************************************
* @Función que Inserta los permisos a todos los sistemas
* @parametros: 
* @retorno: 
* @fecha de creación: 11/09/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	public function insertarPermisosGlobalesGrupo() 
	{
		$this->mensaje = 'Incluyo el perfil para el Grupo '.$this->codusu.' en el sistema '.$this->codsis;
		$concat = $this->conexionbd->concat('codsis','codmenu');
		$this->conexionbd->StartTrans();	
		try
		{			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codmenu,visible,enabled,leer, 				".
			            " 							   incluir,cambiar,eliminar,imprimir,anular,ejecutar,administrativo,			".
			            " 							   ayuda,cancelar,enviarcorreo,descargar) 		".
						" 	   SELECT '{$this->codemp}',sss_usuarios_en_grupos.codusu, codsis,codmenu,visible,enabled,leer,incluir, ".
						" 		      cambiar,eliminar,imprimir,anular,ejecutar,administrativo,ayuda,cancelar,enviarcorreo,descargar		".
						"        FROM sss_derechos_grupos 					       ".
						"       INNER JOIN sss_usuarios_en_grupos                           ".
						"		   ON sss_usuarios_en_grupos.codemp= '$this->codemp' ".
						"		  AND sss_usuarios_en_grupos.nomgru= '$this->nomgru'  ".
						"		  AND sss_usuarios_en_grupos.codemp= sss_derechos_grupos.codemp ".
						"		  AND sss_usuarios_en_grupos.nomgru= sss_derechos_grupos.nomgru ".
						"       WHERE codsis='{$this->codsis}' 				           ".
						"         AND {$concat} NOT IN (SELECT {$concat}                   ".
						"			   			        FROM {$this->_table}           ".
						"					           WHERE codemp=sss_usuarios_en_grupos.codemp  ".
						"				 		         AND codusu=sss_usuarios_en_grupos.codusu  ".
						"						         AND codsis='{$this->codsis}') ";	
			$result = $this->conexionbd->Execute($consulta);
									
			if ($this->codmenu!='')
			{
				$criterio = "   AND codmenu='{$this->codmenu}'";				
			}
			else
			{
				$criterio = "   AND codmenu IN (SELECT codmenu ".
							"				      FROM sss_derechos_grupos ".
							"					 WHERE sss_derechos_grupos.codemp= '$this->codemp'".
							"					   AND sss_derechos_grupos.nomgru= '$this->nomgru'".
							"					   AND sss_derechos_grupos.codsis= '$this->codsis')";
						
			}
			$consulta = " UPDATE {$this->_table} 			".
						"    SET ".
     					"        visible=(SELECT MAX(visible) ".
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     enabled=(SELECT MAX(enabled) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     leer=(SELECT MAX(leer) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     incluir=(SELECT MAX(incluir) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     cambiar=(SELECT MAX(cambiar) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     eliminar=(SELECT MAX(eliminar) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     imprimir=(SELECT MAX(imprimir) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     administrativo=(SELECT MAX(administrativo) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     anular=(SELECT MAX(anular) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     ejecutar=(SELECT MAX(ejecutar) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     ayuda=(SELECT MAX(ayuda) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     cancelar=(SELECT MAX(cancelar) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     enviarcorreo=(SELECT MAX(enviarcorreo) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis), ".
						"	     descargar=(SELECT MAX(descargar) ".  
			            "                   FROM sss_derechos_grupos ".
						"				   WHERE sss_derechos_grupos.codemp= '$this->codemp' 	".
      					"				     AND sss_derechos_grupos.nomgru='$this->nomgru' ".
						"                    AND {$this->_table}.codmenu=sss_derechos_grupos.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_derechos_grupos.codsis)  ".
						" WHERE codemp='{$this->codemp}'".
			            "   AND codusu IN (SELECT codusu ".
			            "				     FROM sss_usuarios_en_grupos ".
						"					WHERE sss_usuarios_en_grupos.codemp= '$this->codemp'".
						"					  AND sss_usuarios_en_grupos.nomgru= '$this->nomgru')".
						"   AND codsis='{$this->codsis}'".
						$criterio;
			$result = $this->conexionbd->Execute($consulta);
		}	
		catch (exception $e) 
	   	{
			$this->valido  = false;				
			$this->mensaje='Error al Incluir el Perfil para todos los menus para el Usuario '.$this->codusu.' en el sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
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
******************************************************************************************/			
	public function eliminarFisicamente()
	{
		$this->mensaje = 'Elimino el perfil para el usuario '.$this->codusu.' en el sistema '.$this->codsis;
		try
		{
			$consulta = " DELETE FROM {$this->_table} ".
						" WHERE codemp= '{$this->codemp}'				";			
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
				if(trim($this->criterio[$contador]['operador']) <> '')
				{
					$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
							   $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
				}
            }
            $consulta.= $cadena;
			$result = $this->conexionbd->Execute($consulta);
		}	
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje=' Error al Eliminar el perfil para el usuario '.$this->codusu.' en el sistema '.$this->codsis.$this->conexionbd->ErrorMsg();
	   	} 
		$this->incluirSeguridad('ELIMINAR',$this->valido);
	}

	function copiarDerechos()
	{
		$this->mensaje = 'Incluyo los derechos del usuario '.$this->codusuori.'  para el Usuario '.$this->codusu;
		try
		{
			$concat = $this->conexionbd->concat('codemp','codsis','codmenu');
			
			$consulta = "INSERT INTO {$this->_table} (codemp,codusu,codsis,codmenu, ".
						"            visible,enabled,leer,incluir,cambiar,eliminar,imprimir,anular, ".
						"	         ejecutar,administrativo,ayuda,cancelar,enviarcorreo,descargar) ". 	
						"	SELECT DISTINCT '$this->codemp','$this->codusu',codsis,codmenu,visible, ".
						"	       enabled,leer,incluir,cambiar,eliminar,imprimir,anular,ejecutar,".
						"          administrativo,ayuda,cancelar,enviarcorreo, descargar ".		 		
 						"	  FROM {$this->_table}  ". 	
 						" 	 WHERE codemp='{$this->codemp}' ". 
						"	   AND codusu='{$this->codusuori}' ".
						"      AND visible='1' ".
						"      AND enabled='1' ".
					    "      AND ".$concat." NOT IN (SELECT ".$concat." ".
						"                              FROM {$this->_table} ".
						"							  WHERE codusu='{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);	

			$consulta = " UPDATE {$this->_table} 			".
						"    SET ".
     					"        visible=(SELECT visible ".
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     enabled=(SELECT enabled ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     leer=(SELECT leer ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     incluir=(SELECT incluir ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".			
						"	     cambiar=(SELECT cambiar ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     eliminar=(SELECT eliminar ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     imprimir=(SELECT imprimir ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     administrativo=(SELECT administrativo ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     anular=(SELECT anular ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     ejecutar=(SELECT ejecutar ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     ayuda=(SELECT ayuda ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     cancelar=(SELECT cancelar ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     enviarcorreo=(SELECT enviarcorreo ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis), ".
						"	     descargar=(SELECT descargar ".  
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis) ".
						" WHERE codemp='{$this->codemp}'".
			            "   AND ".$concat." IN (SELECT ".$concat." ".
			            "                   FROM {$this->_table} as ORIGEN ".
						"				   WHERE ORIGEN.codemp= '$this->codemp' 	".
      					"				     AND ORIGEN.codusu='$this->codusuori' ".
						"      				 AND visible='1' ".
						"                    AND enabled='1' ".
						"                    AND {$this->_table}.codemp=ORIGEN.codemp ". 
						"                    AND {$this->_table}.codmenu=ORIGEN.codmenu ". 
						"				     AND {$this->_table}.codsis=ORIGEN.codsis) ";
						$result = $this->conexionbd->Execute($consulta);	
			
		}	
		catch (exception $e) 
		{
			$this->mensaje='Error al incluir/actualizar los derechos al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		$this->incluirSeguridad('INSERTAR',$this->valido);	
	}	
	
	function agregarTodos()
	{
		$this->mensaje = 'Incluyo todos los derechos al Usuario '.$this->codusu;
		try
		{
			$concat = $this->conexionbd->concat('codsis','codmenu');
			
			$consulta = "INSERT INTO {$this->_table} (codemp,codusu,codsis,codmenu, ".
						"            visible,enabled,leer,incluir,cambiar,eliminar,imprimir,anular, ".
						"	         ejecutar,administrativo,ayuda,cancelar,enviarcorreo,descargar) ". 	
						"	SELECT DISTINCT '$this->codemp','$this->codusu',codsis,codmenu,visible, ".
						"	       enabled,leer,incluir,cambiar,eliminar,imprimir,anular,ejecutar,".
						"          administrativo,ayuda,cancelar,enviarcorreo, descargar ".		 		
 						"	  FROM sss_sistemas_ventanas  ". 	
 						" 	 WHERE visible='1' ".
						"      AND enabled='1' ".
					    "      AND ".$concat." NOT IN (SELECT ".$concat." ".
						"                              FROM {$this->_table} ".
						"							  WHERE codusu='{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);	

			$consulta = " UPDATE {$this->_table} 			".
						"    SET ".
     					"        visible=(SELECT visible ".
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     enabled=(SELECT enabled ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     leer=(SELECT leer ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     incluir=(SELECT incluir ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".			
						"	     cambiar=(SELECT cambiar ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     eliminar=(SELECT eliminar ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     imprimir=(SELECT imprimir ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     administrativo=(SELECT administrativo ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     anular=(SELECT anular ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     ejecutar=(SELECT ejecutar ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     ayuda=(SELECT ayuda ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     cancelar=(SELECT cancelar ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     enviarcorreo=(SELECT enviarcorreo ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis), ".
						"	     descargar=(SELECT descargar ".  
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis) ".
						" WHERE codemp='{$this->codemp}'".
			            "   AND ".$concat."   IN (SELECT ".$concat." ".
			            "                   FROM sss_sistemas_ventanas ".
						"				   WHERE visible='1' ".
						"                    AND enabled='1' ".
						"                    AND {$this->_table}.codmenu=sss_sistemas_ventanas.codmenu ". 
						"				     AND {$this->_table}.codsis=sss_sistemas_ventanas.codsis) ";
						$result = $this->conexionbd->Execute($consulta);	
			
		}	
		catch (exception $e) 
		{
			$this->mensaje='Error al incluir/actualizar los derechos al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}	
		$this->incluirSeguridad('INSERTAR',$this->valido);	
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
		if ($this->seguridad==true)
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
		$objEvento->codemp = $this->codemp;//'0001';
		$objEvento->codsis = 'SSS';
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $evento;
		$objEvento->desevetra = $this->mensaje;
		$objEvento->incluirEvento();
		unset($objEvento);
		}
	}
	
	public function existeDerechoUsuario()
	{
		$existe= true;
		$cadenaSQL="SELECT codusu ".
  				   "  FROM sss_derechos_usuarios ".
  				   " WHERE codemp='{$this->codemp}' ".
				   "   AND codsis='{$this->codsis}'  ".
    			   "   AND codusu='{$this->codusu}'";
		$result = $this->conexionbd->Execute($cadenaSQL);
		if ($result->EOF) {
			$existe = false;		
		}
		
		return $existe;
	}
}	
?>