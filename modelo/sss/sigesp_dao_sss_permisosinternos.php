<?php
/***********************************************************************************
* @Modelo para proceso de asignación de los permisos internos a los usuarios
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
**********************************************************************
* @fecha modificacion  
* @autor  
* @descripcion  
***********************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');

class PermisosInternos extends DaoGenerico
{
	public $mensaje;
	public $evento;
	public $valido = true;
	public $existe = true;
	public $seguridad = true;
	public $codsis;
	public $codintper;
	public $nomfisico;
	public $admin = array();
	public $usuarioeliminar = array();
	public $criterio = array();
	public $objDerechos;
	public $codmenu='';
	public $codusuori='';
	
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
		parent::__construct ( 'sss_permisos_internos' );
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
	public function selecionarConexion ()
	{
		if ($this->tipoconexionbd != 'DEFECTO')
		{
			$this->conexionbd = conectarBD($this->servidor, $this->usuario, $this->clave, $this->basedatos, $this->gestor, $this->puerto);
		}
	}
	
/***********************************************************************************
* @Función que inserta los permisos de un usuario para: una constante, una nomina, 
* un personal
* @parametros: 
* @retorno:
* @fecha de creación: 09/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 
* @descripción: 
* @autor: Ing. 
***********************************************************************************/
	function incluirPermisosInternos()
	{
		$this->selecionarConexion ();
		$this->mensaje='Incluyo el Permiso '.$this->codintper.' para el Usuario '.$this->codusu.' en el sistema '.$this->codsis;

		$this->criterio[0]['operador'] = " AND";
		$this->criterio[0]['criterio'] = "codsis";
		$this->criterio[0]['condicion'] = "=";
		$this->criterio[0]['valor'] = "'".$this->codsis."'";
		
		$this->criterio[1]['operador'] = " AND";
		$this->criterio[1]['criterio'] = "codintper";
		$this->criterio[1]['condicion'] = "=";
		$this->criterio[1]['valor'] = "'".$this->codintper."'";

		$this->leerTodosLocal();
		if ($this->existe===true)
		{
			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
						"    AND codsis = '{$this->codsis}' ". 
						"    AND codintper = '{$this->codintper}'";
			$result = $this->conexionbd->Execute($consulta);			
		}
		else
		{	
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
					  	" VALUES ('{$this->codemp}','{$this->codusu}','{$this->codsis}','$this->codintper',1)	";
			$result = $this->conexionbd->Execute($consulta);
		}	
		if ($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}	
		$this->incluirSeguridad('INSERTAR',$this->valido);		
	}
	
	
/***********************************************************************************
* @Función que busca si un grupo tiene permiso para un sistema
* @parametros: 
* @retorno:
* @fecha de creación: 21/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 
* @descripción: 
* @autor: Ing. 
***********************************************************************************/		
	function leerTodosLocal()
	{
		$this->selecionarConexion ();
		try 
		{ 
			$consulta=" SELECT codsis,codusu,codintper ".
					  " FROM {$this->_table} ".
					  " WHERE codemp= '{$this->codemp}' ".
					  " AND codusu= '{$this->codusu}' ";
			$cadena=" ";
            $total = count((array)$this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
            }
            $consulta.= $cadena;					
			$result = $this->conexionbd->Execute($consulta);
			if ($result->EOF)
			{		
				$this->existe = false;		
			}			
			return $result;			
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje = 'Error al consultar el permiso para el Usuario '.$this->codusu.' en el Sistema'.$this->codsis.' '.$this->conexionbd->ErrorMsg();			
		}
	}
/***********************************************************************************
* @Función que busca si un grupo tiene permiso para un sistema
* @parametros: 
* @retorno:
* @fecha de creación: 21/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 
* @descripción: 
* @autor: Ing. 
***********************************************************************************/		
	function incluirDefecto()
	{
		$this->selecionarConexion ();
		try 
		{ 
			$consulta=" SELECT codsis,codusu,codintper ".
					  " FROM sss_permisos_internos ".
					  " WHERE codemp= '{$this->codemp}' ".
					  " AND codusu= '{$this->codusu}' ".
					  " AND codintper='----------'";
			$cadena=" ";
            $consulta.= $cadena;					
			$result = $this->conexionbd->Execute($consulta);
			if ($result->EOF)
			{		
				$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
							" VALUES ('{$this->codemp}','{$this->codusu}','{$this->codsis}','----------',1)	";
				$result = $this->conexionbd->Execute($consulta);                        
			}			
			return true;			
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje = 'Error al consultar el permiso para el Usuario '.$this->codusu.' en el Sistema'.$this->codsis.' '.$this->conexionbd->ErrorMsg();			
		}
	}
	
	
