<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirctrscg = "";
$dirctrscg = dirname(__FILE__);
$dirctrscg = str_replace("\\","/",$dirctrscg);
$dirsrvrpc = str_replace("/modelo/servicio/scg","",$dirctrscg); 
$dirctrscg = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirctrscg."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirctrscg."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirctrscg."/modelo/servicio/scg/sigesp_srv_scg_imovimientos_mes.php");

class servicioMovimientosMes implements iMovimientosMes
{
	private $daoPago;
	private $daoRegistroEvento;
	private $daoRegistroFalla;
	public  $mensaje; 
	public  $valido; 
	
	public function __construct()
	{
		$this->daoPago = null;
		$this->daoRegistroEvento = null;
		$this->daoRegistroFalla  = null;
		$this->mensaje = '';
		$this->valido = true;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarCentroCostos() {
		$this->daoPago = FabricaDao::CrearDAO("N", "sigesp_cencosto");
		$datacencos = $this->daoPago->leerTodos('codcencos',1,'');
		unset($this->daoPago);
		return $datacencos;
	}
	
	 /* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarAnios() {
			
		if($_SESSION["ls_gestor"]=='INFORMIX')
		{
		  $ls_selec="distinct substr(fecsal,1,4) as anuales ";
		}
		else 
		{
		  $ls_selec="distinct substr(cast(fecsal as char(10)),1,4) as anuales ";
		}
		
		$cadenasql= " SELECT $ls_selec ".
					" FROM scg_saldos WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
					" ORDER BY anuales";
	//	echo $cadenasql;
	//	break;		   

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}
}
?>