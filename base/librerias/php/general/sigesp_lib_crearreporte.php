<?php
/*********************************************************************************************************
* @Clase que permite generar el archivo Xml para mostrar los datos del reporte.
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class crearReporte
{
	var $nomArXml=array(); //nombre de los archivos xml 
	var $cantArchivos=1;
	var $nomRep="";   //nombre del reporte diseñado
	private $codsis='';
	
	public function __construct($codsis)
	{
		$this->codsis = $codsis;
	}

/***********************************************************************************
* @Función para crear un archivo XML a partir de un resultset.
* @parametros: nomArchivo, datos
* @retorno: true o false 
* @fecha de creación: 
* @autor: Johny Porras.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function  crearXml($nomArchivo,$datos) 
	{
		$this->nomArXml[$this->cantArchivos] = $nomArchivo;
		$this->getNombre();
		$dom = new DOMDocument(); //crear el documento DOM
		// create root element
		$root = $dom->createElement("registros"); 
		$dom->appendChild($root);  //agregar el elemento root al final de la lista  
		foreach ($datos as $Rg)
		{	
			$registro = $dom->createElement("registro");
			foreach ($Rg as $Indice =>$valor)
			{
				if (is_numeric($Indice))
				{
					continue;	
				}
				else
				{
					if ($valor!="" && $Indice!="_original")
					{
						$campo= $dom->createElement($Indice);
						$registro->appendChild($campo); 
						$text = $dom->createTextNode(utf8_encode($valor));
						$campo->appendChild($text);
					}
				}				
			}			
			$root->appendChild($registro);
		}		
		if ($dom->save("../../base/xml/reportes/$this->codsis/".$this->nomArXml[$this->cantArchivos]))
		{
			$this->cantArchivos++; 
			return true;		 
		}
		else
		{
			return false;
		}	
		
	}
	
	function  crearXml2($nomArchivo,$datos) //usada para reporte
	{
		
		$this->nomArXml[$this->cantArchivos] = $nomArchivo;
		$this->getNombre();
		$dom = new DOMDocument(); //crear el documento DOM
		// create root element
		$root = $dom->createElement("registros"); 
		$dom->appendChild($root);  //agregar el elemento root al final de la lista  
		while (!$datos->EOF)
		{	
			$registro = $dom->createElement("registro");
			
			foreach ($datos->fields as $Indice =>$valor)
			{
				if (is_numeric($Indice))
				{
					continue;	
				}
				else
				{
					if ($valor!="" && $Indice!="_original")
					{
						
						$campo= $dom->createElement($Indice);
						$registro->appendChild($campo); 
						$text = $dom->createTextNode(utf8_encode($valor));
						$campo->appendChild($text);
					}
				}
				
			}			
			$root->appendChild($registro);
			$datos->MoveNext();
		}		
		if ($dom->save("../../base/xml/reportes/$this->codsis/".$this->nomArXml[$this->cantArchivos]))
		{
			return true;
		 
		}
		else
		{
			return false;
		}	
		$this->cantArchivos++; 
	}
	
	
/***********************************************************************************
* @Función para crear un archivo XML a partir de un datastore.
* @parametros: nomArchivo, datos
* @retorno: 
* @fecha de creación:
* @autor: Johny Porras.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function  crearXmlAcumCuenta($nomArchivo,$datos){
		$this->nomArXml[$this->cantArchivos] = $nomArchivo;
		$this->GetNombre();
		$dom = new DOMDocument();
		// create root element
		$root = $dom->createElement("registros");
		$dom->appendChild($root);
		//echo $dom->save("order.xml");
			
			for($i=0;$i<count($datos);$i++)
			{
				$Registro = $dom->createElement("registro");
				if(is_array($datos[$i]->fields))
				{
					foreach($datos[$i]->fields as $Indice=>$valor)
					{
						if(!is_numeric($Indice))
						{
							$Campo= $dom->createElement($Indice);
							$Registro->appendChild($Campo);
							$text = $dom->createTextNode(utf8_encode($valor));
							$Campo->appendChild($text);
						}
					}
				}
				$root->appendChild($Registro);
			}
					
		 if($dom->save("../../base/xml/reportes/$this->codsis/".$this->nomArXml[$this->cantArchivos]))
		 {
		 	$this->cantArchivos++;
			return true;
		 }
		 else
		 {
			return false;
		 }
	}
	
	
/***********************************************************************************
* @Función para crear un archivo XML a partir de un arreglo.
* @parametros: nomArchivo, datos
* @retorno: 
* @fecha de creación:
* @autor: Johny Porras.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function  crearXmlArr($nomArchivo,$datos){
		$this->nomArXml[$this->cantArchivos] = $nomArchivo;
		$this->GetNombre();
		$dom = new DOMDocument();
		$root = $dom->createElement("registros");
		$dom->appendChild($root);
		
		$Registro = $dom->createElement("registro");
		foreach($datos as $Indice=>$valor){
			if(!is_numeric($Indice)){
				$Campo= $dom->createElement($Indice);
				$Registro->appendChild($Campo);
				$text = $dom->createTextNode(utf8_encode($valor));
				$Campo->appendChild($text);
			}
		}
				
		$root->appendChild($Registro);
							
		if($dom->save("../../base/xml/reportes/$this->codsis/".$this->nomArXml[$this->cantArchivos])){
		 	$this->cantArchivos++;
			return true;		 
		}
		else{
			return false;
		}
	}
		
/***********************************************************************************
* @Función para eliminar un archivo XML luego de cierto tiempo de haberse generado.
* @parametros: nomArchivo, datos
* @retorno: 
* @fecha de creación:
* @autor: Johny Porras.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/			
	function  eliminarXml($directorio)
	{
		if (is_dir($directorio)) 
		{
		   if ($hdir = opendir($directorio)) 
		   {
		       while (($archivo = readdir($hdir)) !== false) 
		       {
					if (!is_dir($archivo)) 
			   		{
						$tiempo =  $this->calculartiempotrasnc(date("H:i"),date("H:i",filemtime("{$directorio}/".$archivo)));
						$Artiempo = explode(":",$tiempo);
						if ($Artiempo[0]!="" && $Artiempo[0]>=1)
						{
							unlink ("{$directorio}/".$archivo);
						}
			   		}
		       }
		       closedir($hdir);
		   }
		}
	}
	
	
/***********************************************************************************
* @Función para obtener el nombre de  un archivo XML.
* @parametros: 
* @retorno: 
* @fecha de creación:
* @autor: Johny Porras.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function getNombre()
	{
		if ($this->nomArXml[$this->cantArchivos])
		{
			$this->nomArXml[$this->cantArchivos] = $this->nomArXml[$this->cantArchivos].time().$_SERVER["REMOTE_ADDR"].".xml";
		}
	}
	
	
/***********************************************************************************
* @Función para mostrar el reporte a partir de un archivo XML.
* @parametros: 
* @retorno: ruta donde se encuentra el reporte
* @fecha de creación:
* @autor: Johny Porras.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function mostrarReporte($dir_birt='/birt')
	{
		$parametros = "";
		if(!$this->nomRep)
		{
			return "noreporte";
		}
		elseif(!$this->nomArXml)
		{
			return "noxml";
		}
		else
		{
			$server = "http://".$_SERVER["SERVER_ADDR"];
			$dirsrvsfp = dirname(__FILE__);
			$dirsrvsfp = str_replace("\\","/",$dirsrvsfp);
			$dirsrvsfp = str_replace("/base/librerias/php/general","",$dirsrvsfp);
			$rutaXml = $dirsrvsfp."/base/xml/reportes/$this->codsis/";
			$rutaRep ="http://".$_SESSION['tomcatservidor'].":".$_SESSION['tomcatpuerto'].$dir_birt."/frameset?__report=";
			for($i = 1;$i<=count($this->nomArXml);$i++)
			{
				$parametros.="&rutaarchivo{$i}={$rutaXml}{$this->nomArXml[$i]}";
			}
			$this->nomRep = $this->nomRep.".rptdesign{$parametros}";
			$rutaCompleta = "{$rutaRep}{$this->nomRep}";
			return $rutaCompleta;
		}
	}

	
/***********************************************************************************
* @Función para obtener el tiempo transcurrido
* @parametros: hora1, hora2
* @retorno: tiempo en horas
* @fecha de creación:
* @autor: Johny Porras.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/		
	function calculartiempotrasnc($hora1,$hora2)
	{ 
		$separar[1]=explode(":",$hora1); 
		$separar[2]=explode(":",$hora2); 
		$total_minutos_trasncurridos[1] = ($separar[1][0]*60)+$separar[1][1]; 
		$total_minutos_trasncurridos[2] = ($separar[2][0]*60)+$separar[2][1]; 
		$total_minutos_trasncurridos = $total_minutos_trasncurridos[1]-$total_minutos_trasncurridos[2]; 
		if ($total_minutos_trasncurridos<=59) 
			return($total_minutos_trasncurridos." Minutos"); 
		elseif ($total_minutos_trasncurridos>59)
		{ 
			$HORA_TRANSCURRIDA = round($total_minutos_trasncurridos/60); 
			if ($HORA_TRANSCURRIDA<=9) 
				$HORA_TRANSCURRIDA=$HORA_TRANSCURRIDA; 
				$MINUITOS_TRANSCURRIDOS = $total_minutos_trasncurridos%60; 
				if ($MINUITOS_TRANSCURRIDOS<=9)
					$MINUITOS_TRANSCURRIDOS=$MINUITOS_TRANSCURRIDOS; 
			return ($HORA_TRANSCURRIDA.":".$MINUITOS_TRANSCURRIDOS." Horas"); 
		}
	}
}
?>