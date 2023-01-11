<?php
/***********************************************************************************
* @librer�a que contiene las funciones comunes usadas es todas las clases
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

/****************************************************************************
* 				FUNCIONES PARA EL MANEJO DE LOS DATOS
****************************************************************************/

/**************************************************************************
* @Convertir un objeto en un arreglo asociativo.
* @Esta funci�n convierte un objeto en un arreglo asociativo de iteraci�n
* a trav�s de sus propiedades p�blicas debido a que esta funci�n usa
* el foreach, el constructor y las iteraciones son respetados.
* Tambi�n trabaja con arreglo de objetos.
*
* @Par�metros: objeto $var
* @Valor de retorno: arreglo.
* @Funci�n predeterminada.
***************************************************************************/
function object_to_array($var)
{
    $result = array();
    $references = array();

    // loop over elements/properties
    foreach ($var as $key => $value)
    {
        // recursively convert objects
        if (is_object($value) || is_array($value))
	{
            // but prevent cycles
            if (!in_array($value, $references))
	    {
                $result[$key] = object_to_array($value);
                $references[] = $value;
            }
        }
	else
	{
            // simple values are untouched
            $result[$key] = $value;
        }
    }
    return $result;
}


/********************************************************************************
* @Convertir un valor a JSON
* @Esta funci�n devuelve una representaci�n JSON de $param. Utiliza json_encode
* Para lograr esto, convierte arreglos y objetos que contengan objetos a
* arreglos asociativos en primer lugar. As�, los objetos que no expongan
* (todas) sus propiedades directamente sino s�lo a trav�s de interfaz de
* iteraci�n, tambi�n son codificados correctamente.
*
* @Par�metros: objeto $param
* @Valor de retorno: objeto Json.
* @Funci�n predeterminada.
********************************************************************************/
function json_encode2($param) {
    if (is_object($param) || is_array($param)) {
        $param = object_to_array($param);
    }
    return json_encode($param);
}


/*******************************************************
* @Funci�n para obtener el objeto Json de acuerdo a la
* ejecuci�n de un Execute en el modelo.
* @par�metros: $datos
* @retorno: $textJson arreglo de objetos
********************************************************/
function generarJson($datos,$formatoFecha=true,$formatoNumero=true)
{
	global $json;
	$j=0;
	$arRegistros = null;

   	while((!$datos->EOF) && (is_object($datos)))
   	{
   		$i=0;
    	foreach ($datos->fields as $Propiedad=>$valor)
    	{
     		if (!is_numeric($Propiedad))
     		{
				$campo = $datos->FetchField($i);
				$tipo =  $datos->MetaType($campo->type);
				if ($formatoFecha) {
					if ($tipo == 'D' || $tipo == 'T')
					{
						$valor = convertirFecha($valor);
					}
				}								
				if ($tipo=='N' && $formatoNumero)
				{
					$valor = number_format($valor,2,',','.');
				}
				$Propiedad = strtolower($Propiedad);
                $arRegistros[$j][$Propiedad] = utf8_encode($valor);

     		}
			$i++;
    	}
    	$datos->MoveNext();
		$j++;
   	}
   	$datos->Close();
	unset($datos);
    //aqui se pasa el arreglo de arreglos a un objeto json
   	$textJso = array('raiz'=>$arRegistros);
   	$textJson = json_encode($textJso);
	$textJson = utf8_encode(html_entity_decode($textJson));

   	return $textJson;
}

function generarJsonFila($fila) {
	$strJson = '';
	foreach ($fila as $campo=>$valor){
		if ($strJson=='') {
			$strJson .= '{"'.$campo.'":"'.utf8_encode($valor).'"';
		}
		else {
			$strJson .= ',"'.$campo.'":"'.utf8_encode($valor).'"';
		}
	}
	$strJson .= '}';
	
	return json_decode($strJson);
}

function generarJsonArreglo($arreglo) {
	
	$textJso = array('raiz'=>$arreglo);
   	$textJson = json_encode($textJso);
	$textJson = utf8_encode(html_entity_decode($textJson));

   	return $textJson;
}

