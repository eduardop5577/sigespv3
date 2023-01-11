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

interface icambioestatus
{	
	/**
	 * @author Neneskha Salas
	 * @desc Metodo que verifica si existe un Proveedor
	 */ 
	public function buscarProveedor($as_cedprov,$as_nomprov,$as_dirprov,$as_rifprov);
        
    /**
     * @author Neneskha Salas
     * @desc Metodo que actualiza todos los registros de proveedores segun el estatus nuevo seleccionado
     * @param string $cod_prodesde - codigo de empresa
	 * @param string $cod_prohasta - json con los datos del proveedor
     * @param int $estprovnew - nuevo estatus que sera asignado al o a los proveedores
	 * @param array $arrevento - arreglo con los datos del log
     * */

	public function cargarProveedores($cod_prodesde,$cod_prohasta,$estprovnew);
	
    /**
     * @author Neneskha Salas
     * @desc Metodo que actualiza todos los registros de proveedores segun el estatus nuevo seleccionado
     * @param string $codemp - codigo de empresa
	 * @param json $objson - json con los datos del proveedor
     * @param int $estprovnew - nuevo estatus que sera asignado al o a los proveedores
	 * @param array $arrevento - arreglo con los datos del log
     * */
	public function actualizarEstatus($codemp,$arrProveedor,$estprovnew, $arrEvento);
        
}