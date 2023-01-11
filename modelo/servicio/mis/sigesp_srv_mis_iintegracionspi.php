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

interface IIntegracionSPI {
	 
	 /**
	 * @author Ing. Maryoly Caceres
  * @desc Metodo que busca los comprobantes de modificaciones presupuestaria de ingreso segun filtros
	 * @param string $comprobante - comprobante de la modificacion presupuestaria de ingreso
	 * @param string $procede - procedencia
	 * @param date $fecha - fecha del comprobante
	 * @param string $estatus - estatus del comprobante 
	 */
	public function buscarCmpSpi($comprobante,$procede,$fecha,$estatus);
		
}
?>