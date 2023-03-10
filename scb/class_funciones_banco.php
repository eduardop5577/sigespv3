<?php
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class class_funciones_banco
{
	public function __construct($path='../')
	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:  class_funciones_banco
		//	Description:  Constructor de la Clase
		//////////////////////////////////////////////////////////////////////////////
	    require_once($path."base/librerias/php/general/sigesp_lib_include.php");
		require_once($path."base/librerias/php/general/sigesp_lib_sql.php");
		$io_include   = new sigesp_include();
		$io_conexion  = $io_include->uf_conectar();
		$this->io_sql = new class_sql($io_conexion);
	    $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obteneroperacion()
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obteneroperacion
		//		   Access: public
		//	      Returns: operacion valor de la variable
		//	  Description: Funci?n que obtiene que tipo de operaci?n se va a ejecutar (NUEVO, GUARDAR, ELIMINAR)
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 01/02/2007 								Fecha ?ltima Modificaci?n :
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
	}// end function uf_obteneroperacion
	//-----------------------------------------------------------------------------------------------------------------------------------

 	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtenerexiste()
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtenerexiste
		//		   Access: public
		//	      Returns: existe valor de la variable
		//	  Description: Funci?n que obtiene si existe el registro ? no
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 01/02/2007 								Fecha ?ltima Modificaci?n :
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
	}// end function uf_obtenerexiste
 	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_obtenertipo()
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtenertipo
		//		   Access: public
		//	  Description: Funci?n que obtiene que tipo de llamada del catalogo
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 01/02/2007 								Fecha ?ltima Modificaci?n :
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
   	}// end function uf_obtenertipo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtenervalor($as_valor,$as_valordefecto)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtenervalor
		//		   Access: public
		//	    Arguments: as_valor  // Variable que deseamos obtener
		//				   as_valordefecto  // Valor por defecto de la variable
		//	      Returns: valor contenido de la variable
		//	  Description: Funci?n que obtiene el valor de una variable que viene de un submit y si no trae valor coloca el
		//				   por defecto
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 01/02/2007 								Fecha ?ltima Modificaci?n :
		//////////////////////////////////////////////////////////////////////////////
		$valor="";
		if(array_key_exists($as_valor,$_POST))
		{
			$valor=$_POST[$as_valor];
		}
		if(trim($valor)=="")
		{
			$valor=$as_valordefecto;
		}
		return $valor;
	}// end function uf_obtenervalor
	//-----------------------------------------------------------------------------------------------------------------------------------

   //----------------------------------------------------------------------------------------------------------------------------
   function uf_load_seguridad($as_sistema,$as_ventanas,$as_permisos,$aa_seguridad,$aa_permisos,$path='../')
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_seguridad
		//		   Access: public (en todas las clases que usen seguridad)
		//	    Arguments: as_sistema // Sistema del que se desea verificar la seguridad
		//				   as_ventanas // Ventana del que se desea verificar la seguridad
		//				   as_permisos  // persimo si puede entrar ? no a la p?gina
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//				   aa_permisos  // arreglo de permisos (incluir, modificar, eliminar, etc )
		//	  Description: Funci?n que verifica si un usuario teine permiso en una pantalla y de ser asi los carga
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creaci?n: 01/02/2007 								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		require_once($path."shared/class_folder/sigesp_c_seguridad.php");
		$io_seguridad= new sigesp_c_seguridad();
		$ls_empresa=$_SESSION["la_empresa"]["codemp"];
		$ls_logusr=$_SESSION["la_logusr"];
		$aa_seguridad["empresa"]=$ls_empresa;
		$aa_seguridad["logusr"]=$ls_logusr;
		$aa_seguridad["sistema"]=$as_sistema;
		$aa_seguridad["ventanas"]=$as_ventanas;
		$as_permisos="";
		$aa_permisos = array();
		$aa_permisos["leer"]="";
		$aa_permisos["incluir"]="";
		$aa_permisos["cambiar"]="";
		$aa_permisos["eliminar"]="";
		$aa_permisos["imprimir"]="";
		$aa_permisos["anular"]="";
		$aa_permisos["ejecutar"]="";
		if($ls_logusr=="PSEGIS")
		{
			$as_permisos="1";
			$aa_permisos=$io_seguridad->uf_sss_load_permisossigesp();
		}
		else
		{
			$arr_Resultado=$io_seguridad->uf_sss_load_permisos($ls_empresa,$ls_logusr,$as_sistema,$as_ventanas,$aa_permisos);
			$as_permisos=$arr_Resultado['lb_valido'];
			$aa_permisos=$arr_Resultado['aa_permisos'];
		}
		$ls_return["as_permisos"]=$as_permisos;
		$ls_return["aa_seguridad"]=$aa_seguridad;
		$ls_return["aa_permisos"]=$aa_permisos;
		return $ls_return;
		unset($io_seguridad);
   }// end function uf_load_seguridad
   //----------------------------------------------------------------------------------------------------------------------------

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
   	function uf_obtenervalor_get($as_variable,$as_valordefecto)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtenervalor_get
		//		   Access: public
		//	  Description: Funci?n que obtiene que tipo de llamada del catalogo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n :
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
   	}// end function uf_obtenervalor_get
	//-----------------------------------------------------------------------------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_asignarvalor($as_valor, $as_valordefecto)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_asignarvalor
		//		   Access: public
		//	    Arguments: as_valor  // Variable que deseamos obtener
		//				   as_valordefecto  // Valor por defecto de la variable
		//	      Returns: valor contenido de la variable
		//	  Description: Funci?n que obtiene el valor de una variable que viene de un submit
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n :
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
   }// end function uf_asignarvalor
   //--------------------------------------------------------------

    function uf_select_cheques($as_codban,$as_ctaban,$as_codusu,$as_numchequera)
	{
	    $ls_valor="";
	    $ls_sql = "SELECT numche AS numche, numchequera,
		                  max(orden) as orden
		             FROM scb_cheques
		            WHERE codemp = '".$this->ls_codemp."'
					  AND codban = '".$as_codban."'
					  AND ctaban = '".$as_ctaban."'
					  AND estche = 0
					  AND codusu LIKE '%:".rtrim($as_codusu).":%'
					GROUP BY numchequera, numche
					ORDER BY orden ASC LIMIT 1";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			echo $this->io_sql->message;
			$lb_valido=false;
		}
		else
		{
		  if ($row=$this->io_sql->fetch_row($rs_data))
			 {
			   $ls_valor 	   = $row["numche"];
			   $as_numchequera = $row["numchequera"];
			 }
		  $this->io_sql->free_result($rs_data);
		}
		$ls_return["ls_valor"]=$ls_valor;
		$ls_return["as_numchequera"]=$as_numchequera;
		return $ls_return;
	}// end function uf_select_cheques
	
	function uf_select_chequera($as_codban,$as_ctaban)
	{
		$lb_valido=false;
		$ls_sql = "SELECT numche 
		             FROM scb_cheques
		            WHERE codemp = '".$this->ls_codemp."'
					  AND codban = '".$as_codban."'
					  AND ctaban = '".$as_ctaban."'
					  LIMIT 1";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if($rs_data===false) {
			$lb_valido=false;
		}
		else {
			if ($row=$this->io_sql->fetch_row($rs_data)){
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_cheques

	//-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_load_estatus_cierre($li_estciespi,$li_estciescg)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_estatus_cierre
		//		   Access: public
		//	  Description: Funci?n que obtiene que tipo orden de compra
		//	   Creado Por: Ing. N?stor Falc?n.
		// Fecha Creaci?n: 28/04/2007 								Fecha ?ltima Modificaci?n :
		//////////////////////////////////////////////////////////////////////////////

		$ls_sql = "SELECT estciespg,estciespi,estciescg ".
		          "FROM sigesp_empresa ".
				  "WHERE codemp='".$this->ls_codemp."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		   {
			 $this->io_mensajes->message("CLASE->class_funciones_banco();M?TODO->uf_load_estatus_cierre();ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			 $lb_valido=false;
		   }
		else
		   {
	  	     if ($row=$this->io_sql->fetch_row($rs_data))
			    {
			      $li_estciespg = $row["estciespg"];
				  $li_estciespi = $row["estciespi"];
				  $li_estciescg = $row["estciescg"];
			    }
		   }
		//unset($io_include,$this->io_conexion,$this->io_sql,$row);//----> Comentado al dar error con rs_data
		$ls_return["li_estciespg"]=$li_estciespg;
		$ls_return["li_estciespi"]=$li_estciespi;
		$ls_return["li_estciescg"]=$li_estciescg;
		return $ls_return;
   	}// end function uf_load_estatus_cierre
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_load_numero_orden_pago($as_numordpagmin,$as_codtipfon)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_estatus_cierre
		//		   Access: public
		//	  Description: Funci?n que obtiene que tipo orden de compra
		//	   Creado Por: Ing. N?stor Falc?n.
		// Fecha Creaci?n: 18/02/2009 			Fecha ?ltima Modificaci?n : 18/02/2009
		//////////////////////////////////////////////////////////////////////////////

		$lb_valido = true;
		$ls_sql = "SELECT codemp
		             FROM scb_movbco
					WHERE codemp = '".$this->ls_codemp."'
					  AND numordpagmin = '".$as_numordpagmin."'
					  AND codtipfon = '".$as_codtipfon."'";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if ($rs_data===false)
		   {
			 $this->io_mensajes->message("CLASE->class_funciones_banco();M?TODO->uf_load_numero_orden_pago();ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			 $lb_valido=false;
		   }
		else
		   {
	  	     if ($row=$this->io_sql->fetch_row($rs_data))
			    {
			      $lb_valido = false;
			    }
		   }
		unset($io_include,$this->io_conexion,$this->io_sql,$row);
		return $lb_valido;
   	}// end function uf_load_numero_orden_pago
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>