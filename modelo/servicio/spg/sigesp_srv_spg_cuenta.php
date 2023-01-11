<?php
/*****************************************************************************
* @Modelo para las funciones de cuentas spg.
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/base/librerias/php/general/sigesp_lib_conexion.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/modelo/sss/sigesp_dao_sss_registroeventos.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/modelo/sss/sigesp_dao_sss_registrofallas.php');

class Cuenta extends DaoGenerico
{
	var $_table = 'spg_cuentas';
	public $valido = true;
	public $mensaje;
	public $seguridad = true;
	public $codsis;
	public $nomfisico;
	public $criterio;
	public $tipoconsulta;
	public $tipoconexionbd = 'DEFECTO';
	public $codemp;
	public $codestpro1;
	public $codestpro2;
	public $codestpro3;
	public $codestpro4;
	public $codestpro5;
	public $estcla;
	public $spg_cuenta;
	public $denominacion;
	public $sc_cuenta;

	public function __construct()
	{
		parent::__construct ( 'spg_cuentas' );
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
* @Función que Busca uno o todas las cuentas spg
* @parametros: 
* @retorno:
* @fecha de creación: 26/11/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
************************************************************************************/		
	public function leer() 
 	{	
 		$this->seleccionarConexion(); 	
 		try
		{	
			if ($this->tipoconsulta=='todos')
			{
				//esta consulta asi ya que se repite por denominación
				$consulta = " SELECT TRIM(spg_cuenta) as spg_cuenta, sigesp_plan_unico_re.denominacion,1 as valido ".
							" FROM {$this->_table} ".
							" INNER JOIN sigesp_plan_unico_re ON sigesp_plan_unico_re.sig_cuenta=spg_cuentas.spg_cuenta ".
							" WHERE codemp='{$this->codemp}'".
							" AND {$this->_table}.status='C'";
				$agrupar = " GROUP BY spg_cuenta,sigesp_plan_unico_re.denominacion ";
				$ordenar = " ORDER BY spg_cuenta ";
				
			}
			else
			{	
				$consulta = " SELECT TRIM(spg_cuenta) as spg_cuenta, MAX(denominacion) AS denominacion, codestpro1,codestpro2, ".
							" 	codestpro3, codestpro4, codestpro5, MAX(status) AS status, ".
							"	SUM((asignado-(comprometido+precomprometido)+aumento-disminucion)) as disponible, ".
							"	MAX(sc_cuenta) AS sc_cuenta,1 as valido ".
							" FROM {$this->_table} ".
							" WHERE codemp='{$this->codemp}'".
							" AND status='C'";
				$agrupar = " GROUP BY codestpro1,estcla,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta  ";
				$ordenar = " ORDER BY codestpro1,estcla,codestpro2,codestpro3,codestpro4,codestpro5,spg_cuenta ASC ";
			}
			$cadena=" ";
            $total = count($this->criterio);
            for ($contador = 0; $contador < $total; $contador++)
			{
            	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 			               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
            }
            $consulta.= $cadena;
            $consulta.= $agrupar;
            $consulta.= $ordenar;
            $result = $this->conexionbd->Execute($consulta);
		 	return $result;
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar la Estructura Presupuestaria '.$consulta.' '.$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('CONSULTAR',$this->valido);
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

	function obtenerCuentasCatalogoCxp($manejador,$estmodest,$loncod1,$loncod2,$loncod3,$loncod4,$loncod5)
	{
		$cadprogramatica ='';

		if ($manejador=='POSTGRES') {
			if($estmodest==1){
				$cadprogramatica = "(substr(spg_cuentas.codestpro1,25-".$loncod1."+1,length(spg_cuentas.codestpro1))||' - '||substr(spg_cuentas.codestpro2,25-".$loncod2."+1,length(spg_cuentas.codestpro2))||' - '||
			                     	substr(spg_cuentas.codestpro3,25-".$loncod3."+1,length(spg_cuentas.codestpro3))) as programatica ";
			}
			else {
				$cadprogramatica = "(substr(spg_cuentas.codestpro1,25-".$loncod1."+1,length(spg_cuentas.codestpro1))||' - '||substr(spg_cuentas.codestpro2,25-".$loncod2."+1,length(spg_cuentas.codestpro2))||' - '||
			                     	substr(spg_cuentas.codestpro3,25-".$loncod3."+1,length(spg_cuentas.codestpro3))||' - '||substr(spg_cuentas.codestpro4,25-".$loncod4."+1,length(spg_cuentas.codestpro4))||' - '||
			                     	substr(spg_cuentas.codestpro5,25-".$loncod5."+1,length(spg_cuentas.codestpro5))) as programatica ";
			}
		}
		else{
			if($estmodest==1){
				$cadprogramatica = "CONCAT(substr(spg_cuentas.codestpro1,-".$loncod1."),' - ',substr(spg_cuentas.codestpro2,-".$loncod2."),' - ',substr(spg_cuentas.codestpro3,-".$loncod3.")) as programatica ";
			}
			else {
				$cadprogramatica = "CONCAT(substr(spg_cuentas.codestpro1,-".$loncod1."),' - ',substr(spg_cuentas.codestpro2,-".$loncod2."),' - ',substr(spg_cuentas.codestpro3,-".$loncod3.")
											,' - ',substr(spg_cuentas.codestpro4,-".$loncod4."),' - ',substr(spg_cuentas.codestpro5,-".$loncod5.")) as programatica ";
			}
		}
		
		$cadenasql = 	" SELECT                           ".
					"	  spg_cuentas.codestpro1,      ".
					"	  spg_cuentas.codestpro2,      ".
					"	  spg_cuentas.codestpro3,      ".
					"	  spg_cuentas.codestpro4,      ".
					"	  spg_cuentas.codestpro5,      ".
					"	  spg_cuentas.estcla,          ".
					"	  spg_cuentas.sc_cuenta,       ".
					"	  spg_cuentas.spg_cuenta,      ".
					"	  spg_cuentas.denominacion,    ".$cadprogramatica.
		            "	FROM                           ".
					"	  spg_cuentas                  ".
					"	WHERE                          ".
					"	  spg_cuentas.codemp = '".$this->codemp."'".
     				"	  AND spg_cuentas.status = 'C' ".
			        "	  AND spg_cuentas.codestpro1 LIKE '%".$this->codestpro1."%'".
			        "	  AND spg_cuentas.codestpro2 LIKE '%".$this->codestpro2."%'".
			        "	  AND spg_cuentas.codestpro3 LIKE '%".$this->codestpro3."%'".
			        "	  AND spg_cuentas.codestpro4 LIKE '%".$this->codestpro4."%'".
			        "	  AND spg_cuentas.codestpro5 LIKE '%".$this->codestpro5."%'".
			        "	  AND spg_cuentas.estcla LIKE '%".$this->estcla."%'".
      				"     AND spg_cuentas.spg_cuenta LIKE '".$this->spg_cuenta."%'".
     				"     AND spg_cuentas.denominacion LIKE  '%".$this->denominacion."%'".
     				"     AND spg_cuentas.sc_cuenta LIKE '".$this->sc_cuenta."%'".
                    "  ORDER BY 1,2,3,4,5,8 ";        
		return $this->buscarSql($cadenasql);

	}
	
}	
?>