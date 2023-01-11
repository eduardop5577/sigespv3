<?php 
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_c_logs {


	public function __construct()($texto_log,$as_ruta)
	{
		
			   $fp = @fopen($as_ruta,"a");
			   @fwrite($fp,date("j/n/Y")." - ".date("h:i s a").':    '.$texto_log."\r\n");	   
			   @fclose($fp); 

	}



}// fin de la clase sigesp_c_logs


?>