/***********************************************************************************
* @Funci�n que retorna el objeto JSON cargado con las variables de SESION
* @parametros:
* @retorno:
* @fecha de creaci�n: 17/11/2008
* @autor: Johny Porras
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function generarJsonSesion()
{
	global $json;
	$i=0;
	foreach($_SESSION as $Propiedad=>$valor)
	{
		if(!is_numeric($Propiedad))
		{
			$Propiedad = strtolower($Propiedad);
			$arRegistros[$Propiedad]= utf8_encode($valor);
		}
	}
	$TextJson = json_encode($arRegistros);
	return $TextJson;
}

function utf8_to_latin9($utf8str) 
{ 
	$trans = array("?"=>"?", "?"=>"?", "?"=>"?", "?"=>"?", "?"=>"?", "?"=>"?", "?"=>"?", "?"=>"?");
	$wrong_utf8str = strtr($utf8str, $trans);
	$latin9str = utf8_decode($wrong_utf8str);
	return $latin9str;
}

function isUTF8 ($string)
{
	$string_utf8 = utf8_encode($string);
	if( strpos($string_utf8,"�",0) !== false ) {
		return true;  // La cadena est� en UTF8
	}
	else {
		return false; //No sabemos si la cadena est� en UTF8 o no, pero se deber�a convertir a UTF8 por si acaso
	}
}
/*****************************************************************
* Funci�n para evaluar si coinciden los nombres de las propiedades
* del objeto JSON con los del objeto DAO y copiar sus valores.
* parametros: objDao: objeto DAO, objJson: objeto JSON,
* evento: operaci�n.
* fecha de creaci�n:
* autor: Johny Porras.
******************************************************************/
function pasarDatos($objDao,$objSon,$evento=Array())
{
	$arDao = $objDao->getAttributeNames();
	foreach ($objDao as $IndiceD =>$valorD)
	{
		foreach ($objSon as $Indice =>$valor)
		{
			if ($Indice==$IndiceD)
			{
				if (isUTF8($valor))
				{
					$objDao->$Indice = utf8_to_latin9($valor);
				}
				else
				{
					$objDao->$Indice = $valor;
				}
				
			}
			else
			{
				$evento[$Indice] = $valor;
			}
		}
	}
	$arrResultado["objDao"]=$objDao;
	$arrResultado["evento"]=$evento;
	return $arrResultado;
}


/**************************************************************************************
* 							FUNCIONES PARA MANEJO DE FECHAS
***************************************************************************************/

/******************************************************
* @Funci�n para convertir la fecha que viene de la base
* @de datos con formato a�o-mm-dd al formato dia/mes/a�o
* @parametros: $fecha: fecha a convertir.
* @retorno: $fechamostrar: fecha a mostrar.
* @fecha de creaci�n: 06/08/2008
* @autor:
*******************************************************/
function convertirFecha($fecha)
{
	$fechamostrar='';
	$pos  = strpos($fecha,'-');
	$pos2 = strpos($fecha,'/');
	if (($pos==4) || ($pos2==4))
	{
		$fechamostrar=(substr($fecha,8,2).'/'.substr($fecha,5,2).'/'.substr($fecha,0,4));
	}
	elseif(($pos==2)||($pos2==2))
	{
		$fechamostrar=$fecha;
	}
	return $fechamostrar;
}


/************************************************************************************
* @Funci�n para convertir la fecha que viene del
* @formulario con formato dia/mes/a�o al formato a�o-mes-dia
* @parametros: $fecha: fecha a convertir.
* @retorno: $fechamostrar: fecha a mostrar.
* @fecha de creaci�n: 28/08/2008
* @autor:
*************************************************************************************/
function convertirFechaBd($fecha)
{
	if (trim($fecha)=='')
	{
		$fecha='1900-01-01';
	}
	$fechabd   = '';
	$posicion  = strpos($fecha,'/');
	$posicion2 = strpos($fecha,'-');
	if (($posicion==2) || ($posicion2==2))
	{
		$fechabd = (substr($fecha,6,4).'-'.substr($fecha,3,2).'-'.substr($fecha,0,2));
	}
	elseif (($posicion==4) || ($posicion2==4))
	{
		$fechabd = str_replace('/','-',$fecha);
	}
	return $fechabd;
}


/***********************************************************************************
* @Funci�n que valida que al tener dos fechas (un periodo de tiempo) la fecha que
* inicia el periodo no sea mayor a la fecha que cierra el periodo; es decir que
* las fechas no esten solapadas.
* @parametros: fecha_desde, fecha_hasta
* @retorno: Fecha valida
* @fecha de creaci�n: 24/11/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
************************************************************************************/
function compararFecha($fecdesde,$fechasta)
{
	$fechavalida = false;
	$fecdesdeaux = convertirFechaBd($fecdesde);
	$fechastaaux = convertirFechaBd($fechasta);

	if (($fecdesdeaux=="")||($fechastaaux==""))
	{
		$fechavalida = false;
	}
	else
	{
		$anodesde = substr($fecdesdeaux,0,4);
		$mesdesde = substr($fecdesdeaux,5,2);
		$diadesde = substr($fecdesdeaux,8,2);
		$anohasta = substr($fechastaaux,0,4);
		$meshasta = substr($fechastaaux,5,2);
		$diahasta = substr($fechastaaux,8,2);

		if($anodesde < $anohasta)
		{
			$fechavalida = true;
		}
		elseif ($anodesde==$anohasta)
		{
			if ($mesdesde < $meshasta)
			{
				$fechavalida = true;
			}
			elseif ($mesdesde==$meshasta)
			{
				if ($diadesde <= $diahasta)
				{
					$fechavalida=true;
				}
			}
		}
	}
	return $fechavalida;
}


