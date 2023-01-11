<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

interface IComprobanteApertura 
{
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Método que carga la información de la apertura de cuentas en un data store.
	 * @param string $codemp - codigo de empresa
	 * @param string $codestpro1 - estructura nivel 1
	 * @param string $codestpro2 - estructura nivel 2
	 * @param string $codestpro3 - estructura nivel 3
	 * @param string $codestpro4 - estructura nivel 4
	 * @param string $codestpro5 - estructura nivel 5
	 * @param string $estcla - estatus de clasificacion
	 * @return Resulset Adodb con los datos de las cuentas.
	 */
	public function buscarCuentasApertura($codemp,$codestpro1,$codestpro2,$codestpro3,$codestpro4,$codestpro5,$estcla);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Método que carga la información de las fuentes de financiamiento asiocadas a la estructura y cuenta.
	 * @param string $codemp - codigo de empresa
	 * @param string $codestpro1 - estructura nivel 1
	 * @param string $codestpro2 - estructura nivel 2
	 * @param string $codestpro3 - estructura nivel 3
	 * @param string $codestpro4 - estructura nivel 4
	 * @param string $codestpro5 - estructura nivel 5
	 * @param string $estcla - estatus de clasificacion
	 * @param string $spg_cuenta - cuenta presupuestaria
	 * @return Resulset Adodb con los datos de las fuentes de financiamiento.
	 */
	public function buscarFuentesFinanciamiento($codemp,$codestpro1,$codestpro2,$codestpro3,$codestpro4,$codestpro5,$estcla,$spg_cuenta);
}