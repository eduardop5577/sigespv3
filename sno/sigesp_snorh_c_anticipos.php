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

class sigesp_snorh_c_anticipos
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_fun_nomina;
	var $io_fideiconfigurable;
	var $io_personal;
	var $io_sno;
	var $ls_codemp;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_c_anticipos
		//		   Access: public (sigesp_snorh_d_sueldoshistoricos)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad=new sigesp_c_seguridad();
		require_once("class_folder/class_funciones_nomina.php");
		$this->io_fun_nomina=new class_funciones_nomina();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		
	}// end function sigesp_snorh_c_anticipos
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_sueldoshistoricos)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fun_nomina);
        unset($this->ls_codemp);
        
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_datos($as_codper, $ai_pormaxant, $as_codant, $ai_monpreant, $ai_monintant, $ai_monantant, $ai_monantint, $ai_saldo, $ai_saldoint)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_datos
		//		   Access: private (uf_guardar) 
		//	    Arguments: as_codper  // código del personal
		//				   ai_pormaxant  // Porcentaje Máximo de Prestación
		//				   as_codant  // código del anticipo
		//				   ai_monpreant  // Monto Prestación antiguedad Anterior
		//				   ai_monintant  // Monto Intereses Anteriores
		//				   ai_monantant  // Monto Anticipos Anteriores
		//				   ai_monantant  // Monto Anticipos Intereses Anteriores
		//				   ai_saldo  // Saldo
		//	      Returns: lb_valido True si lo obtuvo correctamente ó False si hubo error
		//	  Description: Funcion que busca los datos del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_codant=1;
		$ls_sql="SELECT MAX(codant) AS codant, SUM(monant) AS monantant, SUM(monint) AS monantint, 0 as monpreant, 0 as monintant, ".
				"		(SELECT SUM(monant) ".
				"          FROM sno_anticipoprestaciones ".
				"         WHERE codemp='".$this->ls_codemp."'".
				"           AND codper='".$as_codper."'".
				"           AND estant='X'".
				"         GROUP BY codper) AS monantantanu, ".
				"		(SELECT SUM(monint) ".
				"          FROM sno_anticipoprestaciones ".
				"         WHERE codemp='".$this->ls_codemp."'".
				"           AND codper='".$as_codper."'".
				"           AND estant='X'".
				"         GROUP BY codper) AS monantintanu ".
				"  FROM sno_anticipoprestaciones ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ".
				" UNION ".
				"SELECT '000' AS codant, 0 AS monantant, 0 AS monantint, 0 as monpreant, SUM(monint) as monintant, 0 AS monantantanu, 0 AS monantintanu  ".
				"  FROM sno_fideiperiodointereses ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ".
				" UNION ".
				"SELECT '000' AS codant, 0 AS monantant, 0 AS monantint, SUM(apoper) as monpreant, 0 as monintant, 0 AS monantantanu, 0 AS monantintanu  ".
				"  FROM sno_fideiperiodo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ".
				" UNION ".
				"SELECT '000' AS codant, SUM(monant) AS monantant, 0 AS monantint, SUM(monpreant) as monpreant, SUM(monint) as monintant, 0 AS monantantanu, 0 AS monantintanu  ".
				"  FROM sno_deudaanterior ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ".
				" ORDER BY codant ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_load_datos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$lb_intereses=true;
			while(!$rs_data->EOF)
			{
				$as_codant=intval($rs_data->fields["codant"]+1);
				$ai_monantant=$ai_monantant+number_format($rs_data->fields["monantant"],2,".","")-number_format($rs_data->fields["monantantanu"],2,".","");
				$ai_monantint=$ai_monantint+number_format($rs_data->fields["monantint"],2,".","")-number_format($rs_data->fields["monantintanu"],2,".","");
				$ai_monpreant=$ai_monpreant+number_format($rs_data->fields["monpreant"],2,".","");
				$ai_monintant=$ai_monintant+number_format($rs_data->fields["monintant"],2,".","");
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		$ai_saldo=number_format(($ai_monpreant*($ai_pormaxant/100)),2,".","")-$ai_monantant;
		$ai_saldoint=($ai_monintant-$ai_monantint);
		$as_codant=str_pad($as_codant,3,"0",0);
		$ai_monantant=number_format($ai_monantant,2,",",".");
		$ai_monantint=number_format($ai_monantint,2,",",".");
		$ai_monpreant=number_format($ai_monpreant,2,",",".");
		$ai_monintant=number_format($ai_monintant,2,",",".");
		$ai_saldo=number_format($ai_saldo,2,",",".");
		$ai_saldoint=number_format($ai_saldoint,2,",",".");
		$arrResultado['as_codant']=$as_codant;
		$arrResultado['ai_monpreant']=$ai_monpreant;
		$arrResultado['ai_monintant']=$ai_monintant;
		$arrResultado['ai_monantant']=$ai_monantant;
		$arrResultado['ai_monantint']=$ai_monantint;
		$arrResultado['ai_saldo']=$ai_saldo;
		$arrResultado['ai_saldoint']=$ai_saldoint;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_datos
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reload_datos($as_codper, $ai_pormaxant, $as_codant, $ai_monpreant, $ai_monintant, $ai_monantant, $ai_monantint, $ai_saldo, $ai_saldoint,$as_mes,$as_anio)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_datos
		//		   Access: private (uf_guardar) 
		//	    Arguments: as_codper  // código del personal
		//				   ai_pormaxant  // Porcentaje Máximo de Prestación
		//				   as_codant  // código del anticipo
		//				   ai_monpreant  // Monto Prestación antiguedad Anterior
		//				   ai_monintant  // Monto Intereses Anteriores
		//				   ai_monantant  // Monto Anticipos Anteriores
		//				   ai_monantant  // Monto Anticipos Intereses Anteriores
		//				   ai_saldo  // Saldo
		//	      Returns: lb_valido True si lo obtuvo correctamente ó False si hubo error
		//	  Description: Funcion que busca los datos del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_monpreant=0;
		$ai_monintant=0;
		$ai_monantant=0;
		$ai_monantint=0;
		$ai_saldo=0;
		$ai_saldoint=0;
		$as_codant=1;
		if ($as_mes==01)
		{
			$as_mes=12;
			$as_anio=$as_anio-1;
		}
		$ld_fecha_sin=$as_anio."-".$as_mes."-"."01";
		$ls_concat="";
		$ls_concat2="";
		$ls_concat=$ls_concat.$this->io_conexion->Concat('sno_fideiperiodointereses.anocurper',"'-'",'sno_fideiperiodointereses.mescurper',"'-'","'01'");	
		$ls_concat2=$ls_concat2.$this->io_conexion->Concat('sno_fideiperiodo.anocurper',"'-'",'sno_fideiperiodo.mescurper',"'-'","'01'");	
		$ls_sql="SELECT MAX(codant) AS codant, SUM(monant) AS monantant, SUM(monint) AS monantint, 0 as monpreant, 0 as monintant, ".
				"		(SELECT SUM(monant) ".
				"          FROM sno_anticipoprestaciones ".
				"         WHERE codemp='".$this->ls_codemp."'".
				"           AND codper='".$as_codper."'".
				"           AND estant='X'".
				"         GROUP BY codper) AS monantantanu, ".
				"		(SELECT SUM(monint) ".
				"          FROM sno_anticipoprestaciones ".
				"         WHERE codemp='".$this->ls_codemp."'".
				"           AND codper='".$as_codper."'".
				"           AND estant='X'".
				"         GROUP BY codper) AS monantintanu ".
				"  FROM sno_anticipoprestaciones ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ".
				" UNION ".
				"SELECT '000' AS codant, 0 AS monantant, 0 AS monantint, 0 as monpreant, SUM(monint) as monintant, 0 AS monantantanu, 0 AS monantintanu  ".
				"  FROM sno_fideiperiodointereses ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND CAST(($ls_concat) AS DATE) <  '".$ld_fecha_sin."' ".
				" GROUP BY codper ".
				" UNION ".
				"SELECT '000' AS codant, 0 AS monantant, 0 AS monantint, SUM(apoper) as monpreant, 0 as monintant, 0 AS monantantanu, 0 AS monantintanu  ".
				"  FROM sno_fideiperiodo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND CAST(($ls_concat2) AS DATE) <  '".$ld_fecha_sin."' ".
				" GROUP BY codper ".
				" UNION ".
				"SELECT '000' AS codant, SUM(monant) AS monantant, 0 AS monantint, SUM(monpreant) as monpreant, SUM(monint) as monintant, 0 AS monantantanu, 0 AS monantintanu  ".
				"  FROM sno_deudaanterior ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" GROUP BY codper ".
				" ORDER BY codant ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_reload_datos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$lb_intereses=true;
			while(!$rs_data->EOF)
			{
				$as_codant=intval($rs_data->fields["codant"]+1);
				$ai_monantant=$ai_monantant+number_format($rs_data->fields["monantant"],2,".","")-number_format($rs_data->fields["monantantanu"],2,".","");
				$ai_monantint=$ai_monantint+number_format($rs_data->fields["monantint"],2,".","")-number_format($rs_data->fields["monantintanu"],2,".","");
				$ai_monpreant=$ai_monpreant+number_format($rs_data->fields["monpreant"],2,".","");
				$ai_monintant=$ai_monintant+number_format($rs_data->fields["monintant"],2,".","");
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		$ai_saldo=number_format(($ai_monpreant*($ai_pormaxant/100)),2,".","")-$ai_monantant;
		$ai_saldoint=($ai_monintant-$ai_monantint);
		$as_codant=str_pad($as_codant,3,"0",0);
		$ai_monantant=number_format($ai_monantant,2,",",".");
		$ai_monantint=number_format($ai_monantint,2,",",".");
		$ai_monpreant=number_format($ai_monpreant,2,",",".");
		$ai_monintant=number_format($ai_monintant,2,",",".");
		$ai_saldo=number_format($ai_saldo,2,",",".");
		$ai_saldoint=number_format($ai_saldoint,2,",",".");
		$arrResultado['as_codant']=$as_codant;
		$arrResultado['ai_monpreant']=$ai_monpreant;
		$arrResultado['ai_monintant']=$ai_monintant;
		$arrResultado['ai_monantant']=$ai_monantant;
		$arrResultado['ai_monantint']=$ai_monantint;
		$arrResultado['ai_saldo']=$ai_saldo;
		$arrResultado['ai_saldoint']=$ai_saldoint;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_reload_datos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_anticipos($as_codper,$as_codant)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_anticipos
		//		   Access: private
		//   	Arguments: as_codper  // Código del Personal
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si el Anticipo está registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codper ".
				"  FROM sno_anticipoprestaciones ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND codant='".$as_codant."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_select_anticipos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($rs_data->EOF)
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_existe;
	}// end function uf_select_anticipos
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_anticipos($as_codper,$as_codant,$as_estant,$ad_fecant,$ai_monpreant,$ai_monintant,$ai_monantant,$ai_monantint,
								 $ai_porant,$ai_monant,$ai_monint,$as_motant,$as_obsant,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_anticipos
		//		   Access: private
		//	    Arguments: as_codper  // Código del Personal
		//				   as_codant  // Código del Anticipo
		//				   as_estant  // Estatus del Anticipo
		//				   ad_fecant  // Fecha del Anticipo
		//				   ai_monpreant  // Monto Prestación Acumulada
		//				   ai_monantant  // Monto Anticipos Anteriores
		//				   ai_porant  // Porcentaje de Anticipo
		//				   ai_monant  // Monto de Anticipo
		//				   as_motant  // Motivo de Anticipo
		//				   as_obsant  // Observación de Anticipo
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla de Anticipos Prestaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_anticipoprestaciones ".
				"(codemp,codper,codant,estant,fecant,monpreant,monintant,monantant,monantint,porant,monant,monint,motant,obsant)VALUES".
				"('".$this->ls_codemp."','".$as_codper."','".$as_codant."','".$as_estant."','".$ad_fecant."',".$ai_monpreant.",".
				" ".$ai_monintant.",".$ai_monantant.",".$ai_monantint.",".$ai_porant.",".$ai_monant.",".$ai_monint.",'".$as_motant."','".$as_obsant."')";
       	$this->io_sql->begin_transaction();
	   	$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_insert_anticipos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó el Anticipos Prestaciones ".$as_codant." asociado al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Anticipos Prestaciones fue registrado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_insert_anticipos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_insert_anticipos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_codper,$as_codant,$as_estant,$ad_fecant,$ai_monpreant,$ai_monintant,$ai_monantant,$ai_monantint,$ai_porant,
						$ai_monant,$ai_monint,$as_motant,$as_obsant,$as_existe,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_snorh_d_fideicomiso)
		//	    Arguments: as_codper  // Código del Personal
		//				   as_codant  // Código del Anticipo
		//				   as_estant  // Estatus del Anticipo
		//				   ad_fecant  // Fecha del Anticipo
		//				   ai_monpreant  // Monto Prestación Acumulada
		//				   ai_monantant  // Monto Anticipos Anteriores
		//				   ai_porant  // Porcentaje de Anticipo
		//				   ai_monant  // Monto de Anticipo
		//				   as_motant  // Motivo de Anticipo
		//				   as_obsant  // Observación de Anticipo
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el grabar ó False si hubo error en el grabar
		//	  Description: Funcion que graba en la tabla de Anticipos Prestaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
		$ad_fecant=$this->io_funciones->uf_convertirdatetobd($ad_fecant);
		$ai_monpreant=str_replace(".","",$ai_monpreant);
		$ai_monpreant=str_replace(",",".",$ai_monpreant);				
		$ai_monintant=str_replace(".","",$ai_monintant);
		$ai_monintant=str_replace(",",".",$ai_monintant);				
		$ai_monantant=str_replace(".","",$ai_monantant);
		$ai_monantant=str_replace(",",".",$ai_monantant);				
		$ai_monantint=str_replace(".","",$ai_monantint);
		$ai_monantint=str_replace(",",".",$ai_monantint);				
		$ai_monant=str_replace(".","",$ai_monant);
		$ai_monant=str_replace(",",".",$ai_monant);				
		$ai_monint=str_replace(".","",$ai_monint);
		$ai_monint=str_replace(",",".",$ai_monint);	
		$ai_porant=str_replace(".","",$ai_porant);
		$ai_porant=str_replace(",",".",$ai_porant);					
		switch ($as_existe)
		{
			case "FALSE":
				if($this->uf_select_anticipos($as_codper,$as_codant)===false)
				{
						$lb_valido=$this->uf_insert_anticipos($as_codper,$as_codant,$as_estant,$ad_fecant,$ai_monpreant,$ai_monintant,$ai_monantant,
														      $ai_monantint,$ai_porant,$ai_monant,$ai_monint,$as_motant,$as_obsant,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El Anticipo de Prestación ya existe, no lo puede incluir.");
				}
				break;							
		}		
		return $lb_valido;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar($ad_fecdes,$ad_fechas,$as_codper,$as_tipooperacion,$ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar
		//		   Access: private
		//	    Arguments: ad_fecdes  // Fecha Desde
		//				   ad_fechas  // Fecha Hasta
		//				   as_codper  // Código de Personal
		//				   as_tipooperacion  // Tipo de Operación
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que busca los anticipos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ad_fecdes=$this->io_funciones->uf_convertirdatetobd($ad_fecdes);
		$ad_fechas=$this->io_funciones->uf_convertirdatetobd($ad_fechas);
		$ls_criterio=" AND sno_anticipoprestaciones.fecant >='".$ad_fecdes."'".
					 " AND sno_anticipoprestaciones.fecant <='".$ad_fechas."'";
		if ($as_codper!="")
		{
			$ls_criterio=$ls_criterio." AND sno_anticipoprestaciones.codper='".$as_codper."'";
		}
		switch($as_tipooperacion)
		{
			case "A":
				$ls_criterio=$ls_criterio." AND sno_anticipoprestaciones.estant='R'";
			break;
		
			case "R":
				$ls_criterio=$ls_criterio." AND sno_anticipoprestaciones.estant='A'";
			break;
		
			case "X":
				$ls_criterio=$ls_criterio." AND sno_anticipoprestaciones.estant='R'";
			break;
		}
		$ls_sql="SELECT sno_anticipoprestaciones.codper, sno_personal.nomper, sno_personal.apeper, sno_anticipoprestaciones.monant, ".
				"       sno_anticipoprestaciones.monint, sno_anticipoprestaciones.motant, sno_anticipoprestaciones.codant, ".
				"		sno_anticipoprestaciones.fecant ".
				"  FROM sno_anticipoprestaciones ".
				" INNER JOIN sno_personal ".
				"    ON sno_anticipoprestaciones.codemp='".$this->ls_codemp."'".
				$ls_criterio.
				"   AND sno_anticipoprestaciones.codemp = sno_personal.codemp ".
				"   AND sno_anticipoprestaciones.codper = sno_personal.codper ".
				" ORDER BY sno_anticipoprestaciones.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_buscar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			$ai_totrows=0;
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codant=$rs_data->fields["codant"];
				$ls_nomper=$rs_data->fields["apeper"].", ".$rs_data->fields["nomper"];
				$ld_fecant=$this->io_funciones->uf_convertirfecmostrar($rs_data->fields["fecant"]);
				$li_monant=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["monant"]+$rs_data->fields["monint"]);
				$ls_motant=$rs_data->fields["motant"];
				$ls_obsant=$rs_data->fields["obsant"];
				$ai_totrows++;
				$ao_object[$ai_totrows][1]="<div align='center'><input name=chkper".$ai_totrows." type=checkbox id=chkper".$ai_totrows." value=1 class=sin-borde></div>";
				$ao_object[$ai_totrows][2]="<div align='center'>".$ls_codant."<input name=codant".$ai_totrows." type='hidden' id=codant".$ai_totrows." value='".$ls_codant."'></div>";
				$ao_object[$ai_totrows][3]="<div align='center'>".$ls_codper."<input name=codper".$ai_totrows." type='hidden' id=codper".$ai_totrows." value='".$ls_codper."'></div>";
				$ao_object[$ai_totrows][4]="<div align='left'>".$ls_nomper."</div>";
				$ao_object[$ai_totrows][5]="<div align='center'>".$ld_fecant."</div>";
				$ao_object[$ai_totrows][6]="<div align='right'>".$li_monant."<input name=monant".$ai_totrows." type='hidden' id=monant".$ai_totrows." value='".$rs_data->fields["monant"]."'>".
										   "<input name=monint".$ai_totrows." type='hidden' id=monint".$ai_totrows." value='".$rs_data->fields["monint"]."'></div>";
				$ao_object[$ai_totrows][7]="<div align='left'>".$ls_motant."<input name=motant".$ai_totrows." type='hidden' id=motant".$ai_totrows." value='".$rs_data->fields["motant"]."'></div>";
				$rs_data->MoveNext();
			}
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_existe']=$lb_existe;
		return $arrResultado;						
	}// end function uf_buscar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cambiar_estatus($as_codant,$as_codper,$as_estact,$as_estant,$ai_monant,$ai_monint,$ai_calintpreant,$as_motant,$as_tipdocant,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cambiar_estatus
		//		   Access: private
		//	    Arguments: as_codant  // Código de Anticipo
		//				   as_codper  // Código de Personal
		//				   as_estact  // Estatus Actual
		//				   as_estant  // Estatus Anterior
		//				   ai_monant  // Monto del anticipo prestacion
		//				   ai_monint  // Monto del anticipo intereses
		//				   ai_calintpreant  // Estatus si se calculan los intereses
		//				   as_motant  // motivo del anticipo
		//				   as_tipdocant  // tipo de documento
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si proceso ó False si no proceso
		//	  Description: Funcion que le cambia el estatus a los anticipos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_anticipoprestaciones ".
				"   SET estant = '".$as_estact."'".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND codant = '".$as_codant."'".
				"   AND codper = '".$as_codper."'".
				"   AND estant = '".$as_estant."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_cambiar_estatus ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			switch($as_estact.$ai_calintpreant)
			{
				case "A1": // Aprobado
				// Comienzo de lo agregado y modificado por Ofimatica de Venezuela el 06-06-2011, para permitir la aprobacion de los anticipos tanto para las persona que se les abona el fideicomiso en el banco como aquellas que se le abono en la contabilidad de loa empresa.
				$arrResultado= $this->uf_verificar_calculo_int_individual($as_codper, $as_calintfid);
				$as_calintfid=$arrResultado['as_calintfid'];
				$lb_valido=$arrResultado['lb_valido'];
				if ($lb_valido && $as_calintfid=='1')
				{    
  				     $lb_valido= $this->uf_generar_contabilizacion($as_codant,$as_codper,$ai_monant,$ai_monint,$as_motant,$as_tipdocant,
															  $ai_calintpreant,$aa_seguridad);
				}
				break;

				case "R1": // Reverso de aprobación
				$arrResultado= $this->uf_verificar_calculo_int_individual($as_codper, $as_calintfid);
				$as_calintfid=$arrResultado['as_calintfid'];
				$lb_valido=$arrResultado['lb_valido'];
				if ($lb_valido && $as_calintfid=='1')
				{
					$lb_valido= $this->uf_delete_contabilizacion($as_codant,$as_codper,$aa_seguridad);
				}
				// fin de lo agregado y modificado el 06-06-2011
				break;
			}
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="UPDATE";
				$ls_descripcion ="Actualizo el Estatus del Anticipo Prestaciones ".$as_codant." asociado al personal ".$as_codper." de ".$as_estant." a ".$as_estact;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			}
		}
		return $lb_valido;
	}// end function uf_cambiar_estatus
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_eliminar($as_codper,$as_codant,$as_estant,$ad_fecant,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cambiar_estatus
		//		   Access: private
		//	    Arguments: as_codant  // Código de Anticipo
		//				   as_codper  // Código de Personal
		//				   as_estact  // Estatus Actual
		//				   as_estant  // Estatus Anterior
		//				   ai_monant  // Monto del anticipo prestacion
		//				   ai_monint  // Monto del anticipo intereses
		//				   ai_calintpreant  // Estatus si se calculan los intereses
		//				   as_motant  // motivo del anticipo
		//				   as_tipdocant  // tipo de documento
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si proceso ó False si no proceso
		//	  Description: Funcion que le cambia el estatus a los anticipos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/11/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ad_feceli=$this->io_funciones->uf_convertirdatetobd($ad_fecant);
		$lb_valido=true;
		$ls_sql="DELETE FROM sno_anticipoprestaciones ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND codant = '".$as_codant."'".
				"   AND codper = '".$as_codper."'".
				"   AND estant = '".$as_estant."'".
				"   AND fecant = '".$ad_feceli."'";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_eliminar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="DELETE";
				$ls_descripcion ="Elimino el Anticipo Prestaciones ".$as_codant." asociado al personal ".$as_codper;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			}
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Anticipos Prestaciones fue Eliminado.");
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	}// end function uf_cambiar_estatus
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generar_contabilizacion($as_codant,$as_codper,$ai_monant,$ai_monint,$as_motant,$as_tipdocant,$ai_calintpreant,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generar_contabilizacion  
		//	    Arguments: as_codant  // Código de Anticipo
		//				   as_codper  // Código de Personal
		//				   ai_monant  // Monto del anticipo prestacion
		//				   ai_monint  // Monto del anticipo intereses
		//				   as_motant  // motivo del anticipo
		//				   as_tipdocant  // tipo de documento
		//				   ai_calintpreant  // Estatus si se calculan los intereses
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la informacion para la recepcion de documento 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/11/2010 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT rpc_beneficiario.ced_bene, rpc_beneficiario.sc_cuenta, sno_fideicomiso.scg_cuentafid, ".
				"       sno_fideicomiso.scg_cuentaintfid, sno_nomina.codnom, sno_nomina.peractnom  ".
				"  FROM sno_personal ".
				" INNER JOIN sno_fideicomiso  ".
				"    ON sno_personal.codemp='".$this->ls_codemp."' ". 
				"   AND sno_personal.codper='".$as_codper."' ".
				"   AND sno_personal.codemp=sno_fideicomiso.codemp ".
				"   AND sno_personal.codper=sno_fideicomiso.codper ".
				" INNER JOIN rpc_beneficiario  ".
				"    ON sno_personal.codemp='".$this->ls_codemp."' ". 
				"   AND sno_personal.codper='".$as_codper."' ".
				"   AND sno_personal.codemp=rpc_beneficiario.codemp ".
				"   AND sno_personal.cedper=rpc_beneficiario.ced_bene ".
				" INNER JOIN (sno_personalnomina ".
				"       INNER JOIN sno_nomina ".
				"          ON sno_personalnomina.codemp='".$this->ls_codemp."' ". 
				"         AND sno_personalnomina.codper='".$as_codper."' ".
				"         AND (sno_personalnomina.staper='1' OR sno_personalnomina.staper='2') ".
				"         AND sno_nomina.espnom='0' ".
				"         AND sno_personalnomina.codemp=sno_nomina.codemp ".
				"         AND sno_personalnomina.codnom=sno_nomina.codnom) ".
				"    ON sno_personal.codemp='".$this->ls_codemp."' ". 
				"   AND sno_personal.codper='".$as_codper."' ".
				"   AND sno_personal.codemp=sno_personalnomina.codemp ".
				"   AND sno_personal.codper=sno_personalnomina.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_generar_contabilizacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{		 
				$li_estatus=0; // No contabilizado
				$ls_codnom=$rs_data->fields["codnom"];
				$ls_peractnom=$rs_data->fields["peractnom"];
				$ls_ced_bene=$rs_data->fields["ced_bene"];
				$ls_sc_cuenta=trim($rs_data->fields["sc_cuenta"]);
				$ls_scg_cuentafid=trim($rs_data->fields["scg_cuentafid"]);
				$ls_scg_cuentaintfid=trim($rs_data->fields["scg_cuentaintfid"]);
				if(($ls_sc_cuenta=="")||($ls_scg_cuentafid=="")||(($ls_scg_cuentaintfid=="")&&($ai_calintpreant=="1")))
				{
					$lb_valido=false;
					$this->io_mensajes->message("Debe verificar las cuentas Contables para la Prestación Antiguedad, para los Intereses de Prestación antiguedad y para el beneficiario"); 
				}
				else
				{
					if($ls_scg_cuentafid==$ls_scg_cuentaintfid)
					{
						$ai_monant=$ai_monant+$ai_monint;
						$ai_monint=0;
					}
					$ls_codcom=$as_codper.$as_codant."-X";
					$ls_tipnom="X";
					$ls_codpro="----------";
					$ls_descripcion="ANTICIPO DE PRESTACION ANTIGUEDAD, PARA EL PERSONAL ".$as_codper.". MOTIVO ".$as_motant;
					$li_genrecdoc="1";
					if ($ai_monant>0)
					{
						$ls_sql="INSERT INTO sno_dt_scg(codemp,codnom,codperi,codcom,tipnom,sc_cuenta,debhab,codconc,cod_pro,ced_bene,tipo_destino,".
								"descripcion,monto,estatus,estrd,codtipdoc,estnumvou,estnotdeb,codcomapo) VALUES ('".$this->ls_codemp."','".$ls_codnom."',".
								"'".$ls_peractnom."','".$ls_codcom."','".$ls_tipnom."','".$ls_scg_cuentafid."','D','0000000000',".
								"'".$ls_codpro."','".$ls_ced_bene."','B','".$ls_descripcion."',".number_format($ai_monant,2,".","").",".$li_estatus.",".
								"'".$li_genrecdoc."','".$as_tipdocant."','0','0','000000000000000')";
						$li_row=$this->io_sql->execute($ls_sql);
						if($li_row===false)
						{
							$lb_valido=false;
							$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_insert_contabilizacion_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
						}
					}
					if (($ai_monint>0)&&($lb_valido))
					{
						$ls_sql="INSERT INTO sno_dt_scg(codemp,codnom,codperi,codcom,tipnom,sc_cuenta,debhab,codconc,cod_pro,ced_bene,tipo_destino,".
								"descripcion,monto,estatus,estrd,codtipdoc,estnumvou,estnotdeb,codcomapo) VALUES ('".$this->ls_codemp."','".$ls_codnom."',".
								"'".$ls_peractnom."','".$ls_codcom."','".$ls_tipnom."','".$ls_scg_cuentaintfid."','D','0000000000',".
								"'".$ls_codpro."','".$ls_ced_bene."','B','".$ls_descripcion."',".number_format($ai_monint,2,".","").",".$li_estatus.",".
								"'".$li_genrecdoc."','".$as_tipdocant."','0','0','000000000000000')";
						$li_row=$this->io_sql->execute($ls_sql);
						if($li_row===false)
						{
							$lb_valido=false;
							$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_insert_contabilizacion_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
						}
					}
					if ($lb_valido)
					{
						$ls_sql="INSERT INTO sno_dt_scg(codemp,codnom,codperi,codcom,tipnom,sc_cuenta,debhab,codconc,cod_pro,ced_bene,tipo_destino,".
								"descripcion,monto,estatus,estrd,codtipdoc,estnumvou,estnotdeb,codcomapo) VALUES ('".$this->ls_codemp."','".$ls_codnom."',".
								"'".$ls_peractnom."','".$ls_codcom."','".$ls_tipnom."','".$ls_sc_cuenta."','H','0000000000',".
								"'".$ls_codpro."','".$ls_ced_bene."','B','".$ls_descripcion."',".number_format($ai_monant+$ai_monint,2,".","").",".$li_estatus.",".
								"'".$li_genrecdoc."','".$as_tipdocant."','0','0','000000000000000')";
						$li_row=$this->io_sql->execute($ls_sql);
						if($li_row===false)
						{
							$lb_valido=false;
							$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_insert_contabilizacion_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
						}
					}
				}
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("Debe verificar Si la persona es un beneficiario, Se encuentra en una nómina Normal ó si esta definida su configuración de fideicomiso."); 
			}
		}	
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Inserto la Información para la Recepcion de documento del Anticipo ".$as_codant." asociado al personal ".$as_codper." Comprobante ".$ls_codcom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_generar_contabilizacion
	//------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_contabilizacion($as_codant,$as_codper,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_contabilizacion  
		//	    Arguments: as_codant  // Código de Anticipo
		//				   as_codper  // Código de Personal
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de eliminar la informacion para la recepcion de documento 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/11/2010 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_codcom=$as_codper.$as_codant."-X";
		$ls_tipnom="X";
		$ls_sql="DELETE ".
				"  FROM sno_dt_scg ".
				" WHERE codemp='".$this->ls_codemp."' ". 
				"   AND codcom='".$ls_codcom."' ".
				"   AND tipnom='".$ls_tipnom."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_delete_contabilizacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Elimino la Información para la Recepcion de documento del Anticipo ".$as_codant." asociado al personal ".$as_codper." Comprobante ".$ls_codcom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_delete_contabilizacion
	//------------------------------------------------------------------------------------------------------------------------------------
	
	function uf_verificar_calculo_int_individual($as_codper,$as_calintfid)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_calculo_int_individual
		//		   Access: private
		//   	Arguments: as_codper  // Código del Personal
		//                 as_calintfid // valor de retorno que indica si el personal se le abona en la contabilidad de la empresa o no
		//	      Returns: lb_valido True si todo esta bien ó False si no
		//	  Description: Funcion que verifica si la persona se le abona en la contabilidad o no el fideicomiso.
		//	   Creado Por: Ofimatica de Venezuela, C.A.
		// Fecha Creación: 06/06/2011								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT calintfid ".
				"  FROM sno_fideicomiso ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Anticipos Prestaciones MÉTODO->uf_verificar_calculo_int_individual ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$as_calintfid=$rs_data->fields["calintfid"];
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_calintfid']=$as_calintfid;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_verificar_calculo_int_individual
    //------------------------------------------------------------------------------------------------------------------------------------
}
?>