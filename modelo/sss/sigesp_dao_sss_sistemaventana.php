<?php
/***********************************************************************************
* @Modelo para la definici�n de Sistema Ventana. 
* de datos.
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
**********************************************************************
* @fecha modificacion  
* @autor  
* @descripcion  
***********************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_registroeventos.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/modelo/sss/sigesp_dao_sss_registrofallas.php');

class SistemaVentana extends DaoGenerico
{
	public $valido = true;
	public $mensaje;
	public $criterio;
	public $campo;
	public $arrsistema = array();
	private $conexionbd;
	
	public function __construct()
	{
		parent::__construct ( 'sss_sistemas_ventanas' );
		$this->conexionbd = $this->obtenerConexionBd(); 
	}
	
/***********************************************************************************
* @Funci�n para insertar las ventanas por sistema
* @parametros: 
* @retorno:
* @fecha de creaci�n: 02/10/2008.
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/			
	function incluirLocal()
	{
		$this->save();	
		if($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('INSERTAR',$this->valido);
		}
	}

/***********************************************************************************
* @Funci�n para insertar las ventanas por sistema
* @parametros: 
* @retorno:
* @fecha de creaci�n: 02/10/2008.
* @autor: Ing.Gusmary Balza
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/			
	function modificarLocal()
	{
		$this->Replace();	
		if($this->conexionbd->HasFailedTrans())
		{
			$this->valido  = false;	
			$this->mensaje=$this->conexionbd->ErrorMsg();
			$this->incluirSeguridad('ACTUALIZAR',$this->valido);
		}
	}
	
/***********************************************************************************
* @Funci�n que busca el c�digo del sistema ventana
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 09/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	public function obtenerCodigoMenu()
	{
            $codmenu="";
		$consulta = "SELECT codmenu ".
					"  FROM $this->_table ".
					" WHERE $this->_table.codsis = '$this->codsis' ".
					"	AND $this->_table.nomfisico ='$this->nomfisico' ";
		$result = $this->conexionbd->Execute($consulta); 
		if($result === false)
		{
			$this->valido  = false;
		}
		else
		{
			if(!$result->EOF)
			{   
				$codmenu=$result->fields["codmenu"];
			}
			$result->Close();
		}
		return $codmenu;
	}
	
	
/***********************************************************************************
* @Funci�n que busca especificacmente si una opci�n de menu es v�lida � no 
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 12/11/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	public function verificarCampoMenu()
	{
		$campo=0;
		$consulta = "SELECT codmenu ".
					"  FROM $this->_table ".
					" WHERE $this->_table.codsis = '$this->codsis' ".
					"	AND $this->_table.codmenu = '$this->codmenu' ".
					"   AND $this->_table.$this->campo = '1'";
		$result = $this->conexionbd->Execute($consulta); 
		if($result === false)
		{
			$this->valido  = false;
		}
		else
		{
			if(!$result->EOF)
			{   
				$campo=1;
			}
			$result->Close();
		}
		return $campo;
	}
	
	
	
/***********************************************************************************
* @Funci�n que busca las opciones de menu seg�n el usuario
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 28/08/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	public function obtenerMenuUsuario()
	{
		$consulta = "SELECT $this->_table.codmenu, $this->_table.codsis, nomlogico, nomfisico, codpadre, nivel, hijo, marco, orden, ".
					" $this->_table.visible,$this->_table.enabled,$this->_table.leer,$this->_table.incluir,$this->_table.cambiar,$this->_table.eliminar,$this->_table.imprimir,$this->_table.administrativo,".
					" $this->_table.anular,$this->_table.ejecutar,$this->_table.ayuda,$this->_table.cancelar,$this->_table.enviarcorreo, $this->_table.descargar,  1 as valido ".
					"  FROM $this->_table ".
					" WHERE $this->_table.hijo = 1 ".
					"   AND $this->_table.codsis = '$this->codsis' ".
					"   AND $this->_table.visible = 1 ".
					"   AND $this->_table.enabled = 1 ".
					" UNION ".
					" SELECT $this->_table.codmenu, $this->_table.codsis, nomlogico, nomfisico, codpadre, nivel, hijo, marco, orden, ".
					" $this->_table.visible,$this->_table.enabled,$this->_table.leer,$this->_table.incluir,$this->_table.cambiar,$this->_table.eliminar,$this->_table.imprimir,$this->_table.administrativo,".
					" $this->_table.anular,$this->_table.ejecutar,$this->_table.ayuda,$this->_table.cancelar,$this->_table.enviarcorreo, $this->_table.descargar, 1 as valido ".
					"  FROM $this->_table ".
					" INNER JOIN sss_derechos_usuarios ".
					"    ON $this->_table.hijo = 0 ".
					"   AND $this->_table.codsis = '$this->codsis' ".
					"   AND sss_derechos_usuarios.codusu = '$this->codusu' ".
					"   AND sss_derechos_usuarios.enabled = 1 ". 
					"   AND sss_derechos_usuarios.visible = 1 ". 
					"   AND $this->_table.codsis = sss_derechos_usuarios.codsis ".
					"   AND $this->_table.codmenu = sss_derechos_usuarios.codmenu ".
					" ORDER BY nivel, orden";
		$result = $this->conexionbd->Execute($consulta); 
		return $result;
	}
	
	
/***********************************************************************************
* @Funci�n que busca las funcionalidades del menu
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 07/11/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	public function obtenerOpcionesMenu()
	{
		$consulta = "SELECT $this->_table.codmenu, $this->_table.codsis, nomlogico, nomfisico, codpadre, nivel, hijo, marco, orden, ".
					" 		$this->_table.visible,$this->_table.enabled,$this->_table.leer,$this->_table.incluir,$this->_table.cambiar,$this->_table.eliminar,$this->_table.imprimir,$this->_table.administrativo,".
					" 		$this->_table.anular,$this->_table.ejecutar,$this->_table.ayuda,$this->_table.cancelar,$this->_table.enviarcorreo, $this->_table.descargar, 1 as valido ".
					"  FROM $this->_table ".				
					" WHERE $this->_table.codsis='$this->codsis'".
			        "   AND $this->_table.enabled=1 ".
			        "   AND $this->_table.visible=1 "; 								   
		$cadena=" ";
        $total = count((array)$this->criterio);
        for ($contador = 0; $contador < $total; $contador++)
		{
          	$cadena.= $this->criterio[$contador]['operador']." ".$this->criterio[$contador]['criterio']." ".
 		               $this->criterio[$contador]['condicion']." ".$this->criterio[$contador]['valor']." ";
        }
        $consulta.= $cadena;
        $consulta.= "ORDER BY codmenu, nomlogico";
		$result = $this->conexionbd->Execute($consulta); 
		return $result;
	}
		
	
/***********************************************************************************
* @Funci�n que busca las opciones de la Barra de Herramientas seg�n el usuario
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 29/08/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	public function obtenerBarraHerramientaUsuario()
	{
		$consulta = "SELECT $this->_table.codmenu, $this->_table.codsis, MAX(sss_derechos_usuarios.visible) AS visible, MAX(sss_derechos_usuarios.leer) as leer, ".
					"       MAX(sss_derechos_usuarios.incluir) as incluir, MAX(sss_derechos_usuarios.cambiar) as cambiar, MAX(sss_derechos_usuarios.eliminar) as eliminar, ".
					"       MAX(sss_derechos_usuarios.imprimir) as imprimir, MAX(sss_derechos_usuarios.anular) as anular, MAX(sss_derechos_usuarios.ejecutar) as ejecutar, ".
					"       MAX(sss_derechos_usuarios.administrativo) as administrativo, MAX(sss_derechos_usuarios.ayuda) as ayuda, MAX(sss_derechos_usuarios.cancelar) as cancelar, ".
					"       MAX(sss_derechos_usuarios.descargar) as descargar ".
					"  FROM $this->_table ".
					" INNER JOIN sss_derechos_usuarios ".
					"    ON $this->_table.hijo = 0 ".
					"   AND $this->_table.codsis = '$this->codsis' ".
					"   AND $this->_table.nomfisico = '$this->nomfisico' ".
					"   AND sss_derechos_usuarios.codusu = '$this->codusu' ".
					"   AND sss_derechos_usuarios.visible = '1' ". 
					"   AND $this->_table.codsis = sss_derechos_usuarios.codsis ".
					"   AND $this->_table.codmenu = sss_derechos_usuarios.codmenu ".
					" GROUP BY $this->_table.codmenu, $this->_table.codsis, nivel, orden ".										
					" ORDER BY codmenu,codsis, nivel, orden";

		$result = $this->conexionbd->Execute($consulta); 
		return $result;
	}
	

/****************************************************************************
* @Funci�n que Verifica que el usuario tenga acceso a la funcionalidad y 
* a la acci�n que proceso
* @parametros: 
* @retorno: Verdadero � false seg�n la permisolog�a
* @fecha de creaci�n: 03/09/2008
* @autor: Ing. Yesenia Moreno de Lang
*****************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
*******************************************************************************/		
	public function verificarUsuario()
	{
		$usuariovalido = false;
		$consulta = "SELECT $this->_table.codmenu ".
					"  FROM $this->_table ".
					" INNER JOIN sss_derechos_usuarios ".
					"    ON $this->_table.hijo = 0 ".
					"   AND $this->_table.codsis = '$this->codsis' ".
					"   AND $this->_table.nomfisico = '$this->nomfisico' ".
					"   AND sss_derechos_usuarios.codusu = '$this->codusu' ".
					"   AND sss_derechos_usuarios.visible = '1' ". 
					"   AND sss_derechos_usuarios.$this->campo = '1' ". 
					"   AND $this->_table.codsis = sss_derechos_usuarios.codsis ".
					"   AND $this->_table.codmenu = sss_derechos_usuarios.codmenu ";
		$result = $this->conexionbd->Execute($consulta); 
		if(!$result->EOF)
		{   
			$usuariovalido=true;
		}
		$result->Close();
		return $usuariovalido;
	}
	
	
