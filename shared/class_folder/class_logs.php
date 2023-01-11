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



		class logs {
		
					public function __construct()
					{
					
							global $ruta;
							
							if(!$ruta){$this->ruta = '../';}							
							else{$this->ruta = $ruta;}
					
					
					}
					
					
					function sislog($texto_log,$archivo_log=''){
							
								if($this->abrir_archivo($archivo_log) and $texto_log){
								        $this->escribe_log($texto_log);
										return true;
								}
								return false;
					
					}
					
					
					function escribe_log($texto_log){	
	
							
							if ($this->archivo_log)
							{
								if (@fwrite($this->archivo_log,$texto_log."\r\n")===false)//Escritura
								{
									//echo "No se puede escribir el log del sistema <br>";
									return false;
									
								}
							}
							else
							{
								//echo "Error al abrir el archivo de logs <br>";
								return false;
								
							}						
							
							return true;
					
					}
					
					
					function abrir_archivo($archivo_log=''){
					
					
							if(!$archivo_log){
									$archivo_log = $this->ruta.'shared/logs/log_sistema_'.date("dmY").'.txt';																	
							}
							else{
									$archivo_log = $this->ruta.$archivo_log;
							}
				
							
							$log_sistema = @fopen($archivo_log,"a+"); //creamos y abrimos el archivo para escritura
						
							
							$this->archivo_log = $log_sistema;							
							return true;			
					
					
					}
					
		
					function borrar_archivo($ls_nombrearchivo){
					
						if (file_exists($this->ruta.$ls_nombrearchivo))
						{
							unlink ($this->ruta.$ls_nombrearchivo);//Borrar el archivo de texto existente para crearlo nuevo.			
						}
					
					}
		
		}
















?>