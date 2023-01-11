<?php
/***********************************************************************************
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirsrvcfg = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrvcfg."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirsrvcfg."/modelo/servicio/cfg/sigesp_srv_cfg_spg_ivalidacionestructura.php");

class ServicioValidacionEstructura implements IValidacionEstructura
{
	private $daoValidacionEstructura;
	private $daoRegistroEvento;
	private $conexionBaseDatos;
	public $mensajeinsertar;
	public $mensajeeliminar;
	
	public function __construct()
	{
		$this->daoValidacionEstructura = null;
		$this->daoRegistroEvento = null;
		$this->mensajeinsertar='';
		$this->mensajeeliminar='';
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
	}
	
	public function buscarEstructurasValidacion($datosEmpresa)
	{
		switch($datosEmpresa["estmodest"])
		{
			case "1": // Modalidad por Proyecto
				$cadenaSQL = "SELECT SUBSTR(vest.codestpro1,length(vest.codestpro1)-{$datosEmpresa["loncodestpro1"]}) AS codestpro1, ".
				             "       SUBSTR(vest.codestpro2,length(vest.codestpro2)-{$datosEmpresa["loncodestpro2"]}) AS codestpro2, ".
				             "       SUBSTR(vest.codestpro3,length(vest.codestpro3)-{$datosEmpresa["loncodestpro3"]}) AS codestpro3, ".
				             "       vest.codestpro4, vest.codestpro5, vest.estcla, denestpro3 ".
							 "	FROM spg_val_estructura vest ".
							 " INNER JOIN spg_ep3 ep3  ".
							 "    ON vest.codemp=ep3.codemp ".
							 "	 AND vest.codestpro1=ep3.codestpro1 ".
							 "	 AND vest.codestpro2=ep3.codestpro2 ".
							 "	 AND vest.codestpro3=ep3.codestpro3 ".
							 "	 AND vest.estcla=ep3.estcla ".
							 " WHERE vest.codemp='{$datosEmpresa['codemp']}' ";
				break;
				
			case "2": // Modalidad por Programatica
				$cadenaSQL = "SELECT SUBSTR(vest.codestpro1,length(vest.codestpro1)-{$datosEmpresa["loncodestpro1"]}) AS codestpro1, ".
				             "       SUBSTR(vest.codestpro2,length(vest.codestpro2)-{$datosEmpresa["loncodestpro2"]}) AS codestpro2, ".
				             "       SUBSTR(vest.codestpro3,length(vest.codestpro3)-{$datosEmpresa["loncodestpro3"]}) AS codestpro3, ".
				             "       SUBSTR(vest.codestpro4,length(vest.codestpro4)-{$datosEmpresa["loncodestpro4"]}) AS codestpro4, ".
				             "       SUBSTR(vest.codestpro5,length(vest.codestpro5)-{$datosEmpresa["loncodestpro5"]}) AS codestpro5, ".
				             "       vest.estcla, denestpro5 ".
							 "	FROM spg_val_estructura vest ".
							 " INNER JOIN spg_ep5 ep5 ".
							 "    ON vest.codemp=ep5.codemp ".
							 "	 AND vest.codestpro1=ep5.codestpro1 ".
							 "	 AND vest.codestpro2=ep5.codestpro2 ".
							 "	 AND vest.codestpro3=ep5.codestpro3 ".
							 "	 AND vest.codestpro4=ep5.codestpro4 ".
							 "	 AND vest.codestpro5=ep5.codestpro5 ".
							 "	 AND vest.estcla=ep5.estcla ".
							 " WHERE vest.codemp='{$datosEmpresa['codemp']}' ";
				break;
		}
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function grabarEstructurasValidar($codemp, $arrjson)
	{
		$resultado = 0;
		DaoGenerico::iniciarTrans();
		
		$estructuraInsertar = $arrjson->inc_estructuras;
		$numEstInc = count((array)$estructuraInsertar);
		$this->mensajeinsertar = "Inserto la(s) estructura(s) ";
		for ($i = 0; $i < $numEstInc; $i++)
		{
			$this->daoValidacionEstructura = FabricaDao::CrearDAO('N','spg_val_estructura');
			$estructuraInsertar[$i]->codestpro1 = str_pad($estructuraInsertar[$i]->codestpro1,25,0,0);
			$estructuraInsertar[$i]->codestpro2 = str_pad($estructuraInsertar[$i]->codestpro2,25,0,0);
			$estructuraInsertar[$i]->codestpro3 = str_pad($estructuraInsertar[$i]->codestpro3,25,0,0);
			$estructuraInsertar[$i]->codestpro4 = str_pad($estructuraInsertar[$i]->codestpro4,25,0,0);
			$estructuraInsertar[$i]->codestpro5 = str_pad($estructuraInsertar[$i]->codestpro5,25,0,0);
			
			$this->daoValidacionEstructura->setData($estructuraInsertar[$i]);
			$this->daoValidacionEstructura->codemp = $codemp;
			if(!$this->daoValidacionEstructura->incluir(false,'',false,0,true))
			{
				break;
			}
			else
			{
				$this->mensajeinsertar .= "{$estructuraInsertar[$i]->codestpro1} - {$estructuraInsertar[$i]->codestpro2} -  {$estructuraInsertar[$i]->codestpro3} - ".
										   "{$estructuraInsertar[$i]->codestpro4} - {$estructuraInsertar[$i]->codestpro5} ";
			}
			$this->mensajeinsertar .= " de la validacion de estructuras asociada a la empresa {$codemp}";
			unset($this->daoValidacionEstructura);
		}
		if ($i>0)
		{
			$this->mensajeinsertar='';
		}
		$estructuraEliminar = $arrjson->eli_estructuras;
		$numEstInc = count((array)$estructuraEliminar);
		$this->mensajeeliminar = "Elimino la(s) estructura(s) ";
		for ($i = 0; $i < $numEstInc; $i++)
		{
			$this->daoValidacionEstructura = FabricaDao::CrearDAO('N','spg_val_estructura');
			$estructuraEliminar[$i]->codestpro1 = str_pad($estructuraEliminar[$i]->codestpro1,25,0,0);
			$estructuraEliminar[$i]->codestpro2 = str_pad($estructuraEliminar[$i]->codestpro2,25,0,0);
			$estructuraEliminar[$i]->codestpro3 = str_pad($estructuraEliminar[$i]->codestpro3,25,0,0);
			$estructuraEliminar[$i]->codestpro4 = str_pad($estructuraEliminar[$i]->codestpro4,25,0,0);
			$estructuraEliminar[$i]->codestpro5 = str_pad($estructuraEliminar[$i]->codestpro5,25,0,0);
			
			$this->daoValidacionEstructura->setData($estructuraEliminar[$i]);
			$this->daoValidacionEstructura->codemp = $codemp;
			if(!$this->daoValidacionEstructura->eliminar())
			{
				break;
			}
			else
			{
				$this->mensajeeliminar .= "{$estructuraEliminar[$i]->codestpro1} - {$estructuraEliminar[$i]->codestpro2} -  {$estructuraEliminar[$i]->codestpro3} - ".
										   "{$estructuraEliminar[$i]->codestpro4} - {$estructuraEliminar[$i]->codestpro5} ";
				unset($this->daoRegistroEvento);
			}
			$this->mensajeeliminar .= " de la validacion de estructuras asociada a la empresa {$codemp}";
			unset($this->daoValidacionEstructura);
		}
		if ($i>0)
		{
			$this->mensajeeliminar='';
		}
		if (DaoGenerico::completarTrans())
		{
			$resultado = 1;
		}
		return $resultado;
	}
}