/****************************************************************************
* @Funci�n que obtiene las opciones de menu de un usuario y funcionalidad
* @parametros: 
* @retorno: Verdadero � false seg�n la permisolog�a
* @fecha de creaci�n: 03/09/2008
* @autor: Ing. Gusmary Balza
*****************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
*******************************************************************************/		
	function obtenerMenu()
	{
		try
		{
			$consulta = " SELECT $this->_table.codmenu, $this->_table.codsis, nomlogico, $this->_table.visible,$this->_table.enabled,".
						" 		 $this->_table.leer,$this->_table.incluir,$this->_table.cambiar,$this->_table.eliminar,".
						" 		 $this->_table.imprimir,$this->_table.administrativo,$this->_table.anular, ".
						" 		 $this->_table.ejecutar,$this->_table.ayuda,$this->_table.cancelar,$this->_table.enviarcorreo, ".
						"        $this->_table.descargar, 1 as valido".
						"   FROM $this->_table ".
						"  WHERE $this->_table.hijo = 0 ".
						"    AND $this->_table.codsis = '$this->codsis' ".
						"    AND $this->_table.codmenu = $this->codmenu ";
			 
			$result = $this->conexionbd->Execute($consulta);
			return $result;
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al consultar el men� de la funcionalidad '.$consulta.' '.$this->conexionbd->ErrorMsg();
	   	} 	
	}
	
