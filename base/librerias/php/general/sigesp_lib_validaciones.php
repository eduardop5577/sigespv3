<?php
/***********************************************************************************
* @librer�a que contiene las validaciones generales
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

function validaciones($var,$long,$tipo)
{
	$arregloval = explode("|",$tipo);
	for ($i=0;$i<count($arregloval);$i++)
	{
		switch ($arregloval[$i])
		{
			//validar dato vacio
			case 'novacio':
				$correcto = false;
				if ($var!='')
				{					
					$correcto = true;
				}
				return $correcto;
			break;
			
			//validar dato num�rico
			case 'numero':
				$correcto = false;
				$longitud = strlen($var);
				if ($longitud <= $long)
				{
					if (preg_match('/^\d+$/', $var))					
					{
						//echo $var.' no es un dato numerico';
					}
					else
					{
						$correcto = true;
					}
				}				
				return $correcto;
			break;			
						
			//validar datos de tipo entero
			case 'entero':
				$correcto = false;
				if (filter_var($var,FILTER_VALIDATE_INT))
				{
					$correcto = true;			
				}				
				return $correcto;
			break;
			
			//validar datos de tipo float	
			case 'float':
				$correcto = false;
				if (filter_var($var,FILTER_VALIDATE_FLOAT))
				{
					$correcto = true;
				}				
				return $correcto;		
			break;
			
			//validar datos de tipo alfanumerico:letras,n�meros,comas, espacios, guiones
			case 'alfanumerico':
				$correcto = false;
				$longitud = strlen($var);
				if ($longitud <= $long)
				{
				 	if (preg_match('/^[a-zA-Z0-9������������()_@\s.\-]+$/', $var))				
					{
						$correcto = true;
					}
					else
					{
						
					}					
				}				
				return $correcto;		
			break;
			
			case 'vacioalfanumerico':
				$correcto = false;
				$longitud = strlen($var);
				if (($longitud <= $long) && ($longitud>0))
				{
				 	if (preg_match('/^[a-zA-Z0-9������������\s.\-]+$/', $var))	
					{
						$correcto = true;
					}				
				
				}
				elseif ($longitud==0)
				{
				 	$correcto = true;
				}
				return $correcto;
			break;
			
			//validar que no tenga caracteres especiales
			case 'vaciocaracteres':
				$correcto = false;
				$longitud = strlen($var);
				if (($longitud <= $long) && ($longitud>0))
				{
					if (filter_var($var,FILTER_SANITIZE_SPECIAL_CHARS))
					{
						$correcto = true;
					}					
				}
				elseif ($longitud==0)
				{
					 $correcto = true;
				}				
				return $correcto;		
			break;			
			
			case 'caracteres':
				$correcto = false;
				if (filter_var($var,FILTER_SANITIZE_SPECIAL_CHARS))
				{
					$correcto = true;
				}
				return $correcto;		
			break;
			
			//validar datos de email		
			case 'email':
				$correcto = false;
				if (filter_var($var, FILTER_VALIDATE_EMAIL))
				{   		
					$correcto = true;
				}
				return $correcto;
			break;
			
			case 'vacioemail':
				$correcto = false;
				$longitud = strlen($var);
				if (($longitud <= $long) && ($longitud>0))
				{
					if (filter_var($var, FILTER_VALIDATE_EMAIL))
					{   		
						$correcto = true;
					}					
				}	
				elseif ($longitud==0)
				{
				 	$correcto = true;
				}
				return $correcto;
			break;
			
			//validar datos de tel�fono con formato 5555-5555555
			case 'telefonoFormato':
				$correcto = false;
				if (preg_match('/^\d{4}-\d{7}$/', $var))
				{
					$correcto = true;
				}			
				return $correcto;		
			break;
			
			case 'vaciotelefono':
				$correcto = false;
				$longitud = strlen($var);
				if (($longitud <= $long) && ($longitud>0))
				{
					if (preg_match('/^\d{4}-\d{7}$/', $var))
					{
						$correcto = true;
					}					
				}	
				elseif ($longitud==0)
				{
				 	$correcto = true;
				}
				return $correcto;		
			break;
			
			case 'telefono':
				$correcto = false;
				$longitud = strlen($var);
				if (($longitud <= $long) && ($longitud>0))
				{
				 	if (preg_match('/^[0-9-\s.\-]+$/', $var))	
					{
						$correcto = true;
					}				
				
				}
				elseif ($longitud==0)
				{
				 	$correcto = true;
				}
				return $correcto;
			break;
			
			//validar datos de nombre y/o apellido
			case 'nombre':				
				$correcto = false;
				$longitud = strlen($var);				
				if (($longitud <= $long) && ($longitud>0))
				{
					if (!preg_match('/^[a-z ������.]*$/', $var))
					{
						//echo $var.' contiene caracteres no validos';
					}
					else
					{
						$correcto = true;
					}
				}	
				return $correcto;					
			break;			
			
			//validar datos de nombre y/o apellido con longitud exacta
			case 'longexacta':
				$correcto = false;
				$longitud = strlen($var);
				if ($longitud==long)
				{
					if (!preg_match('/^[a-z ������]*$/', $var))
					{
						//echo $var.' contiene caracteres no validos';
					}
					else
					{
						$correcto = true;
					}
				}	
				return $correcto;					
			break;
					
			//validar dato login de usuario de 4 hasta 30 caracteres de longitud, alfanumericos y guiones bajos.
			case 'login':
				$correcto = false;
				if (preg_match('/^[a-zd_]{4,20}$/i', $var))
				{
					$correcto = true;
				}
				return $correcto;		
			break;
			
			//validar contrase�a de usuario
			case 'contrase�a':
				$correcto = false;	
				if (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{4,8}$/', $var))
				{
					$correcto = true;			
				}
				return $correcto;			
			break;
			
			case 'cedula':
		//	'/^\d{8}$/'     
			/*	$correcto = false;	
				if (preg_match('^[a-zA-Z]\w{3,14}$', $var))
				{
					$correcto = true;			
				}
				else
				{
					echo $var.' no es una contrase�a segura';
				}
				return $correcto;*/			
			break;	
			
			
			default:
				echo 'No se estan ejecutando validaciones';		
			break;
		}
	}	
	return $correcto;					
}

?>