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

class class_sql
{
	var $conn;	//objeto de conexion a la base de datos
	var $fetch_row;
	var $fetch_array;
	var $message;
	var $errno;
	var $datastore;//Instancia de la clase datastore

	public function __construct($con) // contructor
	{
		require_once("sigesp_lib_datastore.php");
		$obj=new class_datastore();

		require_once("sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();

		$this->datastore=$obj;
		$this->conn=$con;
	}
	
	function seleccionar($ps_sentencia,$pa_datos)
	{
	   
	   $lb_valido = false;
	   $resultado =$this->conn->Execute($ps_sentencia);
	   
				if ($resultado != null && $this->num_rows($resultado) > 0)
				{
				  $lb_valido = true;
				  $i = 0;
				  while ($fila = $this->fetch_row($resultado))
				  {
					for ($j=0; $j<count((array)$fila); $j++)
					{
					  $ls_campo =  $this->field_name($resultado,$j);
					  $pa_datos[$ls_campo][$i] = $fila[$ls_campo];
					}
					$i++;		
				  }
			
				}
	   if(empty($pa_datos))
	   {
	   $pa_datos="";
	   }
				
		$arrResultado['valido']=$lb_valido;
		$arrResultado['pa_datos']=$pa_datos;
		return $arrResultado;
	} // end function
	  

	function select($sql)
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	 select
		// Description : Realiza una consulta SQL y retorna un resulset con los datos obtenidos.
		// Arguments:
		//			     $sql->cadena que contiene la sentencia SQL
		////////////////////////////////////////////////////////////////////////////////////////////
		try {
			$result=$this->conn->Execute($sql);
			if(!$result)
			{
				$this->message  = 'Invalid query: ' .$this->conn->ErrorMsg(). "\n";
				$this->message .= 'Whole query: ' . $sql;
				return false;
			}
			else
			{
				return $result;
			}
		}
		catch (Exception $e){
			return false;
		}
	} // end function
		
	function execute($sql)
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	 execute
		// Description : Realiza una transaccion SQL y retorna el numero de filas afectadas
		// Arguments:
		//			     $sql->cadena que contiene la sentencia SQL
		////////////////////////////////////////////////////////////////////////////////////////////
		try {
			$result=$this->conn->Execute($sql);
			if(!$result)
			{
				$this->message  = 'Invalid query: ' .$this->conn->ErrorMsg(). "\n";
				$this->message .= 'Whole query: ' . $sql;
				$this->errno    = $this->conn->ErrorNo();
	
	
				return false;
			}
			else
			{
				return $result;
			}
		}
		catch (Exception $e){
			return false;
		}
	}// end function
	
	function fetch_row($rs_data)
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	 fetch_row
		// Description : Retorna el valor siguiente encontrado en el resulset
		// Arguments:
		//			     $rs_data->Resulset obtenido del metodo select.
		////////////////////////////////////////////////////////////////////////////////////////////
		if(isset($rs_data)) 
		{
			if (empty($rs_data))
			{
				$this->fetch_row = "";
			}
			else
			{
				$this->fetch_row = $rs_data->FetchRow();
			}
		}
		return $this->fetch_row;
	}// end function

	function num_rows($rs_data) 
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	 num_rows
		// Description : Retorna el numero de registros que resultaron del metodo select
		// Arguments:
		//			     $rs_data->Resulset obtenido del metodo select.
		////////////////////////////////////////////////////////////////////////////////////////////
		if(isset($rs_data)) 
		{
			if (empty($rs_data))
			{
				$this->numrows = 0;
			}
			else
			{
				$this->numrows = $rs_data->RecordCount();
			}
		}
		return $this->numrows;
	}// end function

	function field_name($rs_data,$field) 
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	filed_name
		// Description : Retorna el nombre de la columna correspondiente al numero enviado como parametro
		// Arguments:
		//			     $rs_data->Resulset obtenido del metodo select.
		//				 $field->numero de la columna a buscar
		////////////////////////////////////////////////////////////////////////////////////////////		
		if(isset($rs_data) && isset($field)) 
		{
			$campo = $rs_data->FetchField($field);
			$this->fetcharray = $campo->name;
		}
		return $this->fetcharray;
	}// end function

	function field_type ($rs_data,$field) 
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	filed_type
		// Description : Retorna el tipo de datos de la columna correspondiente al numero enviado como parametro
		// Arguments:
		//			     $rs_data->Resulset obtenido del metodo select.
		//				 $field->numero de la columna a buscar
		////////////////////////////////////////////////////////////////////////////////////////////				
		if(isset($rs_data) && isset($field)) 
		{
				$campo = $rs_data->FetchField($field);
				$this->fetcharray = $campo->type;
		}
		return $this->fetcharray;
	}// end function

	function free_result ($result)  
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	free_result
		// Description : Libera de la memoria el resultado de la ejecucion del metodo select
		// Arguments:
		//			     $result->Resulset obtenido del metodo select.
		////////////////////////////////////////////////////////////////////////////////////////////
		$result->Close(); 		
	}// end function
	
	function close () 
	{
		$this->closeid =  $this->conn->Close();
		return $this->closeid;
	}// end function
	
	function obtener_datos($result)
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	 obtener_datos
		// Description : Metodo que retorna una matriz datastore con los valores obtenidos de la ejecucion del metodo select
		// Arguments:
		//			     $result->Resulset obtenido del metodo select.
		////////////////////////////////////////////////////////////////////////////////////////////		
		$this->datastore->reset_ds();//Blanqueo la matriz $data.
		if($result->RecordCount()>0)
		{
			$i = 0;
			$result->MoveFirst();
			$columnas=count((array)$result->FetchRow());
			$result->MoveFirst();
			while($result->EOF===false)
			{
				for ($j=0; $j<$columnas; $j++)
				{
					$campo = $result->FetchField($j);
					$nombre = $campo->name;
					$tipo =  $result->MetaType($campo->type);
					$valor = $result->fields[$nombre];
					if( $tipo == 'D' || $tipo == 'T') 
					{
						$valor =$this->io_funciones->uf_formatovalidofecha($valor);
					}
					$this->datastore->insertRow($nombre,$valor);
				}
				$i++;	
				$result->MoveNext();
			}
		}
		return $this->datastore->data;	
	}// end function

	function begin_transaction()
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	begin_transaction
		// Description : Metodo que inicia una transaccion SQL
		////////////////////////////////////////////////////////////////////////////////////////////		
		$this->conn->BeginTrans();
	}// end function
	
	function commit()
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	commit
		// Description : Realiza el cierre satisfactorio de la transaccion.Depende de la ejecucion anterior del begin_transaction
		////////////////////////////////////////////////////////////////////////////////////////////				
		$this->conn->CommitTrans();
	}// end function
	
	function rollback()
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		// Function:   	commit
		// Description : Realiza el aborto o reverso de la transaccion ejecutada(INSERT,UPDATE,DELETE).Depende de la ejecucion anterior del begin_transaction
		////////////////////////////////////////////////////////////////////////////////////////////				
		$this->conn->RollbackTrans();
	}	// end function
} // end class
?>