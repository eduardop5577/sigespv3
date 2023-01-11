<?php
/***********************************************************************************
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_sss_class_report
{
	var $obj="";
	var $io_sql;
	var $ds;
	var $ds_detalle;
	var $siginc;
	var $con;

	function __construct() // contructor
	{
		require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_datastore.php");
		$this->io_msg=new class_mensajes();
		$this->dat_emp=$_SESSION["la_empresa"];
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
		$this->io_sql=new class_sql($this->con);
		$this->io_funcion = new class_funciones();
		$this->ds=new class_datastore();
		$this->ds_detalle=new class_datastore();
	}

	function uf_sss_select_auditoria($codemp,$codusu,$evento,$codsis,$fecdes,$fechas,$numdocumento,$numprefijo)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function:   uf_sss_select_auditoria
		//	           Access:   public
		//  		Arguments:   codemp    // codigo de empresa
		//  			         codusu    // codigo de ususario
		//  			         evento    // codigo de evento
		//  			         codsis    // codigo de sistema
		//  			         ad_fecdes    // fecha de inicio del periodo de busqueda
		//  			         ad_fecdes    // fecha de cierre del periodo de busqueda
		//						 rs_data    // arreglo con los resultados de la consulta
		//	         Returns : $valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description:  Función que se encarga de realizar la busqueda  de las operaciones del sistema registradas en el modulo 
		//						de seguridad.
		//         Creado por:  Ing. Luis Anibal Lang           
		//     Modificado por:  Ing. María Beatriz Unda
		//   Fecha de Cracion:   20/05/2006							Fecha de Ultima Modificación:   25/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$valido=true;
		$criterio=" sss_registro_eventos.codemp='".$codemp."' ";
		if(!empty($codusu))
		{
			$criterio .= "  AND sss_registro_eventos.codusu ='".$codusu."'";
		}
		if(!empty($evento))
		{
			$criterio .="  AND sss_registro_eventos.evento ='".$evento."'";
		}
		if(!empty($codsis))
		{
			$criterio .="  AND sss_registro_eventos.codsis ='".$codsis."'";
		}
		if(!empty($numdocumento))
		{
			$criterio .="  AND sss_registro_eventos.desevetra  LIKE '%".$numdocumento."%'";
		}
		if(!empty($numprefijo))
		{
			$criterio .="  AND sss_registro_eventos.desevetra  LIKE '%".$numprefijo."%'";
		}
		if((!empty($fecdes))&&(!empty($fechas)))
		{
			$auxdesde=$this->io_funcion->uf_convertirdatetobd($fecdes);
			$auxhasta=$this->io_funcion->uf_convertirdatetobd($fechas);
			$min=" 23:59:59";
			$criterio .= " AND sss_registro_eventos.fecevetra >= '".$auxdesde."'".
			             " AND sss_registro_eventos.fecevetra <='".$auxhasta.$min."'" ;
		}
		$cadenasql="SELECT nomusu, apeusu, nomsis, evento, fecevetra, equevetra, desevetra, ".
				   "      (SELECT nomlogico FROM sss_sistemas_ventanas ".
				   "        WHERE sss_registro_eventos.codmenu=sss_sistemas_ventanas.codmenu".
				   "          AND sss_registro_eventos.codsis=sss_sistemas_ventanas.codsis) AS titven".
				   "  FROM sss_registro_eventos ".
				   " INNER JOIN sss_sistemas ".
				   "    ON ".$criterio.
				   "   AND sss_registro_eventos.codsis = sss_sistemas.codsis ".
				   " INNER JOIN sss_usuarios ".
				   "    ON ".$criterio.
				   "   AND sss_registro_eventos.codemp = sss_usuarios.codemp ".
				   "   AND sss_registro_eventos.codusu = sss_usuarios.codusu ".
				   " ORDER BY sss_registro_eventos.numeve";
	    $this->ds=$this->io_sql->select($cadenasql);
		if( $this->ds===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_sss_select_auditoria ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$valido=false;
		}
		return $valido; 
	} // fin function uf_sss_select_auditoria

	function uf_sss_select_permisos_usuario($codemp,$codusu,$codsis,$ai_orden)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function:   uf_sss_select_permisos_usuario
		//	           Access:   public
		//  		Arguments:   
		//  			         as_codemp     // codigo de empresa
		//  			         as_codusu     // codigo de usuario
		//  			         as_codsis     // codigo de sistema
		//  			         ai_orden   // parametro por el cual se ordenara el reporte (sistema ó usuario)
		//	         Returns :  $valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description:  Funcion que obtiene el nombre y apellido de un usuario y el nombre de los sistemas
		//         Creado por:  Ing. Luis Anibal Lang           
		//   Fecha de Cracion:  10/06/2006							Fecha de Ultima Modificación: 10/06/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$valido=false;
		$orden="sss_derechos_usuarios.codusu";
		$cadenasql="SELECT sss_derechos_usuarios.codsis, sss_derechos_usuarios.codusu, MAX(sss_sistemas.nomsis) AS nomsis, ".
				   "	   MAX(sss_usuarios.nomusu) AS nomusu, MAX(sss_usuarios.apeusu) AS apeusu ".
				   "  FROM sss_derechos_usuarios ".
				   " INNER JOIN sss_sistemas ".
				   "    ON sss_derechos_usuarios.codsis=sss_sistemas.codsis ".
				   " INNER JOIN sss_usuarios ".
				   "    ON sss_derechos_usuarios.codemp=sss_usuarios.codemp ".
				   "   AND sss_derechos_usuarios.codusu=sss_usuarios.codusu ".
				   " WHERE sss_derechos_usuarios.codemp='".$codemp."'";
		if($codusu!="")
		{
			$cadenasql=$cadenasql." AND sss_derechos_usuarios.codusu='".$codusu."'";
		}
		if($codsis!="")
		{
			$cadenasql=$cadenasql." AND sss_derechos_usuarios.codsis='".$codsis."'";
		}
		if($ai_orden==1)
		{
			$orden="sss_derechos_usuarios.codsis";
		}
		$cadenasql=$cadenasql." GROUP BY sss_derechos_usuarios.codsis, sss_derechos_usuarios.codusu ".
						      " ORDER BY ". $orden ."";
	    $this->rs_data=$this->io_sql->select($cadenasql);
		if($this->rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_sss_select_permisos_usuario ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if (!$this->rs_data->EOF)
			{
				$valido=true;
			}
		}
		return $valido; 
	} // fin function uf_sss_select_permisos_usuario

	function uf_sss_select_dt_permisos_usuario($codemp,$codusu,$codsis)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function:   uf_sss_select_dt_permisos_usuario
		//	           Access:   public
		//  		Arguments:   
		//  			         as_codemp     // codigo de empresa
		//  			         as_codusu     // codigo de usuario
		//  			         as_codsis     // codigo de sistema
		//	         Returns :  $valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description:  Funcion que obtiene el nombre y apellido de un usuario y el nombre de los sistemas
		//         Creado por:  Ing. Luis Anibal Lang           
		//   Fecha de Cracion:  10/06/2006							Fecha de Ultima Modificación: 10/06/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$valido=false;
		$cadenasql="SELECT codemp, codusu, codsis, MAX(visible) AS visible, MAX(enabled) AS enabled, MAX(leer) AS leer, MAX(incluir) AS incluir,".
				   "       MAX(cambiar) AS cambiar, MAX(eliminar) AS eliminar, MAX(imprimir) AS imprimir, MAX(administrativo) AS administrativo, ".
				   "	   MAX(anular) AS anular, MAX(ejecutar) AS ejecutar, ".
				   "       (SELECT nomlogico FROM sss_sistemas_ventanas ".
				   "         WHERE sss_derechos_usuarios.codmenu=sss_sistemas_ventanas.codmenu".
				   "           AND sss_derechos_usuarios.codsis=sss_sistemas_ventanas.codsis) as titven".
				   "  FROM sss_derechos_usuarios ".
				   " WHERE codemp='".$codemp."'".
				   "   AND codusu='".$codusu."'".
				   "   AND codsis='".$codsis."' ".
				   " GROUP BY codemp,codmenu,codsis,codusu ".
				   " ORDER BY titven";
	    $this->rs_data_detalle=$this->io_sql->select($cadenasql);
		if($this->rs_data_detalle===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_sss_select_dt_permisos_usuario ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if (!$this->rs_data_detalle->EOF)
			{
				$valido=true;
			}
		}
		return $valido;     
	} // fin function uf_sss_select_dt_permisos_usuario

	function uf_sss_select_permisos_grupo($codemp,$nomgru,$codsis,$ai_orden)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function:   uf_sss_select_permisos_grupo
		//	           Access:   public
		//  		Arguments:   
		//  			         as_codemp     // codigo de empresa
		//  			         as_nomgru     // Nombre de Grupo
		//  			         as_codsis     // codigo de sistema
		//  			         ai_orden   // parametro por el cual se ordenara el reporte (sistema ó usuario)
		//	         Returns :  $valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description:  Funcion que obtiene el nombre y apellido de un usuario y el nombre de los sistemas
		//         Creado por:  Ing. Luis Anibal Lang           
		//   Fecha de Cracion:  10/06/2006							Fecha de Ultima Modificación: 10/06/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$valido=false;
		$orden="sss_derechos_grupos.nomgru";
		$cadenasql="SELECT sss_derechos_grupos.codsis, sss_derechos_grupos.nomgru AS codusu, MAX(sss_sistemas.nomsis) AS nomsis, ".
				   "		'' AS nomusu, '' AS apeusu ".
				   "  FROM sss_derechos_grupos ".
				   " INNER JOIN sss_sistemas ".
				   "    ON sss_derechos_grupos.codsis=sss_sistemas.codsis ".
				   " INNER JOIN sss_grupos ".
				   "    ON sss_derechos_grupos.codemp=sss_grupos.codemp ".
				   "   AND sss_derechos_grupos.nomgru=sss_grupos.nomgru ".
				   " WHERE sss_derechos_grupos.codemp='".$codemp."'";
		if($nomgru!="")
		{
			$cadenasql=$cadenasql." AND sss_derechos_grupos.nomgru='".$nomgru."'";
		}
		if($codsis!="")
		{
			$cadenasql=$cadenasql." AND sss_derechos_grupos.codsis='".$codsis."'";
		}
		if($ai_orden==1)
		{
			$orden="sss_derechos_grupos.codsis";
		}
		$cadenasql=$cadenasql." GROUP BY sss_derechos_grupos.codsis, sss_derechos_grupos.nomgru ".
						      " ORDER BY ". $orden ."";
	    $this->rs_data=$this->io_sql->select($cadenasql);
		if($this->rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_sss_select_permisos_grupo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if (!$this->rs_data->EOF)
			{
				$valido=true;
			}
		}
		return $valido; 
	} // fin function uf_sss_select_permisos_grupo

	function uf_sss_select_dt_permisos_grupo($codemp,$nomgru,$codsis)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function:   uf_sss_select_dt_permisos_grupo
		//	           Access:   public
		//  		Arguments:   
		//  			         as_codemp     // codigo de empresa
		//  			         as_nomgru     // codigo de usuario
		//  			         as_codsis     // codigo de sistema
		//	         Returns :  $valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description:  Funcion que obtiene el nombre y apellido de un usuario y el nombre de los sistemas
		//         Creado por:  Ing. Luis Anibal Lang           
		//   Fecha de Cracion:  10/06/2006							Fecha de Ultima Modificación: 10/06/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$valido=false;
		$cadenasql="SELECT codemp, nomgru, codsis, MAX(visible) AS visible, MAX(enabled) AS enabled, MAX(leer) AS leer, MAX(incluir) AS incluir,".
				   "		MAX(cambiar) AS cambiar, MAX(eliminar) AS eliminar, MAX(imprimir) AS imprimir, MAX(administrativo) AS administrativo, ".
			   	   "		MAX(anular) AS anular, MAX(ejecutar) AS ejecutar, ".
				   "       (SELECT nomlogico FROM sss_sistemas_ventanas ".
				   "         WHERE sss_derechos_grupos.codmenu=sss_sistemas_ventanas.codmenu".
				   "           AND sss_derechos_grupos.codsis=sss_sistemas_ventanas.codsis) as titven".
				   "  FROM sss_derechos_grupos ".
				   " WHERE codemp='".$codemp."'".
				   "   AND nomgru='".$nomgru."'".
				   "   AND codsis='".$codsis."' ".
				   " GROUP BY codemp,codmenu,codsis,nomgru ".
				   " ORDER BY titven";
	    $this->rs_data_detalle=$this->io_sql->select($cadenasql);
		if($this->rs_data_detalle===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_sss_select_dt_permisos_grupo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if (!$this->rs_data_detalle->EOF)
			{
				$valido=true;
			}
		}
		return $valido; 
	} // fin function uf_sss_select_dt_permisos_grupo

	function uf_sss_select_grupos_por_usuario($codemp,$codusu)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function:   uf_sss_select_grupos_por_usuario
		//	           Access:   public
		//  		Arguments:   
		//  			         as_codemp     // codigo de empresa
		//  			         as_codusu     // codigo de usuario
		//	         Returns :  $valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description:  Funcion que obtiene el nombre y apellido de un usuario y el nombre de los sistemas
		//         Creado por:  Ing. Yesenia Moreno           
		//   Fecha de Cracion:  23/11/2015							Fecha de Ultima Modificación: 10/06/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$valido=false;
		$cadenasql="SELECT sss_usuarios.codusu, sss_usuarios.nomusu, sss_usuarios.apeusu, sss_usuarios_en_grupos.nomgru ".
				   "  FROM sss_usuarios_en_grupos ".
				   " INNER JOIN sss_usuarios ".
				   "    ON sss_usuarios_en_grupos.codemp='".$codemp."'".
				   "   AND sss_usuarios_en_grupos.codusu='".$codusu."'".
				   "   AND sss_usuarios_en_grupos.codemp=sss_usuarios.codemp ".
				   "   AND sss_usuarios_en_grupos.codusu=sss_usuarios.codusu ".
				   " ORDER BY sss_usuarios_en_grupos.nomgru ";
	    $this->rs_data=$this->io_sql->select($cadenasql);
		if($this->rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_sss_select_grupos_por_usuario ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if (!$this->rs_data->EOF)
			{
				$valido=true;
			}
		}
		return $valido; 
	} // fin function uf_sss_select_grupos_por_usuario

	function uf_sss_select_usuarios_por_grupo($codemp,$nomgru)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function:   uf_sss_select_usuarios_por_grupo
		//	           Access:   public
		//  		Arguments:   
		//  			         as_codemp     // codigo de empresa
		//  			         as_codusu     // codigo de usuario
		//	         Returns :  $valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description:  Funcion que obtiene el nombre y apellido de un usuario y el nombre de los sistemas
		//         Creado por:  Ing. Yesenia Moreno           
		//   Fecha de Cracion:  23/11/2015							Fecha de Ultima Modificación: 10/06/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$valido=false;
		$campo1 = $this->con->Concat('sss_usuarios.codusu',"'-'",'sss_usuarios.apeusu',"','",'sss_usuarios.nomusu');
		$cadenasql="SELECT ".$campo1." AS nomgru, sss_usuarios_en_grupos.nomgru AS codusu, '' AS nomusu, '' AS apeusu ".
				   "  FROM sss_usuarios_en_grupos ".
				   " INNER JOIN sss_usuarios ".
				   "    ON sss_usuarios_en_grupos.codemp='".$codemp."'".
				   "   AND sss_usuarios_en_grupos.nomgru='".$nomgru."'".
				   "   AND sss_usuarios_en_grupos.codemp=sss_usuarios.codemp ".
				   "   AND sss_usuarios_en_grupos.codusu=sss_usuarios.codusu ".
				   " ORDER BY sss_usuarios.codusu ";
	    $this->rs_data=$this->io_sql->select($cadenasql);
		if($this->rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_sss_select_grupos_por_usuario ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if (!$this->rs_data->EOF)
			{
				$valido=true;
			}
		}
		return $valido; 
	} // fin function uf_sss_select_usuarios_por_grupo
}
?>
