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

interface ipersonal
{
	
	/**
	 * @author Ing. Yesenia Moreno de Lang
	 * @desc Metodo que busca un lote de personal
	 * @param string $codemp - codigo de empresa
	 * @param string $codper - codigo de personal
	 * @param string $cedper - Cedula de personal
	 * @param string $nomper - Nombre de Personal
	 * @param string $apeper - Apellido de Personal
	 * @param string $esBeneficiario - Si se desea filtrar si es beneficiario o no
	 */
	public function buscarPersonal($codemp,$codper,$cedper,$nomper,$apeper,$esBeneficiario);
}