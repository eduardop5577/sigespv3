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

interface IModPrePro 
{
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que busca las cuentas presupuestarias asociadas a la estructura
	 * @param string $codemp - codigo de empresa
	 * @param string $codigo - numero de la cuenta presupuestaria
	 * @param string $denominacion - denominacion de la cuenta presupuestaria
	 * @param string $codcontable - cuenta contable asociada a la cuenta presupuestaria
	 * @param string $codestpro1 - estructura nivel 1
	 * @param string $codestpro2 - estructura nivel 2
	 * @param string $codestpro3 - estructura nivel 3
	 * @param string $codestpro4 - estructura nivel 4
	 * @param string $codestpro5 - estructura nivel 5
	 * @param string $estcla - estatus de clasificacion
	 * @return Resulset Adodb con los datos de las cuentas presupuestarias.
	 */ 
	public function buscarCuentasPresupuestarias($codemp,$codigo,$denominacion,$codcontable,$codestpro1,$codestpro2,$codestpro3,$codestpro4,$codestpro5,$estcla);

	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que guardar la modificacion presupuestaria programada
	 * @param string $codemp - codigo de empresa
	 * @param Json $objson - json con los datos de la interfaz
	 * @param Array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function buscarDisponibilidadMensual($codemp,$objson,$arrevento);
}
?>