/***********************************************************************************
* @Función que actualiza los permisos asignados a usuarios  
* @parametros: 
* @retorno:
* @fecha de creación: 27/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 22/12/2008
* @descripción: se agrego el criterio
* @autor: Ing. Gusmary Balza
***********************************************************************************/		
	function actualizar()
	{
		try 
		{ 
			$total=	count((array)$this->usuarioeliminar);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->usuarioeliminar[$contador]->criterio[0]['operador'] = "AND";
				$this->usuarioeliminar[$contador]->criterio[0]['criterio'] = "codsis";
				$this->usuarioeliminar[$contador]->criterio[0]['condicion'] = "=";
				$this->usuarioeliminar[$contador]->criterio[0]['valor'] = "'".$this->codsis."'";
				
				$this->usuarioeliminar[$contador]->criterio[1]['operador'] = "AND";
				$this->usuarioeliminar[$contador]->criterio[1]['criterio'] = "trim(codintper)";
				$this->usuarioeliminar[$contador]->criterio[1]['condicion'] = "=";
				$this->usuarioeliminar[$contador]->criterio[1]['valor'] = "'".$this->codintper."'";
				
				$this->usuarioeliminar[$contador]->criterio[2]['operador'] = "AND";
				$this->usuarioeliminar[$contador]->criterio[2]['criterio'] = "codusu";
				$this->usuarioeliminar[$contador]->criterio[2]['condicion'] = "=";
				$this->usuarioeliminar[$contador]->criterio[2]['valor'] = "'".$this->usuarioeliminar[$contador]->codusu."'";
				
				$this->usuarioeliminar[$contador]->eliminarTodos();				
			}
			$total=	count((array)$this->admin);
			for ($contador=0; $contador < $total; $contador++)
			{	
				$this->admin[$contador]->incluirPermisosInternos();
			}
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al Modificar el permiso '.$this->codintper.' para el usuario '.$this->codusu.' en el sistema '.$this->codsis.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);	
	}
	
	
