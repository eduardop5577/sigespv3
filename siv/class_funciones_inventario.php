<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class class_funciones_inventario
{
    
	public function __construct()
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  class_funciones_nomina
		//	Description:  Constructor de la Clase
		//////////////////////////////////////////////////////////////////////////////
		
	}
	
   //--------------------------------------------------------------
   function uf_obteneroperacion()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_obteneroperacion
		//	Returns:	$operacion valor de la variable
		//	Description: Funci?n que obtiene que tipo de operaci?n se va a ejecutar
		//////////////////////////////////////////////////////////////////////////////
		
		if(array_key_exists("operacion",$_POST))
		{
			$operacion=$_POST["operacion"];
		}
		else
		{
			$operacion="NUEVO";
		}
   		return $operacion; 
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_obtenerexiste()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_obtenerexiste
		//	Returns:	$existe valor de la variable
		//	Description: Funci?n que obtiene si existe el registro ? no
		//////////////////////////////////////////////////////////////////////////////
		if(array_key_exists("existe",$_POST))
		{
			$existe=$_POST["existe"];
		}
		else
		{
			$existe="FALSE";
		}
   		return $existe; 
   }
   //--------------------------------------------------------------
	
   //--------------------------------------------------------------
   function uf_seleccionarcombo($as_valores,$as_seleccionado,$aa_parametro,$li_total)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_seleccionarcombo
		//	Arguments:    as_valores  // valores que contiene el combo
		//				  as_seleccionado  // Valor que se debe seleccionar
		//				  aa_parametro  // arreglo de valores
		//				  li_total  // Valor toatl de valores
		//	Description: Funci?n que seleciona un valor de un combo
		//////////////////////////////////////////////////////////////////////////////
   		$la_valores = explode("-",$as_valores);
		for($li_index=0;$li_index<$li_total;++$li_index)
		{
			if($la_valores[$li_index]==$as_seleccionado)
			{
				$aa_parametro[$li_index]=" selected";
			}
		}
		return $aa_parametro;
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_obtenervalor($as_valor, $as_valordefecto)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_obtenervalor
		//	Arguments:    as_valor  // Variable que deseamos obtener
		//				  as_valordefecto  // Valor por defecto de la variable
		//	Returns:	  $valor contenido de la variable
		//	Description: Funci?n que obtiene el valor de una variable que viene de un submit
		//////////////////////////////////////////////////////////////////////////////
		if(array_key_exists($as_valor,$_POST))
		{
			$valor=$_POST[$as_valor];
		}
		else
		{
			$valor=$as_valordefecto;
		}
   		return $valor; 
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_obtenervariable($as_variable, $as_caso1, $as_caso2, $as_valor1, $as_valor2, $as_defecto)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_obtenervariable
		//	Arguments: as_variable  // Variable que deseamos obtener
		//			   as_caso1  // condici?n 1
		//			   as_caso2  // condici?n 2
		//			   as_valor1  // Valor si se cumple la condici?n 1
		//			   as_valor2  // Valor si se cumple la condici?n 2
		//	  		   as_defecto  // Valor por defecto de la variable
		//	Returns:	 $valor contenido de la variable
		//	Description: Funci?n que dependiendo del caso trae un valor u otro
		//////////////////////////////////////////////////////////////////////////////
		switch($as_variable)
		{
			case $as_caso1:
				$valor = $as_valor1;
				break;
					
			case $as_caso2:
				$valor = $as_valor2;
				break;					
			
			default:
				$valor = $as_defecto;
				break;
		}
   		return $valor; 
   }
   //--------------------------------------------------------------

  //-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_formatonumerico($as_valor)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_formatonumerico
		//	Arguments:    as_valor  // valor sin formato num?rico
		//	Returns:	  $as_valor valor num?rico formateado
		//	Description:  Funci?n que le da formato a los valores num?ricos que vienen de la BD
		//////////////////////////////////////////////////////////////////////////////
		$as_valor=str_replace(".",",",$as_valor);
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
		return $as_valor;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

  //-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_formatocalculo($as_valor)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_formatonumerico
		//	Arguments:    as_valor  // valor sin formato num?rico
		//	Returns:	  $as_valor valor num?rico formateado
		//	Description:  Funci?n que le da formato a los valores num?ricos que vienen de la BD
		//////////////////////////////////////////////////////////////////////////////
		$as_valor=str_replace(".","",$as_valor);
		$as_valor=str_replace(",",".",$as_valor);
		return $as_valor;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_obtenertipo()
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_obtenertipo
		//	Description: Funci?n que obtiene que tipo de llamada del catalogo
		//////////////////////////////////////////////////////////////////////////////
		if(array_key_exists("tipo",$_GET))
		{
			$tipo=$_GET["tipo"];
		}
		else
		{
			$tipo="";
		}
   		return $tipo; 
   	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_obtenervalor_get($as_variable,$as_valordefecto)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  uf_obtenertipo
		//	Description: Funci?n que obtiene que tipo de llamada del catalogo
		//////////////////////////////////////////////////////////////////////////////
		if(array_key_exists($as_variable,$_GET))
		{
			$valor=$_GET[$as_variable];
		}
		else
		{
			$valor=$as_valordefecto;
		}
   		return $valor; 
   	}
	//-----------------------------------------------------------------------------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_asignarvalor($as_valor, $as_valordefecto)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function: uf_asignarvalor
		//	Arguments:    as_valor  // Variable que deseamos obtener
		//				  as_valordefecto  // Valor por defecto de la variable
		//	Returns:	  $valor contenido de la variable
		//	Description: Funci?n que obtiene el valor de una variable que viene de un submit
		//////////////////////////////////////////////////////////////////////////////
		if(array_key_exists($as_valor,$_POST))
		{
			$valor=$_POST[$as_valor];
		}
		else
		{
			$valor=$as_valordefecto;
		}
		
		if ($valor=="")
		{
			$valor=$as_valordefecto;
		}
   		return $valor; 
   }
   //--------------------------------------------------------------
   
   function uf_load_seguridad($as_sistema,$as_ventanas,$as_permisos,$aa_seguridad,$aa_permisos)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_seguridad
		//		   Access: public (en todas las clases que usen seguridad)
		//	    Arguments: as_sistema // Sistema del que se desea verificar la seguridad
		//				   as_ventanas // Ventana del que se desea verificar la seguridad
		//				   as_permisos  // persimo si puede entrar ? no a la p?gina
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//				   aa_permisos  // arreglo de permisos (incluir, modificar, eliminar, etc )
		//	  Description: Funci?n que obtiene el valor de una variable que viene de un submit
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$io_seguridad= new sigesp_c_seguridad();
		$ls_empresa=$_SESSION["la_empresa"]["codemp"];
		$ls_logusr=$_SESSION["la_logusr"];
		$aa_seguridad["empresa"]=$ls_empresa;
		$aa_seguridad["logusr"]=$ls_logusr;
		$aa_seguridad["sistema"]=$as_sistema;
		$aa_seguridad["ventanas"]=$as_ventanas;
		$as_permisos="";
		$aa_permisos["leer"]="";
		$aa_permisos["incluir"]="";
		$aa_permisos["cambiar"]="";
		$aa_permisos["eliminar"]="";
		$aa_permisos["imprimir"]="";
		$aa_permisos["anular"]="";
		$aa_permisos["ejecutar"]="";
		if (array_key_exists("permisos",$_POST)||($ls_logusr=="PSEGIS"))
		{	
			if($ls_logusr=="PSEGIS")
			{
				$as_permisos="1";
				$aa_permisos=$io_seguridad->uf_sss_load_permisossigesp();
			}
			else
			{
				$as_permisos=$_POST["permisos"];
				$aa_permisos["leer"]=$_POST["leer"];
				$aa_permisos["incluir"]=$_POST["incluir"];
				$aa_permisos["cambiar"]=$_POST["cambiar"];
				$aa_permisos["eliminar"]=$_POST["eliminar"];
				$aa_permisos["imprimir"]=$_POST["imprimir"];
				$aa_permisos["anular"]=$_POST["anular"];
				$aa_permisos["ejecutar"]=$_POST["ejecutar"];
			}
		}
		else
		{
			$arrResultado=$io_seguridad->uf_sss_load_permisos($ls_empresa,$ls_logusr,$as_sistema,$as_ventanas,$aa_permisos);
			$aa_permisos = $arrResultado['aa_permisos'];
			$as_permisos= $arrResultado['lb_valido'];
		}
		unset($io_seguridad);
		$arrResultado['as_permisos']=$as_permisos;
		$arrResultado['aa_seguridad']=$aa_seguridad;
		$arrResultado['aa_permisos']=$aa_permisos;
		return $arrResultado;		
   }// end function uf_load_seguridad
   
   //----------------------------------------------------------------------------------------------------------------------------
   function uf_print_permisos($as_permisos,$aa_permisos,$as_logusr,$as_accion)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_permisos
		//		   Access: public
		//	    Arguments: as_permisos  // permisos que tiene el usuario en la p?gina
		//				   aa_permisos  // arreglo de permisos (incluir, modificar, eliminar, etc )
		//				   as_logusr  // login de usuario
		//				   as_accion  // acci?n que va a ejecutar si no tiene permiso el usuario
		//	  Description: Funci?n que imprime el permiso de seguridad en las p?ginas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		if (($as_permisos)||($as_logusr=="PSEGIS"))
		{
			print("<input type=hidden name=permisos id=permisos value='$as_permisos'>");
			print("<input type=hidden name=leer id=leer value='$aa_permisos[leer]'>");
			print("<input type=hidden name=incluir id=incluir value='$aa_permisos[incluir]'>");
			print("<input type=hidden name=cambiar id=cambiar value='$aa_permisos[cambiar]'>");
			print("<input type=hidden name=eliminar id=eliminar value='$aa_permisos[eliminar]'>");
			print("<input type=hidden name=imprimir id=imprimir value='$aa_permisos[imprimir]'>");
			print("<input type=hidden name=anular id=anular value='$aa_permisos[anular]'>");
			print("<input type=hidden name=ejecutar id=ejecutar value='$aa_permisos[ejecutar]'>");
		}
		else
		{
			print("<script language=JavaScript>");
			print("".$as_accion."");
			print("</script>");
		}
   }// end function uf_print_permisos
   
   //----------------------------------------------------------------------------------------------------------------------------
   function uf_load_seguridad_reporte($as_sistema,$as_ventanas,$as_descripcion)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_seguridad_reporte
		//		   Access: public (en todas las clases que usen seguridad)
		//	    Arguments: as_sistema // Sistema del que se desea verificar la seguridad
		//				   as_ventanas // Ventana del que se desea verificar la seguridad
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	  Description: Funci?n que obtiene el valor de una variable que viene de un submit
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 27/04/2006 								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		require_once("../../shared/class_folder/sigesp_c_seguridad.php");
		$io_seguridad= new sigesp_c_seguridad();
		$ls_empresa=$_SESSION["la_empresa"]["codemp"];
		$ls_logusr=$_SESSION["la_logusr"];
		$la_seguridad["empresa"]=$ls_empresa;
		$la_seguridad["logusr"]=$ls_logusr;
		$la_seguridad["sistema"]=$as_sistema;
		$la_seguridad["ventanas"]=$as_ventanas;
		$ls_evento="REPORT";
		$lb_valido= $io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
								$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
								$la_seguridad["ventanas"],$as_descripcion);
		unset($io_seguridad);
		return $lb_valido;
   }// end function uf_load_seguridad
	
  function uf_chkciescg()
	{
	  require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	 require_once("../base/librerias/php/general/sigesp_lib_include.php");
	 require_once("../shared/class_folder/sigesp_c_seguridad.php");
	 require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	 require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	 $io_msg=new class_mensajes();
	 $in=new sigesp_include();
	 $con=$in->uf_conectar();
	 $io_sql=new class_sql($con);
	 $io_funcion = new class_funciones();
	 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
	 $li_estciescg ="";
	 $lb_valido=false;
	 $ls_sql = "SELECT estciescg FROM sigesp_empresa  ".
			   "   WHERE codemp='".$ls_codemp."' " ; 	   
	 $rs_data=$io_sql->select($ls_sql);
	 if($rs_data===false)
	 {
	  $io_msg->message("CLASE->activo M?TODO->uf_chkciescg ERROR->".$io_funcion->uf_convertirmsg($io_sql->message));
	 }
	 else
	 {
	   while(!$rs_data->EOF)
	   {
	    $li_estciescg = $rs_data->fields["estciescg"];
		$rs_data->MoveNext();
	   }
	   
	   if($li_estciescg == 1)
	   {
	    $ls_valido = true;
		$io_msg->message("Hay un cierre de Contabilidad, no se puede generar movimientos!!!");
		print "<script language=JavaScript>";
        print "location.href='sigespwindow_blank.php'";
        print "</script>";
	   }
       $io_sql->free_result($rs_data);
	 }

	return $lb_valido;
	}
	
   function uf_chkciespg()
	{
	 require_once("../base/librerias/php/general/sigesp_lib_sql.php");
	 require_once("../base/librerias/php/general/sigesp_lib_include.php");
	 require_once("../shared/class_folder/sigesp_c_seguridad.php");
	 require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
	 require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
	 $io_msg=new class_mensajes();
	 $in=new sigesp_include();
	 $con=$in->uf_conectar();
	 $io_sql=new class_sql($con);
	 $io_funcion = new class_funciones();
	 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
	 $lb_valido=false;
	 $ls_sql = "SELECT estciespg FROM sigesp_empresa  ".
			   "   WHERE codemp='".$ls_codemp."' " ;		   
	 $rs_data=$io_sql->select($ls_sql);
	 if($rs_data===false)
	 {
	  $io_msg->message("CLASE->activo M?TODO->uf_chkciespg ERROR->".$io_funcion->uf_convertirmsg($io_sql->message));
	 }
	 else
	 {
	   while(!$rs_data->EOF)
	   {
	    $li_estciespg = $rs_data->fields["estciespg"];
		$rs_data->MoveNext();
	   }
	   
	   if($li_estciespg == 1)
	   {
	    $ls_valido = true;
		$io_msg->message("Hay un cierre Presupuestario, no se puede generar movimientos!!!");
		print "<script language=JavaScript>";
        print "location.href='sigespwindow_blank.php'";
        print "</script>";
	   }
       $io_sql->free_result($rs_data);
	 }
	return $lb_valido;
	}
  //-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci?n a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci?n que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		$ls_valor="";
		$ls_sql="SELECT value ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' "; 
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SNO M?TODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
		//				   as_seccion  // Secci?n a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funci?n que inserta la variable de configuraci?n
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		 require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		 $this->io_mensajes=new class_mensajes();
	 	require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones = new class_funciones();
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
			$this->io_mensajes->message("CLASE->SNO M?TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
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
				$this->io_mensajes->message("CLASE->SNO M?TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	function uf_select_cierre_presupuestario()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci?n a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci?n que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
   		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();				
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql="SELECT estciespg ".
				"  FROM sigesp_empresa ".
				" WHERE codemp='".$this->ls_codemp."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->SNO M?TODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_estciespg=$row["estciespg"];
				if($ls_estciespg==0)
				{
					$lb_valido=true;
				}
				else
				{
					$this->io_mensajes->message("Se ha procesado el cierre presupuestario del sistema");
				}
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $lb_valido;
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	
	//-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_loadmodalidad($ai_len1,$ai_len2,$ai_len3,$ai_len4,$ai_len5,$as_titulo)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_loadmodalidad
		//		   Access: public
		//	  Description: Funci?n que obtiene que tipo de modalidad y da las longitudes por accion
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 19/04/2007 								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		$ai_len1=$_SESSION["la_empresa"]["loncodestpro1"];
		$ai_len2=$_SESSION["la_empresa"]["loncodestpro2"];
		$ai_len3=$_SESSION["la_empresa"]["loncodestpro3"];
		$ai_len4=$_SESSION["la_empresa"]["loncodestpro4"];
		$ai_len5=$_SESSION["la_empresa"]["loncodestpro5"];
		switch($ls_modalidad)
		{
			case "1": // Modalidad por Proyecto
				$as_titulo="Estructura Presupuestaria";
				break;
				
			case "2": // Modalidad por Programatica
				$as_titulo="Estructura Programatica";
				break;
		}
		$arrResultado['ai_len1']=$ai_len1;
		$arrResultado['ai_len2']=$ai_len2;
		$arrResultado['ai_len3']=$ai_len3;
		$arrResultado['ai_len4']=$ai_len4;
		$arrResultado['ai_len5']=$ai_len5;
		$arrResultado['as_titulo']=$as_titulo;
		return $arrResultado;		
   	}// end function uf_loadmodalidad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_formatoprogramatica($as_codpro,$as_programatica)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_formatoprogramatica
		//		   Access: public
		//	  Description: Funci?n que obtiene que de acuerdo a la modalidad imprime la programatica
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 19/04/2007 								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
		$li_len1=0;
		$li_len2=0;
		$li_len3=0;
		$li_len4=0;
		$li_len5=0;
		$ls_titulo="";
		$arrResultado = $this->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
		$li_len1 = $arrResultado['ai_len1'];
		$li_len2 = $arrResultado['ai_len2'];
		$li_len3 = $arrResultado['ai_len3'];
		$li_len4 = $arrResultado['ai_len4'];
		$li_len5 = $arrResultado['ai_len5'];
		$ls_titulo = $arrResultado['as_titulo'];
		$ls_codest1=substr($as_codpro,0,25);
		$ls_codest2=substr($as_codpro,25,25);
		$ls_codest3=substr($as_codpro,50,25);
		$ls_codest4=substr($as_codpro,75,25);
		$ls_codest5=substr($as_codpro,100,25);
		$ls_codest1=substr($ls_codest1,(25-$li_len1),$li_len1);
		$ls_codest2=substr($ls_codest2,(25-$li_len2),$li_len2);
		$ls_codest3=substr($ls_codest3,(25-$li_len3),$li_len3);
		$ls_codest4=substr($ls_codest4,(25-$li_len4),$li_len4);
		$ls_codest5=substr($ls_codest5,(25-$li_len5),$li_len5);		
		switch($ls_modalidad)
		{
			case "1": // Modalidad por Proyecto
				$as_programatica=$ls_codest1."-".$ls_codest2."-".$ls_codest3;
				break;

			case "2": // Modalidad por Programa
				$as_programatica=$ls_codest1."-".$ls_codest2."-".$ls_codest3."-".$ls_codest4."-".$ls_codest5;
				break;
		}
		return $as_programatica;
   	}// end function uf_obtenertipo
	//-----------------------------------------------------------------------------------------------------------------------------------
  function uf_convertirdatetobd($as_fecha)
  {
	  ////////////////////////////////////////////////////////////////////////////////////////////////////////	   
	  //    Function:   uf_convertirdatetobd
	  // Descripci?n:   m?todo que convierte el formato de una fecha tipo caracter a formato (yyyy/mm/dd)
	  //   Arguments:   $cadena: cadena, $catacter->caracter a ser rellenado , longitud de la cadena
	  ////////////////////////////////////////////////////////////////////////////////////////////////////////	   	   
      $ls_fecreg=""; 
 	  $li_pos=strpos($as_fecha,"/");
 	  $li_pos2=strpos($as_fecha,"-");
	  if(($li_pos==2)||($li_pos2==2))
  	  {
		 $ls_fecreg=(substr($as_fecha,6,4)."-".substr($as_fecha,3,2)."-".substr($as_fecha,0,2)); 
 	  }
	  elseif(($li_pos==4)||($li_pos2==4))
 	  {
	 	 $ls_fecreg=str_replace("/","-",$as_fecha);
	  }
      return $ls_fecreg;
  } // end function
	
}
?>