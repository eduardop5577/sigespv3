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

interface IModComprobante 
{
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que busca los comprobantes existentes segun los filtros de busqueda
	 * @param string $codemp - codigo de empresa
	 * @param string $comprobante - numero del comprobante
	 * @param string $fecdesde - rango de fecha desde
	 * @param string $fechasta - rango de fecha hasta
	 * @return Resulset Adodb con los datos de los comprobantes.
	 */
	public function buscarComprobantes($codemp,$comprobante,$procede,$fecdesde,$fechasta);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que busca las unidades administrativas
	 * @param string $codemp - codigo de empresa
	 * @param string $codigo - codigo de la unidad
	 * @param string $denominacion - denominacion de la unidad
	 * @return Resulset Adodb con los datos de las unidades administrativas.
	 */
	public function buscarUnidadAdministrativa($codemp,$codigo,$denominacion);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que guardar el comprobante de modificacion presupuestaria (rectificacion)
	 * @param string $codemp - codigo de empresa
	 * @param Json $objson - json con los datos de la interfaz
	 * @param Array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function guardar($codemp,$objson,$arrevento);
	
	/**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que elimina el comprobante de modificacion presupuestaria (rectificacion)
	 * @param string $codemp - codigo de empresa
	 * @param Json $objson - json con los datos de la interfaz
	 * @param Array $arrevento - arreglo con los datos del log
	 * @return integer $resultado - numero que indica si el proceso fue efectivo valor 1 y 0
	 */
	public function eliminarLocal($codemp,$objson,$arrevento);
	
	 /**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que verifica si existe un comprobante
	 * @param string $codemp - codigo de empresa
	 * @param string $procede - procede
	 * @param string $comprobante - nro de comprobante
	 * @param string $fecha - fecha
	 * @return boolean $existe - Si existe el comprobante o no
	 */	
	public function existeComprobante($codemp,$procede,$comprobante,$fecha);
	
	 /**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que verifica si existe un procede
	 * @param string $codemp - codigo de empresa
	 * @param string $procede - procede
	 * @return boolean $existe - Si existe la procedencia
	 */	
	public function existeProcedencia($procede);	
	
	 /**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que verifica si la información del comprobante esta correcta
	 * @return boolean $valido - Si la información del comprobante es válida
	 */	
	public function validarComprobante($arrdetallespg);		

	 /**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que obtiene los detalles presupuestario de un comprobante 
	 * @param arreglo $arrdetallespg - Arreglo con la información del detalle presupuestario de gasto
	 * @param string  $tipoevento    - tipo de evento si es para normal ó si es para anular
	 * @param string  $fechaanula    - Fecha de anulación para el caso de que el tipo evento sea de anulación 
	 * @param string  $procedeanula  - Procede de anulación para el caso de que el tipo evento sea de anulación
	 * @param string  $conceptoanula - Concepto de anulación para el caso de que el tipo evento sea de anulación
	 * @return boolean $valido - Si se obtuvieron los detalles de manera exitosa.
	 */
	public function cargarDetallesComprobante($tipoevento='',$fechaanula='',$procedeanula='',$conceptoanula='');	
	
	 /**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que guarda un comprobante
	 * @param arreglo $arrcabecera - Arreglo con la información de la cabecera
	 * @param arreglo $arrdetallespg - Arreglo con la información del detalle presupuestario de gasto
	 * @param  arreglo $arrevento - Arreglo con la información del evento que se está ejecutando 
	 * @return boolean $valido - Si el comprobante se guardo de manera exitosa
	 */
	public function guardarComprobante($arrcabecera,$arrdetallespg,$arrevento,$evento);

	 /**
	 * @author Ing. Maryoly Caceres
	 * @desc Metodo que elimina un comprobante
	 * @param arreglo $arrcabecera - Arreglo con la información de la cabecera
	 * @param  arreglo $arrevento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $valido - Si se elimino el comprobante de manera exitosa
	 */
	public function eliminarComprobante($arrcabecera,$arrevento);	
	
}
?>