/***********************************************************************************
* @Funci�n que retorna el �ltimo d�a del mes
* @parametros: $mes, $anio
* @retorno: Fecha final
* @fecha de creaci�n: 04/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function ultimoDiaMes ($mes, $anio)
{
	$dia=28;
	while (checkdate($mes, ($dia + 1),$anio))
	{
	   $dia++;
	}
	$fecha=$anio.'-'.$mes.'-'.$dia;
	return $fecha;
}


/***********************************************************************************
* @Funci�n que retorna le suma a una fecha la cantidad de d�as indicados
* @parametros: $fecha, $dias
* @retorno: Fecha con d�as sumando
* @fecha de creaci�n: 04/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function sumarDias ($fecha, $ndias)
{
	if ($ndias > 0)
	{
		$dia=substr($fecha,8,2);
		$mes=substr($fecha,5,2);
		$anio=substr($fecha,0,4);
		$ultimo_dia=date("d",mktime(0, 0, 0,$mes+1,0,$anio));
		$dias_adelanto=$ndias;
		$siguiente=$dia+$dias_adelanto;
		if ($ultimo_dia < $siguiente)
		{
			$dia_final=$siguiente-$ultimo_dia;
			$mes++;
			if($ndias=='365')
			{
				$dia_final=$dia;
			}
			if($mes=='13')
			{
				$anio++;
				$mes='01';
			}
			$fecha_final=$anio.'-'.str_pad($mes,2,"0",0).'-'.str_pad($dia_final,2,"0",0);
		}
		else
		{
			$fecha_final=$anio.'-'.str_pad($mes,2,"0",0).'-'.str_pad($siguiente,2,"0",0);
		}
		$dia=substr($fecha_final,8,2);
		$mes=substr($fecha_final,5,2);
		$anio=substr($fecha_final,0,4);
		while(checkdate($mes,$dia,$anio)==false)
		{
		   $dia=$dia-1;
		   break;
		}
		$fecha_final=$anio.'-'.$mes.'-'.$dia;
	}
	else
	{
		$fecha_final=convertirFechaBd($fecha);
	}
	return $fecha_final;
}


/***********************************************************************************
* @Funci�n para validar la fecha en cuanto al mes de apertura de la misma
* @parametros:
* @retorno:
* @fecha de creaci�n: 26/12/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function validarFechaMes($fecha)
{
	 $mesabierto=true;
	 $fecha=convertirFechaBd($fecha);
	 $mes = intval(substr($fecha,5,2));
		switch ($mes)
		{
			case 1:
				if($_SESSION['la_empresa']['m01']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 2:
				if($_SESSION['la_empresa']['m02']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 3:
				if($_SESSION['la_empresa']['m03']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 4:
				if($_SESSION['la_empresa']['m04']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 5:
				if($_SESSION['la_empresa']['m05']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 6:
				if($_SESSION['la_empresa']['m06']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 7:
				if($_SESSION['la_empresa']['m07']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 8:
				if($_SESSION['la_empresa']['m08']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 9:
				if($_SESSION['la_empresa']['m09']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 10:
				if($_SESSION['la_empresa']['m10']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 11:
				if($_SESSION['la_empresa']['m11']!=1)
				{
					$mesabierto = false;
				}
				break;
			case 12:
				if($_SESSION['la_empresa']['m12']!=1)
				{
					$mesabierto = false;
				}
				break;
			default:
		}
	return $mesabierto;
}


/***********************************************************************************
* @Funci�n para validar la fecha en cuanto al mes de apertura de la misma
* @parametros:
* @retorno:
* @fecha de creaci�n: 26/12/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function validarFechaPeriodo($fecha)
{
    $valido = true;
    $fecha=convertirFechaBd($fecha);
    $anio = intval(substr($fecha,0,4));
    $mes = intval(substr($fecha,5,2));
    $anio_periodo = intval(substr($_SESSION['la_empresa']['periodo'],0,4));
    $mes_periodo = intval(substr($_SESSION['la_empresa']['periodo'],5,2));
    $ld_periodo_final = '31/12/'.$anio_periodo;
	if ($anio == $anio_periodo)
	{
		if($mes >= $mes_periodo)
		{
		   if(validarFechaMes($fecha))
		   {
		 	  $valido = true;
		   }
		   else	 
		   {
			  $valido = false;
		   }
		} 			
		else
		{
			$valido = false;	
		}
	}
	else 
	{
		$valido = false;	
	}
	return $valido;	
	
}


/***************************************************************************************
* 			FUNCIONES PARA MANEJO DE LOS ARCHIVOS XML
****************************************************************************************/

/***********************************************************************************
* @Funci�n que escribe en un archivo de Texto los Resultados de la Conversi�n
* @parametros:
* @retorno:
* @fecha de creaci�n: 22/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function  escribirArchivo($archivo,$mensaje)
{
	$mensaje=$mensaje."\r\n";
	if ($archivo)
	{
		@fwrite($archivo,$mensaje);
	}
	return $archivo;
}


/***********************************************************************************
* @Funci�n para abrir el archivo xml de configuraci�n de base de datos.
* @parametros: $ruta, $archivo
* @retorno: $doc documento xml
* @fecha de creaci�n: 30/07/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function abrirArchivoXml($ruta,$archivo)
{
	$doc = new DOMDocument();
	$doc->preserveWhiteSpace = true;
	$doc->load($ruta.$archivo);
	return $doc;
}


/********************************************************************************
* 				FUNCIONES PARA EL MANEJO DE LAS SESIONES
********************************************************************************/

