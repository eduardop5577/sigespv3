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

interface IComprobante
{
		
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si existe un comprobante
	 * @param string $codemp - codigo de empresa
	 * @param string $procede - procede
	 * @param string $comprobante - nro de comprobante
	 * @param string $codban - codigo de banco
	 * @param string $ctaban - cuenta de banco
	 * @return boolean $existe - Si existe el comprobante o no
	 */	
	public function existeComprobante($codemp,$procede,$comprobante,$codban,$ctaban);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si existe un procede
	 * @param string $codemp - codigo de empresa
	 * @param string $procede - procede
	 * @return boolean $existe - Si existe la procedencia
	 */	
	public function existeProcedencia($procede);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si la informaci�n del comprobante esta correcta
	 * @return boolean $valido - Si la informaci�n del comprobante es v�lida
	 */	
	public function validarComprobante($arrdetallespg,$arrdetallescg,$arrdetallespi);		

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene los detalles de un comprobante presupuestario
	 * @param arreglo $arrdetallespg - Arreglo con la informaci�n del detalle presupuestario de gasto
	 * @param arreglo $arrdetallescg - Arreglo con la informaci�n del detalle contable
	 * @param arreglo $arrdetallespi - Arreglo con la informaci�n del detalle presupuestario de ingreso
	 * @param string  $tipoevento    - tipo de evento si es para normal � si es para anular
	 * @param string  $fechaanula    - Fecha de anulaci�n para el caso de que el tipo evento sea de anulaci�n 
	 * @param string  $procedeanula  - Procede de anulaci�n para el caso de que el tipo evento sea de anulaci�n
	 * @param string  $conceptoanula - Concepto de anulaci�n para el caso de que el tipo evento sea de anulaci�n
	 * @return boolean $valido - Si se obtuvieron los detalles de manera exitosa.
	 */
	public function cargarDetallesComprobante($tipoevento='',$fechaanula='',$procedeanula='',$conceptoanula='');	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que guarda un comprobante
	 * @param arreglo $arrcabecera - Arreglo con la informaci�n de la cabecera
	 * @param arreglo $arrdetallespg - Arreglo con la informaci�n del detalle presupuestario de gasto
	 * @param arreglo $arrdetallescg - Arreglo con la informaci�n del detalle contable
	 * @param arreglo $arrdetallespi - Arreglo con la informaci�n del detalle presupuestario de ingreso
	 * @param  arreglo $arrevento - Arreglo con la informaci�n del evento que se est� ejecutando 
	 * @return boolean $valido - Si el comprobante se guardo de manera exitosa
	 */
	public function guardarComprobante($arrcabecera,$arrdetallespg,$arrdetallescg,$arrdetallespi,$arrevento);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que guarda un comprobante
	 * @param arreglo $arrcabecera - Arreglo con la informaci�n de la cabecera
	 * @param  arreglo $arrevento - Arreglo con la informaci�n del evento que se est� ejecutando
	 * @return boolean $valido - Si se elimino el comprobante de manera exitosa
	 */
	public function eliminarComprobante($arrcabecera,$arrevento);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que guarda un comprobante de anulado
	 * @param arreglo $arrcabecera - Arreglo con la informaci�n de la cabecera
	 * @param string  $fechaanula    - Fecha de anulaci�n  
	 * @param string  $procedeanula  - Procede de anulaci�n 
	 * @param string  $conceptoanula - Concepto de anulaci�n 
	 * @param arreglo $arrevento - Arreglo con la informaci�n del evento que se est� ejecutando 
	 * @return boolean $valido - Si el comprobante se guardo de manera exitosa
	 */
	public function anularComprobante($arrcabecera,$fechaanula,$procedeanula,$conceptoanula,$arrevento);
	
	public function buscarCodigoComprobante($codemp);
}
?>