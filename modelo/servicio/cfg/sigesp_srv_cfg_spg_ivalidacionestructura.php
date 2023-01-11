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

interface IValidacionEstructura {
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que obtiene todas las las estructuras presupuestarias a las cuales aplica validacion.
	 * @param array $dataEmpresa - arreglo con los datos de la empresa
	 * @return retorna resultset de Adodb con el grupo de cuentas perteneciente a las estructura indicada.
	 */
	public function buscarEstructurasValidacion($dataEmpresa);
	
	/**
	 * @author Ing. Gerardo Cordero
	 * @desc Metodo que guarda la configuracion de las estructuras a las cuales aplica validacion para la 
	 *       disponibilidad.
	 * @param string $codemp    - codigo de la empresa
	 * @param json   $arrjson   - objeto json con los parametros captados en la vista para realizar la operacion.
	 * @param array  $arrEvento - arreglo con los datos para el log de seguridad
	 * @return retorna true si la configuracion fue guardada.
	 */
	public function grabarEstructurasValidar($codemp, $arrjson);
	
		
}

?>