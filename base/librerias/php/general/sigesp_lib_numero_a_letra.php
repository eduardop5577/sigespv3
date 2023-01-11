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

class class_numero_a_letra
{
///////////////////////////////////////////////////////////////////////////////////////////////////
// 	Propiedades:
// 	$numero:	Es la cantidad a ser convertida a letras maximo 999,999,999,999.99
// 	$genero:	0 para femenino y 1 para masculino, es util dependiendo de la
// 				moneda ej: cuatrocientos pesos / cuatrocientas pesetas
// 	$moneda:	nombre de la moneda
// 	$prefijo:	texto a imprimir antes de la cantidad 
// 	$sufijo:	texto a imprimir despues de la cantidad
// 				tanto el $sufijo como el $prefijo en la impresion de cheques o
// 				facturas, para impedir que se altere la cantidad
// 	$mayusculas: 0 para minusculas, 1 para mayusculas indica como debe 
// 				mostrarse el texto
// 	$textos_posibles: contiene todas las posibles palabras a usar
// 	$arrtexto:	es el arreglo de los textos que se usan de acuerdo al genero 
// 				seleccionado
////////////////////////////////////////////////////////////////////////////////////////////////// 
	var $numero=0;
	var $genero=1;
	var $moneda="PESOS";
	var $prefijo="(***";
	var $sufijo="***)";
	var $mayusculas=1;
	//textos
	var $textos_posibles= array(
	0 => array ('UNA ','DOS ','TRES ','CUATRO ','CINCO ','SEIS ','SIETE ','OCHO ','NUEVE ','UN '),
	1 => array ('ONCE ','DOCE ','TRECE ','CATORCE ','QUINCE ','DIECISEIS ','DIECISIETE ','DIECIOCHO ','DIECINUEVE ',''),
	2 => array ('DIEZ ','VEINTE ','TREINTA ','CUARENTA ','CINCUENTA ','SESENTA ','SETENTA ','OCHENTA ','NOVENTA ','VEINTI'),
	3 => array ('CIEN ','DOSCIENTAS ','TRESCIENTAS ','CUATROCIENTAS ','QUINIENTAS ','SEISCIENTAS ','SETECIENTAS ','OCHOCIENTAS ','NOVECIENTAS ','CIENTO '),
        4 => array ('CIEN ','DOSCIENTOS ','TRESCIENTOS ','CUATROCIENTOS ','QUINIENTOS ','SEISCIENTOS ','SETECIENTOS ','OCHOCIENTOS ','NOVECIENTOS ','CIENTO '),
	5 => array ('MIL ','MILLON ','MILLONES ','CERO ','Y ','UNO ','DOS ','CON ','',''),
	6 => array (' ',' ',' ','','','','','','','')
	);
	var $arrtexto;
	var $io_funciones;

//////////////////////////////////////////////////////////////////////////////////////
//	Metodos:
//	_construct:	Inicializa textos
//	setNumero:	Asigna el numero a convertir a letra
//  setPrefijo:	Asigna el prefijo
//	setSufijo:	Asiga el sufijo
//	setMoneda:	Asigna la moneda
//	setGenero:	Asigan genero 
//	setMayusculas:	Asigna uso de mayusculas o minusculas
//	letra:		Convierte numero en letra
//	letraUnidad: Convierte unidad en letra, asigna miles y millones
//	letraDecena: Contiene decena en letra
//	letraCentena: Convierte centena en letra
//////////////////////////////////////////////////////////////////////////////////////////
	public function __construct()
    {
            for($i=0; $i<7;$i++)
   		for($j=0;$j<10;$j++)
                    $this->arrtexto[$i][$j]=$this->textos_posibles[$i][$j];
            require_once("sigesp_lib_funciones2.php");
            $this->io_funciones=new class_funciones();
	}
	

	function setNumero($num)
        {
		$this->numero=doubleval($num);
	}

	function setPrefijo($pre)
        {
		$this->prefijo=$pre;
	}

	function setSufijo($sub)
        {
		$this->sufijo=$sub;
	}

	function setMoneda($mon)
        {
		$this->moneda=$mon;
	}

	function setGenero($gen)
        {
		$this->genero=intval($gen);
	}

	function setMayusculas($may)
        {
		$this->mayusculas=intval($may);
	}

