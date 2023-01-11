<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_sno
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;
	var $ls_codnom;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno
		//		   Access: public
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
   		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();				
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->ls_codnom="0000";
		if(array_key_exists("la_nomina",$_SESSION))
		{
			if(array_key_exists("codnom",$_SESSION["la_nomina"]))
			{
				$this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
			}
		}
	}// end function sigesp_sno
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Sección a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Función que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT value ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";
		if(trim($as_valor)=="")		
		{
			if($as_tipo=='C')
			{
				$as_valor="-";
			}
			else
			{
				$as_valor="0";
			}
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["value"];
				$li_i=$li_i+1;
			}
			if($li_i==0)
			{
				$lb_valido=$this->uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
				if ($lb_valido)
				{
					$ls_valor=$this->uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
				}
			}
			$this->io_sql->free_result($rs_data);		
		}
		return rtrim($ls_valor);
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Sección a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Función que inserta la variable de configuración
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="DELETE ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			if(trim($as_valor)=="")		
			{
				if($as_tipo=='C')
				{
					$as_valor="-";
				}
				else
				{
					$as_valor="0";
				}
			}
			switch ($as_tipo)
			{
				case "C"://Caracter
					$valor = $as_valor;
					break;

				case "D"://Double
					$as_valor=str_replace(".","",$as_valor);
					$as_valor=str_replace(",",".",$as_valor);
					$valor = $as_valor;
					break;

				case "B"://Boolean
					$valor = $as_valor;
					break;

				case "I"://Integer
					$valor = intval($as_valor);
					break;
			}
			$ls_sql="INSERT INTO sigesp_config(codemp, codsis, seccion, entry, value, type)VALUES ".
					"('".$this->ls_codemp."','".$as_sistema."','".$as_seccion."','".$as_variable."','".$valor."','".$as_tipo."')";
					
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
			else
			{
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	}// end function uf_insert_config	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_fin_mes($ad_fecdes,$ad_fechas)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_fin_mes
		//		   Access: public
		//	    Arguments: ad_fecdes // Fecha Desde
		//				   ad_fechas // fecha Hasta
		//	      Returns: lb_valido True si se cumple la condición de fin de mes ó False si no se cumple
		//	  Description: función que dada una fecha de inicio y una fecha fin se verifica si es fin de mes
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fechas=$this->io_funciones->uf_convertirfecmostrar($ad_fechas);
		$ad_fechas=$this->uf_suma_fechas($ad_fechas,1);
		$ls_mesdes=substr($ad_fecdes,5,2);
		$ls_meshas=substr($ad_fechas,3,2);
		if($ls_mesdes==$ls_meshas)
		{
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_fin_mes	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_suma_fechas($ad_fecha,$ai_ndias)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_suma_fechas
		//		   Access: public
		//	    Arguments: ad_fecha // Fecha a la que se desa sumar
		//                 ai_ndias // Cantidad de dias a sumar          
		//	      Returns: nuevafecha-> variable date
		//	  Description: suma una cantidad de dias pasado por parametros  a una fecha pasada por parametros 
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($ai_ndias>0)
		{
			$dia=substr($ad_fecha,0,2);      
			$mes=substr($ad_fecha,3,2);      
			$anio=substr($ad_fecha,6,4);      
			$ultimo_dia=adodb_date("d",adodb_mktime(0, 0, 0,$mes+1,0,$anio));
			$dias_adelanto=$ai_ndias;
			$siguiente=$dia+$dias_adelanto;
			if($ultimo_dia<$siguiente)
			{        
				$dia_final=$siguiente-$ultimo_dia;
				$mes++;         
				if($ai_ndias=='365')
				{
					$dia_final=$dia;
				}    
				if($mes=='13')
				{            
					$anio++;
					$mes='01';        
				}      
				$fecha_final=str_pad($dia_final,2,"0",0).'/'.str_pad($mes,2,"0",0).'/'.$anio; 
			}
			else   
			{ 
				$fecha_final=str_pad($siguiente,2,"0",0).'/'.str_pad($mes,2,"0",0).'/'.$anio; 
			} 
			$ls_dia=substr($fecha_final,0,2);
			$ls_mes=substr($fecha_final,3,2);
			$ls_ano=substr($fecha_final,6,4);
			while(checkdate(intval($ls_mes),intval($ls_dia),intval($ls_ano))==false)
			{ 
			   	$ls_dia=$ls_dia-1; 
				break;
			} 
			while(checkdate(intval($ls_mes),intval($ls_dia),intval($ls_ano))==false)
			{ 
			   	$ls_dia=$ls_dia-1; 
				break;
			} 
			while(checkdate(intval($ls_mes),intval($ls_dia),intval($ls_ano))==false)
			{ 
			   	$ls_dia=$ls_dia-1; 
				break;
			} 
			$fecha_final=$ls_dia."/".$ls_mes."/".$ls_ano;
		}
		else
		{
			$fecha_final=$ad_fecha;
		}
		
		return $fecha_final;
    }// end function uf_suma_fechas	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_nro_lunes($ad_fecdes,$ad_fechas)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_nro_lunes
		//		   Access: public
		//	    Arguments: ad_fecdes // Fecha Desde
		//				   ad_fechas // fecha Hasta
		//	      Returns: $li_valor cantidad de lunes del rango de fecha
		//	  Description: función que dada una fecha de inicio y una fecha fin se cuentan cuantos lunes tienen
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_valor=0;
		$ad_fecdes=$this->io_funciones->uf_convertirfecmostrar($ad_fecdes);
		$ad_fechas=$this->io_funciones->uf_convertirfecmostrar($ad_fechas);
		$ld_desde=adodb_mktime(0,0,0,substr($ad_fecdes,3,2),substr($ad_fecdes,0,2),substr($ad_fecdes,6,4));
		$ld_hasta=adodb_mktime(0,0,0,substr($ad_fechas,3,2),substr($ad_fechas,0,2),substr($ad_fechas,6,4));
		while ($ld_desde<=$ld_hasta)
		{
			$ld_fecha=adodb_mktime(0,0,0,substr($ad_fecdes,3,2),substr($ad_fecdes,0,2),substr($ad_fecdes,6,4));
			if(strftime('%w',$ld_fecha)==1)
			{
				$li_valor=$li_valor+1;
			}
			$ad_fecdes=$this->uf_suma_fechas($ad_fecdes,1);
			$ld_desde=adodb_mktime(0,0,0,substr($ad_fecdes,3,2),substr($ad_fecdes,0,2),substr($ad_fecdes,6,4));
		}
		return $li_valor;
	}// end function uf_nro_lunes	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_nro_sabydom($ad_fecdes,$ad_fechas)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_nro_sabydom
		//		   Access: public
		//	    Arguments: ad_fecdes // Fecha Desde
		//				   ad_fechas // fecha Hasta
		//				   ai_dia // día de la semana 6->sábado y 0->Domingo
		//	      Returns: $li_valor cantidad de sábados y domingos del rango de fecha
		//	  Description: función que dada una fecha de inicio y una fecha fin se cuentan cuantos sábados y domingos tienen
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 16/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_valor=0;
		$ad_fecdes=$this->io_funciones->uf_convertirfecmostrar($ad_fecdes);
		$ad_fechas=$this->io_funciones->uf_convertirfecmostrar($ad_fechas);
		$ld_desde=adodb_mktime(0,0,0,substr($ad_fecdes,3,2),substr($ad_fecdes,0,2),substr($ad_fecdes,6,4));
		$ld_hasta=adodb_mktime(0,0,0,substr($ad_fechas,3,2),substr($ad_fechas,0,2),substr($ad_fechas,6,4));
		while ($ld_desde<=$ld_hasta)
		{
			$ld_fecha=adodb_mktime(0,0,0,substr($ad_fecdes,3,2),substr($ad_fecdes,0,2),substr($ad_fecdes,6,4));
			if((strftime('%w',$ld_fecha)==6)||(strftime('%w',$ld_fecha)==0))
			{
				$li_valor=$li_valor+1;
			}
			$ad_fecdes=$this->uf_suma_fechas($ad_fecdes,1);
			$ld_desde=adodb_mktime(0,0,0,substr($ad_fecdes,3,2),substr($ad_fecdes,0,2),substr($ad_fecdes,6,4));
		}
		return $li_valor;
	}// end function uf_nro_sabydom	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_periodo_previo($ai_anoprev,$ai_periprev)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_periodo_previo
		//		   Access: public
		//	    Arguments: ai_anoprev // Año Previo
		//                 ai_periprev // periodo previo          
		//	      Returns: lb_valido True si se ejecuto correctamente la funación y false si hubo error
		//	  Description: función que devuelve el período previo de la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_anoprev=$_SESSION["la_nomina"]["anocurnom"];
		$ai_periprev=(intval($_SESSION["la_nomina"]["peractnom"])-1);
		if($ai_periprev<1)
		{
			$ai_anoprev=(intval($ai_anoprev)-1);
			$ls_sql="SELECT numpernom ".
					"  FROM sno_hnomina ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ".
					"   AND anocurnom='".$ai_anoprev."' ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_periodo_previo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$lb_valido=false;
			}
			else
			{
				while($row=$this->io_sql->fetch_row($rs_data))
				{
					$ai_periprev=$row["numpernom"];
				}
				if($ai_periprev<1)
				{
					$lb_valido=false;
				}
				$this->io_sql->free_result($rs_data);
			}
		}
		$ai_periprev=str_pad($ai_periprev,3,"0",0);
		$arrResultado['ai_anoprev']=$ai_anoprev;
		$arrResultado['ai_periprev']=$ai_periprev;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
    }// end function uf_periodo_previo	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_anios_historicos()
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_anios_historicos
		//		   Access: public
		//	    Arguments: 
		//	      Returns: lb_valido True si se ejecuto correctamente la funación y false si hubo error
		//	  Description: función que devuelve los años historicos que hay
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/01/2021 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$anocurnom="";
		$ls_sql="SELECT anocurnom ".
				"  FROM sno_hnomina ".
				" WHERE codemp='".$this->ls_codemp."' ".
				" GROUP BY anocurnom";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_anios_historicos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$anocurnom .= $rs_data->fields["anocurnom"].",";
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}
		return $anocurnom;						
    }// end function uf_anios_historicos	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_sessionnomina()
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_sessionnomina
		//		   Access: public (en toda las pantallas de procesos)
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que crea la sessión de la nómina actual
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/02/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if(array_key_exists("nom",$_GET))
		{
			$ls_nomina=$_GET["nom"];
		}
		else
		{
			$ls_nomina=$_SESSION["la_nomina"]["codnom"];
		}
		$ls_sql="SELECT sno_nomina.codnom, sno_nomina.desnom, sno_nomina.peractnom, sno_nomina.diabonvacnom, sno_nomina.diareivacnom, ".
				"		sno_nomina.adenom, sno_periodo.fecdesper, sno_periodo.fechasper, sno_periodo.cerper, sno_periodo.conper,".
				"		sno_periodo.totper, sno_nomina.anocurnom, sno_nomina.tippernom, sno_nomina.perresnom, sno_nomina.racnom, ".
				"		sno_nomina.subnom, sno_nomina.espnom, sno_nomina.consulnom, sno_nomina.descomnom, sno_nomina.codpronom, ".
				"		sno_nomina.codbennom, sno_nomina.conaponom, sno_nomina.cueconnom, sno_nomina.notdebnom, ".
				"       sno_nomina.numvounom, sno_nomina.recdocnom, sno_nomina.recdocapo, sno_nomina.tipdocnom, sno_nomina.tipdocapo, ".
				"		sno_nomina.tipnom, sno_nomina.fecininom, sno_nomina.numpernom, sno_nomina.conpernom, sno_nomina.conpronom, ".
				"		sno_nomina.titrepnom, sno_nomina.divcon, sno_nomina.subnom, sno_nomina.informa, sno_nomina.recdoccaunom, sno_nomina.tipdoccaunom, ".
				"       sno_nomina.recdocpagperche, sno_nomina.tipdocpagperche, sno_nomina.estctaalt, sno_nomina.racobrnom, sno_nomina.ctnom, ".
				"		sno_nomina.nomliq, sno_nomina.hojtienom, sno_nomina.codfuefin, sno_nomina.cersindis, sno_periodo.codperi, sno_nomina.estagrapo, ".
				"		sno_nomina.codclanom, sno_nomina.codclaapo, sno_nomina.codclafid, sno_nomina.codclacau, sno_nomina.codclaper, sno_nomina.estctaaltapo ".
				"  FROM sno_nomina, sno_periodo ".
				" WHERE sno_nomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_nomina.codnom='".$ls_nomina."' ".
				"   AND sno_nomina.codemp=sno_periodo.codemp ".
				"   AND sno_nomina.codnom=sno_periodo.codnom ".
				"   AND sno_nomina.peractnom=sno_periodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_crear_sessionnomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				unset($_SESSION["la_nomina"]);
				$_SESSION["la_nomina"]=$row;
				$ld_fecdesper=$this->io_funciones->uf_formatovalidofecha($_SESSION["la_nomina"]["fecdesper"]);
				$ld_fechasper=$this->io_funciones->uf_formatovalidofecha($_SESSION["la_nomina"]["fechasper"]);
				$_SESSION["la_nomina"]["fecdesper"]=$ld_fecdesper;
				$_SESSION["la_nomina"]["fechasper"]=$ld_fechasper;
				$ld_fecdesper=$this->io_funciones->uf_convertirfecmostrar($ld_fecdesper);
				$ld_fechasper=$this->io_funciones->uf_convertirfecmostrar($ld_fechasper);
				$ls_desper=" Año <strong>".$_SESSION["la_nomina"]["anocurnom"]."</strong> Período <strong>".$_SESSION["la_nomina"]["peractnom"]."</strong> ".$ld_fecdesper." - ".$ld_fechasper."";
				$_SESSION["la_nomina"]["descripcionperiodo"]=$ls_desper;
				$_SESSION["la_nomina"]["tiponomina"]="NORMAL";
			}
			else
			{
				unset($_SESSION["la_nomina"]);
				$lb_valido=false;
				$this->io_mensajes->message("Favor verifique los datos de la nómina. No se pueden cargar los datos."); 
				print "<script language=JavaScript>";
				print "location.href='sigespwindow_blank.php'";
				print "</script>";		
			}
			$this->io_sql->free_result($rs_data);
		}
      	return ($lb_valido);  
    }// end function uf_crear_sessionnomina	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_sessionhnomina()
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_sessionhnomina
		//		   Access: public (en toda las pantallas de procesos)
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que crea la sessión de la nómina actual
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/02/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if(array_key_exists("codnom",$_GET))
		{
			$ls_codnom=$_GET["codnom"];
			$li_anocurnom=$_GET["anocurnom"];
			$ls_peractnom=$_GET["peractnom"];
		}
		else
		{
			$ls_codnom=$_SESSION["la_nomina"]["codnom"];
			$li_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
			$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
			unset($_SESSION["la_nomina"]);
		}
		$ls_sql="SELECT sno_hnomina.codnom, sno_hnomina.desnom, sno_hnomina.peractnom, sno_hnomina.diabonvacnom, sno_hnomina.diareivacnom, ".
				"		sno_hnomina.adenom, sno_hperiodo.fecdesper, sno_hperiodo.fechasper, sno_hperiodo.cerper, sno_hperiodo.conper,".
				"		sno_hperiodo.totper, sno_hnomina.anocurnom, sno_hnomina.tippernom, sno_hnomina.perresnom, sno_hnomina.racnom, ".
				"		sno_hnomina.subnom, sno_hnomina.espnom, sno_hnomina.consulnom, sno_hnomina.descomnom, sno_hnomina.codpronom, ".
				"		sno_hnomina.codbennom, sno_hnomina.conaponom, sno_hnomina.cueconnom, sno_hnomina.notdebnom, sno_hnomina.hojtienom, ".
				"       sno_hnomina.numvounom, sno_hnomina.recdocnom, sno_hnomina.recdocapo, sno_hnomina.tipdocnom, sno_hnomina.tipdocapo, ".
				"		sno_hnomina.tipnom, sno_hnomina.fecininom, sno_hnomina.numpernom, sno_hnomina.conpernom, sno_hnomina.conpronom, ".
				"		sno_hnomina.titrepnom, sno_hnomina.divcon, sno_hnomina.subnom, sno_hnomina.informa, sno_hnomina.nomliq,sno_hnomina.recdoccaunom, ".
				"       sno_hnomina.recdocpagperche, sno_hnomina.tipdocpagperche, sno_hnomina.estctaalt, sno_hnomina.racobrnom,sno_hnomina.ctnom, ".
				"       sno_hnomina.codfuefin, sno_hnomina.cersindis, sno_hperiodo.codperi, sno_hnomina.estagrapo, sno_hnomina.tipdoccaunom, ".
				"		sno_hnomina.codclanom, sno_hnomina.codclaapo, sno_hnomina.codclafid, sno_hnomina.codclacau, sno_hnomina.codclaper, sno_hnomina.estctaaltapo ".
				"  FROM sno_hnomina, sno_hperiodo ".
				" WHERE sno_hnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hnomina.codnom='".$ls_codnom."' ".
				"   AND sno_hnomina.anocurnom='".$li_anocurnom."' ".
				"   AND sno_hnomina.peractnom='".$ls_peractnom."' ".
				"   AND sno_hnomina.codemp=sno_hperiodo.codemp ".
				"   AND sno_hnomina.codnom=sno_hperiodo.codnom ".
				"   AND sno_hnomina.anocurnom=sno_hperiodo.anocur ".
				"   AND sno_hnomina.peractnom=sno_hperiodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_crear_sessionhnomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$_SESSION["la_nomina"]=$row;
				$ld_fecdesper=$this->io_funciones->uf_formatovalidofecha($_SESSION["la_nomina"]["fecdesper"]);
				$ld_fechasper=$this->io_funciones->uf_formatovalidofecha($_SESSION["la_nomina"]["fechasper"]);
				$_SESSION["la_nomina"]["fecdesper"]=$ld_fecdesper;
				$_SESSION["la_nomina"]["fechasper"]=$ld_fechasper;
				$ld_fecdesper=$this->io_funciones->uf_convertirfecmostrar($ld_fecdesper);
				$ld_fechasper=$this->io_funciones->uf_convertirfecmostrar($ld_fechasper);
				$ls_desper="<strong>Histórico</strong> - Año <strong>".$_SESSION["la_nomina"]["anocurnom"]."</strong> Período <strong>".$_SESSION["la_nomina"]["peractnom"]."</strong> ".$ld_fecdesper." - ".$ld_fechasper."";
				$_SESSION["la_nomina"]["descripcionperiodo"]=$ls_desper;
				$_SESSION["la_nomina"]["tiponomina"]="HISTORICA";
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("Favor verifique los datos de los históricos. No se pueden cargar los datos."); 
				print "<script language=JavaScript>";
				print "location.href='sigespwindow_blank.php'";
				print "</script>";		
			}
			$this->io_sql->free_result($rs_data);
		}
      	return ($lb_valido);  
    }// end function uf_crear_sessionhnomina	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar_configuracion($ai_vac_reportar,$as_vac_codconvac,$as_vac_metvac,$as_est_codconcsue,$as_est_estnom,$as_est_ordcons,
									  $as_est_ordconc,$as_est_estrec,$ai_est_numlin,$as_est_prilpt,$as_est_agrsem,$ai_con_parnom,
									  $as_con_consue,$as_con_cuecon,$as_con_conapo,$as_con_conpro,$ai_con_agrcon,$ai_con_gennotdeb,
									  $ai_con_genvou,$as_con_descon,$ai_par_excpersus,$ai_par_perrep,$ad_par_fecfinano,$as_par_metcalfid,
									  $as_fpj_codorgfpj,$as_fpj_codconcfpj,$as_fpj_metfpj,$as_lph_codconclph,$as_lph_metlph,$as_fpa_codconcfpa,
									  $as_fpa_metfpa,$ai_fps_antcom,$ai_fps_fraali,$as_fps_metfps,$as_man_cueconc,$as_man_cueconccaj,
									  $ai_man_actblofor,$ai_man_actblocalnom,$as_man_metrescon,$as_dis_metdisnom,$ai_con_genrecdoc,
									  $ai_con_genrecdocapo,$as_con_tipdocnom,$as_con_tipdocapo,$as_ipas_codorgipas,
									  $as_ipas_codconcahoipas,$as_ipas_codconcseripas,$as_ipas_conhipespipas,$as_ipas_conhipampipas,
									  $as_ipas_conhipconipas,$as_ipas_conhiphipipas,$as_ipas_conhiplphipas,$as_ipas_conhipvivipas,
									  $as_ipas_conperipas,$as_ipas_conturipas,$as_ipas_conproipas,$as_ipas_conasiipas,
									  $as_ipas_convehipas,$as_ipas_concomipas,$as_ivss_numemp,$ai_vac_desincorporar,$as_par_concsuelant,
									  $as_par_confpre,$as_par_camuniadm,$as_par_campasogrado,$as_par_incperben,$as_par_cueconben,
									  $as_par_codunirac,$as_par_comautrac,$as_par_ajusuerac,$ai_par_loncueban,$as_par_modpensiones,$ai_par_valloncueban, 
									  $ai_par_valporpre,$as_con_confidnom,$as_con_recdocfid,$as_con_recdocguar,$as_con_tipdocfid,$as_con_tipdocguar,
									  $as_con_cueconfid,$as_con_codbenfid,$as_con_codguarcontper,$as_con_codguarcontobr,$as_con_codguarcontpercon,
									  $as_con_codguarcontobrcon,$as_ivss_metodo,$ai_par_alfnumcodper,
									  $ai_con_parfpj,$as_edadM,$as_edadF,$as_anoM, $as_anoT, $ai_prestamo, $la_par_campsuerac, 
									  $ai_fps_intasiextra,$as_sueint,$ai_par_camdedtipper,$ai_persobregiro,$ai_genrecdocpagperche,
									  $as_tipdocpagperche,$ai_salvacper,$ai_presalvacper,$ai_estctaalt,$ai_fps_incvacagui,$as_codban,$as_cuenta_banco,
									  $ai_chkgenpagperche,$as_codbanperche,$as_cuenta_bancoperche,$as_codconcbanavih,$as_codorgvipladin,
									  $as_grupovipladin,$as_codubivipladin,$as_distritovipladin,$as_municipiovipladin,$as_vigenciavipladin,
									  $ai_fps_calintpreant,$ai_fps_pormaxant,$as_fps_tipdocant,$ai_fps_calintpercon,$ai_fps_diasadicionalesBV, 
									  $ai_fps_calperact,$ai_ivss_nomesp,$as_con_benrecdocguar,$ai_fps_acuintdiaadi,$ai_fps_presocdiaadi,$ai_difconpnom,
									  $as_depsalnorqui,$as_depsalnoradi,$as_depsalnorvac,$as_fps_metcalalibonvac,$ai_fps_antprimeranio,$as_fps_forcalpres,
									  $as_estagrapo,$as_ivss_dirtalhumivss,$as_ivss_ceddirtalhumivss,$ai_con_recdoccaunom,$as_con_tipdoccaunom,
									  $as_codconcsalbasfpj,$as_codconcantfpj,$as_codconcefifpj,$as_codconcotrprifpj,$ai_percobmoncer,$ai_chkmancueban,
                                      $ai_chkpercargoalfa,$ai_fps_uniestpre,$as_fps_codestpro1,$as_fps_estcla,$as_fps_codestpro2,$as_fps_codestpro3,$as_fps_codestpro4,
									  $as_fps_codestpro5,$aa_seguridad)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar_configuracion
		//		   Access: public (sigesp_snorh_p_configuracion.php)
		//	    Arguments: ai_vac_reportar // reportar vacaciones             as_vac_codconvac // código de concepto de vacaciones
		//				   as_vac_metvac // método de vacaciones              as_est_codconcsue // código de concepto de sueldo
		//				   as_est_estnom // estilo de la nómina				  as_est_ordcons // ordenar las constantes
		//				   as_est_ordconc // ordenar los conceptos            as_est_estrec // estilo de la nómina  
		//				   ai_est_numlin // número de líneas del recibo       as_est_prilpt // reporte 
		//				   as_est_agrsem // agrupación de semana              ai_con_parnom // parámetros por nómina
		//				   as_con_consue // contabilización de los sueldos	  as_con_cuecon // cuenta contable 
		//				   as_con_conapo // contabilización de aportes        as_con_conpro // contabilización de programática 
		//				   ai_con_agrcon // agrupar contable   				  ai_con_gennotdeb // generar nota de débito
		//				   ai_con_genvou // generar voucher a nota de débito  as_con_descon // contabilización de programática 
		//				   ai_par_excpersus // excluir personas suspendidas   ai_par_perrep // no permitir repetidos
		//				   ad_par_fecfinano // fecha tope de bono de fin año  as_par_metcalfid // método de fideicomiso
		//				   as_fpj_codorgfpj // código de organismo            as_fpj_codconcfpj // código de concepto 
		//				   as_fpj_metfpj // método de fpj                     as_lph_codconclph // código de concepto 
		//				   as_lph_metlph // método de lph 					  as_fpa_codconcfpa // código de concepto
		//				   as_fpa_metfpa // método de fpa                     ai_fps_antcom // Antiguedad complementaria 
		//				   ai_fps_fraali // Fracción de Alicuota              as_fps_metfps // método de fps 
		//				   as_man_cueconc // cuentas de conceptos   		  as_man_cueconccaj // cuentas de conceptos caja
		//				   ai_man_actblofor // bloqueo formulas conceptos 	  ai_man_actblocalnom // activar bloqueo de cálculo de la nómina 
		//				   as_man_metrescon // método de resumen contable     as_dis_metdisnom // método de disco de nómina
		//				   ai_con_genrecdoc // generar recepción documento    ai_con_genrecdocapo // Generar recepción de documento aportes
		//				   as_con_tipdocnom // Tipo de documento de nom       as_con_tipdocapo // tipo de documento de aporte
		//				   as_ipas_codorgipas // código de Organismo IPASME   as_ipas_codconcahoipas // Código Concepto Ahorro IPASME
		//				   as_ipas_codconcseripas //  Código Concepto Servicio Asistencial IPASME
		//				   as_ipas_conhipespipas //  Código Concepto Hipotecario Especial IPASME
		//				   as_ipas_conhipampipas //  Código Concepto Hipotecario Ampliación IPASME
		//				   as_ipas_conhipconipas //  Código Concepto Hipotecario Construcción IPASME
		//				   as_ipas_conhiphipipas //  Código Concepto Hipotecario Hipoteca IPASME
		//				   as_ipas_conhiplphipas //  Código Concepto Hipotecario LPH IPASME
		//				   as_ipas_conhipvivipas //  Código Concepto Hipotecario Vivienda IPASME
		//				   as_ipas_conperipas //  Código Concepto Personal IPASME
		//				   as_ipas_conturipas //  Código Concepto Turisticos IPASME
		//				   as_ipas_conproipas //  Código Concepto Proveduria IPASME
		//				   as_ipas_conasiipas //  Código Concepto Asistenciales IPASME
		//				   as_ipas_convehipas //  Código Concepto Vehiculo IPASME
		//				   as_ipas_concomipas //  Código Concepto Comerciales IPASME	
		//				   as_ivss_numemp	 // Código de empresa que asigna el  IVSS
		//				   ai_vac_desincorporar	 // Si se desincorpora cuando el personal este de vacaciones 
		//				   as_par_concsuelant // Código de concepto de sueldo anterior
		//				   as_par_camuniadm  // Cambiar unidad administrativa
		//				   as_par_campasogrado // Cambiar paso y grado
		//				   as_par_incperben // Incluir el personal como beneficiario.
		//				   as_par_cueconben // Cuenta contable para los beneficiarios
		//				   as_par_codunirac // Código ?nico de RAC
		//				   as_par_comautrac // Compensación automática de RAC
		//				   as_par_ajusuerac // Ajuste de sueldo de rac
		//				   as_par_modpensiones  // Modificar datos de pensiones
		//				   ai_par_valporpre // Validar que el prestamo no sea mayor al 30% del sueldo
		//				   as_ivss_metodo // Metodo sueldos (Integral o neto) a usar en IVSS
		//				   ai_par_alfnumcodper // Permitir alfanumericos en el código de personal
		//                 ai_con_parfpj  // configuraciòn de FPJ
		//                 as_sueint // Denominación del Sueldo Integral
		//                 ai_par_camdedtipper// Permite cambiar la asignacion y tipo de personal cuando la nomina es de RAC
		//                 ai_persobregiro//Permite sobregiro en las cuentas del trabajos al calcular la nomina
		//                 ai_genrecdocpagperche//Generar Recepcion de Documento para el Pago del Personal
		//                 as_tipdocpagperche //Tipo de recepcion de Documento del pago de personal 
		//                 ai_salvacper // Permite la salida de vacaciones del personal antes de la fecha de venciomiento
		//                 ai_estctaalt // Permite utilizar la cuenta contable alternativa para el beneficiario
		//	      Returns: lb_valido True si se ejecuto correctamente el proceso y false si hubo error
		//	  Description: Función que graba todos los campos de la configuración
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/02/2006 								Fecha Última Modificación : 20/10/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//// Modificado por Ofimatica de Venezuela el 02-06-2011, para agregar un nuevo campo a la configuracion de la nomina para el manejo o no de los dias adicionales de Bono Vacacional, el cual es obligatorio tomar en cuenta segun la LOT y su reglamento. Este campo dentro de los parametros es $ai_fps_diasadicionalesBV

		$lb_valido=true;
		//-------------------------------------SUELDO INTEGRAL--------------------------------------------------
		if($lb_valido)
		{// reportar vacaciones
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","DENOMINACION SUELDO INTEGRAL",$as_sueint,"C");
		}
		//-------------------------------------SALARIO NORMAL---------------------------------------------------
		if($lb_valido)
		{// reportar vacaciones
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","SALARIO NORMAL DEPOSITO QUINCENA",$as_depsalnorqui,"C");
		}
		if($lb_valido)
		{// reportar vacaciones
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","SALARIO NORMAL DEPOSITO ADICIONAL",$as_depsalnoradi,"C");
		}
		if($lb_valido)
		{// reportar vacaciones
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","SALARIO NORMAL DEPOSITO VACACION",$as_depsalnorvac,"C");
		}
		//-------------------------------------VACACIONES------------------------------------------------------
		if($lb_valido)
		{// reportar vacaciones
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","MOSTRAR VACACION",$ai_vac_reportar,"C");
		}
		if($lb_valido)
		{// desincorporar de nómina vacaciones
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","DESINCORPORAR DE NOMINA",$ai_vac_desincorporar,"C");
		}
		if($lb_valido)
		{// código de concepto de vacaciones
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO VACACION",$as_vac_codconvac,"C");
		}
		if($lb_valido)
		{// método de vacaciones
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","METODO_VACACIONES",$as_vac_metvac,"C");
		}
		if($lb_valido)
		{// salida de las vacaciones del personal
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","SALIDA VACACION",$ai_salvacper,"C");
		}
		if($lb_valido)
		{// salida de las vacaciones del personal
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","PRESTAMO SALIDA VACACION",$ai_presalvacper,"C");
		}
		//------------------------------------------------------------------------------------------------------

		//--------------------------------------ESTILO DE NÓMINA-----------------------------------------------
		if($lb_valido)
		{// código de concepto de sueldo
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","SNO COD SUELDO",$as_est_codconcsue,"C");
		}
		if($lb_valido)
		{// estilo de la nómina
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","REP NOMINA",$as_est_estnom,"C");
		}
		if($lb_valido)
		{// ordenar las constantes
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","ORDEN CONSTANTE",$as_est_ordcons,"C");
		}
		if($lb_valido)
		{// ordenar los conceptos
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","ORDEN CONCEPTO",$as_est_ordconc,"C");
		}
		if($lb_valido)
		{// estilo de la nómina
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","REP RECIBOS",$as_est_estrec,"C");
		}
		if($lb_valido)
		{// número de líneas del recibo
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","REP RECIBO LINEAS",$ai_est_numlin,"C");
		}
		if($lb_valido)
		{// reporte
			$lb_valido=$this->uf_insert_config("SNO","PRINT","RECIBOS",$as_est_prilpt,"C");
		}
		if($lb_valido)
		{// agrupación de semana
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","NOM_SEM_SR",$as_est_agrsem,"C");
		}
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------CONTABILIZACION-------------------------------------------------
		if($lb_valido)
		{// parámetros por nómina
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CONTA GLOBAL",$ai_con_parnom,"I");
		}
		if($lb_valido)
		{// contabilización de los sueldos
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CONTABILIZACION",$as_con_consue,"C");
		}
		if($lb_valido)
		{// cuenta contable 
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CTA.CONTA",$as_con_cuecon,"C");
		}
		if($lb_valido)
		{// contabilización de aportes
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CONTABILIZACION APORTES",$as_con_conapo,"C");
		}
		if($lb_valido)
		{// agrupar aportes
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","AGRUPAR APORTES",$as_estagrapo,"I");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","SPG","CONTABILIZACION",$as_con_conpro,"C");
		}
		if($lb_valido)
		{// agrupar contable
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","AGRUPARCONTA",$ai_con_agrcon,"I");
		}
		if($lb_valido)
		{// generar nota de débito
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","GENERAR NOTA DEBITO",$ai_con_gennotdeb,"I");
		}
		if($lb_valido)
		{// generar recepción de documento
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO",$ai_con_genrecdoc,"I");
		}
		if($lb_valido)
		{// generar recepción de documento aportes
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO APORTE",$ai_con_genrecdocapo,"I");
		}
		if($lb_valido)
		{// Tipo de documento de nómina
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","TIPO DOCUMENTO NOMINA",$as_con_tipdocnom,"C");
		}
		if($lb_valido)
		{// Tipo de documento de aporte
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","TIPO DOCUMENTO APORTE",$as_con_tipdocapo,"C");
		}
		if($lb_valido)
		{// generar voucher a nota de débito
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","VOUCHER GENERAR",$ai_con_genvou,"I");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CONTABILIZACION DESTINO",$as_con_descon,"C");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CONTABILIZACION FIDEICOMISO",$as_con_confidnom,"C");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO FIDEICOMISO",$as_con_recdocfid,"I");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","TIPO DOCUMENTO FIDEICOMISO",$as_con_tipdocfid,"C");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO GUARDERIA",$as_con_recdocguar,"I");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO BENEFICIARIO GUARDERIA",$as_con_benrecdocguar,"I");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","GUARDERIA",$as_con_tipdocguar,"C");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CTA.CONTABLE_FIDEICOMISO",$as_con_cueconfid,"C");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","DESTINO FIDEICOMISO",$as_con_codbenfid,"C");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","DESTINO GUARDERIA PERSONAL",$as_con_codguarcontper,"C");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","DESTINO GUARDERIA OBRERO",$as_con_codguarcontobr,"C");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","DESTINO GUARDERIA PERSONAL CONTRATADO",$as_con_codguarcontpercon,"C");
		}
		if($lb_valido)
		{// contabilización de programática
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","DESTINO GUARDERIA OBRERO CONTRATADO",$as_con_codguarcontobrcon,"C");
		}
		if($lb_valido)
		{// Configuración de generar recepción de documento para el pago del personal
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO PAGO PERSONAL CHEQUE",$ai_genrecdocpagperche,"I");
		}
		if($lb_valido)
		{// Tipo de Documento de la Recepción de Documento del pago del personal
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","TIPO DOCUMENTO PAGO PERSONAL CHEQUE",$as_tipdocpagperche,"C");
		}
		if($lb_valido)
		{// Utilizar la cuenta contable alternativa para el Beneficiario
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","UTILIZAR_CTA.CONTA_REC_DOC_PROV_BEN",$ai_estctaalt,"I");
		}
		if($lb_valido)
		{// Utilizar la cuenta contable alternativa para el Beneficiario
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO CAUSA",$ai_con_recdoccaunom,"I");
		}
		if($lb_valido)
		{// Utilizar la cuenta contable alternativa para el Beneficiario
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","TIPO DOCUMENTO CAUSADO",$as_con_tipdoccaunom,"C");
		}
		//-----------------------------------------------------------------------------------------------------

		//------------------------------------------PARÁMETROS-------------------------------------------------
		if($lb_valido)
		{// excluir personas suspendidas
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","EXCLUIR_SUSPENDIDOS",$ai_par_excpersus,"I");
		}
		if($lb_valido)
		{// no permitir repetidos
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","NOPERMITIR_REPETIDOS",$ai_par_perrep,"I");
		}
		if($lb_valido)
		{// fecha tope de bono de fin de año
			$lb_valido=$this->uf_insert_config("SNO","ANTIGUEDAD","FECHA_TOPE",$ad_par_fecfinano,"C");
		}
		if($lb_valido)
		{// método de fideicomiso
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","METODO FIDECOMISO",$as_par_metcalfid,"C");
		}
		if($lb_valido)
		{// fecha tope de bono de fin de año
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","BANCO BANAVIH 2.0",$as_codban,"C");
		}
		if($lb_valido)
		{// método de fideicomiso
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CTA. BANAVIH 2.0",$as_cuenta_banco,"C");
		}
		if($lb_valido)
		{// Conceptos Seleccionados para el banavih
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CONCEPTOS_BANAVIH",$as_codconcbanavih,"C");
		}
		if($lb_valido)
		{// fecha tope de bono de fin de año
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","BANCO PERSONAL CHEQUE",$as_codbanperche,"C");
		}
		if($lb_valido)
		{// método de fideicomiso
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CTA. PERSONAL CHEQUE",$as_cuenta_bancoperche,"C");
		}
		
		if($lb_valido)
		{// Código de concepto de sueldo
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CONCEPTO_SUELDO_ANT",$as_par_concsuelant,"C");
		}
		if($lb_valido)
		{// Configuración de prestamos por cuotas ó por montos
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CONFIGURACION_PRESTAMO",$as_par_confpre,"C");
		}
		if($lb_valido)
		{// Configuración de si las nóminas están por rac que se pueda cambiar la unidad administrativa
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CAMBIAR_UNIDAD_ADM_RAC",$as_par_camuniadm,"I");
		}
		if($lb_valido)
		{// Configuración de si las nóminas están por rac que se pueda cambiar el Paso y el Grado
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CAMBIAR_PASO_GRADO_RAC",$as_par_campasogrado,"I");
		}
		if($lb_valido)
		{// Configuración de si el personal se agrega automáticamente como beneficiario
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","INCLUIR_A_BENEFICIARIO",$as_par_incperben,"I");
		}
		if($lb_valido)
		{// Configuración de la cuenta contable para el beneficiario
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CUENTA_CONTABLE_BENEFICIARIO",$as_par_cueconben,"C");
		}
		if($lb_valido)
		{// Configuración del código unico de rac
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CODIGO_UNICO_RAC",$as_par_codunirac,"I");
		}
		if($lb_valido)
		{// Configuración de la compensación automática
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","COMPENSACION_AUTOMATICA_RAC",$as_par_comautrac,"I");
		}
		if($lb_valido)
		{// Configuración del ajuste de sueldo del rac
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","AJUSTAR_SUELDO_RAC",$as_par_ajusuerac,"I");
		}
		if($lb_valido)
		{// Configuración del ajuste de sueldo del rac
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CAMBIAR_PENSIONES",$as_par_modpensiones,"I");
		}
		if($lb_valido)
		{// Configuración del validacion de lontitud de cuenta de banco
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","LONGITUD_CUENTA_BANCO",$ai_par_loncueban,"I");
		}
		if($lb_valido)
		{// Configuración del validacion de lontitud de cuenta de banco
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","VALIDAR_LONGITUD_CUEBANCO",$ai_par_valloncueban,"I");
		}
		if($lb_valido)
		{// Configuración del validacion de lontitud de cuenta de banco
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","VAL_PORCENTAJE_PRESTAMO",$ai_par_valporpre,"I");
		}
		if($lb_valido)
		{// Configuración del validacion de lontitud de cuenta de banco
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","ALFNUM_CODPER",$ai_par_alfnumcodper,"I");
		}
		if($lb_valido)
		{// Configuración del validacion de prestamos al personal del mismo tipo
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","VAL_TIPO_PRESTAMO",$ai_prestamo,"I");
		}
		if($lb_valido)
		{// Configuración de si las nóminas están por rac que se pueda cambiar el sueldo
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CAMBIAR_SUELDO_RAC",$la_par_campsuerac,"I");
		}
		if($lb_valido)
		{// Configuración de si las nóminas están por rac que se pueda cambiar la dedicación y el tipo de personal
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CAMBIAR_DEDICACION_TIPO_PERSONAL_RAC",$ai_par_camdedtipper,"I");
		}
		if($lb_valido)
		{// Configuración de permiritir sobregiro en las cuentas del trabajador al calcular la nómina
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","SOBREGIRO_CUENTAS_TRABAJADOR",$ai_persobregiro,"I");
		}
		if($lb_valido)
		{// Configuración de permiritir sobregiro en las cuentas del trabajador al calcular la nómina
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","PAGO_DIRECTO_PERSONAL_CHEQUE",$ai_chkgenpagperche,"I");
		}
		if($lb_valido)
		{// Utilizar la cuenta contable alternativa para el Beneficiario
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","TRABAJADOR_MONTO_CERO",$ai_percobmoncer,"I");
		}
		if($lb_valido)
		{// Configuración de permiritir sobregiro en las cuentas del trabajador al calcular la nómina
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","MANTENER_CUENTA_BANCO",$ai_chkmancueban,"I");
		}
		if($lb_valido)
		{// Configuración de permitir cargos alfanumericos
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","PERMITIR_CARGO_ALFANUMERICO",$ai_chkpercargoalfa,"I");
		}
		
		//------------------------------------------------------------------------------------------------------
		
		//-------------------------------------Aportes FPJ-----------------------------------------------------
		if($lb_valido)
		{// código de organismo
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD ORGANISMO FPJ",$as_fpj_codorgfpj,"C");
		}
		if($lb_valido)
		{// código de concepto
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO FPJ",$as_fpj_codconcfpj,"C");
		}
		if($lb_valido)
		{// método de fpj
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","METODO FPJ",$as_fpj_metfpj,"C");
		}
		if($lb_valido)
		{// parámetros por jubilación
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","CONF JUB",$ai_con_parfpj,"I");
		}	
		if($lb_valido)
		{// Fecha de jubilación Personal Masculino
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","EDADM",$as_edadM,"C");
		}	
		if($lb_valido)
		{// Fecha de jubilación Personal Femenino
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","EDADF",$as_edadF,"C");
		}	
		if($lb_valido)
		{// Años de servicios minimos
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","ANOM",$as_anoM,"C");
		}	
		if($lb_valido)
		{// Años de servicios máximos
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","ANOT",$as_anoT,"C");
		}			
		if($lb_valido)
		{// Años de servicios máximos
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","SALARIO_BASE_FPJ",$as_codconcsalbasfpj,"C");
		}			
		if($lb_valido)
		{// Años de servicios máximos
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","ANTIGUEDAD_FPJ",$as_codconcantfpj,"C");
		}			
		if($lb_valido)
		{// Años de servicios máximos
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","EFICIENCIA_FPJ",$as_codconcefifpj,"C");
		}			
		if($lb_valido)
		{// Años de servicios máximos
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","OTRAS_PRIMAS_FPJ",$as_codconcotrprifpj,"C");
		}			
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------Aportes LPH-----------------------------------------------------
		if($lb_valido)
		{// código de concepto
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO LPH",$as_lph_codconclph,"C");
		}
		if($lb_valido)
		{// método de lph
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","METODO LPH",$as_lph_metlph,"C");
		}
		//-----------------------------------------------------------------------------------------------------
		
		//-------------------------------------Aportes FPA-----------------------------------------------------
		if($lb_valido)
		{// código de concepto
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO FPA",$as_fpa_codconcfpa,"C");
		}
		if($lb_valido)
		{// método de fpa
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","METODO FPA",$as_fpa_metfpa,"C");
		}
		//-----------------------------------------------------------------------------------------------------
		
		//-------------------------------------Aportes FPS-----------------------------------------------------
		if($lb_valido)
		{// Antiguedad complementaria
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COMPLEMENTO ANTIGUEDAD",$ai_fps_antcom,"I");
		}
		if($lb_valido)
		{// Antiguedad complementaria
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","DIFERENCIAR CONCEPTOS NOMINA",$ai_difconpnom,"I");
		}
		{// Fracción de Alicuota
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","FRACCION ALICUOTA",$ai_fps_fraali,"I");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","METODO FPS",$as_fps_metfps,"C");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","INT_ASIG_EXTRA",$ai_fps_intasiextra,"I");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CALCULO_INT_FIDEICOISO",$ai_fps_calintpreant,"I");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CALCULO_ACUM_INT_DIAS_ADICIONALES",$ai_fps_acuintdiaadi,"I");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","REGIMEN_PREST_SOCIALES_97",$ai_fps_presocdiaadi,"I");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","INC_VACACIONES_AGUINALDO",$ai_fps_incvacagui,"I");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","POR_MAX_ANTICIPO",$ai_fps_pormaxant,"I");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","TIPO_DOC_ANTICIPO",$as_fps_tipdocant,"C");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CALCULO_INT_PERSONAL_CONF",$ai_fps_calintpercon,"I");
		}
		/// Agregado por Ofimatica de Venezuela el 02-06-2011, para el manejo o no de los dias adicionales de Bono Vacacional, lo cual es obligatorio segun  la LOY y su reglamento.
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","DIAS_ADICIONALES_BV",$ai_fps_diasadicionalesBV,"I");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CALCULAR_PERSONAL_ACTIVO",$ai_fps_calperact,"I");
		}		
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","MET ALI BONO VAC",$as_fps_metcalalibonvac,"C");
		}		
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","ANTIGUEDAD_PRIMER_AÑO",$ai_fps_antprimeranio,"I");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","FORMA_CALCULO_PRES",$as_fps_forcalpres,"C");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","UNIFICAR_ESTRUCTURA_PRESUPUESTARIA",$ai_fps_uniestpre,"I");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","FPS_CODESTPRO1",$as_fps_codestpro1,"C");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","FPS_ESTCLA",$as_fps_estcla,"C");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","FPS_CODESTPRO2",$as_fps_codestpro2,"C");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","FPS_CODESTPRO3",$as_fps_codestpro3,"C");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","FPS_CODESTPRO4",$as_fps_codestpro4,"C");
		}
		if($lb_valido)
		{// método de fps
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","FPS_CODESTPRO5",$as_fps_codestpro5,"C");
		}
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------Mantenimiento---------------------------------------------------
		if($lb_valido)
		{// cuentas de conceptos
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","SPGCUENTA",$as_man_cueconc,"C");
		}
		if($lb_valido)
		{// cuentas de conceptos caja
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CTACAJA",$as_man_cueconccaj,"C");
		}
		if($lb_valido)
		{// activar bloqueo de formulas de conceptos
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","ACTIVAR_BLOQUEO",$ai_man_actblofor,"I");
		}
		if($lb_valido)
		{// activar bloqueo de cálculo de la nómina
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","BLOQUEO_ACTIVAR",$ai_man_actblocalnom,"I");
		}
		if($lb_valido)
		{// método de resumen contable
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","METODO RESUMEN CONTABLE",$as_man_metrescon,"C");
		}
		//-----------------------------------------------------------------------------------------------------
		
		//-------------------------------------Disco Nómina----------------------------------------------------
		if($lb_valido)
		{// método de disco de nómina
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","METODO GD NOMINA",$as_dis_metdisnom,"C");
		}
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------Aportes IPASME-----------------------------------------------------
		if($lb_valido)
		{// Código de Organismo
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD ORGANISMO IPAS",$as_ipas_codorgipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto de Ahorro
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO AHORRO IPAS",$as_ipas_codconcahoipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto de Servicio Asistencial
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO SERVICIO IPAS",$as_ipas_codconcseripas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Hipotecario Especial
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO ESPECIAL IPAS",$as_ipas_conhipespipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Hipotecario Ampliación
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO AMLIACION IPAS",$as_ipas_conhipampipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Hipotecario Construcción
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO CONSTRUCCION IPAS",$as_ipas_conhipconipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Hipotecario Hipoteca
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO HIPOTECA IPAS",$as_ipas_conhiphipipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Hipotecario LPH
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO LPH IPAS",$as_ipas_conhiplphipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Hipotecario Vivienda
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO VIVIENDA IPAS",$as_ipas_conhipvivipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Personal
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO PERSONAL IPAS",$as_ipas_conperipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Turísticos
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO TURISTICOS IPAS",$as_ipas_conturipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Proveeduria
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO PROVEEDURIA IPAS",$as_ipas_conproipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Asistenciales
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO ASISTENCIALES IPAS",$as_ipas_conasiipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Vehículos
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO VEHICULOS IPAS",$as_ipas_convehipas,"C");
		}
		if($lb_valido)
		{// Código de Concepto Comerciales
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD CONCEPTO COMERCIALES IPAS",$as_ipas_concomipas,"C");
		}
		//-----------------------------------------------------------------------------------------------------
		
		//-------------------------------------IVSS-----------------------------------------------------
		if($lb_valido)
		{// Código de Empresa 
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","COD ORGANISMO IVSS",$as_ivss_numemp,"C");
		}
		if($lb_valido)
		{// Metodo sueldo 
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","METODO IVSS",$as_ivss_metodo,"C");
		}
		if($lb_valido)
		{// Metodo sueldo 
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","NOMINAS_ESPECIALES_IVSS",$ai_ivss_nomesp,"C");	
		}
		if($lb_valido)
		{// Metodo sueldo 
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","DIR_TALENTO_HUMANO",$as_ivss_dirtalhumivss,"C");	
		}
		if($lb_valido)
		{// Metodo sueldo 
			$lb_valido=$this->uf_insert_config("SNO","NOMINA","CED_DIR_TALENTO_HUMANO",$as_ivss_ceddirtalhumivss,"C");	
		}		
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------VIPLADIN--------------------------------------------------------
		if($lb_valido)
		{// Código de Organismo
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","COD_ORGANISMO_VIPLADIN",$as_codorgvipladin,"C");
		}
		if($lb_valido)
		{// Grupo
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","GRUPO_VIPLADIN",$as_grupovipladin,"C");
		}
		if($lb_valido)
		{// Ubicacion Geografica
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","COD_UBICACION_VIPLADIN",$as_codubivipladin,"C");
		}
		if($lb_valido)
		{// Distrito
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","DISTRITO_VIPLADIN",$as_distritovipladin,"C");
		}
		if($lb_valido)
		{// Municipio
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","MUNICIPIO_VIPLADIN",$as_municipiovipladin,"C");
		}
		if($lb_valido)
		{// Fecha de Vigencia
			$lb_valido=$this->uf_insert_config("SNO","CONFIG","VIGENCIA_VIPLADIN",$as_vigenciavipladin,"C");
		}
		//-----------------------------------------------------------------------------------------------------
		
		if($lb_valido)
		{ 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó la configuración de las nómina.";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////				
		}
      	return ($lb_valido);  
    }// end function uf_guardar_configuracion	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reparar_subnominas($aa_seguridad)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reparar_subnominas
		//		   Access: public (sigesp_snorh_p_configuracion.php)
		//	    Arguments: aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que le inserta subnóminas a todas aquellas nóminas que no tengan sub nóminas asociadas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/04/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="INSERT INTO sno_subnomina (codemp, codnom, codsubnom, dessubnom) ".
				"     SELECT codemp, codnom, '0000000000' AS codsubnom, 'Sin Subnomina' AS dessubnom ".
				"       FROM sno_nomina ".
				"      WHERE codemp='".$this->ls_codemp."' ".
				"        AND codnom NOT IN (SELECT codnom FROM sno_subnomina WHERE (codemp='".$this->ls_codemp."'))";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_reparar_subnominas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Se realizó el proceso de reparar subnóminas.";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////				
			if($lb_valido)
			{	
				$this->io_mensajes->message("El proceso de reparar subnóminas se realizó correctamente.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_reparar_subnominas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
		}
      	return ($lb_valido);  
    }// end function uf_reparar_subnominas	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reparar_conceptopersonal($aa_seguridad)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_reparar_conceptopersonal
		//		   Access: public (sigesp_snorh_p_configuracion.php)
		//	    Arguments: aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que le inserta los conceptos y constantes a personal que no se le haya asociado alguno
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/04/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="INSERT INTO sno_conceptopersonal (codemp, codnom, codper, codconc, aplcon, valcon, acuemp, acuiniemp, acupat, acuinipat) ".
				"     SELECT sno_personalnomina.codemp, concepto.codnom, sno_personalnomina.codper, concepto.codconc, ".
				"			 concepto.glocon, 0, 0, 0, 0, 0 ".
				"       FROM sno_personalnomina, sno_concepto concepto ".
				"      WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"        AND sno_personalnomina.codper NOT IN (SELECT sno_conceptopersonal.codper FROM sno_conceptopersonal ".
				"												 WHERE sno_conceptopersonal.codemp='".$this->ls_codemp."' ".
				"												   AND sno_conceptopersonal.codnom=concepto.codnom".
				"												   AND sno_conceptopersonal.codconc=concepto.codconc) ".
				"        AND sno_personalnomina.codemp=concepto.codemp ".
				"		 AND sno_personalnomina.codnom=concepto.codnom ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_reparar_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			$ls_sql="INSERT INTO sno_constantepersonal (codemp, codnom, codper, codcons, moncon,montopcon) ".
					"     SELECT sno_personalnomina.codemp, constante.codnom, sno_personalnomina.codper, constante.codcons, constante.valcon,constante.topcon ".
					"       FROM sno_personalnomina, sno_constante constante ".
					"      WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					"        AND sno_personalnomina.codper NOT IN (SELECT sno_constantepersonal.codper FROM sno_constantepersonal ".
					"												 WHERE sno_constantepersonal.codemp='".$this->ls_codemp."' ".
					"												   AND sno_constantepersonal.codnom=constante.codnom".
					"												   AND sno_constantepersonal.codcons=constante.codcons) ".
					"        AND sno_personalnomina.codemp=constante.codemp ".
					"		 AND sno_personalnomina.codnom=constante.codnom ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_reparar_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
			else
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="PROCESS";
				$ls_descripcion ="Se realizó el proceso de reparar concepto-personal.";
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////				
				if($lb_valido)
				{	
					$this->io_mensajes->message("El proceso de reparar concepto-personal se realizó correctamente.");
					$this->io_sql->commit();
				}
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_reparar_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$this->io_sql->rollback();
				}
			}
		}
      	return ($lb_valido);  
    }// end function uf_reparar_conceptopersonal	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_recalcular_sueldointegral($aa_seguridad)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_recalcular_sueldointegral
		//		   Access: public (sigesp_snorh_p_configuracion.php)
		//	    Arguments: aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que le actualiza el sueldo integral del personal en las tablas de históricos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/04/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="UPDATE sno_hpersonalnomina ".
				"   SET sueintper=(CASE WHEN (SELECT SUM(CASE WHEN sno_hsalida.valsal IS NOT NULL THEN sno_hsalida.valsal ELSE 0 END) ".
				"					 FROM sno_hsalida, sno_hconcepto  ".
				"					WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"					  AND sno_hconcepto.sueintcon=1 ".
				"					  AND sno_hsalida.codemp=sno_hconcepto.codemp ".
				"					  AND sno_hsalida.codnom=sno_hconcepto.codnom ".
				"					  AND sno_hsalida.anocur=sno_hconcepto.anocur ".
				"					  AND sno_hsalida.codperi=sno_hconcepto.codperi ".
				"					  AND sno_hsalida.codconc=sno_hconcepto.codconc ".
				"					  AND sno_hsalida.codemp=sno_hpersonalnomina.codemp ".
				"					  AND sno_hsalida.codnom=sno_hpersonalnomina.codnom ".
				"					  AND sno_hsalida.anocur=sno_hpersonalnomina.anocur ".
				"					  AND sno_hsalida.codperi=sno_hpersonalnomina.codperi ".
				"					  AND sno_hsalida.codper=sno_hpersonalnomina.codper GROUP BY sno_hsalida.codper ) IS NOT NULL ".
				"			    THEN (SELECT SUM(CASE WHEN sno_hsalida.valsal IS NOT NULL THEN sno_hsalida.valsal ELSE 0 END) ".
				"					 FROM sno_hsalida, sno_hconcepto  ".
				"					WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"					  AND sno_hconcepto.sueintcon=1 ".
				"					  AND sno_hsalida.codemp=sno_hconcepto.codemp ".
				"					  AND sno_hsalida.codnom=sno_hconcepto.codnom ".
				"					  AND sno_hsalida.anocur=sno_hconcepto.anocur ".
				"					  AND sno_hsalida.codperi=sno_hconcepto.codperi ".
				"					  AND sno_hsalida.codconc=sno_hconcepto.codconc ".
				"					  AND sno_hsalida.codemp=sno_hpersonalnomina.codemp ".
				"					  AND sno_hsalida.codnom=sno_hpersonalnomina.codnom ".
				"					  AND sno_hsalida.anocur=sno_hpersonalnomina.anocur ".
				"					  AND sno_hsalida.codperi=sno_hpersonalnomina.codperi ".
				"					  AND sno_hsalida.codper=sno_hpersonalnomina.codper GROUP BY sno_hsalida.codper ) ELSE 0 END), ".
				"    salnorper=(CASE WHEN (SELECT SUM(CASE WHEN sno_hsalida.valsal IS NOT NULL THEN sno_hsalida.valsal ELSE 0 END) ".
				"					 FROM sno_hsalida, sno_hconcepto  ".
				"					WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"					  AND sno_hconcepto.persalnor='1' ".
				"					  AND sno_hsalida.codemp=sno_hconcepto.codemp ".
				"					  AND sno_hsalida.codnom=sno_hconcepto.codnom ".
				"					  AND sno_hsalida.anocur=sno_hconcepto.anocur ".
				"					  AND sno_hsalida.codperi=sno_hconcepto.codperi ".
				"					  AND sno_hsalida.codconc=sno_hconcepto.codconc ".
				"					  AND sno_hsalida.codemp=sno_hpersonalnomina.codemp ".
				"					  AND sno_hsalida.codnom=sno_hpersonalnomina.codnom ".
				"					  AND sno_hsalida.anocur=sno_hpersonalnomina.anocur ".
				"					  AND sno_hsalida.codperi=sno_hpersonalnomina.codperi ".
				"					  AND sno_hsalida.codper=sno_hpersonalnomina.codper GROUP BY sno_hsalida.codper ) IS NOT NULL ".
				"			    THEN (SELECT SUM(CASE WHEN sno_hsalida.valsal IS NOT NULL THEN sno_hsalida.valsal ELSE 0 END) ".
				"					 FROM sno_hsalida, sno_hconcepto  ".
				"					WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"					  AND sno_hconcepto.persalnor='1' ".
				"					  AND sno_hsalida.codemp=sno_hconcepto.codemp ".
				"					  AND sno_hsalida.codnom=sno_hconcepto.codnom ".
				"					  AND sno_hsalida.anocur=sno_hconcepto.anocur ".
				"					  AND sno_hsalida.codperi=sno_hconcepto.codperi ".
				"					  AND sno_hsalida.codconc=sno_hconcepto.codconc ".
				"					  AND sno_hsalida.codemp=sno_hpersonalnomina.codemp ".
				"					  AND sno_hsalida.codnom=sno_hpersonalnomina.codnom ".
				"					  AND sno_hsalida.anocur=sno_hpersonalnomina.anocur ".
				"					  AND sno_hsalida.codperi=sno_hpersonalnomina.codperi ".
				"					  AND sno_hsalida.codper=sno_hpersonalnomina.codper GROUP BY sno_hsalida.codper ) ELSE 0 END) ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_recalcular_sueldointegral ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Se realizó el proceso de recalcular sueldo integral.";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////				
			if($lb_valido)
			{	
				$this->io_mensajes->message("El proceso de recalcular sueldo se realizó correctamente.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_recalcular_sueldointegral ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
		}
      	return ($lb_valido);  
    }// end function uf_recalcular_sueldointegral
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_mantenimiento_historicos($aa_seguridad)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_mantenimiento_historicos
		//		   Access: public (sigesp_snorh_p_configuracion.php)
		//	    Arguments: aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que elimina de la tabla de resumen histórico los registros que no están en la tabla de salida
		//				   históricos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/04/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="DELETE ".
				"  FROM sno_hresumen ".
				" WHERE codnom NOT IN (SELECT codnom ".
				"					  	  FROM sno_hsalida ".
				"					  	 WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"						   AND sno_hsalida.codemp=sno_hresumen.codemp ".
				"					  	   AND sno_hsalida.codnom=sno_hresumen.codnom ".
				"						   AND sno_hsalida.anocur=sno_hresumen.anocur ".
				"					  	   AND sno_hsalida.codperi=sno_hresumen.codperi ".
				"					  	   AND sno_hsalida.codper=sno_hresumen.codper) ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_historicos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Se realizó el proceso de mantenimiento históricos.";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////				
			if($lb_valido)
			{	
				$this->io_mensajes->message("El proceso de mantenimiento históricos se realizó correctamente.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_historicos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
		}
      	return ($lb_valido);  
    }// end function uf_mantenimiento_historicos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_mantenimiento_repararacumuladoconceptos($aa_seguridad)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_mantenimiento_repararacumuladoconceptos
		//		   Access: public (sigesp_snorh_p_configuracion.php)
		//	    Arguments: aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que actualiza el valor acumulado de los conceptos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/04/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT sno_periodo.codnom, sno_nomina.anocurnom, sno_periodo.codperi ".
				"  FROM sno_periodo, sno_nomina ".
				" WHERE sno_periodo.codemp='".$this->ls_codemp."' ".
				"	AND sno_periodo.cerper = 1 ".
				"   AND sno_periodo.codemp = sno_nomina.codemp ".
				"	AND sno_periodo.codnom = sno_nomina.codnom ".
				" ORDER BY sno_periodo.codnom, sno_nomina.anocurnom, sno_periodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_repararacumuladoconceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$this->io_sql->begin_transaction();
			if($lb_valido)
			{
				$ls_sql="UPDATE sno_hconceptopersonal ".
						"   SET acupat =  0,  ".
						"  		acuemp =  0  ".
						" WHERE codemp='".$this->ls_codemp."'  ";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_repararacumuladoconceptos 2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				}
			}
			if($lb_valido)
			{
				$ls_sql="UPDATE sno_conceptopersonal ".
						"   SET acupat =  0,  ".
						"  		acuemp =  0  ".
						" WHERE codemp='".$this->ls_codemp."'  ";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_repararacumuladoconceptos 2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				}
			}
			while ((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codnom=$rs_data->fields["codnom"];
				$ls_anocurnom=$rs_data->fields["anocurnom"];
				$ls_codperi=$rs_data->fields["codperi"];
				$ls_sql="UPDATE sno_hconceptopersonal ".
						"   SET acuemp =  COALESCE((SELECT SUM(valsal) ".
						"		   FROM sno_hsalida ".
						"                  WHERE sno_hsalida.codemp=sno_hconceptopersonal.codemp ".
						"		    AND sno_hsalida.anocur=sno_hconceptopersonal.anocur ". 
						"		    AND sno_hsalida.codnom=sno_hconceptopersonal.codnom ". 
						"                    AND sno_hsalida.codconc=sno_hconceptopersonal.codconc  ".
						"                    AND sno_hsalida.codper=sno_hconceptopersonal.codper ".
						"                    AND sno_hsalida.codperi <=  sno_hconceptopersonal.codperi ".
						"		    AND tipsal='P1'),0)  ".
						" WHERE codemp='".$this->ls_codemp."'  ".
						"   AND anocur='".$ls_anocurnom."' ".
						"   AND codperi='".$ls_codperi."'".
						"   AND codnom IN (SELECT codnom  ".
						"		    		 FROM sno_hconcepto  ".
						"                   WHERE sno_hconcepto.codemp=sno_hconceptopersonal.codemp ".
						"                     AND sno_hconcepto.codnom=sno_hconceptopersonal.codnom ".
						"                     AND sno_hconcepto.anocur=sno_hconceptopersonal.anocur ".
						"                     AND sno_hconcepto.codperi=sno_hconceptopersonal.codperi ".
						"                     AND sno_hconcepto.codconc=sno_hconceptopersonal.codconc ".
						"                     AND sno_hconcepto.sigcon='P') ";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_repararacumuladoconceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				}
				if($lb_valido)
				{
					$ls_sql="UPDATE sno_hconceptopersonal ".
							"   SET acupat =  COALESCE((SELECT SUM(valsal) ".
							"		   FROM sno_hsalida ".
							"                  WHERE sno_hsalida.codemp=sno_hconceptopersonal.codemp ".
							"		    AND sno_hsalida.anocur=sno_hconceptopersonal.anocur ". 
							"		    AND sno_hsalida.codnom=sno_hconceptopersonal.codnom ". 
							"                    AND sno_hsalida.codconc=sno_hconceptopersonal.codconc  ".
							"                    AND sno_hsalida.codper=sno_hconceptopersonal.codper ".
							"                    AND sno_hsalida.codperi <=  sno_hconceptopersonal.codperi ".
							"		    AND tipsal='P2'),0)  ".
							" WHERE codemp='".$this->ls_codemp."'  ".
							"   AND anocur='".$ls_anocurnom."' ".
							"   AND codperi='".$ls_codperi."'".
							"   AND codnom IN (SELECT codnom  ".
							"		    		 FROM sno_hconcepto  ".
							"                   WHERE sno_hconcepto.codemp=sno_hconceptopersonal.codemp ".
							"                     AND sno_hconcepto.codnom=sno_hconceptopersonal.codnom ".
							"                     AND sno_hconcepto.anocur=sno_hconceptopersonal.anocur ".
							"                     AND sno_hconcepto.codperi=sno_hconceptopersonal.codperi ".
							"                     AND sno_hconcepto.codconc=sno_hconceptopersonal.codconc ".
							"                     AND sno_hconcepto.sigcon='P') ";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$lb_valido=false;
						$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_repararacumuladoconceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					}
				}
				if($lb_valido)
				{
					$ls_sql="UPDATE sno_hconceptopersonal ".
							"   SET acuemp =  COALESCE((SELECT SUM(valsal) ".
							"		   FROM sno_hsalida ".
							"                  WHERE sno_hsalida.codemp=sno_hconceptopersonal.codemp ".
							"		    AND sno_hsalida.anocur=sno_hconceptopersonal.anocur ". 
							"		    AND sno_hsalida.codnom=sno_hconceptopersonal.codnom ". 
							"                    AND sno_hsalida.codconc=sno_hconceptopersonal.codconc  ".
							"                    AND sno_hsalida.codper=sno_hconceptopersonal.codper ".
							"                    AND sno_hsalida.codperi <=  sno_hconceptopersonal.codperi),0)  ".
							" WHERE codemp='".$this->ls_codemp."'  ".
							"   AND anocur='".$ls_anocurnom."' ".
							"   AND codperi='".$ls_codperi."'".
							"   AND codnom IN (SELECT codnom  ".
							"		    		 FROM sno_hconcepto  ".
							"                   WHERE sno_hconcepto.codemp=sno_hconceptopersonal.codemp ".
							"                     AND sno_hconcepto.codnom=sno_hconceptopersonal.codnom ".
							"                     AND sno_hconcepto.anocur=sno_hconceptopersonal.anocur ".
							"                     AND sno_hconcepto.codperi=sno_hconceptopersonal.codperi ".
							"                     AND sno_hconcepto.codconc=sno_hconceptopersonal.codconc ".
							"                     AND sno_hconcepto.sigcon<>'P') ";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$lb_valido=false;
						$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_repararacumuladoconceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					}
				}
				$rs_data->MoveNext();
			}
			if($lb_valido)
			{
				$ls_sql="UPDATE sno_conceptopersonal ".
						"   SET acuemp =  COALESCE((SELECT SUM(valsal) ".
						"		   					  FROM sno_hsalida ".
						"                  			 WHERE sno_hsalida.codemp=sno_conceptopersonal.codemp ".
						"		    				   AND sno_hsalida.codnom=sno_conceptopersonal.codnom ". 
						"                              AND sno_hsalida.codconc=sno_conceptopersonal.codconc  ".
						"                              AND sno_hsalida.codper=sno_conceptopersonal.codper ".
						"		                       AND sno_hsalida.tipsal='P1'),0)  ".
						" WHERE codemp='".$this->ls_codemp."'  ".
						"   AND codnom IN (SELECT codnom  ".
						"		    		 FROM sno_concepto  ".
						"                   WHERE sno_concepto.codemp=sno_conceptopersonal.codemp ".
						"                     AND sno_concepto.codnom=sno_conceptopersonal.codnom ".
						"                     AND sno_concepto.codconc=sno_conceptopersonal.codconc ".
						"                     AND sno_concepto.sigcon='P') ";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_repararacumuladoconceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				}
			}
			if($lb_valido)
			{
				$ls_sql="UPDATE sno_conceptopersonal ".
						"   SET acupat =  COALESCE((SELECT SUM(valsal) ".
						"		   					  FROM sno_hsalida ".
						"                  			 WHERE sno_hsalida.codemp=sno_conceptopersonal.codemp ".
						"		    				   AND sno_hsalida.codnom=sno_conceptopersonal.codnom ". 
						"                              AND sno_hsalida.codconc=sno_conceptopersonal.codconc  ".
						"                              AND sno_hsalida.codper=sno_conceptopersonal.codper ".
						"		                       AND sno_hsalida.tipsal='P2'),0)  ".
						" WHERE codemp='".$this->ls_codemp."'  ".
						"   AND codnom IN (SELECT codnom  ".
						"		    		 FROM sno_concepto  ".
						"                   WHERE sno_concepto.codemp=sno_conceptopersonal.codemp ".
						"                     AND sno_concepto.codnom=sno_conceptopersonal.codnom ".
						"                     AND sno_concepto.codconc=sno_conceptopersonal.codconc ".
						"                     AND sno_concepto.sigcon='P') ";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_repararacumuladoconceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				}
			}
			if($lb_valido)
			{
				$ls_sql="UPDATE sno_conceptopersonal ".
						"   SET acuemp =  COALESCE((SELECT SUM(valsal) ".
						"		   					  FROM sno_hsalida ".
						"                  			 WHERE sno_hsalida.codemp=sno_conceptopersonal.codemp ".
						"		    				   AND sno_hsalida.codnom=sno_conceptopersonal.codnom ". 
						"                              AND sno_hsalida.codconc=sno_conceptopersonal.codconc  ".
						"                              AND sno_hsalida.codper=sno_conceptopersonal.codper),0)  ".
						" WHERE codemp='".$this->ls_codemp."'  ".
						"   AND codnom IN (SELECT codnom  ".
						"		    		 FROM sno_concepto  ".
						"                   WHERE sno_concepto.codemp=sno_conceptopersonal.codemp ".
						"                     AND sno_concepto.codnom=sno_conceptopersonal.codnom ".
						"                     AND sno_concepto.codconc=sno_conceptopersonal.codconc ".
						"                     AND sno_concepto.sigcon<>'P') ";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_repararacumuladoconceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				}
			}
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Se realizó el proceso de Reparar Acumulado de Conceptos.";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////				
			if($lb_valido)
			{	
				$this->io_mensajes->message("El proceso de Reparar Acumulado de Conceptos se realizó correctamente.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_mantenimiento_repararacumuladoconceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
		}
      	return ($lb_valido);  
    }// end function uf_mantenimiento_repararacumuladoconceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
   	function uf_numero_IVSS()
   	{ 	
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_numero_IVSS
		//		   Access: public (sigesp_snorh_p_configuracion.php)
		//	    Arguments:
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que busca el numero de seguro social en sigesp_empresa
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/08/2008								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		$ls_sql="SELECT nroivss FROM sigesp_empresa WHERE codemp='".$this->ls_codemp."'";
		$rs_data=$this->io_sql->select($ls_sql);
		
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_numero_IVSS ERROR->".
			                            $this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_nroivss=$row["nroivss"];
			}		
		}
		if ($as_nroivss=="")
		{
			$as_nroivss="XXXXXXXXX";
		}		
		return $as_nroivss;
   	}//fin de uf_numero_IVSS()
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_sessionnomina_encargaduria($as_codnom)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_sessionnomina_encargaduria
		//		   Access: public (en toda las pantallas de procesos)
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que crea la sessión de la nómina actual
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 26/12/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($as_codnom!="")
		{
			$ls_nomina=$as_codnom;
		}
		else
		{
			$ls_nomina=$_SESSION["la_nomina"]["codnom"];
		}
		$ls_sql="SELECT sno_nomina.codnom, sno_nomina.desnom, sno_nomina.peractnom, sno_nomina.diabonvacnom, sno_nomina.diareivacnom, ".
				"		sno_nomina.adenom, sno_periodo.fecdesper, sno_periodo.fechasper, sno_periodo.cerper, sno_periodo.conper,".
				"		sno_periodo.totper, sno_nomina.anocurnom, sno_nomina.tippernom, sno_nomina.perresnom, sno_nomina.racnom, ".
				"		sno_nomina.subnom, sno_nomina.espnom, sno_nomina.consulnom, sno_nomina.descomnom, sno_nomina.codpronom, ".
				"		sno_nomina.codbennom, sno_nomina.conaponom, sno_nomina.cueconnom, sno_nomina.notdebnom, ".
				"       sno_nomina.numvounom, sno_nomina.recdocnom, sno_nomina.recdocapo, sno_nomina.tipdocnom, sno_nomina.tipdocapo, ".
				"		sno_nomina.tipnom, sno_nomina.fecininom, sno_nomina.numpernom, sno_nomina.conpernom, sno_nomina.conpronom, ".
				"		sno_nomina.titrepnom, sno_nomina.divcon, sno_nomina.subnom, sno_nomina.informa, ".
				"       sno_nomina.recdocpagperche, sno_nomina.tipdocpagperche, sno_nomina.estctaalt, sno_nomina.racobrnom, sno_nomina.nomliq ".
				"  FROM sno_nomina, sno_periodo ".
				" WHERE sno_nomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_nomina.codnom='".$ls_nomina."' ".
				"   AND sno_nomina.codemp=sno_periodo.codemp ".
				"   AND sno_nomina.codnom=sno_periodo.codnom ".
				"   AND sno_nomina.peractnom=sno_periodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_crear_sessionnomina_encargaduria ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$_SESSION["la_nominaenc"]=$row;
				$ld_fecdesper=$this->io_funciones->uf_formatovalidofecha($_SESSION["la_nominaenc"]["fecdesper"]);
				$ld_fechasper=$this->io_funciones->uf_formatovalidofecha($_SESSION["la_nominaenc"]["fechasper"]);
				$_SESSION["la_nominaenc"]["fecdesper"]=$ld_fecdesper;
				$_SESSION["la_nominaenc"]["fechasper"]=$ld_fechasper;
				$ld_fecdesper=$this->io_funciones->uf_convertirfecmostrar($ld_fecdesper);
				$ld_fechasper=$this->io_funciones->uf_convertirfecmostrar($ld_fechasper);				
				$_SESSION["la_nominaenc"]["tiponomina"]="NORMAL";
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("Favor verifique los datos de la nómina de encargaduría. No se pueden cargar los datos."); 
				print "<script language=JavaScript>";
				print "location.href='sigespwindow_blank.php'";
				print "</script>";		
			}
			$this->io_sql->free_result($rs_data);
		}
      	return ($lb_valido);  
    }// end function uf_crear_sessionnomina	
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//------------------------------------------------------------------------------------------------------------------------------------	
	function uf_buscar_personal($as_codnom,$codperi,$as_valor,$ai_inicio,$ai_registros,$ai_totpag, $as_codperdes, $as_codperhas,$as_codban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_personalnomina
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: 
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 08/01/2009								Fecha Última Modificación :  		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_orden="";
		$ls_pag="";			
		$ls_gestor=$_SESSION["ls_gestor"];
		switch($ls_gestor)
		{
			case "MYSQLT":
				$ls_pag= " LIMIT ".$ai_inicio.",".$ai_registros."";
			break;
			case "MYSQLI":
				$ls_pag= " LIMIT ".$ai_inicio.",".$ai_registros."";
			break;
			case "POSTGRES":
				$ls_pag= " LIMIT ".$ai_registros." OFFSET ".$ai_inicio."";
			
			break;			
		}	
		$ls_criterio="";
		if($as_codban!="")
		{
			$ls_criterio=" AND sno_personalnomina.codban= '".$as_codban."' ";
		}		
		$ls_sql="  SELECT sno_personalnomina.codper,".
		        " (SELECT count(sno_personalnomina.codper) ".
				"     FROM sno_personal, sno_personalnomina, sno_resumen ".
                "     WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"     AND sno_personalnomina.codnom='".$as_codnom."' ".
				"     AND sno_personalnomina.pagbanper=1 ".
				"     AND sno_personalnomina.pagefeper=0 ".
				"     AND sno_personalnomina.pagtaqper=0 ".
				"     AND sno_resumen.codperi='".$codperi."' ".
				"     AND sno_resumen.monnetres > 0 ".
				"	  AND sno_personalnomina.codemp = sno_resumen.codemp ".
				"     AND sno_personalnomina.codnom = sno_resumen.codnom ".
				"     AND sno_personalnomina.codper = sno_resumen.codper ".$ls_criterio.
				"     AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	  AND sno_personal.codper = sno_personalnomina.codper) as valor  ".
                "     FROM sno_personal, sno_personalnomina, sno_resumen ".
                "  WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_personalnomina.codnom='".$as_codnom."' ".
				"   AND sno_personalnomina.pagbanper=1 ".
				"   AND sno_personalnomina.pagefeper=0 ".
				"   AND sno_personalnomina.pagtaqper=0 ".
				"   AND sno_resumen.codperi='".$codperi."' ".
				"   AND sno_resumen.monnetres > 0 ".
				"	AND sno_personalnomina.codemp = sno_resumen.codemp ".
				"   AND sno_personalnomina.codnom = sno_resumen.codnom ".
				"   AND sno_personalnomina.codper = sno_resumen.codper ".
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".$ls_criterio.
				"	AND sno_personal.codper = sno_personalnomina.codper ".
                "  GROUP BY sno_personalnomina.codper ".
				"  ORDER BY sno_personalnomina.codper ".$ls_pag;	       
		$rs_data=$this->io_sql->select($ls_sql);
	    $li_numero=$this->io_sql->num_rows($rs_data);
		$li=1;	
		while($row=$this->io_sql->fetch_row($rs_data))
		{
			$as_valor=$row["valor"];
			if ($li==1)
			{
				$as_codperdes=$row["codper"];
				$li=0;
			}			
			$li_numero=$li_numero-1;
			if ($li_numero==0)	
			{
				$as_codperhas=$row["codper"]; 
			}
											
		}				
		$ai_totpag = ceil($as_valor / $ai_registros); 
				
		$arrResultado['as_valor']=$as_valor;
		$arrResultado['ai_totpag']=$ai_totpag;
		$arrResultado['as_codperdes']=$as_codperdes;
		$arrResultado['as_codperhas']=$as_codperhas;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_buscar_personal
	//------------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_personal_historico($as_codnom,$codperi,$as_valor,$ai_inicio,$ai_registros,$ai_totpag, $as_codperdes, $as_codperhas,$as_codban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_personal_historico
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: 
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 08/01/2009								Fecha Última Modificación :  		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_orden="";
		$ls_pag="";			
		$ls_gestor=$_SESSION["ls_gestor"];
		switch($ls_gestor)
		{
			case "MYSQLT":
				$ls_pag= " LIMIT ".$ai_inicio.",".$ai_registros."";
			break;
			case "MYSQLI":
				$ls_pag= " LIMIT ".$ai_inicio.",".$ai_registros."";
			break;
			case "POSTGRES":
				$ls_pag= " LIMIT ".$ai_registros." OFFSET ".$ai_inicio."";
			
			break;			
		}
		$ls_criterio="";
		if($as_codban!="")
		{
			$ls_criterio=" AND sno_thpersonalnomina.codban= '".$as_codban."' ";
		}			
		$ls_sql="  SELECT sno_thpersonalnomina.codper,".
		        " (SELECT count(sno_thpersonalnomina.codper) ".
				"     FROM sno_personal, sno_thpersonalnomina, sno_thresumen ".
                "     WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"     AND sno_thpersonalnomina.codnom='".$as_codnom."' ".
				"     AND sno_thpersonalnomina.pagbanper=1 ".
				"     AND sno_thpersonalnomina.pagefeper=0 ".
				"     AND sno_thpersonalnomina.pagtaqper=0 ".
				"     AND sno_thresumen.codperi='".$codperi."' ".
				"     AND sno_thresumen.monnetres > 0 ".
				"	  AND sno_thpersonalnomina.codemp = sno_thresumen.codemp ".
				"     AND sno_thpersonalnomina.codnom = sno_thresumen.codnom ".
				"     AND sno_thpersonalnomina.codper = sno_thresumen.codper ".$ls_criterio.
				"     AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
				"	  AND sno_personal.codper = sno_thpersonalnomina.codper) as valor  ".
                "     FROM sno_personal, sno_thpersonalnomina, sno_thresumen ".
                "  WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$as_codnom."' ".
				"   AND sno_thpersonalnomina.pagbanper=1 ".
				"   AND sno_thpersonalnomina.pagefeper=0 ".
				"   AND sno_thpersonalnomina.pagtaqper=0 ".
				"   AND sno_thresumen.codperi='".$codperi."' ".
				"   AND sno_thresumen.monnetres > 0 ".
				"	AND sno_thpersonalnomina.codemp = sno_thresumen.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thresumen.codnom ".
				"   AND sno_thpersonalnomina.codper = sno_thresumen.codper ".
				"   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".$ls_criterio.
				"	AND sno_personal.codper = sno_thpersonalnomina.codper ".
                "  GROUP BY sno_thpersonalnomina.codper ".
				"  ORDER BY sno_thpersonalnomina.codper ".$ls_pag;	       
		$rs_data=$this->io_sql->select($ls_sql);
	    $li_numero=$this->io_sql->num_rows($rs_data);
		$li=1;	
		while($row=$this->io_sql->fetch_row($rs_data))
		{
			$as_valor=$row["valor"];
			if ($li==1)
			{
				$as_codperdes=$row["codper"];
				$li=0;
			}			
			$li_numero=$li_numero-1;
			if ($li_numero==0)	
			{
				$as_codperhas=$row["codper"]; 
			}
											
		}				
		$ai_totpag = ceil($as_valor / $ai_registros); 
				
		$arrResultado['as_valor']=$as_valor;
		$arrResultado['ai_totpag']=$ai_totpag;
		$arrResultado['as_codperdes']=$as_codperdes;
		$arrResultado['as_codperhas']=$as_codperhas;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_buscar_personal_historico
	//------------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_sueldos_deuda_anterior($ai_sueldoshistoricos,$ai_deudaanterior)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_sueldos_deuda_anterior
		//		   Access: public
		//	    Arguments: ai_sueldoshistoricos  // Si existe sueldos anteriores
		//				   ai_deudaanterior  // Si existe deuda anterior
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Función que obtiene 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT count(codemp) AS sueldoshistoricos, 0 as deudaanterior  ".
				"  FROM sno_sueldoshistoricos ".
				" WHERE codemp='".$this->ls_codemp."' ".
				" UNION ".
				"SELECT 0 AS sueldoshistoricos, count(codemp) as deudaanterior  ".
				"  FROM sno_deudaanterior ".
				" WHERE codemp='".$this->ls_codemp."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_select_sueldos_deuda_anterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$li_i=0;
			while(!$rs_data->EOF)
			{
				$ai_sueldoshistoricos=$ai_sueldoshistoricos+number_format($rs_data->fields["sueldoshistoricos"],2,".","");
				$ai_deudaanterior=$ai_deudaanterior+number_format($rs_data->fields["deudaanterior"],2,".","");
				$rs_data->MoveNext();;
			}
			$this->io_sql->free_result($rs_data);		
		}
		$arrResultado['ai_sueldoshistoricos']=$ai_sueldoshistoricos;
		$arrResultado['ai_deudaanterior']=$ai_deudaanterior;
		$arrResultado['ls_valor']=rtrim($ls_valor);
		return $arrResultado;
	}// end function uf_select_sueldos_deuda_anterior
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//---------------------------------------------------------------------------------------------------------------------------
	function utf8_to_latin9($utf8str) 
	{ // replaces utf8_decode()
		$trans = array("?"=>"?", "?"=>"?", "?"=>"?", "?"=>"?", "?"=>"?", "?"=>"?", "?"=>"?", "?"=>"?");
		$wrong_utf8str = strtr($utf8str, $trans);
		$latin9str = utf8_decode($wrong_utf8str);
		return $latin9str;
	}
	//---------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_recalcular_conceptos($aa_seguridad)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_recalcular_conceptos
		//		   Access: public (sigesp_snorh_p_configuracion.php)
		//	    Arguments: aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto correctamente la función y false si hubo error
		//	  Description: Función que le actualiza el sueldo integral del personal en las tablas de históricos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/04/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();	
		$ls_sql="SELECT sno_periodo.codnom, sno_periodo.cerper, substr(cast(sno_periodo.fecdesper as char(10)),6,2) AS mes, substr(cast(sno_periodo.fecdesper as char(10)),1,4) AS anio  ".
 				"  FROM sno_periodo ".
				" INNER JOIN sno_nomina ".
				"    ON sno_nomina.espnom='0' ".
				"   AND sno_nomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_periodo.fecdesper <>'1900-01-01'".
                                "   AND sno_periodo.cerper =1 ".
				"   AND sno_periodo.codemp = sno_nomina.codemp".
				"   AND sno_periodo.codnom = sno_nomina.codnom".
				" GROUP BY sno_periodo.codnom,cerper, substr(cast(sno_periodo.fecdesper as char(10)),1,4), substr(cast(sno_periodo.fecdesper as char(10)),6,2) ".
				" ORDER BY sno_periodo.codnom,substr(cast(sno_periodo.fecdesper as char(10)),1,4), substr(cast(sno_periodo.fecdesper as char(10)),6,2),cerper DESC ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_recalcular_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ls_sql="DELETE FROM sno_sueldoshistoricos ".
					" WHERE codemp = '".$this->ls_codemp."' ".
					"   AND estmodcont = '1' ".
					"   AND fecsue IN (SELECT CAST(substr(cast(sno_periodo.fecdesper as char(10)),1,4)||'-'||substr(cast(sno_periodo.fecdesper as char(10)),6,2)||'-01' AS DATE) FROM sno_periodo WHERE cerper = 1 GROUP BY sno_periodo.fecdesper) ";
			$rs_data3=$this->io_sql->select($ls_sql);
			if($rs_data3===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE-SNO MÉTODO->uf_recalcular_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$codnom=$rs_data->fields["codnom"];
				$mes=$rs_data->fields["mes"];
				$anio=$rs_data->fields["anio"];
				if($rs_data->fields["cerper"]==1)
				{
					$ls_sql="SELECT sno_hpersonalnomina.codper, MIN(sno_hpersonalnomina.codded) AS codded, MIN(sno_hpersonalnomina.codtipper) AS codtipper, SUM(sno_hsalida.valsal) AS fijo, ".
							"       0 as variable, MAX(substr(cast(sno_periodo.fecdesper as char(10)),1,7)) as fecha, ".
							"		0 AS salnorper,0 AS sueintper, sno_hconcepto.salnor	".
							"  FROM sno_hsalida ".
							" INNER JOIN sno_periodo ".
							"    ON sno_hsalida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hsalida.codnom = '".$codnom."' ". 
							"   AND substr(cast(sno_periodo.fecdesper as char(10)),6,2)='".$mes."' ".
							"   AND sno_periodo.cerper = '1'  ".
							"   AND sno_hsalida.valsal <> 0  ".
							"   AND sno_hsalida.codemp = sno_periodo.codemp ".
							"   AND sno_hsalida.codnom = sno_periodo.codnom ".
							"   AND sno_hsalida.codperi = sno_periodo.codperi ".
							"  LEFT JOIN sno_fideiperiodo ".
							"    ON sno_hsalida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hsalida.codnom = '".$codnom."' ". 
							"   AND sno_fideiperiodo.anocurper='".$anio."' ".
							"   AND sno_fideiperiodo.mescurper=".intval($mes)." ".
							"   AND sno_hsalida.valsal <> 0  ".
							"   AND sno_hsalida.codemp = sno_fideiperiodo.codemp ".
							"   AND sno_hsalida.codnom = sno_fideiperiodo.codnom ".
							"   AND sno_hsalida.codper = sno_fideiperiodo.codper ".
							" INNER JOIN sno_hpersonalnomina ".
							"    ON sno_hsalida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hsalida.codnom = '".$codnom."'  ".
							"   AND sno_hsalida.valsal <> 0  ".
							"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
							"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
							"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
							"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
							"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
							" INNER JOIN sno_hconcepto ".
							"    ON sno_hsalida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hsalida.codnom = '".$codnom."' ".
							"   AND sno_hconcepto.salnor = 'F' ".
							"   AND sno_hsalida.valsal <> 0  ".
							"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
							"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
							"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
							"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
							"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
							" GROUP BY sno_hpersonalnomina.codper, sno_hconcepto.salnor ".
							"  UNION  ".
							"SELECT sno_hpersonalnomina.codper, MIN(sno_hpersonalnomina.codded) AS codded, MIN(sno_hpersonalnomina.codtipper) AS codtipper,  0 AS fijo, ".
							"		SUM(sno_hsalida.valsal) as variable, MAX(substr(cast(sno_periodo.fecdesper as char(10)),1,7)) as fecha, ".
							"		0 AS salnorper, 0 AS sueintper, sno_hconcepto.salnor".
							"  FROM sno_hsalida ".
							" INNER JOIN sno_periodo ".
							"    ON sno_hsalida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hsalida.codnom = '".$codnom."'  ".
							"   AND substr(cast(sno_periodo.fecdesper as char(10)),6,2)='".$mes."' ".
							"   AND sno_periodo.cerper = '1'  ".
							"   AND sno_hsalida.valsal <> 0  ".
							"   AND sno_hsalida.codemp = sno_periodo.codemp ".
							"   AND sno_hsalida.codnom = sno_periodo.codnom ".
							"   AND sno_hsalida.codperi = sno_periodo.codperi ".
							"  LEFT JOIN sno_fideiperiodo ".
							"    ON sno_hsalida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hsalida.codnom = '".$codnom."' ". 
							"   AND sno_fideiperiodo.anocurper='".$anio."' ".
							"   AND sno_fideiperiodo.mescurper=".intval($mes)." ".
							"   AND sno_hsalida.valsal <> 0  ".
							"   AND sno_hsalida.codemp = sno_fideiperiodo.codemp ".
							"   AND sno_hsalida.codnom = sno_fideiperiodo.codnom ".
							"   AND sno_hsalida.codper = sno_fideiperiodo.codper ".
							" INNER JOIN sno_hpersonalnomina ".
							"    ON sno_hsalida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hsalida.codnom = '".$codnom."' ". 
							"   AND sno_hsalida.valsal <> 0  ".
							"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
							"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
							"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
							"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
							"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
							" INNER JOIN sno_hconcepto ".
							"    ON sno_hsalida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hsalida.codnom = '".$codnom."' ". 
							"   AND sno_hconcepto.salnor = 'V' ".
							"   AND sno_hsalida.valsal <> 0  ".
							"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
							"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
							"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
							"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
							"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
							" GROUP BY sno_hpersonalnomina.codper, sno_hconcepto.salnor  ".
							"  UNION  ".
							"SELECT sno_hpersonalnomina.codper, MIN(sno_hpersonalnomina.codded) AS codded, MIN(sno_hpersonalnomina.codtipper) AS codtipper,  0 AS fijo, ".
							"		0 as variable, MAX(substr(cast(sno_periodo.fecdesper as char(10)),1,7)) as fecha, ".
							"		SUM(sno_hpersonalnomina.salnorper) AS salnorper, SUM(sno_hpersonalnomina.sueintper) AS sueintper,'F' AS salnor".
							"  FROM sno_hpersonalnomina ".
							" INNER JOIN sno_periodo ".
							"    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hpersonalnomina.codnom = '".$codnom."'  ".
							"   AND substr(cast(sno_periodo.fecdesper as char(10)),6,2)='".$mes."' ".
							"   AND sno_periodo.cerper = '1'  ".
							"   AND (sno_hpersonalnomina.salnorper > 0 OR sno_hpersonalnomina.sueintper > 0)  ".
							"   AND sno_hpersonalnomina.codemp = sno_periodo.codemp ".
							"   AND sno_hpersonalnomina.codnom = sno_periodo.codnom ".
							"   AND sno_hpersonalnomina.codperi = sno_periodo.codperi ".
							" GROUP BY sno_hpersonalnomina.codper  ".
							" ORDER BY codper, codded, codtipper  ";	
					$rs_data2=$this->io_sql->select($ls_sql);
					if($rs_data2===false)
					{
						$lb_valido=false;
						$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_recalcular_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					}
					else
					{
						$codperant='';
						while((!$rs_data2->EOF)&&($lb_valido))
						{
							$lb_existe=false;
							$ls_codper=$rs_data2->fields["codper"];
							$ls_codded=$rs_data2->fields["codded"];
							$ls_codtipper=$rs_data2->fields["codtipper"];
							$ld_fecha=$rs_data2->fields["fecha"].'-01';
							$li_salnorper=number_format($rs_data2->fields["salnorper"],2,'.','');
							$li_sueprodia=number_format($li_salnorper/30,2,'.','');
							$li_sueintper=number_format($rs_data2->fields["sueintper"],2,'.','');
							$li_fijo=number_format($rs_data2->fields["fijo"],2,'.','');
							$li_variable=number_format($rs_data2->fields["variable"],2,'.','');
							$arrResultado=$this->uf_existe_sueldo_historico($ls_codper,$ld_fecha,$lb_existe);
							$lb_existe=$arrResultado['ab_existe'];
							$lb_valido=$arrResultado['lb_valido'];
							if(!$lb_existe)
							{
								$ls_sql="INSERT INTO sno_sueldoshistoricos (codemp,codper,fecsue,codded,codtipper,suebas,sueint,sueprodia,bonvac,bonfinanio,".
										"			 otrasig,confij,convar,estmodcont) VALUES ('".$this->ls_codemp."','".$ls_codper."','".$ld_fecha."','".$ls_codded."',".
										"			 '".$ls_codtipper."',".$li_salnorper.",".$li_sueintper.",".$li_sueprodia.",0,0,0,".$li_fijo.",".$li_variable.",'1') ";
								$rs_data3=$this->io_sql->select($ls_sql);
								if($rs_data3===false)
								{
									$lb_valido=false;
									$this->io_mensajes->message("CLASE-SNO MÉTODO->uf_recalcular_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
								}
							}
							else
							{
								$ls_sql="UPDATE sno_sueldoshistoricos ".
										"	SET suebas = suebas + ".$li_salnorper.", ".
										"       sueint = sueint + ".$li_sueintper.", ".
										"	    confij = confij + ".$li_fijo.", ".
										"       convar = convar + ".$li_variable." ".
										" WHERE codemp = '".$this->ls_codemp."' ".
										"   AND codper = '".$ls_codper."' ".
										"   AND fecsue = '".$ld_fecha."' ".
										"   AND estmodcont = '1'";
								$rs_data3=$this->io_sql->select($ls_sql);
								if($rs_data3===false)
								{
									$lb_valido=false;
									$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_recalcular_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
								}
							}
							$rs_data2->MoveNext();
						}
					}	
				}
				$rs_data->MoveNext();
			}
		}
		if($lb_valido)
		{	
			$this->io_mensajes->message("El proceso de recalcular Conceptos se realizó correctamente.");
			$this->io_sql->commit();
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Se realizó el proceso de recalcular Conceptos.";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////				
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_recalcular_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
      	return ($lb_valido);  
    }// end function uf_recalcular_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------------------
	function uf_existe_sueldo_historico($as_codper,$adt_fecha,$ab_existe)
	{
		$ab_existe=false;
		$ls_sql="SELECT codper ".
                "  FROM sno_sueldoshistoricos  ".
                " WHERE codemp = '".$this->ls_codemp."' ".
                "   AND codper = '".$as_codper."' ".
				"   AND fecsue = '".$adt_fecha."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo MÉTODO->uf_actualizar_grado_medicos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$lb_valido=true;
			if((!$rs_data->EOF)&&($lb_valido))
			{
				$ab_existe=true;
			}
		}
		$arrResultado['ab_existe']=$ab_existe;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
    }// end function uf_existe_sueldo_historico	
	//---------------------------------------------------------------------------------------------------------------------------------------
}
?>