<?php
/***********************************************************************************
* @Modelo para proceso de asignación de los permisos internos a los grupos
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

class PermisosInternosGrupo extends DaoGenerico
{
	public $mensaje;
	public $evento;
	public $valido = true;
	public $existe = true;
	public $seguridad = true;
	public $codsis;
	public $nomfisico;
	public $codest;
	public $codmenu;
	public $objDerechos;
	public $criterio = array();
	public $usuarioeliminar = array();
	public $grupodetalle = array();
	public $iniciartransaccion=true;
	public $fisicamente=false;
	private $conexionbd;

	public function __construct()
	{
		parent::__construct ( 'sss_permisos_internos_grupos' );
		$this->conexionbd = $this->obtenerConexionBd(); 
		$this->codmenu='';
	}

/***********************************************************************************
* @Función que inserta los permisos de un grupo 
* @parametros: 
* @retorno:
* @fecha de creación: 21/10/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 
* @descripción: 
* @autor: Ing. 
***********************************************************************************/
	function incluirPermisosInternos()
	{
		$this->mensaje = 'Incluyo el permiso para el grupo '.$this->nomgru.'en el sistema'.$this->codsis;
		$this->verificarPermiso();
		if ($this->existe==false)
		{
			$consulta= "INSERT INTO {$this->_table} (codemp,nomgru,codsis,codintper,enabled) VALUES ('{$this->codemp}','{$this->nomgru}','{$this->codsis}','$this->codintper',1)	";
			$result = $this->conexionbd->Execute($consulta);
			if ($this->conexionbd->HasFailedTrans())
			{
				$this->valido  = false;	
				$this->mensaje='Error al incluir el permiso para el grupo '.$this->nomgru.'en el sistema'.$this->codsis.$this->conexionbd->ErrorMsg();
			}	
		}
		else
		{
			$consulta = " UPDATE {$this->_table} ".
						"    SET enabled = 1 ".
						"  WHERE codemp = '{$this->codemp}' ".
						"    AND nomgru = '{$this->nomgru}'".
						"    AND codsis = '{$this->codsis}' ". 
						"    AND codintper = '{$this->codintper}'";			
			$result = $this->conexionbd->Execute($consulta);
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
	function verificarPermiso()
	{
		try 
		{ 
			$consulta=" SELECT codemp,codsis,nomgru,codintper ".
					  "   FROM {$this->_table} ".
					  "  WHERE codemp= '{$this->codemp}' ".
					  "    AND nomgru= '{$this->nomgru}' ".
					  "    AND codsis='{$this->codsis}' ".
					  "    AND codintper ='{$this->codintper}'";
			$result = $this->conexionbd->Execute($consulta);
			if ($result->EOF)
			{		
				$this->existe = false;		
			}
			
		}
		catch (exception $e) 
		{
			$this->valido  = false;	
			$this->mensaje = 'Error al consultar el permiso para el Grupo '.$this->nomgru.' en el Sistema'.$this->codsis.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
		}
	}		
	
/***********************************************************************************
* @Función que busca los permisos de un grupo
* @parametros: 
* @retorno: 
* @fecha de creación: 03/11/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function obtenerPermisos() 
	{
		//$this->conexionbd->debug = 1;
		if ($this->tabla=='sno_constante')
		{
			$codigo = $this->conexionbd->Concat(codnom,"'-'",codcons);
			
			$consulta = " SELECT substr(codintper,0,5) as codnom, 						".
						"	substr(codintper,6,10) as {$this->campo},					".
						"	{$this->_table}.codsis,										".
					    "	(SELECT {$this->campo2} FROM {$this->tabla} 				".
					    "		WHERE {$this->tabla}.codemp={$this->_table}.codemp 		".
					    "		AND $codigo={$this->_table}.codintper 					".
					    " 		GROUP BY {$this->campo2}) as {$this->campo2}			".
					    " FROM {$this->_table}											".
					    "  WHERE codemp= '{$this->codemp}'".
					    "    AND nomgru= '{$this->nomgru}'".
					    "    AND codsis= '{$this->sistema}'".
					    "    AND enabled= 1".
					    "    AND codintper IN (SELECT {$codigo}  ".
					    "						 FROM {$this->tabla}".
					    "						WHERE codemp='{$this->codemp}')";
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
			$consulta = " SELECT codintper as {$this->campo},{$this->_table}.codsis,".
					    "        (SELECT {$this->campo2} FROM {$this->tabla} ".
					    "          WHERE {$this->tabla}.codemp={$this->_table}.codemp ".
					    "            AND {$this->tabla}.{$this->campo}={$this->_table}.codintper ) as {$this->campo2}".
					    "  FROM {$this->_table}".
					    "  WHERE codemp= '{$this->codemp}'".
					    "    AND nomgru= '{$this->nomgru}'".
					  //  "    AND codsis= '{$this->sistema}'".
					  	" $sistema	".
					    "    AND enabled= 1".
					    "    AND codintper IN (SELECT {$this->campo} ".
					    "						 FROM {$this->tabla}".
					    "						WHERE codemp='{$this->codemp}')";
			
		}
		$result = $this->conexionbd->Execute($consulta);
		return $result;				
	}

	
/***********************************************************************************
* @Función que elimina los permisos asignados a grupos  
* @parametros: 
* @retorno:
* @fecha de creación: 03/11/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación: 
* @descripción: 
* @autor: Ing. 
***********************************************************************************/			
	public function eliminarLocal()
	{
		$this->mensaje='Elimino el permiso '.$this->codintper.' al Grupo '.$this->nomgru;
		if ($this->iniciartransaccion)
		{
			$this->conexionbd->StartTrans();
		}
		try
		{
			if ($this->fisicamente)
			{
				$consulta = "DELETE  ".
							"  FROM  {$this->_table} ".
							" WHERE codemp='{$this->codemp}'  ";
			}
			else
			{
				$consulta = " UPDATE {$this->_table} ".
							"    SET enabled = 0 ".
							" WHERE codemp='{$this->codemp}'  ";
			}			
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
			$this->mensaje='Error al eliminar el permiso '.$this->codintper.' al Grupo '.$this->nomgru.' '.$this->conexionbd->ErrorMsg();
		}
		if ($this->iniciartransaccion)
		{
			$this->conexionbd->CompleteTrans();
		}
		$this->incluirSeguridad('ELIMINAR',$this->valido);			
	}
	
	
/***********************************************************************************
* @Función que busca los permisos de un grupo para una estructura presupuestaria.
* @parametros: 
* @retorno: 
* @fecha de creación: 03/11/2008.
* @autor: Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
************************************************************************************/		
	function obtenerEstPre() 
	{
		//$this->conexionbd->debug = 1;
		
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
					" AND nomgru='{$this->nomgru}' AND codsis='SPG' ".
					" AND enabled=1 ";	
		
		$result = $this->conexionbd->Execute($consulta);
		return $result;		
	
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