/***********************************************************************************
* @Funci�n para crear las variables de sesi�n para la base de datos seleccionada
* @parametros: $documento, $bd base de datos
* @retorno: $valorbd
* @fecha de creaci�n: 31/07/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function obtenerEmpresa($documento,$bd)
{
	$i=0;
	$conexiones = $documento->getElementsByTagName('conexion');
	if($conexiones)
	{
		foreach ($conexiones as $conexion)
		{
			$io_campo = $conexion->getElementsByTagName('basedatos');
			$valorbd= rtrim($io_campo->item(0)->nodeValue);
			if ($valorbd==$bd)
			{
				$io_campo = $conexion->getElementsByTagName('servidor');
				$_SESSION['sigesp_servidor']  = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('gestor');
				$_SESSION['sigesp_gestor']    = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('login');
				$_SESSION['sigesp_usuario']   = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('password');
				$_SESSION['sigesp_clave']     = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('basedatos');
				$_SESSION['sigesp_basedatos'] = rtrim($io_campo->item(0)->nodeValue);
				$_SESSION['sigesp_intentos'] = 0;
				$io_campo = $conexion->getElementsByTagName('logo');
				$_SESSION['sigesp_logo'] = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('ancho');
				$_SESSION['sigesp_ancho'] = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('alto');
				$_SESSION['sigesp_alto'] = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('puerto');
				$_SESSION['sigesp_puerto'] = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('directorio');
				$_SESSION['sigesp_sitioweb'] = rtrim($io_campo->item(0)->nodeValue);

				// Cuando todos est�n migrados se debe quitar esta line a de c�digo
				$_SESSION['ls_hostname'] = $_SESSION['sigesp_servidor'];
				$_SESSION['ls_login'] = $_SESSION['sigesp_usuario'];
				$_SESSION['ls_password'] = $_SESSION['sigesp_clave'];
				$_SESSION['ls_database'] = $_SESSION['sigesp_basedatos'];
				$_SESSION['ls_gestor'] = $_SESSION['sigesp_gestor'];
				$_SESSION['ls_width']    = $_SESSION['sigesp_ancho'];
				$_SESSION['ls_height']   = $_SESSION['sigesp_alto'];
				$_SESSION['ls_logo']     = $_SESSION['sigesp_logo'];
				$_SESSION['ls_port']     = $_SESSION['sigesp_puerto'];
				return $valorbd;
			}
			$i++;
		}
	}
}


/***********************************************************************************
* @Funci�n para crear las variables de sesi�n para una base de datos de destino.
* (para el transferir usuario)
* @parametros: $documento, $bd base de datos
* @retorno: $valorbd
* @fecha de creaci�n: 18/11/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function crearConexionDestino($documento,$bd)
{
	$i=0;
	$conexiones = $documento->getElementsByTagName('conexion');
	if($conexiones)
	{
		foreach ($conexiones as $conexion)
		{
			$io_campo = $conexion->getElementsByTagName('basedatos');
			$valorbd= rtrim($io_campo->item(0)->nodeValue);
			if ($valorbd==$bd)
			{
				$io_campo = $conexion->getElementsByTagName('servidor');
				$_SESSION['sigesp_servidor_destino']  = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('gestor');
				$_SESSION['sigesp_gestor_destino']    = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('login');
				$_SESSION['sigesp_usuario_destino']   = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('password');
				$_SESSION['sigesp_clave_destino']     = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('basedatos');
				$_SESSION['sigesp_basedatos_destino'] = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('puerto');
				$_SESSION['sigesp_puerto_destino'] = rtrim($io_campo->item(0)->nodeValue);
				$_SESSION['sigesp_intentos_destino']=0;

				// Cuando todos est�n migrados se debe quitar esta line a de c�digo
				$_SESSION['ls_hostname_destino'] = $_SESSION['sigesp_servidor_destino'];
				$_SESSION['ls_login_destino'] = $_SESSION['sigesp_usuario_destino'];
				$_SESSION['ls_password_destino'] = $_SESSION['sigesp_clave_destino'];
				$_SESSION['ls_database_destino'] = $_SESSION['sigesp_basedatos_destino'];
				$_SESSION['ls_gestor_destino'] = $_SESSION['sigesp_gestor_destino'];
				$_SESSION['ls_port_destino']     = $_SESSION['sigesp_puerto_destino'];
				return $valorbd;
			}
			$i++;
		}
	}
}


/***********************************************************************************
* @Funci�n para obtener las base de datos del archivo xml.
* @parametros: $documento, $datos
* @retorno: $datos
* @fecha de creaci�n: 30/07/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function obtenerConexionbd($documento,$datos)
{
	$li_i=0;
	$conexiones=$documento->getElementsByTagName('conexion');
	if($conexiones)
	{
		foreach ($conexiones as $conexion)
		{
			$io_campo = $conexion->getElementsByTagName('basedatos');
			$datos[$li_i]['codbasedatos'] = rtrim($io_campo->item(0)->nodeValue);
			$io_campo = $conexion->getElementsByTagName('nombre');
			$datos[$li_i]['basedatos'] = rtrim($io_campo->item(0)->nodeValue);
			$li_i++;
		}
	}
	return $datos;
}


/***********************************************************************************
* @Funci�n para obtener el valor de una variable de sessi�n
* @parametros: $valor, $valordefecto
* @retorno: $valor
* @fecha de creaci�n: 03/09/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function obtenerValorSession($valor,$valordefecto)
{
	if(array_key_exists($valor,$_SESSION))
	{
		$valor=$_SESSION[$valor];
	}
	else
	{
		$valor=$valordefecto;
	}
	return $valor;
}


/***********************************************************************************
* @Funci�n para que valida si una sessi�n est� activa
* @parametros:
* @retorno: $sessionvalida
* @fecha de creaci�n: 08/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
************************************************************************************/
function validarSession()
{
	$sessionvalida = false;
	if (array_key_exists('la_empresa',$_SESSION))
	{
		$sesion = $_SESSION['session_activa'];
		$tiempo = $_SESSION['tiempo_session'];
		if (time()-$sesion < $tiempo)
		{
			$sessionvalida = true;
		}
	}
	if($sessionvalida==false)
	{
		session_unset();
		$arreglo[0]['mensaje'] = obtenerMensaje('SESION_EXPIRADA');
		$arreglo[0]['valido']  = false;
		$respuesta  = array('raiz'=>$arreglo);
		$respuesta  = json_encode($respuesta);
		echo $respuesta;
	}
	return $sessionvalida;
}


