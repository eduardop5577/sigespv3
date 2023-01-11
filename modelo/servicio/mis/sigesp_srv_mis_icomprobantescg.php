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

Interface IComprobanteSCG {

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc   Metodo que guarda los detalles contables de un comprobante 
	 * @param  arreglo $daoComprobante - objeto con la información de la cabcera del comprobante
	 * @param  arreglo $arrdetallescg - Arreglo con la información del detalle contable
	 * @param  arreglo $arrevento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $valido - Si los detalles contables de un  comprobante se guardaron de manera exitosa
	 */
	public function guardarDetalleSCG($daoComprobante,$arrdetallescg,$arrevento);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Carga en un arreglo los niveles de las cuentas contables
	 * @param 
	 * @return 
	 */
	public function cargarNiveles(); 	
		
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene el nivel de una cuenta contable
	 * @param  string  $cuenta - cuenta el cual se quiere saber el nivel 
	 * @return integer $nivel - Nivel de la cuenta
	 */
	public function obtenerNivel($cuenta); 		
		
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene la cuenta que le sigue según el nivel 
	 * @param  string $cuenta - cuenta el cual se quiere saber el nivel 
	 * @return string $cuenta - cuenta superior segun el nivel.
	 */
	public function obtenerCuentaSiguiente($cuenta); 	
			
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si se hizo el cierre contable
	 * @param 
	 * @return boolean $valido - Si se hizo el cierre devuelve true;
	 */
	public function existeCierreSCG();
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica que exista una cuenta contable y que sea de movimiento
	 * @param 
	 * @return boolean $valido - Si la cuenta Existe y es de movimiento 
	 */
	public function existeCuenta(); 	 			
		
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si existe el movimiento contable
	 * @param  double  $monto - monto del movimiento que se esta verificando 
	 * @param  integer $orden - orden del movimiento que se esta verificando 
	 * @return boolean $existe - Si existe el movimiento devuelve true;
	 */
	public function existeMovimiento();

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Actualiza el saldo de las operaciones de la  cuenta 
	 * @param  double $monto_anterior - Monto del saldo anterior
	 * @param  double $monto_actual  - Monto del saldo del movimiento
	 * @return boolean $valido - si no ocurrio ningún error al actualizar los saldos devuelve true
	 */
	public function saldosUpdate($monto_anterior,$monto_actual);	

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Actualiza el saldo de las operaciones de la  cuenta 
	 * @param  double $monto_anterior - Monto del saldo anterior
	 * @param  double $monto_actual  - Monto del saldo del movimiento
	 * @return boolean $valido - si no ocurrio ningún error al actualizar los saldos devuelve true
	 */
	public function saldoActual($monto_anterior,$monto_actual);		
}	
?>