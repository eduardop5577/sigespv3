<?Php
/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class class_funciones_numericas
{
  
public function __construct()
{
}
	
function ue_convertir_cadenanumero($numero)
{
  //////////////////////////////////////////////////////////////////////////////
 //	Metodo: ue_convertir_cadenanumero	 //
 //	Access:  public
 //	Returns: cadena numerica con formato xxxxx.xx
 //	Description: Funcion que permite transformar una cadena numerica con
 //				  formato xx.xxx,xx a formato xxxxx.xx
 // Fecha: 21/03/2006
 // Autor: Ing. Laura Cabré
 //////////////////////////////////////////////////////////////////////////////
  
  $numero = str_replace(".", "", $numero);
  $numero = str_replace(",", ".", $numero);
  return $numero;
}

function ue_convertir_numerocadena($numero,$tipo="d")
{
  //////////////////////////////////////////////////////////////////////////////
 //	Metodo: ue_convertir_numerocadena	 //
 //	Access:  public
 //	Returns: cadena numerica con formato xx.xxx,xx
 //	Description: Funcion que permite transformar una cadena numerica con
 //				  formato xxxxx.xx a formato xx.xxx,xx
 // Fecha: 21/03/2006
 // Autor: Ing. Laura Cabré
 //////////////////////////////////////////////////////////////////////////////
  
  if ($tipo=="d")
  {
  	$numero = number_format($numero,2, ',', '.');
  }
  elseif ($tipo == "i")
  {
  	$numero = number_format($numero,0, '', '.');    
  }
  return $numero;
}
}
?>