/***********************************************************************************
* @Funci�n que obtiene la base de datos de apertura y carga las variables de session
* @parametros:
* @retorno:
* @fecha de creaci�n: 20/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function obtenerBdApertura($documento,$bd)
{
	$conexiones = $documento->getElementsByTagName('conexion');
	if($conexiones)
	{
		foreach ($conexiones as $conexion)
		{
			$io_campo = $conexion->getElementsByTagName('nombre');
			$valorbd= rtrim($io_campo->item(0)->nodeValue);
			if ($valorbd==$bd)
			{
				$io_campo = $conexion->getElementsByTagName('servidor');
				$_SESSION['sigesp_servidor_apr']  = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('gestor');
				$_SESSION['sigesp_gestor_apr']    = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('login');
				$_SESSION['sigesp_usuario_apr']   = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('password');
				$_SESSION['sigesp_clave_apr']     = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('basedatos');
				$_SESSION['sigesp_basedatos_apr'] = rtrim($io_campo->item(0)->nodeValue);
				$io_campo = $conexion->getElementsByTagName('puerto');
				$_SESSION['sigesp_puerto_apr'] = rtrim($io_campo->item(0)->nodeValue);
				
				return $valorbd;
			}
		}
	}
}


/********************************************************************************
* 								OTRAS FUNCIONES
********************************************************************************/

/***********************************************************************************
* @Funci�n para obtener el mensaje Seg�n el tipo de Mensaje
* @parametros: $tipo
* @retorno: $mensaje
* @fecha de creaci�n: 07/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function obtenerMensaje($tipo,$mensaje='')
{
	switch($tipo)
	{
		case 'USUARIO_NOACTIVO':
			$mensaje=utf8_encode('El usuario no esta Activo. Por favor Contacte con el Administrador del Sistema');
		break;
		case 'SESION_EXPIRADA':
			$mensaje=utf8_encode('Su sessi�n ha expirado. Por favor ingrese nuevamente al sistema.');
		break;
		case 'ACCION_NO_VALIDA':
			$mensaje=utf8_encode('El Usuario no Tiene permiso para esta Acci�n. Comun�quese con el Administrador del sistema.');
		break;
		case 'DATOS_NO_VALIDO':
			$mensaje=utf8_encode('Los Datos no son v�lidos.');
		break;
		case 'OPERACION_EXITOSA':
			$mensaje=utf8_encode('La operaci�n se realiz� de manera exitosa.');
		break;
		case 'OPERACION_FALLIDA':
			$mensaje=utf8_encode('Ocurrio un error al realizar la operaci�n.');
		break;
		case 'REGISTRO_EXISTE':
			$mensaje=utf8_encode('El Registro que est� tratando de agregar ya existe.');
		break;
		case 'REGISTRO_NO_EXISTE':
			$mensaje=utf8_encode('El Registro que est� tratando de actualizar � eliminar no existe.');
		break;

		case 'ARCHIVO_NO_EXISTE':
			$mensaje=utf8_encode('No Existen Archivos.');
		break;

		case 'DATA_NO_EXISTE':
			$mensaje=utf8_encode('No Existen Datos.');
		break;

		case 'RELACION_OTRAS_TABLAS':
			$mensaje=utf8_encode('El Registro que est� tratando de eliminar est� en: '.$mensaje);
	}
	return $mensaje;
}


/*************************************************************
*@Funci�n que rellena una cadena con ceros a la izquierda
* y le suma un n�mero.
*@Parametros: $cod = Cadena a la cual se la sumara un numero,
* $cantidad longitud total de la cadena.
*@Valor de retorno: cadena con la nueva cifra
*@Fecha de Creaci�n:
*@Autor: Victor Mendoza
**************************************************************/
function agregarUno($cod, $cantidad=0)
{
	$cod  = substr($cod,-$cantidad);
	$cod  = limpiarCadenaNumeros($cod);
	$suma = intval($cod) + 1;
    $cad = str_pad(intval($suma), $cantidad, '0', STR_PAD_LEFT);
    return $cad;

}