/***********************************************************************************
* @Funci�n que busca en los arboles de seguridad y los inserta en Sistema Ventana
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 21/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	public function ActualizarMenu()
    {   
		try
		{
			$total=	count((array)$this->arrsistema);
			$this->actualizarData();
			for ($contsis=0; $contsis < $total; $contsis++)
			{	
				$nuevo=false;
				$codsis = $this->arrsistema[$contsis];
				$codsisaux = strtolower($codsis);
				$ext='.php';
				$ruta='../../sss/arbol/sigesp_arbol_';
				$version2=false;
				if (($codsisaux == 'apr') || ($codsisaux == 'sss')|| ($codsisaux == 'sfd'))
				{
					$ruta='../../controlador/sss/arbol/';
					$version2=true;
				}
				if ($codsisaux == 'cfg')
				{
					$accsis=$this->obtenerTipoSistema($codsis);
					if($accsis=='vista/cfg/sigesp_vis_cfg_principal.html')
					{
						$ruta='../../controlador/sss/arbol/';
						$version2=true;
					}
				}
				if ($codsisaux == 'mis')
				{
					$accsis=$this->obtenerTipoSistema($codsis);
					if($accsis=='vista/mis/sigesp_vis_mis_principal.html')
					{
						$ruta='../../controlador/sss/arbol/';
						$version2=true;
					}
				}
				if ($codsisaux == 'rpc')
				{
					$accsis=$this->obtenerTipoSistema($codsis);
					if($accsis=='vista/rpc/sigesp_vis_rpc_principal.html')
					{
						$ruta='../../controlador/sss/arbol/';
						$version2=true;
					}
				}
				if ($codsisaux == 'spg')
				{
					$accsis=$this->obtenerTipoSistema($codsis);
					if($accsis=='vista/spg/sigesp_vis_spg_principal.html')
					{
						$ruta='../../controlador/sss/arbol/';
						$version2=true;
					}
				}
				if ($codsisaux == 'scg')
				{
					$accsis=$this->obtenerTipoSistema($codsis);
					if($accsis=='vista/scg/sigesp_vis_scg_principal.html')
					{
						$ruta='../../controlador/sss/arbol/';
						$version2=true;
					}
				}
				if ($codsisaux == 'scf')
				{
					$accsis=$this->obtenerTipoSistema($codsis);
					if($accsis=='vista/scf/sigesp_vis_scf_principal.html')
					{
						$ruta='../../controlador/sss/arbol/';
						$version2=true;
					}
				}
				if ($codsisaux == 'sep')
				{
					$accsis=$this->obtenerTipoSistema($codsis);
					if($accsis=='vista/sep/sigesp_vis_sep_principal.html')
					{
						$ruta='../../controlador/sss/arbol/';
						$version2=true;
					}
				}
				if ($codsisaux == 'soc')
				{
					$accsis=$this->obtenerTipoSistema($codsis);
					if($accsis=='vista/soc/sigesp_vis_soc_principal.html')
					{
						$ruta='../../controlador/sss/arbol/';
						$version2=true;
					}
				}				
                                if(file_exists($ruta.$codsisaux.$ext))
				{
					include($ruta.$codsisaux.$ext);					
					for ($contador = 1; $contador <= $gi_total; $contador++)
					{
						$codpadre=0;
						if (intval($arbol['padre'][$contador])>0)
						{
							$padre=intval($arbol['padre'][$contador]);
							$this->codsis= $codsis;
							$this->nomlogico=$arbol['nombre_logico'][$padre];
							$this->nomfisico=$arbol['nombre_fisico'][$padre];
							if (trim($this->nomfisico)=='')
							{
								$this->nomfisico=' ';
							}
							$codpadre=$this->obtenerCodigo();
						}
						$hijo = 0;
						if (intval($arbol['numero_hijos'][$contador])>0)
						{
							$hijo = 1;
						}
						$this->actualizarNombreLogico($codsis,$arbol['nombre_logico'][$contador],$arbol['nombre_fisico'][$contador]);
						$obj = new SistemaVentana();
						$obj->codsis    = $codsis;
						$obj->nomlogico = $arbol['nombre_logico'][$contador];
						$obj->nomfisico = $arbol['nombre_fisico'][$contador];
						if (trim($obj->nomfisico)=='')
						{
							$obj->nomfisico=' ';
						}
						$codmenu=$obj->obtenerCodigo();
						if($codmenu>0)
						{
							$obj->codmenu   = $codmenu;	
						}
						else
						{
							$obj->codmenu   = $obj->obtenerCodigoMenuFinal();
							$nuevo=true;	
						}						
						$obj->codpadre  = $codpadre;
						$obj->nivel     = $arbol['nivel'][$contador]+1;
						$obj->hijo      = $hijo;
						$obj->marco     = 'principal';
						$obj->orden     = $arbol['id'][$contador];
						$obj->visible   = 1;
						$obj->enabled   = 1;
						$obj->leer      = 1;
						$obj->incluir   = 1;
						$obj->cambiar   = 1;
						$obj->eliminar  = 1;
						$obj->imprimir  = 1;
						$obj->administrativo = 1;
						$obj->anular         = 1;
						$obj->ejecutar       = 1;
						$obj->ayuda          = 1;
						$obj->cancelar       = 1;
						$obj->enviarcorreo   = 0;
						$obj->descargar      = 1;
						if ($version2)
						{
							$obj->visible   = $arbol['visible'][$contador];
							$obj->enabled   = $arbol['enabled'][$contador];
							$obj->leer      = $arbol['leer'][$contador];
							$obj->incluir   = $arbol['incluir'][$contador];
							$obj->cambiar   = $arbol['cambiar'][$contador];
							$obj->eliminar  = $arbol['eliminar'][$contador];
							$obj->imprimir  = $arbol['imprimir'][$contador];
							$obj->administrativo = $arbol['administrativo'][$contador];
							$obj->anular         = $arbol['anular'][$contador];
							$obj->ejecutar       = $arbol['ejecutar'][$contador];
							$obj->ayuda          = $arbol['ayuda'][$contador];
							$obj->cancelar       = $arbol['cancelar'][$contador];
							$obj->enviarcorreo   = $arbol['enviarcorreo'][$contador];
							$obj->descargar      = $arbol['descargar'][$contador];
						}
						if($nuevo)
						{
							$obj->incluirLocal();
						}
						else
						{
                            $obj->modificarLocal();
						}
						$nuevo=false;
				    	unset($obj);		    	
					}
				}
			}		
		}
		catch (exception $e) 
		{ 
			$this->valido  = false;	
			$this->mensaje='Error al Actualizar el men� ';
	   	}
    }
    
/***********************************************************************************
* @Funci�n que busca el c�digo del padre
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 01/11/2011
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	public function obtenerCodigo()
	{
		$codmenu=0;
		$consulta = "SELECT codmenu ".
					"  FROM $this->_table ".
					" WHERE $this->_table.codsis = '$this->codsis' ".
					"   AND $this->_table.nomlogico = '$this->nomlogico' ".
					"	AND $this->_table.nomfisico = '$this->nomfisico' ";
		$result = $this->conexionbd->Execute($consulta); 
		if($result === false)
		{
			$this->valido  = false;
		}
		else
		{
			if(!$result->EOF)
			{   
				$codmenu=$result->fields["codmenu"];
			}
			$result->Close();
		}
		return $codmenu;
	}    

    
/***********************************************************************************
* @Funci�n que busca el el �ltimo c�digo Menu
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 01/11/2011
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	public function obtenerCodigoMenuFinal()
	{
		$codmenu=0;
		$consulta = "SELECT codmenu ".
					"  FROM $this->_table ".
					"ORDER BY codmenu DESC";
		$result = $this->conexionbd->Execute($consulta); 
		if($result === false)
		{
			$this->valido  = false;
		}
		else
		{
			if(!$result->EOF)
			{   
				$codmenu=$result->fields["codmenu"]+1;
			}
			$result->Close();
		}
		return $codmenu;
	}    	

/***********************************************************************************
* @Funci�n que busca el c�digo del sistema ventana
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 09/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	public function actualizarData()
	{
		$consulta = "UPDATE $this->_table SET nomfisico=' ' ".
					" WHERE trim(nomfisico) ='' ";
		$result = $this->conexionbd->Execute($consulta); 
		
		$consulta = "UPDATE $this->_table SET nomlogico=replace(nomlogico,'á','�')";
		$result = $this->conexionbd->Execute($consulta); 

		$consulta = "UPDATE $this->_table SET nomlogico=replace(nomlogico,'é','�')";
		$result = $this->conexionbd->Execute($consulta); 

		$consulta = "UPDATE $this->_table SET nomlogico=replace(nomlogico,'�','�')";
		$result = $this->conexionbd->Execute($consulta); 
		
		$consulta = "UPDATE $this->_table SET nomlogico=replace(nomlogico,'ó','�')";
		$result = $this->conexionbd->Execute($consulta); 
		
		$consulta = "UPDATE $this->_table SET nomlogico=replace(nomlogico,'ú','�')";
		$result = $this->conexionbd->Execute($consulta); 

	}
/***********************************************************************************
* @Funci�n que busca el c�digo del sistema ventana
* @parametros: 
* @retorno: 
* @fecha de creaci�n: 09/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/		
	public function actualizarNombreLogico($codsis,$nomlogico,$nomfisico)
	{
		$consulta = "UPDATE sss_sistemas_ventanas SET nomlogico = REPLACE(nomlogico,'�','�')";
		$result = $this->conexionbd->Execute($consulta); 
		if(trim($nomfisico)<>'')
		{
			$consulta = "UPDATE $this->_table ".
						"   SET nomlogico='$nomlogico' ".
						" WHERE $this->_table.codsis = '$codsis' ".
						"	AND $this->_table.nomfisico = '$nomfisico' ";
			$result = $this->conexionbd->Execute($consulta); 
		}
	}

	public function obtenerTipoSistema($codsis)
	{
		$accsis='';
		$consulta = "SELECT accsis ".
					"  FROM sss_sistemas ".
					" WHERE codsis = '".strtoupper($codsis)."' ";
		$result = $this->conexionbd->Execute($consulta); 
		if($result === false)
		{
			$this->valido  = false;
		}
		else
		{
			if(!$result->EOF)
			{   
				$accsis=$result->fields["accsis"];
			}
			$result->Close();
		}
		return $accsis;
	}  	
	
/***********************************************************************************
* @Funci�n que Incluye el registro de la transacci�n exitosa
* @parametros: $evento
* @retorno:
* @fecha de creaci�n: 10/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/
	function incluirSeguridad($evento,$tipotransaccion)
	{
		if($tipotransaccion)
		{
			$objEvento = new RegistroEventos();
			$tiponotificacion = 'NOTIFICACION';
		}
		else
		{
			$objEvento = new RegistroFallas();
			$tiponotificacion = 'ERROR';
		}
		// Registro del Evento
		$objEvento->codemp = $this->codemp;
		$objEvento->codsis = 'SSS';
		$objEvento->nomfisico = $this->nomfisico;
		$objEvento->evento = $evento;
		$objEvento->desevetra = $this->mensaje;
		$objEvento->incluirEvento();
		// Env�o de Notificaci�n
		$objEvento->objNotificacion->codemp=$this->codemp;
		$objEvento->objNotificacion->sistema=$this->codsis;
		$objEvento->objNotificacion->tipo=$tiponotificacion;
		$objEvento->objNotificacion->titulo='ACTUALIZAR MENU';
		$objEvento->objNotificacion->usuario=$_SESSION['la_logusr'];
		$objEvento->objNotificacion->operacion=$this->mensaje;
		$objEvento->objNotificacion->enviarNotificacion();
		unset($objEvento);
	}
	
}	
?>