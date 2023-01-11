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
require_once ("sigesp_lib_daogenericoplus.php");
require_once ("sigesp_lib_daoregistroevento.php");

abstract class FabricaDao
{	
	public static function CrearDAO($tipodao,$tabla=null,$arrtabla=null,$strPk=null)
	{
		$objeto = null;
		
		switch ($tipodao) {
			//tipo dao normal(una tabla)
			case 'N':
				$objeto = new DaoGenerico($tabla);
				break;
			
			//tipo dao plus (cabecera - detalle)
			case 'P':
				$objeto = new DaoGenericoPlus($tabla,$arrtabla);
				break;
			
			//tipo dao log de transacciones
			case 'L':
				$objeto = new daoRegistroEvento('sss_registro_eventos');
				break;
			
			//tipo dao log de fallas	
			case 'F':
				$objeto = new daoRegistroEvento('sss_registro_fallas');
				break;

			//tipo dao normal(una tabla - cargada con datos)	
			case 'C':
				$objeto = new DaoGenerico($tabla);
				$objeto->load($strPk);				
				break;
		}
		
		return $objeto;
	}
	
}
?>