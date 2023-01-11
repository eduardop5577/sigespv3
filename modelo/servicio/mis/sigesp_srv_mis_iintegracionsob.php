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

interface IIntegracionSOB {
	 
	 /**
	 * @author Neneskha Salas
  * @desc Metodo que busca las anticipaciones de Obras por contabilizar
	 * @param string $codcon - codigo del contrato
	 * @param string $codant - Codigo de la anticipacion de la obra
	 * @param date $feccon - Fecha de contrato
	 * @param date $fecant - Fecha de de anticipacion
	 * @param date $fecant - Fecha de de contabilizacion
	 */
//	public function buscarContabilizadoSobant($codcon,$codant,$feccon,$fecant, $fechaconta);
	
	
    /**
	 * @author Neneskha Salas
   * @desc Metodo que busca los reversos de anticipacion de Obras 
	 * @param string $codcon - codigo del contrato
	 * @param string $codant - Codigo de la anticipacion de la obra
	 * @param date $feccon - Fecha de contrato
	 * @param date $fecant - Fecha de de anticipacion
	 */
//	public function buscarRevContabilizadoSobant($codcon,$codant,$feccon,$fecant);	
}
?>