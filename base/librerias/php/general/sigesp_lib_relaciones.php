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

require_once ("sigesp_lib_fabricadao.php");
class servicioRelaciones extends DaoGenerico
{
	
	public function __construct()
	{
		$this->valido=true;
		$this->mensaje='';
		$this->existe;
	}

	public function verificarRelaciones($condicion,$tabla,$valor,$mensaje)
	{
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSql='';
		$this->existe=false;
		switch (strtoupper($_SESSION['ls_gestor']))
		{
			case "MYSQLT":
				$cadenaSql = "SELECT DISTINCT TABLE_NAME AS table_name,column_name ".
							 "  FROM INFORMATION_SCHEMA.COLUMNS ".
							 " WHERE TABLE_SCHEMA='".$_SESSION["ls_database"]."' ".$condicion.
							 " AND TABLE_NAME<>'".$tabla."'";
			break;
			
			case "MYSQLI":
				$cadenaSql = "SELECT DISTINCT TABLE_NAME AS table_name,column_name ".
							 "  FROM INFORMATION_SCHEMA.COLUMNS ".
							 " WHERE TABLE_SCHEMA='".$_SESSION["ls_database"]."' ".$condicion.
							 " AND TABLE_NAME<>'".$tabla."'";
			break;
			
			case "POSTGRES":
				$cadenaSql = "SELECT DISTINCT table_name,column_name ".
							 "  FROM INFORMATION_SCHEMA.COLUMNS ".
							 " WHERE table_catalog='".$_SESSION["ls_database"]."' ".$condicion.
							 "   AND table_name<>'".$tabla."'";
			break;
			
			case "INFORMIX":
				$cadenaSql= "SELECT systables.tabname AS table_name, syscolumns.colname AS column_name  ".
							"  FROM syscolumns, systables ".
							"WHERE syscolumns.tabid = systables.tabid ".
							"  AND UPPER(systables.tabname)<>UPPER('".$tabla."') ".
							" ".$condicion." ";	
			break;
			
			case "OCI8PO":
				$cadenaSql = "SELECT DISTINCT table_name,column_name ".
							 "  FROM all_tab_columns ".
							 "  WHERE owner='".$_SESSION["ls_login"]."' ".$condicion.
							 " AND table_name<>'".$tabla."'";
				$cadenaSql = strtoupper($cadenaSql);			
			break;
		}
		$data = $this->conexionBaseDatos->Execute($cadenaSql);
		if($data === false)
		{
			$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
			$this->valido = false;
		}
		else
		{
			while(!$data->EOF)
			{
				$table_name  = $data->fields['table_name'];
				$column_name = $data->fields['column_name'];
				$cadenaSql = "SELECT ".$column_name." ".
							 "  FROM ".$table_name." ".
							 " WHERE ".$column_name." ='".$valor."'";
				$data2 = $this->conexionBaseDatos->Execute($cadenaSql);
				if($data2 === false)
				{
					$this->mensaje .= '  ->'.$this->conexionBaseDatos->ErrorMsg();
					$this->valido = false;
				}
				else
				{
					if(!$data2->EOF)
					{ 
						$this->existe=true;
						if (!empty($mensaje))
						{
							$this->mensaje = $mensaje;
						}
						else
						{
							$this->mensaje="El registro no puede ser eliminado, posee registros asociados a otras tablas.";  
						}
						break;
					 }
				}			
				$data->MoveNext(); 
			}
		}
		return $this->existe;
	}
}	
?>