	function letra()
        {
               if($this->genero==1)
                { //masculino
                    $this->arrtexto[0][0]=$this->textos_posibles[5][5];
                    for($j=0;$j<9;$j++)
                        $this->arrtexto[3][$j]= $this->arrtexto[4][$j];
		}
                else
                {//femenino
                    $this->arrtexto[0][0]=$this->textos_posibles[0][0];
                    for($j=0;$j<9;$j++)
                        $this->arrtexto[3][$j]= $this->arrtexto[3][$j];
		}
                
		$cnumero=sprintf("%.2f",$this->numero);		
		$cnumero=$this->io_funciones->uf_cerosizquierda($cnumero,15);		
                $texto="";
		if(strlen($cnumero)>15)
                {
			$texto="Excede tama�o permitido";
		}
                else
                {
			$hay_significativo=false;
                        
			for ($pos=0; $pos<12; $pos++)
                        {
				// Control existencia D�gito significativo 
   				if (!($hay_significativo)&&(substr($cnumero,$pos,1) == '0')) ;
   				else $hay_dignificativo = true;			
   				// Detectar Tipo de D�gito 
   				switch($pos % 3)
                                {
                                        case 0: $texto.=$this->letraCentena($pos,$cnumero); break;
   					case 1: $texto.=$this->letraDecena($pos,$cnumero); break;
   					case 2: $texto.=$this->letraUnidad($pos,$cnumero); break;
				}
			}			
			// Detectar caso 0 
   			if ($texto == '') $texto = $this->arrtexto[5][3];
			if($this->mayusculas)
			{
                            $this->moneda="CENTIMOS";
                            $texto=strtoupper($this->prefijo.$texto."BOLIVARES CON ".substr($cnumero,-2)."/100 ".$this->moneda." ".$this->sufijo);
			}
			else
			{//minusculas
				$texto=strtolower($this->prefijo.$texto." CON ".substr($cnumero,-2)."/100 ".$this->moneda." ".$this->sufijo);	
			}
		}
		return $texto;

	}

	function __toString()
        {
		return $this->letra();
	}

	//traducir letra a unidad
	function letraUnidad($pos,$cnumero)
        {
            $unidad_texto="";
            if(!((substr($cnumero,$pos,1) == '0') || (substr($cnumero,$pos - 1,1) == '1') || ((substr($cnumero, $pos - 2, 3) == '001') &&  (($pos == 2) || ($pos == 8)) )))
            { 
		if((substr($cnumero,$pos,1) == '1') && ($pos <= 6))
                {
                    $unidad_texto.=$this->arrtexto[0][9]; 
		}
                else
                {
                    $unidad_texto.=$this->arrtexto[0][substr($cnumero,$pos,1) - 1];
		}
            }
            if((($pos == 2) || ($pos == 8)) && (substr($cnumero, $pos - 2, 3) != '000'))
            {//miles
                if(substr($cnumero,$pos,1)=='1')
                {
                    $unidad_texto=substr($unidad_texto,0,-2)." ";
                    $unidad_texto.= $this->arrtexto[5][0]; 
		}
                else
                {
                    $unidad_texto.=$this->arrtexto[5][0]; 
		}
            }
            if($pos == 2 && substr($cnumero, $pos - 2, 3) != '000')
            {
                if(substr($cnumero, 1, 6) == '000001')
                {//millones cambie substr($cnumero, 1, 6) por substr($cnumero, 1, 5)
                   $unidad_texto.=$this->arrtexto[5][1];
                }
                else
                {
                    if(substr($cnumero, 3, 3) == '000')
                    {//millones cambie substr($cnumero, 1, 6) por substr($cnumero, 1, 5)
                    
                        $unidad_texto.=$this->arrtexto[5][2];
                    }
                    else
                    {
                       $unidad_texto.=$this->arrtexto[6][0];
                    }
                }
            }
            if($pos == 5 && substr($cnumero, $pos - 2, 3) != '000')
            {
		if(substr($cnumero, 1, 5) == '00001')
                {//millones cambie substr($cnumero, 1, 6) por substr($cnumero, 1, 5)
                    $unidad_texto.=$this->arrtexto[5][1];
		}
                else
                {
                    $unidad_texto.=$this->arrtexto[5][2];
		}
            }
            return $unidad_texto;
	}
	//traducir digito a decena
	function letraDecena($pos,$cnumero)
        {
            $decena_texto="";
            if (substr($cnumero,$pos,1) == '0')
            {
                return;
            }
            else if(substr($cnumero,$pos + 1,1) == '0')
            { 
                $decena_texto.=$this->arrtexto[2][substr($cnumero,$pos,1)-1];
            }
            else if(substr($cnumero,$pos,1) == '1')
            { 
                $decena_texto.=$this->arrtexto[1][substr($cnumero,$pos+ 1,1)- 1];
            }
            else if(substr($cnumero,$pos,1) == '2')
            {
                $decena_texto.=$this->arrtexto[2][9];
            }
            else
            {
                $decena_texto.=$this->arrtexto[2][substr($cnumero,$pos,1)- 1] . $this->arrtexto[5][4];
            }
            return $decena_texto;
        }
	//traducir digito centena
        function letraCentena($pos,$cnumero)
        {
            $centena_texto="";
            if (substr($cnumero,$pos,1) == '0') return;
            $pos2 = 3;
            if((substr($cnumero,$pos,1) == '1') && (substr($cnumero,$pos+ 1, 2) != '00'))
            {
                $centena_texto.=$this->arrtexto[$pos2][9];
            }
            else
            {
                $centena_texto.=$this->arrtexto[$pos2][substr($cnumero,$pos,1) - 1];
            }
            return $centena_texto;
	}
} // end class
?>