function limpiarCadenaNumeros($cadenaNumero) {
	$arrLetras    = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','�','o','p','q','r','s','t','u','v','w','x','y','z');
	$arrLetrasMay = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','�','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	$arrDigitos   = array(',','-','_','*','.','/','(',')','+','@','#','$','%','&','?','�','�','!');
	
	$cadenaNumero = str_replace($arrLetras, '', $cadenaNumero);
	$cadenaNumero = str_replace($arrLetrasMay, '', $cadenaNumero);
	$cadenaNumero = str_replace($arrDigitos, '', $cadenaNumero);
	
	return trim($cadenaNumero);
}

/************************************************************
* @Funci�n para rellenar una cadena con ceros a la izquierda
* @parametros: $cadena, $longitud
* @retorno: $aux: cadena.
* @fecha de creaci�n: 01/09/2008
* @autor:
************************************************************/
function cerosIzquierda($cadena,$longitud)
{
	$long = 0;
	$aux = $cadena;
	$pos = strlen($cadena);
	$long = $longitud-$pos;
	for ($i=0; $i<$long; $i++)
	{
   		$aux = '0'.$aux;
	}
	return $aux;
}

/***********************************************************************************
* @Funci�n que valida un texto.
* @parametros:
* @retorno:
* @fecha de creaci�n: 10/12/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function validarTexto($valor,$inicio,$longitud,$valordefecto)
{
	$nuevovalor = $valor;
	$nuevovalor = trim($nuevovalor);

	if(($nuevovalor=='')||($nuevovalor==NULL))
	{
		$nuevovalor = $valordefecto;
	}
	else
	{
		$nuevovalor = str_replace("'","",$nuevovalor);
		$nuevovalor = str_replace('"',"",$nuevovalor);
		$nuevovalor = str_replace('\\',"",$nuevovalor);
		$nuevovalor = substr($nuevovalor,$inicio,$longitud);
	}
   	return $nuevovalor;
}

/***********************************************************************************
* @Funci�n que obtiene la empresa de la session para cargarla en un arreglo de 
* javascript.
* @parametros:
* @retorno:
* @fecha de creaci�n: 10/12/2009
* @autor: Ing. Arnaldo Suarez.
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function obtenerEmpresaSession()
{
	$sessionempresa = 'empresa = new Array(); ';
    foreach($_SESSION['la_empresa'] as $clave=>$valor)
    {
     if(!is_numeric($clave))
     {
	  $clave = strtolower($clave);
	  $sessionempresa.= " empresa['".$clave."'] = '".utf8_encode($valor)."'; \n";
     }
    }
	echo $sessionempresa;  
}


/***********************************************************************************
* @Funci�n que retorna el formato numerico apropiado para la base de datos 
* javascript.
* @parametros:
* @retorno:
* @fecha de creaci�n: 10/12/2009
* @autor: Ing. Arnaldo Suarez.
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
function formatoNumericoBd($monto,$tipo=0)
{
	$valor = '';
	$resultado=0;
	$valor = str_replace('.','',$monto);
	$valor = str_replace(',','.',$valor);
 	if($tipo == 0)
 	{
  		$resultado = intval($valor);
 	}
 	elseif($tipo == 1)
 	{
  		$resultado = floatval($valor); 
 	}
 
 	return $resultado;
}

function uf_spg_cuenta_sin_cero($as_cuenta)
{
	$cadena=posceros($as_cuenta);
	//ver($as_cuenta);
	if($cadena!=NULL)
	{
		$posicion=$cadena;
		$criterio=substr($as_cuenta,0,$posicion);
	}
	else
	{
		$criterio=$as_cuenta;
	}
	return $criterio;
}

function posceros($cadena)
{
	for($i=strlen(trim($cadena))-1;$i>=0;$i--)
	{
			if($cadena[$i]=="0")
			{
				$pos=$i;	
			}
			else
			{
				return $pos;
			}
	}
}

function posocurrencia($cadena, $cadenabuscar, $ocurrencia)
{
	$pos = 0;  
	$possig = 0;
	$lencad=0;
    for($i=0; $i<$ocurrencia; $i++)
	{
		if ($i==0)  
		{
			$pos=strpos($cadena,$cadenabuscar);
		} 
	    else 
	    { 
	    	$lencad=strlen($cadenabuscar);
	 	   	$possig=$lencad + $pos;
	       	$pos=strpos($cadena,$cadenabuscar,$possig);
		}
	}	 
    return $pos;
}

function formatoprogramatica($codpro)
{
	$modalidad=$_SESSION['la_empresa']['estmodest'];
	$len1=$_SESSION['la_empresa']['loncodestpro1'];
	$len2=$_SESSION['la_empresa']['loncodestpro2'];
	$len3=$_SESSION['la_empresa']['loncodestpro3'];
	$len4=$_SESSION['la_empresa']['loncodestpro4'];
	$len5=$_SESSION['la_empresa']['loncodestpro5'];
	$codest1=substr(substr($codpro,0,25),(25-$len1),$len1);
	$codest2=substr(substr($codpro,25,25),(25-$len2),$len2);
	$codest3=substr(substr($codpro,50,25),(25-$len3),$len3);
	$codest4=substr(substr($codpro,75,25),(25-$len4),$len4);
	$codest5=substr(substr($codpro,100,25),(25-$len5),$len5);		
	switch($modalidad)
	{
		case '1': // Modalidad por Proyecto
			$programatica=$codest1.'-'.$codest2.'-'.$codest3;
			break;
	
		case '2': // Modalidad por Programa
			$programatica=$codest1.'-'.$codest2.'-'.$codest3.'-'.$codest4.'-'.$codest5;
			break;
	}
	return $programatica;
}


function fillComprobante($comprobante)
{
	$comprobante =str_pad($comprobante, 15, '0', STR_PAD_LEFT);
	return $comprobante;
}

function cargarNiveles($formato,$niveles)
{
	$formato=$formato.'-';
	$posicion=1;
	$indice=1;
	$posicion = posocurrencia($formato,'-' , $indice ) - $indice;	
	do
	{
		$niveles[$indice] = $posicion ;
		$indice = $indice + 1;
		$posicion = posocurrencia($formato,'-' , $indice ) - $indice;
	} while ($posicion>=0);
	return $niveles;
}

function obtenerNivelPlus($cuenta, $formatoCuenta)
{
	$arrNivel = explode('-', $formatoCuenta);
	$nivel = count($arrNivel);
	$inicio        = 0;
	$longitudtotal = 0;
	do
	{
		$longitudtotal = $longitudtotal + strlen($arrNivel[$nivel-1]);
		$longitudnivel = strlen($arrNivel[$nivel-1]);
		$inicio   = strlen($cuenta) - $longitudtotal;
		$cadena   = substr(trim($cuenta),$inicio,$longitudnivel); 
		$li=intval($cadena);
    	if($li>0)
		{
    		return $nivel;
    	}
		$nivel=$nivel-1;
	} 
	while($nivel>=0);
}

function obtenerNivel($cuenta,$niveles)
{
	$nivel=0;
	$anterior=0;
	$longitud=0;
	$cadena='';
	$nivel=count($niveles);
	do
	{
		$anterior=$niveles[$nivel-1]+1;
		$longitud=$niveles[$nivel]-$niveles[$nivel-1];
		$cadena=substr(trim($cuenta),$anterior,$longitud); 
		$li=intval($cadena);
	    if($li>0)
		{
			return $nivel;
		}
		$nivel=$nivel-1;
	}while($nivel>1);
	return $nivel;
}

function obtenerCuentaSiguiente($cuenta,$niveles)
{
  	$MaxNivel=count($niveles);
	$nivel=obtenerNivel($cuenta,$niveles);
	$anterior=0;
	$longitud=0;
	$cadena='';
	if($nivel>1)
	{
		$anterior=$niveles[$nivel - 1]; 
		$cadena=substr($cuenta,0,$anterior+1);
		$longitud=strlen($cadena);
		$long=(($niveles[$MaxNivel]+1) - $longitud);
		$cadena=str_pad(trim($cadena),$long+$longitud,'0');
	} 
	return $cadena;
}

function obtenerCuentaSiguientePlus($cuenta, $formatoCuenta)
{
  	$nivel       = obtenerNivelPlus($cuenta, $formatoCuenta);
  	$cantDigitos = obtenerDigitosNivel($nivel-1, $formatoCuenta);
	if($nivel>1)
	{
		$cuentaSinCero   = substr($cuenta,0,$cantDigitos);
		$cuentaSiguiente = str_pad(trim($cuentaSinCero),strlen($cuenta),'0');
	} 
	return $cuentaSiguiente;
}

function obtenerDigitosNivel($nivel, $formatoCuenta)
{
	$arrNivel = explode('-', $formatoCuenta);
	$cantidad = 0;
	do
	{
		$cantidad = $cantidad + strlen($arrNivel[$nivel-1]);
		$nivel--;
	} 
	while($nivel>=0);
	
	return $cantidad;
}

function obtenerFormatoCuenta($formato, $cuenta)
{
	$formatoAux = str_replace('-', '', $formato );
	$formatoAux = trim($formatoAux);
	$longitud   = strlen($formatoAux);
	$cuentaAux  = trim($cuenta);
	$cuentaAux  = substr($cuentaAux,0,$longitud);
	$cuentaAux  = str_pad($cuentaAux, $longitud,'0');
	
	return $cuentaAux;
}

function selectConfig($sistema,$seccion,$variable,$valor,$tipo)
{
	require_once ("sigesp_lib_fabricadao.php");
	$resultado='';

	$criterio="codemp='".$_SESSION["la_empresa"]["codemp"]."' AND codsis='".$sistema."' AND seccion='".$seccion."' AND entry='".$variable."' ";print $ls_sql;
	$daoConfig = FabricaDao::CrearDAO('C','sigesp_config','',$criterio);		
	if($daoConfig->codemp=='')
	{
		switch ($tipo)
		{
			case 'C'://Caracter
				if(trim($valor)=='')		
				{
					$valor='-';
				}
				$valor=trim($valor);	
			break;
			
			case 'D'://Double
				$valor=str_replace(".","",$valor);
				$valor=str_replace(",",".",$valor);
			break;

			case 'I'://Integer
				$valor = intval($valor);
			break;
		}
		$daoConfig->codemp=$_SESSION['la_empresa']['codemp'];
		$daoConfig->codsis=$sistema;
		$daoConfig->seccion=$seccion;
		$daoConfig->entry=$variable;
		$daoConfig->value=$valor;
		$daoConfig->type=$tipo;
		$valido = $daoConfig->incluir();
		$resultado=$valor;
	}
	else
	{
		$resultado=$daoConfig->value;
	}
	unset($daoConfig);
	return rtrim($resultado);
}


function selectConfig2($sistema,$seccion,$variable)
{
	require_once ("sigesp_lib_fabricadao.php");
	$resultado='';

	$criterio="codemp='".$_SESSION["la_empresa"]["codemp"]."' AND codsis='".$sistema."' AND seccion='".$seccion."' AND entry='".$variable."' ";print $ls_sql;
	$daoConfig = FabricaDao::CrearDAO('C','sigesp_config','',$criterio);		
	if($daoConfig->codemp=='')
	{
            $resultado=false;
        }
	else
	{
            $resultado=true;
	}
	unset($daoConfig);
	return rtrim($resultado);
}

/*******************************************************
* @Funci�n para calcular la fecha del cierre del periodo
* @retorno: string $fecha
********************************************************/
function obtenerFechaCierre()
{
	$dia=0;
	$fecha=$_SESSION['la_empresa']["periodo"];
	$day=-1;
	$fec=intval(substr($fecha,0,4));
	if(($fec % 4) ==0 ){
		$dia = 1;
	}	
	else{
		$dia = 0;
	}
	$dia = (365 + $dia + $day) ;
	$mk=mktime(9,0,0,intval(substr($fecha,5,2)),intval(substr($fecha,8,2)),intval(substr($fecha,0,4)));
	$arr=getdate($mk+ ($dia * 24 * 60 * 60));
	$fecha=	$arr["mday"]."-".$arr["mon"]."-".$arr["year"];
	return $fecha;
}// end function

