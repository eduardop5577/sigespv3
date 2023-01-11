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

require_once ("sigesp_lib_daogenerico.php");

class DaoGenericoPlus 
{
	private $objcabecera;
	private $arrnomtabdetalles;
	private $arrobjdetalles;
	public $daoenerico;
	
	public function getCabecera()
	{
		return $this->objcabecera;
	}
	
	public function getDetalles()
	{
		return $this->arrobjdetalles;
	}
	
	public function getInstaciaDetalle($tabdetalle)
	{
		return $objdetalle = new DaoGenerico ( $tabdetalle );
	}
	
	public function __construct($tabcabecera,$arrtabdetalles=null)
	{
		$i=0;
		$this->objcabecera = new DaoGenerico ( $tabcabecera );
		if($arrtabdetalles!=null)
		{
			foreach ($arrtabdetalles as $tabdetalle => $nomtabla)
			{
				$this->arrnomtabdetalles[$i]=$nomtabla;
				$i++;
			}	
		}
	}
	
	public function setData($arrjson,$codemp)
	{
		$this->arrobjdetalles = array();		
		$this->objcabecera->codemp=$codemp;
		$this->setDataDao($this->objcabecera,$arrjson->datoscabecera[0]);
		foreach ($this->arrnomtabdetalles as $tabdetalle => $nomtabla)
		{
			$j=0;
			foreach ($arrjson->$nomtabla as $recdetalle)
			{
				$nombrereal=substr($nomtabla, 4,strlen($nomtabla));
				$this->arrobjdetalles[$nomtabla][$j] = new DaoGenerico ( $nombrereal );
				$this->arrobjdetalles[$nomtabla][$j]->codemp=$codemp;
				$this->setDataDao($this->arrobjdetalles[$nomtabla][$j], $recdetalle);
				$j++;
			}
		}
	}
		
	public function setDataDao($objdao,$ObJson)
	{
		$arratributos = $objdao->getAttributeNames();
		foreach ( $arratributos as $IndiceDAO )
		{
			foreach ( $ObJson as $IndiceJson => $valorJson )
			{
				if ($IndiceJson == $IndiceDAO && $IndiceJson != "codemp")
				{
					$objdao->$IndiceJson = utf8_decode ( $valorJson );
				} 
			}
		}
	}
	
	public function incluirDto()
	{
		$resultado = array();
		DaoGenerico::iniciarTrans ();
		$resultado[0] = $this->objcabecera->modificar ();
		if(DaoGenerico::completarTrans ())
		{
			if(count($this->arrobjdetalles)>0)
			{
				foreach ($this->arrobjdetalles as $nomtabla => $recdetalles)
				{
					switch (substr($nomtabla, 0,3))
					{
						case 'pel'://para eliminar
							DaoGenerico::iniciarTrans ();
							foreach ($recdetalles as $detalle)
							{
								$detalle->eliminar ();
							}
							if(DaoGenerico::completarTrans ())
							{
								$resultado[1] =1;
							}
							else
							{
								return	$resultado[1] =0;
							}
							break;
							
						case 'ins'://solo inserta no modifica si existe
							DaoGenerico::iniciarTrans ();
							foreach ($recdetalles as $detalle)
							{
								$detalle->modificar (true);
							}
							if(DaoGenerico::completarTrans ())
							{
								$resultado[1] =1;
							}
							else
							{
								return	$resultado[1] =0;
							}
							break;
						case 'imo'://inserta y modifica en el caso que exista
							DaoGenerico::iniciarTrans ();
							foreach ($recdetalles as $detalle)
							{
								$detalle->modificar ();
							}
							if(DaoGenerico::completarTrans ())
							{
								$resultado[1] =1;
							}
							else
							{
								return	$resultado[1] =0;
							}
							break;
						case 'esp'://elimina lo existen y luego inserta
							DaoGenerico::iniciarTrans ();
							$this->objcabecera->deleteDetalle(substr($nomtabla, 4,strlen($nomtabla)));
							if(DaoGenerico::completarTrans ())
							{
								DaoGenerico::iniciarTrans ();
								foreach ($recdetalles as $detalle)
								{
									$detalle->modificar ();
								}
								if(DaoGenerico::completarTrans ())
								{
									$resultado[1] =1;
								}
								else
								{
									return	$resultado[1] =0;
								}
							}
							else
							{
								return	$resultado[1] =0;
							}
							
							break;
					}
				}
			}
			else{
				$resultado[1] =1;
			}
		}
		unset($this->arrobjdetalles);
		return $resultado;		
	}
	
	public function eliminarDto($validarelacion = false, $campo = '', $valor = '', $arrtabignorar = null)
	{
		DaoGenerico::iniciarTrans ();
		if($validarelacion)
		{
			$validarelacion = $this->objcabecera->validarRelacionesPlus($campo, $valor, $arrtabignorar);
		}
		
		
		if(!$validarelacion)
		{
			foreach ($this->arrobjdetalles as $recdetalles)
			{
				foreach ($recdetalles as $detalle)
				{
					$detalle->eliminar ();
				}
			}
			
			$this->objcabecera->eliminar ();
			if(DaoGenerico::completarTrans ())
			{
				return 1;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return -1;
		}
	}
}
?>