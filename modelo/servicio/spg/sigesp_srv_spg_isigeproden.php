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
 
interface ISigeproden
{    
    /**
     * @author Ing. Yesenia Moreno 04125191342
     * @desc Metodo que busca un codigo para un nuevo proyecto
     */
    public function buscarCodigoProyecto();

    /**
     * @author Ing. Yesenia Moreno 04125191342
     * @desc Metodo que busca un listado de proyectos
     */

    public function buscarProyectos($codprosig,$despro);

    /**
     * @author Ing. Yesenia Moreno 04125191342
     * @desc Metodo que guarda un nuevo proyecto
     */
    public function guardarProyecto($objson, $arrevento);
    
    /**
     * @author Ing. Yesenia Moreno 04125191342
     * @desc Metodo que actualiza un proyecto existente
     */
    public function actualizarProyecto($objson, $arrevento);

    /**
     * @author Ing. Yesenia Moreno 04125191342
     * @desc Metodo que elimina un proyecto existente
     */
    public function eliminarProyecto($codprosig, $arrevento);
    
    /**
     * @author Ing. Yesenia Moreno 04125191342
     * @desc Metodo que busca la tasa de cambio para el comprobante
     */
    public function buscarTasaCambio($codmon, $fecha);

    /**
     * @author Ing. Yesenia Moreno 04125191342
     * @desc Metodo que genera un comprobante asociado a un proyecto
     */
    public function generarComprobante($objson, $arrevento);
    
}