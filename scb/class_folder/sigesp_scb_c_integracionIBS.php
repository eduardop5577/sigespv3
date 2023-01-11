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

class sigesp_scb_c_integracionIBS
{
	//Paramentros para la conexion.
	private $dbName = 'BPSUSRLIB';        //nombre de la base de datos DB2 a conectarse
	private $host = '10.1.4.92';          //ip del servidor que aloja la bd BD2
	private $port = '';          //puerto en el cual escucha el servidor para establecer la conexion con DB2
	private $protocol = 'TCPIP'; //protocolo a utiliza para estableces la conexion
	private $user = 'PRUDESA5';          //usuario para conectar con DB2
	private $pwd  = 'S0B3R4N0';          //password del usuario para conectar con DB2
	
	//objetos
	private $io_conDB;
	private $io_conectDB2;
	
	

	public function __construct($ruta="")
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: class_sigesp_scbmov_integracion
		//		   Access: public
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Gerardo Cordero
		// Modificado Por: 								   Fecha Última Modificación : 23/02/2014
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($ruta."../base/librerias/php/general/sigesp_lib_conexion.php");
		$this->io_conDB = new ConexionBaseDatos();
		$this->io_conectDB2 = $this->io_conDB->getConexionPDODB2($this->dbName, $this->host, $this->port, $this->protocol, $this->user, $this->pwd);
	}
	

	
	public function validarNumeroChequeIBS($ls_numche) {
		//PARAMETROS
		$coderr = "";         //CODIGO DE ERROR 
		$codban = "01";       //CODIGO DE BANCO 
		$codofi = "641";      //CODIGO DE OFICINA	
		$codmon = "BSF";      //CODIGO DE MONEDA
		$numche = substr($ls_numche, -8); //NUMERO DE CHEQUE
		
		try {
			// Haciendo Prepare al stored procedure 
			$stmSP = $this->io_conectDB2->prepare('CALL BPSUSRLIB.SP_BUSGER(?, ?, ?, ?, ?)');
			
				
			// Binding de los parametros del stored procedure 
			$stmSP->bindParam(1, $coderr, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 6);
			$stmSP->bindParam(2, $codban, PDO::PARAM_STR);
			$stmSP->bindParam(3, $codofi, PDO::PARAM_STR);
			$stmSP->bindParam(4, $codmon, PDO::PARAM_STR);
			$stmSP->bindParam(5, $numche, PDO::PARAM_STR);
			$respuesta = $stmSP->execute();
			
			if(!$respuesta){
				$arrError = $stmSP->errorInfo();
				$coderr = $arrError[2];
			}
		}
		catch (PDOException $e) {
			$coderr = "Error!: " . $e->getMessage();
		}
		
		return $coderr;
	}

	public function emitirChequeIBS($ls_numche, $ls_monto, $ls_nomben, $ls_nomemp, $ls_cedrif) {
		//PARAMETROS
		$coderr = "";         //CODIGO DE ERROR 
		$codban = "01";       //CODIGO DE BANCO 
		$codofi = "641";      //CODIGO DE OFICINA	
		$codmon = "BSF";      //CODIGO DE MONEDA
		$tipfor = "5";        //TIPO FORMATO ('5'-> INDICA QUE EL CHEQUE ES DE PROVEEDOR)
		$numche = substr($ls_numche, -8);    //NUMERO DE CHEQUE
		$monto  = $ls_monto;                 //MONTO DE CHEQUE
		$nomben = substr($ls_nomben, 0, 60); //NOMBRE DE BENEFICIARIO
		$nomemp = substr($ls_nomemp, 0, 30); //NOMBRE DE EMPRESA
		$cedrif = $ls_cedrif; //CEDULA O RIF
		$usuqsr = "evolution";         //USUARIO QASAR
		
		//FORMATEANDO CEDRIF
		$arrCedrif = explode('-', $cedrif);
		$numcedrif = substr($arrCedrif[1], 0, 9);
		$numcedrif = $numcedrif.$arrCedrif[2];
		$numcedrif = str_pad($numcedrif, 9, '0', STR_PAD_LEFT); 
		$cedrif = $arrCedrif[0].$numcedrif;
				
		try {
			// Haciendo Prepare al stored procedure 
			$stmSP = $this->io_conectDB2->prepare('CALL BPSUSRLIB.SP_CREGERCL(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
			
			// Binding de los parametros del stored procedure 
			$stmSP->bindParam(1, $coderr, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 6);
			$stmSP->bindParam(2, $codban, PDO::PARAM_STR);
			$stmSP->bindParam(3, $codofi, PDO::PARAM_STR);
			$stmSP->bindParam(4, $codmon, PDO::PARAM_STR);
			$stmSP->bindParam(5, $tipfor, PDO::PARAM_STR);
			$stmSP->bindParam(6, $numche, PDO::PARAM_STR);
			$stmSP->bindParam(7, $monto, PDO::PARAM_STR);
			$stmSP->bindParam(8, $nomben, PDO::PARAM_STR);
			$stmSP->bindParam(9, $nomemp, PDO::PARAM_STR);
			$stmSP->bindParam(10, $cedrif, PDO::PARAM_STR);
			$stmSP->bindParam(11, $usuqsr, PDO::PARAM_STR);
			$respuesta = $stmSP->execute();
			
			if(!$respuesta){
				$arrError = $stmSP->errorInfo();
				$coderr = $arrError[2];
			}
		}
		catch (PDOException $e) {
			$coderr = "Error!: " . $e->getMessage();
		}
		
		return $coderr;
	}

	public function procesarTransferenciaIBS($ls_codcue, $ls_cedrif, $ls_monto) {
		//PARAMETROS
		$coderr = "";          //CODIGO DE ERROR 
		$menerr = "";          //MENSAJE O DESCRIPCION DE ERROR
		$codcue = $ls_codcue;  //CODIGO DE LA CUENTA	
		$monto  = $ls_monto;   //MONTO DE LA OPERACION
		$cedrif = $ls_cedrif; //CEDULA O RIF
		
		//FORMATEANDO CEDRIF
		$arrCedrif = explode('-', $cedrif);
		$numcedrif = substr($arrCedrif[1], 0, 9);
		$numcedrif = $numcedrif.$arrCedrif[2];
		$numcedrif = str_pad($numcedrif, 9, '0', STR_PAD_LEFT);
		$cedrif = $arrCedrif[0].$numcedrif."     ";
						
		try {
			// Haciendo Prepare al stored procedure 
			$stmSP = $this->io_conectDB2->prepare('CALL BPSUSRLIB.SP_CLQUA04(?, ?, ?, ?, ?)');
				
			// Binding de los parametros del stored procedure 
			$stmSP->bindParam(1, $codcue, PDO::PARAM_STR);
			$stmSP->bindParam(2, $cedrif, PDO::PARAM_STR);
			$stmSP->bindParam(3, $monto, PDO::PARAM_STR);
			$stmSP->bindParam(4, $coderr, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 6);
			$stmSP->bindParam(5, $menerr, PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT, 30);
			
			$respuesta = $stmSP->execute();
			
			if(!$respuesta) {
				$arrError = $stmSP->errorInfo();
				$coderr = $arrError[2];
			}
		}
		catch (PDOException $e) {
			$coderr = "Error!: " . $e->getMessage();
		}
		
		return $coderr;
	}
	
	public function mensajeError($coderr) {
		$mensaje = '';
		switch ($coderr) {
			case "":
				$mensaje = 'Error. No se obtuvo respuesta';
				break;
			
			case "000":
				$mensaje = 'Codigo: 000 - Procesado Cheque de Gerencia Valido o Emitido';
				break;
			
			case "001":
				$mensaje = 'Codigo: 001 - Numero de Cheque Invalido';
				break;
				
			case "002":
				$mensaje = 'Codigo: 002 - No esta registrado el numero de cheque';
				break;
			
			case "003":
				$mensaje = 'Codigo: 003 - No esta registrado el numero de cheque';
				break;
				
			case "004":
				$mensaje = 'Codigo: 004 - Cheque de gerencia ya Emitido';
				break;
			
			case "005":
				$mensaje = 'Codigo: 005 - Codigo de oficina Errado o no Existe';
				break;
				
			case "006":
				$mensaje = 'Codigo: 006 - Codigo de Moneda Errado';
				break;

			case "007":
				$mensaje = 'Codigo: 007 - Monto de Cheque Errado o en cero';
				break;
				
			case "008":
				$mensaje = 'Codigo: 008 - Falta Beneficiario';
				break;
				
			case "009":
				$mensaje = 'Codigo: 009 - Falta Numero de Cedula - RIF';
				break;
			
			case "010":
				$mensaje = 'Codigo: 010 - Falta Codigo de Usuario';
				break;
			
			case "011":
				$mensaje = 'Codigo: 011 - Error Codigo de Banco';
				break;
				
			case "012":
				$mensaje = 'Codigo: 012 - Error fecha del sistema IBS. Favor Avisar';
				break;	
			
			case "013":
				$mensaje = 'Codigo: 013 - Estatus del Cheque No Permite Anulacion';
				break;
			
			default:
				$mensaje = 'Error no tipificado: '.$coderr;
				break;
		}
		
		return $mensaje;
	}
	
	public function mensajeTransferencia ($coderr) {
	    $coderr = trim($coderr);
		$mensaje = '';
		$arrCodigo = array(0 => '', 1 => '0000', 2 => '0001', 3 => '0002', 4 => '0003', 5 => '0004', 6 => '0005', 7 => '0006', 8 => '0007',
						   9 => '0008', 10 => '0009', 11 => '0010', 12 => '0011', 13 => '0012', 14 => '0013', 15 => '0014', 16 => '0015', 17 => '0016',
						   18 => '0017', 19 => '0018', 20 => '0019', 21 => '0020', 22 => '0021', 23 => '0022', 24 => '0023', 25 => '0024', 26 => '0025',
						   27 => '0026', 28 => '0027', 29 => '0028', 30 => '0029', 31 => '0030', 32 => '0031', 33 => '0032', 34 => '0033', 35 => '0034',
						   36 => '0035', 37 => '0036', 38 => '0037');
		
		$arrMensaje = array(0 => 'Error. No se obtuvo respuesta', 1 => 'Operación Efectuada', 2 => 'Transacción invalida', 
		                    3 => 'Cajero no existe', 4 => 'Cajero no está activo', 5 => 'Concepto no valido', 6 => 'Concepto no existe', 
							7 => 'Concepto no corresponde con operación', 8 => 'Código de cuenta cliente invalido',
							9 => 'Status de la cuenta no validado', 10 => 'Cliente invalido', 11 => 'Identificación no coincide', 
							12 => 'Cuenta No es Uni-Titular', 13 => 'Cuenta se sobregira', 14 => 'Línea de crédito no existe', 
							15 => 'Línea de crédito con fecha vencida', 16 => 'Disponible de la línea de crédito no cubre la operación', 
							17 => 'Registro no encontrado en archivo AUDIT reverso no procede', 18 => 'Operación ya fue reversada', 
							19 => 'Operación aplica a Cta. Cte. y envío Cta. Ahorro', 20 => 'Operación aplica a Cta. Ahorro y envío Cta. Cte.', 
							21 => 'Original o reverso invalido', 22 => 'Consecutivo en operación Original debe contener ceros', 
							23 => 'Consecutivo en operación Reverso no pude contener ceros', 24 => 'Cajero no autorizado a procesar Notas DB o CR Especiales', 
							25 => 'Serial no puede ser cero', 26 => 'Cuenta inválida o no existe', 27 => 'Fecha inválida', 28 => 'Cuenta Cerrada', 
							29 => 'Valor retenido Es Cero o negativo', 30 => 'Cuenta sobregirada en el disponible', 
							31 => 'Código de retención no se encuentra en los archivos', 32 => 'Transacción Realizada Exitosamente', 
							33 => 'Cuenta No Esta Activa', 34 => 'Cuenta no posee monto disponible', 35 => 'Nacionalidad o Cédula Errada',
						    36 => 'No se encontró bloqueo o retención', 37 => 'El tipo de operación incluida esta errada', 
							38 => 'Cuenta no fue abonada ya poseía error. Ver Consulta!');
							
		$indice = array_search($coderr, $arrCodigo);
		if($indice === false) {
			$mensaje = 'Error no tipificado: '.$coderr;
		}
		else {
			$mensaje = 'Codigo: '.$coderr.' - '.$arrMensaje[$indice];
		}
		
		return $mensaje;
	}
}