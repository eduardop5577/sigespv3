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

interface itransferencia
{	
	/**
	 * @author Neneskha Salas
	 * @desc Metodo que busca el personal de Nomina de la empresa
     * @param string $codemp - codigo de la empresa
     * @param string $cedperdes - cedula del personal para filtrar desde
	 * @param string $cedperhas - cedula del personal para filtrar hasta
	 */
	
	public function buscarFiltroPersonal($codemp,$cedperdes,$cedperhas);
	
	/**
	 * @author Neneskha Salas
	 * @desc Metodo que transfiere el personal a Beneficiario
	 * @param string $codemp - codigo de la empresa
	 * @param json $arrjson - json con los datos de la interfaz
	 * @param array $arrevento - arreglo con los datos del log
	*/
	
	public function trasferirPersonalBeneficiario($codemp, $arrjson, $arrEvento);
}