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

Interface IComprobanteSPG {
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc   Metodo que guarda los detalles presupuestarios de un comprobante 
	 * @param  arreglo $daoComprobante - objeto con la información de la cabcera del comprobante
	 * @param  arreglo $arrdetallespg - Arreglo con la información del detalle presupuestario de gasto
	 * @param  arreglo $arrevento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $valido - Si los detalles presupuestarios de un  comprobante se guardaron de manera exitosa
	 */
	public function guardarDetalleSPG($daoComprobante,$arrdetallespg,$arrevento);

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc   Metodo que elimina los detalles presupuestarios de un comprobante 
	 * @param  arreglo $daoComprobante - objeto con la información de la cabcera del comprobante
	 * @param  arreglo $arrdetallespg - Arreglo con la información del detalle presupuestario de gasto
	 * @param  arreglo $arrevento - Arreglo con la información del evento que se está ejecutando
	 * @return boolean $valido - Si los detalles presupuestarios de un  comprobante se eliminaron de manera exitosa
	 */
	public function eliminarDetalleSPG($daoComprobante,$arrdetallespg,$arrevento);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica que exista una cuenta en un estructura y que sea de movimiento
	 * @param 
	 * @return boolean $valido - Si la cuenta Existe y es de movimiento 
	 */
	public function existeCuenta(); 
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica que exista una cuenta asociada a la fuente de financiamiento.
	 * @param 
	 * @return boolean $valido - Si la cuenta Existe y es de movimiento 
	 */
	public function existeCuentaFuenteFinanciamiento(); 
		
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si se hizo el cierre presupuestario
	 * @param 
	 * @return boolean $valido - Si se hizo el cierre devuelve true;
	 */
	public function existeCierreSPG(); 	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene la operación partiendo del mensaje
	 * @param  string $mensaje - Información con el mensaje de lo que se quiere contabilizar
	 * @return string $operaion - valor de la operacion
	 */
	public function buscarOperacion($mensaje); 	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene el mensaje partiendo de la operacion
	 * @param  string $operacion - Información con la operacion de lo que se quiere contabilizar
	 * @return string $mensaje - valor del mensaje
	 */
	public function buscarMensaje($operacion); 
		
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si existe el movimiento presupuestario
	 * @param  string  $tipo_comp - Tipo de comprobante que se quiere verificar 
	 * @param  double  $monto - monto del movimiento que se esta verificando 
	 * @param  integer $orden - orden del movimiento que se esta verificando 
	 * @return boolean $existe - Si existe el movimiento devuelve true;
	 */
	public function existeMovimiento($tipo_comp); 

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Carga en un arreglo los niveles de las cuentas presupuestarias de gasto  
	 * @param 
	 * @return 
	 */
	public function cargarNiveles(); 	
		
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene el nivel de una cuenta presupuestarias de gasto  
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
	 * @desc Metodo que verifica si la estructura presupuestaria del movimiento requiere de la validacion por estructura
	 * @param  
	 * @return boolean $existe - Si existe la estructura del movimiento devuelve true;
	 */
	public function validarEstructura(); 	
			
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene el monto asignado según el programado
	 * @param  date   $fechavalidacion - fecha en la que se debe validar el asignado
	 * @return double $monto - monto del asignado
	 */
	public function calcularAsignadoProgramado($fechavalidacion);	
			
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene el saldo según la operacion por estructura
	 * @param  date   $fechavalidacion - fecha en la que se debe validar el saldo
	 * @param  string $nivel - nivel de validacion de la estructura
	 * @param  string $operacion - operacion de la cual se quiere obtener el saldo
	 * @return double $monto - monto del saldo segun la operacion y estructura
	 */
	public function calcularSaldoEstructura($fechavalidacion,$operacion);	
			
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene el saldo según la operacion 
	 * @param  date   $fechavalidacion - fecha en la que se debe validar el saldo
	 * @param  string $operacion - operacion de la cual se quiere obtener el saldo
	 * @return double $monto - monto del saldo segun la operacion 
	 */
	public function calcularSaldoRango($fechavalidacion,$operacion);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene el saldo del programado según la operacion 
	 * @param  date   $fechavalidacion - fecha en la que se debe validar el saldo
	 * @param  string $operacion - operacion de la cual se quiere obtener el saldo
	 * @return double $monto - monto del saldo del programado segun la operacion 
	 */
	public function calcularSaldoProgramado($fechavalidacion,$operacion);	
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que obtiene el saldo de cada una de las operaciones y segun el tipo de validacion que tengan configurado
	 * @param  string $status - estatus de la cuenta al cual se le esta verificando el saldo
	 * @param  double $asignado - Monto del saldo del asignado
	 * @param  double $aumento  - Monto del saldo del aumento
	 * @param  double $disminucion - Monto del saldo de la disminucion
	 * @param  double $precomprometido - Monto del saldo del precomprometido
	 * @param  double $comprometido - Monto del saldo del comprometido
	 * @param  double $causado - Monto del saldo del causado
	 * @param  double $pagado - Monto del saldo del pagado
	 * @param  string $tipovalidacion - Tipo de validacion si es a la fecha actual o a la fecha del comprobante
	 * @param  integer $estvalest - estatus si se valida por estructura
	 * @param  string $nivelest - nivel de la validacion por estructura
	 * @return boolean $valido - si no ocurrio ningún error al calcular los saldos devuelve true
	 */
	public function saldoSelect($tipovalidacion='COMPROBANTE');	

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica que los saldos no sobregiren el presupuesto
	 * @param  string $tipovalidacion - Tipo de validacion si es a la fecha actual o a la fecha del comprobante
	 * @return boolean $valido - si no ocurrio ningún error al calcular los saldos devuelve true
	 */								 
	public function saldosAjusta($monto_anterior,$monto_actual,$tipovalidacion='COMPROBANTE');								 
								 
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Actualiza el saldo de las operaciones de la  cuenta 
	 * @param  double $monto_anterior - Monto del saldo anterior
	 * @param  double $monto_actual  - Monto del saldo del movimiento
	 * @param  string $tipovalidacion - Tipo de validacion si es a la fecha actual o a la fecha del comprobante
	 * @return boolean $valido - si no ocurrio ningún error al actualizar los saldos devuelve true
	 */
	public function saldosUpdate($monto_anterior,$monto_actual,$tipovalidacion);	

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Actualiza el saldo de las operaciones de la  cuenta 
	 * @param  double $monto_anterior - Monto del saldo anterior
	 * @param  double $monto_actual  - Monto del saldo del movimiento
	 * @return boolean $valido - si no ocurrio ningún error al actualizar los saldos devuelve true
	 */
	public function saldoActual($monto_anterior,$monto_actual);	

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc   Función que valida que el detalle no tenga asociado un comprobante de ajuste 
	 * @param  arreglo $daoComprobante - objeto con la información de la cabcera del comprobante
	 * @return boolean $existe - Si el detalle tiene un comprobante de ajuste
	 */	
	public function validaIntegridadComprobanteAjuste ($daoComprobante);								 

	/**
	 * @author Ing. Yesenia Moreno
	 * @desc   Función que valida que el detalle no tenga asociado otro comprobante 
	 * @param  arreglo $daoComprobante - objeto con la información de la cabcera del comprobante
	 * @return boolean $existe - Si el detalle tiene un comprobante de ajuste
	 */	
	public function validaIntegridadComprobanteOtros ($daoComprobante);	

	/**
	 * @author Ing. Gerardo Cordero
	 * @desc   Función que crea un objeto detallae spg y carga algunos campos para el uso de funciones
	 *         de calculo de saldo entre otras 
	 * @param  arreglo $arrdetallespg - arreglo con los campos a cargar en el objeto
	 * @return void vacio este metodo no retorna ningun valor
	 */
	public function setDaoDetalleSpg($arrdetallespg);
	
}
?>