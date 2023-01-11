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

interface IRecepcion {
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si una recepcion de documentos Existe 
	 * @param string $codemp - Codigo de Empresa
	 * @param string $numrecdoc - numero de recepcion de documentos
	 * @param string $codtipdoc - tipo de documentos
	 * @param string $codpro - Proveedor
	 * @param string $cedbene - Beneficiario
	 * @return boolean $valido - Si existe la recepcion
	 */
	public function existeRecepcion($codemp,$numrecdoc,$codtipdoc,$codpro,$cedbene);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si la procedencia Existe 
	 * @param string $procede - Procede
	 * @return boolean $valido - Si existe la procedencia
	 */
	public function existeProcedencia($procede);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si la Cuenta presupuestaria  Existe 
	 * @param string $codemp - Codigo de Empresa
	 * @param string $codestpro1 - Estructura presupuestaria nivel 1
	 * @param string $codestpro2 - Estructura presupuestaria nivel 2
	 * @param string $codestpro3 - Estructura presupuestaria nivel 3
	 * @param string $codestpro4 - Estructura presupuestaria nivel 4
	 * @param string $codestpro5 - Estructura presupuestaria nivel 5
	 * @param string $estcla - Estatus de Clasificacion
	 * @param string $spg_cuenta - Cuenta de Presupuesto
	 * @param string $codfuefin - Fuente de Financimiento
	 * @return boolean $valido - Si existe la Cuenta PResupuestaria
	 */
	public function existeCuentaSpg($codemp,$codestpro1,$codestpro2,$codestpro3,$codestpro4,$codestpro5,$estcla,$spg_cuenta,$codfuefin);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si la Cuenta Contable  Existe 
	 * @param string $codemp - Codigo de Empresa
	 * @param string $sc_cuenta - Cuenta Contable
	 * @return boolean $valido - Si existe la Cuenta Contable
	 */
	public function existeCuentaScg($codemp,$sc_cuenta);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si el cargo Existe 
	 * @param string $codemp - Codigo de Empresa
	 * @param string $codcar - Código del Cargo
	 * @return boolean $valido - Si existe la Cuenta Contable
	 */
	public function existeCargo($codemp,$codcar);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que verifica si la Deducción Existe 
	 * @param string $codemp - Codigo de Empresa
	 * @param string $codded - Código de la Deducción
	 * @return boolean $valido - Si existe la Cuenta Contable
	 */
	public function existeDeduccion($codemp,$codded);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Valida que la informacion de la Recepcion este correcta
	 * @param arreglo $arrdetallespg - Arreglo con la información del detalle presupuestario de gasto
	 * @param arreglo $arrdetallescg - Arreglo con la información del detalle contable
	 * @return boolean $valido - Si existe la informacion de la recepcion esta correcta
	 */
	public function validarRecepcion($arrdetallespg,$arrdetallescg);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Valida que se puede eliminar la recepcion de documentos
	 * @return boolean $valido - Si se puede eliminar la recepcion
	 */
	public function verificarRecepcion();

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que guarda una Recepcion de documentos
	 * @param arreglo $arrcabecera - Arreglo con la información de la cabecera
	 * @param arreglo $arrdetallespg - Arreglo con la información del detalle presupuestario de gasto
	 * @param arreglo $arrdetallescg - Arreglo con la información del detalle contable
	 * @param arreglo $arrdetallecargos - Arreglo con la información de los cargos
	 * @param arreglo $arrdetallededucciones - Arreglo con la información de las deducciones
	 * @param  arreglo $arrevento - Arreglo con la información del evento que se está ejecutando 
	 * @return boolean $valido - Si la Recepcion se guardo de manera exitosa
	 */
	public function guardarRecepcion($arrcabecera,$arrdetallespg,$arrdetallescg,$arrdetallecargos,$arrdetallededucciones,$arrevento) ;
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Guarda la información de los detalles presupuestarios de la recepciion
	 * @param arreglo $arrdetallespg - Arreglo con la información del detalle presupuestario de gasto
	 * @return boolean $valido - Si los detalles se guardaron de manera exitosa
	 */
	public function guardarDetalleSpg($arrdetallespg);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Guarda la información de los detalles contables de la recepciion
	 * @param arreglo $arrdetallescg - Arreglo con la información del detalle contable
	 * @return boolean $valido - Si los detalles se guardaron de manera exitosa
	 */
	public function guardarDetalleScg($arrdetallescg);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Guarda la información de los detalles de los cargos de la recepciion
	 * @param arreglo $arrdetallecargos - Arreglo con la información del detalle de los cargos
	 * @return boolean $valido - Si los detalles se guardaron de manera exitosa
	 */
	public function guardarDetalleCargos($arrdetallecargos);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que Guarda la información de los detalles de las deducciones de la recepciion
	 * @param arreglo $arrdetallededucciones - Arreglo con la información del detalle de las deducciones
	 * @return boolean $valido - Si los detalles se guardaron de manera exitosa
	 */
	public function guardarDetalleDeducciones($arrdetallededucciones);

	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que elimina una Recepcion de documentos
	 * @param arreglo $arrcabecera - Arreglo con la información de la cabecera
	 * @param  arreglo $arrevento - Arreglo con la información del evento que se está ejecutando 
	 * @return boolean $valido - Si la Recepcion se elimino de manera exitosa
	 */
	public function eliminarRecepcion($arrcabecera,$arrevento);
	
	 /**
	 * @author Ing. Yesenia Moreno
	 * @desc Metodo que elimina los detalles de la Recepcion de documentos
	 * @param arreglo $tabla - Nombre de la tabla que se quiere eliminar
	 * @return boolean $valido - Si el detalle de la Recepcion se elimino de manera exitosa
	 */
	public function eliminarDetalles($tabla);
}
?>