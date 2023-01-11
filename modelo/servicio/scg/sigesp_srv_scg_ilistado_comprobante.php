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

interface iListadoComp 
{
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarCentroCostos();
	
	/**
	 * @author Ing. Carlos Zambrano
	 * @desc Metodo que busca un codigo de parametro para insertar
	 * @param string $codemp - codigo de empresa
	 * @return string $codigo - codigo de parametro de clasificacion
	 */
	public function buscarComprobantes($ls_comprobante,$ls_procedencia);		
}