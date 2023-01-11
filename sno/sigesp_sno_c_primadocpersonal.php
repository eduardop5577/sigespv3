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

class sigesp_sno_c_primadocpersonal
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_personal;
	var $ls_codemp;

	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_c_primadocpersonal
		//		   Access: public (sigesp_snorh_d_proyecto)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/07/2007 								Fecha Última Modificación : 
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
		$this->io_seguridad=new sigesp_c_seguridad();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->ls_codnom="0000";
		if(array_key_exists("la_nomina",$_SESSION))
		{
			if(array_key_exists("codnom",$_SESSION["la_nomina"]))
			{
				$this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
			}
		}
	}// end function sigesp_sno_c_primadocpersonal
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_proyecto)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/07/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_personalprima($as_codper,$ai_totrows,$aa_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_personalprima
		//		   Access: public (sigesp_sno_d_personaproyecto.php)
		//	    Arguments: as_codper  // Código de personal
		//				   ai_totdiaper  // Total de Días del periodo
		//				   ai_porcentaje  // Total de Porcentaje 
		//				   ai_totrows  // Total de Filas
		//				   aa_object  //  Arreglo de objectos que se van a imprimir
		//	      Returns: $lb_valido True si se ejecuto el select ó False si hubo error en el select
		//	  Description: Función que obtiene los proyectos asociados a la persona
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/07/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_totdiaper=0;
		$ai_porcentaje=0;
		$ls_sql="SELECT sno_primadocentepersonal.codper, sno_primadocentepersonal.codnom, sno_primadocentepersonal.codpridoc, sno_primasdocentes.tippridoc, sno_primasdocentes.despridoc".
				"  FROM sno_primadocentepersonal, sno_primasdocentes".
				" WHERE sno_primadocentepersonal.codemp='".$this->ls_codemp."' ".
				"   AND sno_primadocentepersonal.codnom='".$this->ls_codnom."' ".
				"   AND sno_primadocentepersonal.codper='".$as_codper."' ".
				"   AND sno_primadocentepersonal.codemp = sno_primasdocentes.codemp ".
				"   AND sno_primadocentepersonal.codpridoc = sno_primasdocentes.codpridoc ".
				" ORDER BY codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Proyecto MÉTODO->uf_load_personalprima ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$ai_totrows=0;
			while(!$rs_data->EOF)
			{
				$ai_totrows++;
				$ls_codpridoc=$rs_data->fields["codpridoc"];  
				$li_tippridoc=$rs_data->fields["tippridoc"];
				$ls_despridoc=$rs_data->fields["despridoc"];				
				switch ($li_tippridoc)
				{
					case "0":
						$ls_tippridoc='Jerarquia';
					break;
					case "1":
						$ls_tippridoc='Antiguedad';
					break;
					case "2":
						$ls_tippridoc='Hogar e Hijos';
					break;
				}
				$aa_object[$ai_totrows][1]="<div align='center'><a href=javascript:ue_eliminarproyecto('$ls_codpridoc');><img src='../shared/imagebank/tools20/eliminar.gif' alt='Eliminar' width='15' height='15' border='0'></a></div>";
				$aa_object[$ai_totrows][2]="<input type=text   name=txtcodpridoc".$ai_totrows."   id=txtcodpridoc".$ai_totrows."   value='".$ls_codpridoc."'   size=12 class=sin-borde readonly><input type=hidden   name=hidtippridoc".$ai_totrows."   id=hidtippridoc".$ai_totrows."   value='".$li_tippridoc."'>";
				$aa_object[$ai_totrows][3]="<input type=text   name=txtdespridoc".$ai_totrows."   id=txtdespridoc".$ai_totrows."   value='".$ls_despridoc."'   size=50 class=sin-borde readonly >";
				$aa_object[$ai_totrows][4]="<input type=text   name=txttippridoc".$ai_totrows."   id=txttippridoc".$ai_totrows."   value='".$ls_tippridoc."'   size=50 class=sin-borde readonly >";
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['aa_object']=$aa_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_personalprima	
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_personalprima($as_codper,$as_codpridoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_personalproyecto
		//		   Access: private
 		//	    Arguments: as_codproy  // código del proyecto
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si la persona tiene el proyecto asociado existe
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/07/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql= "SELECT codemp ".
				 "  FROM sno_primadocentepersonal ".
				 " WHERE codemp='".$this->ls_codemp."' ".
				 "   AND codnom='".$this->ls_codnom."' ".
				 "   AND codper='".$as_codper."' ".
				 "   AND codpridoc='".$as_codpridoc."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Proyecto MÉTODO->uf_select_personalprima ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_select_personalproyecto
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_personalprima($as_codper,$as_codpridoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_personalprima
		//		   Access: private
		//	    Arguments: as_codper  // código del Personal
		//				   as_codproy  // Código del proyecto
		//				   ai_totdiaper  // total de días Habiles del periodo
		//				   ai_totdiames  // total de días hábiles de las personas en el proyecto
		//				   ai_pordiames  // procentaje del total de días hábiles del proyecto con respecto a los días habiles
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla sno_proyectopersonal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/07/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_primadocentepersonal (codemp,codper,codnom,codpridoc) VALUES ".
				"('".$this->ls_codemp."','".$as_codper."','".$this->ls_codnom."','".$as_codpridoc."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Proyecto MÉTODO->uf_insert_personalprima ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó la Prima Personal ".$as_codpridoc." - ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_insert_personalprima
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_personalprima($as_codper,$as_codpridoc,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//    	 Function: uf_update_personalprima
		//		   Access: private
		//	    Arguments: as_codper  // código del Personal
		//				   as_codproy  // Código del proyecto
		//				   ai_totdiaper  // total de días Habiles del periodo
		//				   ai_totdiames  // total de días hábiles de las personas en el proyecto
		//				   ai_pordiames  // procentaje del total de días hábiles del proyecto con respecto a los días habiles
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el update ó False si hubo error en el update
		//	  Description: Funcion que actualiza en la tabla sno_proyectopersonal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/07/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql= "UPDATE sno_primadocentepersonal ".
				 "   SET codpridoc=".$as_codpridoc.", ".
				 " WHERE codemp='".$this->ls_codemp."' ".
				 "   AND codnom='".$this->ls_codnom."' ".
				 "   AND codper='".$as_codper."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Proyecto MÉTODO->uf_update_personalprima ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			////////////////////////////////         SEGURIDAD               //////////////////////////////
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó la Prima Personal ".$as_codpridoc." - ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_update_personalprima
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar_personalprima($as_codper,$as_codpridoc,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar_personalprima
		//		   Access: public (sigesp_sno_d_personaproyecto)
		//	    Arguments: as_codper  // código del Personal
		//				   as_codproy  // Código del proyecto
		//				   ai_totdiaper  // total de días Habiles del periodo
		//				   ai_totdiames  // total de días hábiles de las personas en el proyecto
		//				   ai_pordiames  // procentaje del total de días hábiles del proyecto con respecto a los días habiles
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Funcion que guarda en la tabla sno_proyectopersonal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/07/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;	
		$lb_valido=$this->uf_insert_personalprima($as_codper,$as_codpridoc,$aa_seguridad);
		return $lb_valido;
	}// end function uf_guardar_personalprima
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_personalprima($as_codper,$as_codpridoc,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar_personalprima
		//		   Access: public (sigesp_sno_d_personaproyecto)
		//	    Arguments: as_codper  // código del Personal
		//				   as_codproy  // Código del proyecto
		//				   ai_totdiaper  // total de días Habiles del periodo
		//				   ai_totdiames  // total de días hábiles de las personas en el proyecto
		//				   ai_pordiames  // procentaje del total de días hábiles del proyecto con respecto a los días habiles
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Funcion que guarda en la tabla sno_proyectopersonal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/07/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if(!$this->uf_select_personalprima($as_codper,$as_codpridoc))
		{
			$ls_sql="DELETE FROM sno_primadocentepersonal ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ".
					"   AND codper='".$as_codper."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Proyecto MÉTODO->uf_delete_personalprima ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{ 
				////////////////////////////////         SEGURIDAD               //////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Elimino la prima  ".$as_codpridoc." - ".$as_codper;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
										$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
										$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			}
		}
		return $lb_valido;
	}// end function uf_guardar_personalprima
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>