/*******************************************************
* @Funci�n que retorna el nombre del mes enviado como parametro
* - $mes: Mes del nombre a obtener el ultimo dia.
* @retorno: string $nommes
********************************************************/
function obtenerNombreMes($mes)
{
	$nommes = "";
	switch($mes)
	{
		case '01':
			$nommes = "ENERO";
		break;
		case '02':
			$nommes = "FEBRERO";
		break;
		case '03':
			$nommes = "MARZO";
		break;
		case '04':
			$nommes = "ABRIL";
		break;
		case '05':
			$nommes = "MAYO";
		break;
		case '06':
			$nommes = "JUNIO";
		break;
		case '07':
			$nommes = "JULIO";
		break;
		case '08':
			$nommes = "AGOSTO";
		break;
		case '09':
			$nommes = "SEPTIEMBRE";
		break;
		case '10':
			$nommes = "OCTUBRE";
		break;
		case '11':
			$nommes = "NOVIEMBRE";
		break;
		case '12':
			$nommes = "DICIEMBRE";
		break;
	}
	return $nommes;
}// end function

   //-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_formatonumerico($as_valor)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_formatonumerico
		//		   Access: public
		//	    Arguments: as_valor  // valor sin formato num�rico
		//	      Returns: as_valor valor num�rico formateado
		//	  Description: Funci�n que le da formato a los valores num�ricos que vienen de la BD
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci�n: 01/02/2007 								Fecha �ltima Modificaci�n : 
		//////////////////////////////////////////////////////////////////////////////
		if (empty($as_valor))
		{
			$as_valor="0.00";
		}
		$as_valor=str_replace(".",",",$as_valor);
		if($as_valor<0)
		{
			$ls_temp="-";
			$as_valor=abs($as_valor);
		}
		else
		{
			$ls_temp="";
		}
		$li_poscoma = strpos($as_valor, ",");
		$li_contador = 1;
		if ($li_poscoma==0)
		{
			$li_poscoma = strlen($as_valor);
			$as_valor = $as_valor.",00";
		}
		$as_valor = substr($as_valor,0,$li_poscoma+3);
		$li_poscoma = $li_poscoma - 1;
		for($li_index=$li_poscoma;$li_index>=0;--$li_index)
		{
			if(($li_contador==3)&&(($li_index-1)>=0)) 
			{
				$as_valor = substr($as_valor,0,$li_index).".".substr($as_valor,$li_index);
				$li_contador=1;
			}
			else
			{
				$li_contador=$li_contador + 1;
			}
		}
		$as_valor=$ls_temp.$as_valor;
		$li_poscoma=strpos($as_valor, ",");
		$as_decimal=str_pad(substr($as_valor,$li_poscoma+1,2),2,"0");
		$as_valor=substr($as_valor,0,$li_poscoma+1).$as_decimal;
		return $as_valor;
	}// end function uf_formatonumerico
	//-----------------------------------------------------------------------------------------------------------------------------------


?>