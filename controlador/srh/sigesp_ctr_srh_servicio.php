<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once ($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION["sigesp_sitioweb"].'/base/librerias/php/general/sigesp_lib_daogenerico.php');

class ServicioSrh
{
	private $daogenerico;


	public function __construct($tabla='')
	{
		if ($tabla != '')
		{
			$this->daogenerico = new DaoGenerico ($tabla);
		}
	}

	public function getDto($cadenapk)
	{
		$resultado=$this->daogenerico->load($cadenapk);
		if($resultado)
		{
			return $this->daogenerico;
		}
		else
		{
			return $resultado;
		}
	}
	
	public function getDaogenerico()
	{
		return $this->daogenerico;
	}

	public function getCodemp()
	{
		return $this->daogenerico->codemp;
	}

	public function setCodemp($codemp)
	{
		$this->daogenerico->codemp = $codemp;
	}
	
	public function setValoresDefectoEmpresa($arrValores)
	{
		$this->daogenerico->codemp = $arrValores['codemp'];
		$this->daogenerico->ciesem1 = $arrValores['ciesem1'];
		$this->daogenerico->ciesem2 = $arrValores['ciesem2'];
	}

	/***********************************/
	/* Metodos Estandar DAO Generico   */
	/***********************************/

	public static function iniTransaccion()
	{
		DaoGenerico::iniciarTrans ();
	}

	public static function comTransaccion()
	{
		return DaoGenerico::completarTrans ();
	}

	public function incluirDto($dto, $multiusuario=false, $consecutivo="", $validarempresa=true, $longitud = 0)
	{
		$this->pasarDatos ( $dto );
		$resultado=$this->daogenerico->incluir ($multiusuario,$consecutivo,$validarempresa,$longitud);
		return $resultado;
	}

	public function modificarDto($dto,$validarexistencia=false)
	{
		$this->pasarDatos ( $dto );
		try
		{
			return $this->daogenerico->modificar($validarexistencia);
		}
		catch (Exception $e)
		{
			return false;
		}
	}

	public function eliminarDto($dto,$campovalidar='',$valor='')
	{
		$errorNo = '';
		$this->pasarDatos ( $dto );
		try
		{
			if(!$this->daogenerico->eliminar($campovalidar,$valor))
			{
				if($this->daogenerico->errorValidacion)
				{
					$errorNo = '-1';
				}
			}
		}
		catch (Exception $e)
		{
			return false;
		}
		return $errorNo;		
	}

	public function pasarDatos($ObJson)
	{
		$arratributos = $this->daogenerico->getAttributeNames();
		foreach ( $arratributos as $IndiceDAO )
		{
			foreach ( $ObJson as $IndiceJson => $valorJson )
			{
				if ($IndiceJson == $IndiceDAO && $IndiceJson != "codemp")
				{
					$this->daogenerico->$IndiceJson = utf8_decode ( $valorJson );
				} 
			}
		}
	}
	
	public function validarEliminar($campo, $valor, $arrtablaignorar=null) 
	{
		return $this->daogenerico->validarRelacionesPlus($campo, $valor, $arrtablaignorar);
	}

	public function buscarTodos($campoorden="",$tipoorden=0)
	{
		return $this->daogenerico->leerTodos ($campoorden,$tipoorden);
	}

	public function buscarCampo($campo, $valor)
	{
		return $this->daogenerico->buscarCampo ( $campo, $valor );
	}

	public function buscarCampoRestriccion($restricciones,$banderatabla=false,$tabla='')
	{
		return $this->daogenerico->buscarCampoRestriccion($restricciones,$banderatabla,$tabla) ;
	}

	public function buscarSql($cadenasql)
	{
		return $this->daogenerico->buscarSql($cadenasql) ;
	}

	public function concatenarSQL($arreglocadena)
	{
		return $this->daogenerico->concatenarCadena($arreglocadena);
	}

	public function obtenerConexionBd()
	{
		return $this->daogenerico->obtenerConexionBd();
	}
	/***************************************/
	/* Fin Metodos Estandar DAO Generico   */
	/***************************************/
}
?>