/***********************************************************************************
* @Función que elimina los permisos asignados a usuarios  
* @parametros: 
* @retorno:
* @fecha de creación: 27/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 22/12/2008
* @descripción: se agrego el criterio
* @autor: Ing. Gusmary Balza
***********************************************************************************/			
	function eliminarTodos()
	{			
		$this->mensaje='Suspendio los permisos '.$this->codintper.' al Usuario '.$this->codusu;
		$this->conexionbd->StartTrans();
		try
		{
			$consulta = " UPDATE {$this->_table} SET enabled = 0 ".
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
			$this->mensaje='Error al eliminar el permiso '.$this->codintper.' al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		$this->conexionbd->CompleteTrans();
		$this->incluirSeguridad('MODIFICAR',$this->valido);		
	}
		
	
/***********************************************************************************
* @Función que busca los usuarios de un personal
* @parametros: 
* @retorno:
* @fecha de creación: 24/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
************************************************************************************/
	public function obtenerUsuarios()
	{
		try 
		{ 
			if ($this->campo=='codest')
			{
				$this->campo = $this->conexionbd->Concat('codestpro1','codestpro2','codestpro3','codestpro4','codestpro5','estcla');
			}
			else
			{
				$this->campo = $this->tabla.'.'.$this->campo;
			}
			if ($this->tabla=='sno_constante')
			{
				$codigo = $this->conexionbd->Concat('codnom',"'-'",'codcons');
				$this->tabla.$this->campo = $codigo;
			}		
			if ($this->tabla=='scb_ctabanco')
			{
				$codigo = $this->conexionbd->Concat('codban',"'-'",'ctaban');
				$this->tabla.$this->campo = $codigo;
			}		
			
			$consulta = " SELECT {$this->_table}.codusu, sss_usuarios.nomusu, sss_usuarios.apeusu, 1 as valido ".
						"  	FROM {$this->_table} ".
						" 	INNER JOIN  ({$this->tabla} ".
					    "  		INNER JOIN sss_usuarios  ".
					   	"   	ON sss_usuarios.codemp = {$this->tabla}.codemp) ".
						"  	ON {$this->_table}.codemp = {$this->tabla}.codemp  ".
						"	AND trim({$this->_table}.codintper) = trim({$this->campo}) ".
						"	AND {$this->_table}.codusu = sss_usuarios.codusu ".
						"	WHERE TRIM({$this->_table}.codintper) = TRIM('{$this->codintper}') ".
						"	AND {$this->_table}.enabled= 1 ".
						"	AND {$this->_table}.codsis='{$this->codsis}' ".
						"	AND {$this->_table}.codemp = '{$this->codemp}' ".
						"	GROUP BY {$this->campo},{$this->_table}.codusu,sss_usuarios.nomusu,sss_usuarios.apeusu ".
						"	ORDER BY {$this->_table}.codusu,sss_usuarios.nomusu,sss_usuarios.apeusu ";
			
			$result = $this->conexionbd->Execute($consulta);
			return $result;
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar los usuarios del Personal '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
	   	} 
	}

	
	
/***********************************************************************************
* @Función que busca los permisos de un usuario para un personal.
* @parametros: 
* @retorno: 
* @fecha de creación: 30/09/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function obtenerPermisos() 
	{
		if ($this->tabla=='sno_constante')
		{
			$codigo = $this->conexionbd->Concat('codnom',"'-'",'codcons');			
				
			$consulta = " SELECT substr(codintper,0,5) as codnom, 						".
						"	substr(codintper,6,10) as {$this->campo},					".
						"	{$this->_table}.codsis,										".
					    "	(SELECT {$this->campo2} FROM {$this->tabla} 				".
					    "		WHERE {$this->tabla}.codemp={$this->_table}.codemp 		".
					    "		AND $codigo={$this->_table}.codintper 					".
					    " 		GROUP BY {$this->campo2}) as {$this->campo2}			".
					    " FROM {$this->_table}											".
					    " WHERE codemp= '{$this->codemp}'								".
					    " AND codusu= '{$this->codusu}'									".
					    " AND codsis= '{$this->sistema}'								".
					    " AND enabled= 1												".
					    " AND codintper IN (SELECT {$codigo} 							".
					    "					FROM {$this->tabla}							".
					    "					WHERE codemp='{$this->codemp}')				";
			
		}	
		else
		{		
			if ($this->tabla=='scb_ctabanco')
			{
				$codigo = $this->conexionbd->Concat('codban',"'-'",'ctaban');			
				$consulta = " SELECT substr(codintper,0,4) as codban, 						".
							"	substr(codintper,5,30) as {$this->campo2},					".
							"	{$this->_table}.codsis										".
							" FROM {$this->_table}											".
							" WHERE codemp= '{$this->codemp}'								".
							" AND codusu= '{$this->codusu}'									".
							" AND codsis= '{$this->sistema}'								".
							" AND enabled= 1												".
							" AND trim(codintper) IN (SELECT {$codigo} 						".
							"					FROM {$this->tabla}							".
							"					WHERE codemp='{$this->codemp}')				";
			}
			else
			{			
			
				if ($this->tabla!='spg_unidadadministrativa')
				{
					$sistema = " AND codsis= '{$this->sistema}' ";
				}
				else
				{
					$sistema = "";
				}
				$consulta = " SELECT codintper as {$this->campo},{$this->_table}.codsis,		".
							"	(SELECT {$this->campo2} FROM {$this->tabla} 					".
							"		WHERE {$this->tabla}.codemp={$this->_table}.codemp 			".
							"		AND {$this->tabla}.{$this->campo}={$this->_table}.codintper ".
							" 		GROUP BY {$this->campo2}) as {$this->campo2}				".
							" FROM {$this->_table}												".
							" WHERE codemp= '{$this->codemp}'									".
							" AND codusu= '{$this->codusu}'										".
							" $sistema															".
							" AND enabled= 1													".
							" AND codintper IN (SELECT {$this->campo} 							".
							"					FROM {$this->tabla}								".
							"					WHERE codemp='{$this->codemp}')					";
			 }
		}		
		$result = $this->conexionbd->Execute($consulta);
		return $result;		
	}
	
	
/***********************************************************************************
* @Función que busca los permisos de un usuario para una estructura presupuestaria.
* @parametros: 
* @retorno: 
* @fecha de creación: 10/10/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function obtenerEstPre() 
	{
		$codcompleto = $this->conexionbd->Concat("spg_ep5.codestpro1","spg_ep5.codestpro2","spg_ep5.codestpro3","spg_ep5.codestpro4","spg_ep5.codestpro5","spg_ep5.estcla");
		
		$longaux1 = $_SESSION['la_empresa']['loncodestpro1'];
		$longest1 = (25-$longaux1)+1;
		$longaux2 = $_SESSION['la_empresa']['loncodestpro2'];
		$longest2 = (25-$longaux2)+1;
		$longaux3 = $_SESSION['la_empresa']['loncodestpro3'];
		$longest3 = (25-$longaux3)+1;
		$longaux4 = $_SESSION['la_empresa']['loncodestpro4'];
		$longest4 = (25-$longaux4)+1;
		$longaux5 = $_SESSION['la_empresa']['loncodestpro5'];
		$longest5 = (25-$longaux5)+1;		
		
		$codest = $this->conexionbd->Concat("substr(substr({$this->_table}.codintper,1,25),$longest1,$longaux1)","substr(substr({$this->_table}.codintper,26,25),$longest2,$longaux2)","substr(substr({$this->_table}.codintper,51,25),$longest3,$longaux3)","substr(substr({$this->_table}.codintper,76,25),$longest4,$longaux4)","substr(substr({$this->_table}.codintper,101,25),$longest5,$longaux5)","substr({$this->_table}.codintper,126,1)");
		
		$nombre = $this->conexionbd->Concat("spg_ep1.denestpro1","'-'","spg_ep2.denestpro2","'-'","spg_ep3.denestpro3","'-'","spg_ep4.denestpro4","'-'","spg_ep5.denestpro5");
		
		$consulta = " SELECT {$codest} as codest, {$codcompleto} as codcompleto,{$nombre} as nombre".
					" FROM {$this->_table} ".
					" INNER JOIN spg_ep5 ".
					"	ON spg_ep5.codemp = {$this->_table}.codemp ".
					"	AND {$codcompleto} = {$this->_table}.codintper ".
					"	AND spg_ep5.codestpro1=substr(codintper,1,25)  ".
					"	AND spg_ep5.codestpro2=substr(codintper,26,25) AND spg_ep5.codestpro3=substr(codintper,51,25) ".
					"	AND spg_ep5.codestpro4=substr(codintper,76,25) AND spg_ep5.codestpro5=substr(codintper,101,25)".
					" INNER JOIN spg_ep1 ON spg_ep1.codemp={$this->_table}.codemp AND spg_ep1.codestpro1=substr(codintper,1,25)  ".
					" INNER JOIN spg_ep2 ON spg_ep2.codemp={$this->_table}.codemp AND spg_ep2.codestpro1=substr(codintper,1,25)  ".
					"	AND spg_ep2.codestpro2=substr(codintper,26,25) ".
					" INNER JOIN spg_ep3 ON spg_ep3.codemp={$this->_table}.codemp AND spg_ep3.codestpro1=substr(codintper,1,25)  ".
					"	AND spg_ep3.codestpro2=substr(codintper,26,25) AND spg_ep3.codestpro3=substr(codintper,51,25) ".
					" INNER JOIN spg_ep4 ON spg_ep4.codemp={$this->_table}.codemp AND spg_ep4.codestpro1=substr(codintper,1,25)  ".
					"	AND spg_ep4.codestpro2=substr(codintper,26,25) AND spg_ep4.codestpro3=substr(codintper,51,25) ".
					"	AND spg_ep4.codestpro4=substr(codintper,76,25)".
					" WHERE {$this->_table}.codemp ='{$this->codemp}' ".
					" AND codusu='{$this->codusu}' AND codsis='SPG' ".
					" AND enabled=1 ";	
		
		$result = $this->conexionbd->Execute($consulta);
		return $result;		
	}
		
		
	
/***********************************************************************************
* @Función que inserta los permisos de un usuario para: una constante, una nomina, 
* un personal
* @parametros: 
* @retorno:
* @fecha de creación: 09/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 
* @descripción: 
* @autor: Ing. 
***********************************************************************************/
	function incluirPermisosInternosGrupos()
	{
		$this->mensaje='Incluyo los permisos del grupo '.$this->nomgru.' para los usuarios del grupo ';
		$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
					" SELECT sss_usuarios_en_grupos.codemp, sss_usuarios_en_grupos.codusu, ".
					"		 sss_permisos_internos_grupos.codsis, sss_permisos_internos_grupos.codintper, ".
					"        sss_permisos_internos_grupos.enabled ".
  					"   FROM sss_usuarios_en_grupos ".
					"  INNER JOIN sss_permisos_internos_grupos ".
					"    ON sss_permisos_internos_grupos.codemp = '$this->codemp' ".
					"   AND sss_permisos_internos_grupos.enabled = 1 ".
					"   AND sss_permisos_internos_grupos.nomgru = '$this->nomgru' ".
					"   AND sss_permisos_internos_grupos.codintper NOT IN (SELECT codintper ". 
					"														 FROM sss_permisos_internos ". 
					"														WHERE sss_permisos_internos.codemp =  sss_permisos_internos_grupos.codemp ".
					"	         											  AND sss_permisos_internos.codsis =  sss_permisos_internos_grupos.codsis ".
					"												          AND sss_permisos_internos.codintper =  sss_permisos_internos_grupos.codintper ".
					"						         						  AND sss_permisos_internos.codemp =  sss_usuarios_en_grupos.codemp ".
					"											  	          AND sss_permisos_internos.codusu =  sss_usuarios_en_grupos.codusu) ".
   					"	AND sss_usuarios_en_grupos.codemp = sss_permisos_internos_grupos.codemp ".
					"   AND sss_usuarios_en_grupos.nomgru = sss_permisos_internos_grupos.nomgru";
		$result = $this->conexionbd->Execute($consulta);
		if ($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}	
		$this->incluirSeguridad('INSERTAR',$this->valido);		
	}
		
/***********************************************************************************
* @Función que elimina los permisos de un usuario para: una constante, una nomina, 
* un personal
* @parametros: 
* @retorno:
* @fecha de creación: 09/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 
* @descripción: 
* @autor: Ing. 
***********************************************************************************/
	function eliminarPermisosInternosGrupos()
	{				
		$this->mensaje='Elimino los permisos del grupo '.$this->nomgru.' para el Usuario '.$this->codusu.' ';
		
		$consulta = " UPDATE {$this->_table} ".
					"    SET enabled = 0 ".
					"  WHERE codemp = '{$this->codemp}' ".
					"    AND codusu = '{$this->codusu}'".
					"    AND enabled = 1 ". 
		            "    AND codintper IN (SELECT codintper ".
		            "						 FROM sss_permisos_internos_grupos ".
					"					    WHERE sss_permisos_internos_grupos.codemp= '$this->codemp'".
					"						  AND sss_permisos_internos_grupos.nomgru= '$this->nomgru'".
					"						  AND sss_permisos_internos_grupos.codemp= sss_permisos_internos.codemp".
					"						  AND sss_permisos_internos_grupos.codsis= sss_permisos_internos.codsis".
					"						  AND sss_permisos_internos_grupos.codintper= sss_permisos_internos.codintper)";
		$result = $this->conexionbd->Execute($consulta);
		if ($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}	
		$this->incluirSeguridad('ELIMINAR',$this->valido);		
	}

/***********************************************************************************
* @Función que elimina los permisos de un usuario para: una constante, una nomina, 
* un personal
* @parametros: 
* @retorno:
* @fecha de creación: 09/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 
* @descripción: 
* @autor: Ing. 
***********************************************************************************/
	function eliminarTodosPermisosInternosGrupos()
	{				
		$this->mensaje='Elimino los permisos del grupo '.$this->nomgru.' para todos los Usuarios';
		$concat = $this->conexionbd->concat('codsis','codintper');
		
		$consulta = " UPDATE {$this->_table} ".
					"    SET enabled = 0 ".
					"  WHERE codemp = '{$this->codemp}' ".
					"    AND enabled = 1 ". 
					"    AND {$concat} IN (SELECT {$concat} ".
		            "						 FROM sss_permisos_internos_grupos ".
					"					    WHERE sss_permisos_internos_grupos.codemp= '$this->codemp'".
					"						  AND sss_permisos_internos_grupos.nomgru= '$this->nomgru'".
					"						  AND sss_permisos_internos_grupos.codemp= sss_permisos_internos.codemp".
					"						  AND sss_permisos_internos_grupos.codsis= sss_permisos_internos.codsis".
					"						  AND sss_permisos_internos_grupos.codintper= sss_permisos_internos.codintper)".
					"   AND codusu IN (SELECT codusu ".
					"					    FROM sss_usuarios_en_grupos ".
					"					   WHERE sss_usuarios_en_grupos.codemp = '{$this->codemp}' ".
					"					     AND sss_usuarios_en_grupos.nomgru = '$this->nomgru') ";
					
		$result = $this->conexionbd->Execute($consulta);
		if ($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}	
		$this->incluirSeguridad('ELIMINAR',$this->valido);		
	}
		
/***********************************************************************************
* @Función que elimina los permisos de un usuario para: una constante, una nomina, 
* un personal
* @parametros: 
* @retorno:
* @fecha de creación: 09/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 
* @descripción: 
* @autor: Ing. 
***********************************************************************************/
	function actualizarPermisosInternosGrupos()
	{		
		$this->mensaje='Actualizo los permisos los usuarios del grupo '.$this->nomgru.' ';
		
		$consulta = " UPDATE {$this->_table} ".
					"	 SET  ".
					"	     enabled=(SELECT enabled ".  
		            "                   FROM sss_permisos_internos_grupos ".
					"                  WHERE sss_permisos_internos_grupos.nomgru = '$this->nomgru' ".
					"                    AND {$this->_table}.codemp=sss_permisos_internos_grupos.codemp ". 
      				"				     AND {$this->_table}.codsis=sss_permisos_internos_grupos.codsis ".
      				"				     AND {$this->_table}.codintper=sss_permisos_internos_grupos.codintper) ".
					"  WHERE {$this->_table}.codemp= '$this->codemp'".
		            "    AND {$this->_table}.codusu IN (SELECT codusu ".
		            "									  FROM sss_usuarios_en_grupos ".
					"									 WHERE sss_usuarios_en_grupos.codemp= '$this->codemp'".
					"									   AND sss_usuarios_en_grupos.nomgru= '$this->nomgru')".
		            "    AND {$this->_table}.codintper IN (SELECT codintper ".
		            "									     FROM sss_permisos_internos_grupos ".
					"									    WHERE sss_permisos_internos_grupos.codemp= '$this->codemp'".
					"									      AND sss_permisos_internos_grupos.nomgru= '$this->nomgru'".
					"										  AND sss_permisos_internos_grupos.codemp= sss_permisos_internos.codemp".
					"										  AND sss_permisos_internos_grupos.codsis= sss_permisos_internos.codsis".
					"										  AND sss_permisos_internos_grupos.codintper= sss_permisos_internos.codintper)";
		$result = $this->conexionbd->Execute($consulta);
		if ($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
		}	
		$this->incluirSeguridad('MODIFICAR',$this->valido);		
	}	

	
/***********************************************************************************
* @Función que elimina Fisicamente los permisos asignados a usuarios  
* @parametros: 
* @retorno:
* @fecha de creación: 12/10/2015
* @autor: Ing. Yesenia Moreno
*************************************************************************************/
	function eliminarFisicamente()
	{
		$this->objDerechos = new DerechosUsuario();
		$this->objDerechos->codemp = $this->codemp;
		
		$this->objDerechos->criterio[0]['operador'] = $this->criterio[0]['operador'];
		$this->objDerechos->criterio[0]['criterio'] = $this->criterio[0]['criterio'];
		$this->objDerechos->criterio[0]['condicion'] = $this->criterio[0]['condicion'];
		$this->objDerechos->criterio[0]['valor'] = $this->criterio[0]['valor'];
		
		$this->objDerechos->criterio[1]['operador'] = $this->criterio[1]['operador'];
		$this->objDerechos->criterio[1]['criterio'] = $this->criterio[1]['criterio'];
		$this->objDerechos->criterio[1]['condicion'] = $this->criterio[1]['condicion'];
		$this->objDerechos->criterio[1]['valor'] = $this->criterio[1]['valor'];
		
		$this->objDerechos->criterio[2]['operador'] = $this->criterio[2]['operador'];
		$this->objDerechos->criterio[2]['criterio'] = $this->criterio[2]['criterio'];
		$this->objDerechos->criterio[2]['condicion'] = $this->criterio[2]['condicion'];
		$this->objDerechos->criterio[2]['valor'] = $this->criterio[2]['valor'];
			
			
		$this->mensaje='Elimino el permiso '.$this->codintper.' al Usuario '.$this->codusu;
		try
		{
            $this->objDerechos->nomfisico = $this->nomfisico;        
            $this->objDerechos->codusu = $this->codusu;        
			 
			$this->objDerechos->eliminarFisicamente();          
			if ($this->objDerechos->valido)
			{
				$consulta = " DELETE FROM {$this->_table} ".
							" WHERE codemp='{$this->codemp}'";
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
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje='Error al eliminar el permiso '.$this->codintper.' al Usuario '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
		}
		$this->incluirSeguridad('ELIMINAR',$this->valido);		
	}


/***********************************************************************************
* @Función que elimina Fisicamente los permisos asignados a usuarios  
* @parametros: 
* @retorno:
* @fecha de creación: 12/10/2015
* @autor: Ing. Yesenia Moreno
*************************************************************************************/
	function copiarPermisosInternos()
	{
		$this->selecionarConexion ();
		$this->mensaje='Incluyo los permisos internos del usuario '.$this->codusuori.'  para el Usuario '.$this->codusu;
		try
		{
		
			$concat = $this->conexionbd->concat('codemp','codsis','codintper');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}',codsis,codintper,enabled ".
			            "   FROM {$this->_table} ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusuori}' ".
						"    AND enabled=1 ".
					    "    AND ".$concat." NOT IN (SELECT ".$concat." ".
						"                              FROM {$this->_table} ".
						"							  WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concat." IN (SELECT ".$concat." ".
					    "                          FROM {$this->_table} ".
					    "						  WHERE codusu = '{$this->codusuori}' ".
				  	    "                           AND enabled=1)	";
			$result = $this->conexionbd->Execute($consulta);			
		}
		catch (exception $e) 
		{
			$this->mensaje='Error al incluir/actualizar los permisos internos al '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}	
		$this->incluirSeguridad('INSERTAR',$this->valido);		
	}
	
	function agregarTodos()
	{
		$this->selecionarConexion ();
		$this->mensaje='Incluyo los permisos internos del usuario '.$this->codusuori.'  para el Usuario '.$this->codusu;
		try
		{
			//NOMINA 
			$concata = $this->conexionbd->concat('codemp',"'SNO'",'codnom');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','SNO',codnom,1 ".
			            "   FROM sno_nomina ".
						"  WHERE codemp = '{$this->codemp}' ".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                          FROM sno_nomina ".
					    "						  WHERE codemp = '{$this->codemp}')	";
			$result = $this->conexionbd->Execute($consulta);			

			//CONSTANTES 
			$concata = $this->conexionbd->concat('codemp',"'SNO'",'codnom',"'-'",'codcons');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			$concat = $this->conexionbd->concat('codnom',"'-'",'codcons');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','SNO',".$concat.",1 ".
			            "   FROM sno_constante ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND conespseg='1'".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                           FROM sno_constante ".
						"					       WHERE codemp = '{$this->codemp}' ".
					    "						     AND conespseg='1')	";
			$result = $this->conexionbd->Execute($consulta);			

			//TIPO PERSONAL 
			$concata = $this->conexionbd->concat('codemp',"'SNO'",'codtippersss');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','SNO',codtippersss,1 ".
			            "   FROM sno_tipopersonalsss ".
						"  WHERE codemp = '{$this->codemp}' ".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                          FROM sno_tipopersonalsss ".
					    "						  WHERE codemp = '{$this->codemp}')	";
			$result = $this->conexionbd->Execute($consulta);			
			
			//PRESUPUESTO 
			$concata = $this->conexionbd->concat('codemp',"'SPG'",'codestpro1','codestpro2','codestpro3','codestpro4','codestpro5','estcla');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			$concat = $this->conexionbd->concat('codestpro1','codestpro2','codestpro3','codestpro4','codestpro5','estcla');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','SPG',".$concat.",1 ".
			            "   FROM spg_ep5 ".
						"  WHERE codemp = '{$this->codemp}' ".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                          FROM spg_ep5 ".
					    "						  WHERE codemp = '{$this->codemp}')	";
			$result = $this->conexionbd->Execute($consulta);			
			
			//UNIDAD EJECUTORA CXP 
			$concata = $this->conexionbd->concat('codemp',"'CXP'",'coduniadm');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','CXP',coduniadm,1 ".
			            "   FROM spg_unidadadministrativa ".
						"  WHERE codemp = '{$this->codemp}' ".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                          FROM spg_unidadadministrativa ".
					    "						  WHERE codemp = '{$this->codemp}')	";
			$result = $this->conexionbd->Execute($consulta);			
			
			//UNIDAD EJECUTORA SEP 
			$concata = $this->conexionbd->concat('codemp',"'SEP'",'coduniadm');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','SEP',coduniadm,1 ".
			            "   FROM spg_unidadadministrativa ".
						"  WHERE codemp = '{$this->codemp}' ".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                          FROM spg_unidadadministrativa ".
					    "						  WHERE codemp = '{$this->codemp}')	";
			$result = $this->conexionbd->Execute($consulta);			

			//UNIDAD EJECUTORA SOC 
			$concata = $this->conexionbd->concat('codemp',"'SOC'",'coduniadm');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','SOC',coduniadm,1 ".
			            "   FROM spg_unidadadministrativa ".
						"  WHERE codemp = '{$this->codemp}' ".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                          FROM spg_unidadadministrativa ".
					    "						  WHERE codemp = '{$this->codemp}')	";
			$result = $this->conexionbd->Execute($consulta);			
			
			//ALMACEN 
			$concata = $this->conexionbd->concat('codemp',"'SIV'",'codalm');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','SIV',codalm,1 ".
			            "   FROM siv_almacen ".
						"  WHERE codemp = '{$this->codemp}' ".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                          FROM siv_almacen ".
					    "						  WHERE codemp = '{$this->codemp}')	";
			$result = $this->conexionbd->Execute($consulta);			
			
			//CENTROS DE COSTO
			$concata = $this->conexionbd->concat('codemp',"'CFG'",'codcencos');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','CFG',codcencos,1 ".
			            "   FROM sigesp_cencosto ".
						"  WHERE codemp = '{$this->codemp}' ".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                          FROM sigesp_cencosto ".
					    "						  WHERE codemp = '{$this->codemp}')	";
			$result = $this->conexionbd->Execute($consulta);			
			
			//CUENTAS DE BANCO
			$concata = $this->conexionbd->concat('codemp',"'SCB'",'codban',"'-'",'ctaban');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			$concat = $this->conexionbd->concat('codban',"'-'",'ctaban');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','SCB',".$concat.",1 ".
			            "   FROM scb_ctabanco ".
						"  WHERE codemp = '{$this->codemp}' ".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                          FROM scb_ctabanco ".
					    "						  WHERE codemp = '{$this->codemp}')	";
			$result = $this->conexionbd->Execute($consulta);			
			
			//ODI
			$concata = $this->conexionbd->concat('codemp',"'SRH'",'nroreg');
			$concatb = $this->conexionbd->concat('codemp','codsis','codintper');
			
			$consulta = " INSERT INTO {$this->_table} (codemp,codusu,codsis,codintper,enabled) ".
						" SELECT codemp,'{$this->codusu}','SRH',nroreg,1 ".
			            "   FROM srh_odi ".
						"  WHERE codemp = '{$this->codemp}' ".
					    "    AND ".$concata." NOT IN (SELECT ".$concatb." ".
						"                              FROM {$this->_table} ".
						"							   WHERE codusu = '{$this->codusu}')";
			$result = $this->conexionbd->Execute($consulta);

			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND codusu = '{$this->codusu}'".
				        "    AND ".$concatb." IN (SELECT ".$concata." ".
					    "                          FROM srh_odi ".
					    "						  WHERE codemp = '{$this->codemp}')	";
			$result = $this->conexionbd->Execute($consulta);			
		}
		catch (exception $e) 
		{
			$this->mensaje='Error al incluir/actualizar los permisos internos al '.$this->codusu.' '.$this->conexionbd->ErrorMsg();
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
****************************************************************************************/
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
		$objEvento->codemp = $this->codemp;
		$objEvento->codsis = 'SSS';
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $evento;
		$objEvento->desevetra = $this->mensaje;
		$objEvento->incluirEvento();
		unset($objEvento);
		}
	}
}	
?>