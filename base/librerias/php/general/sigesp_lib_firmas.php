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

class firmasDinamicas
{
        public $existe = false;
        public $fir1 = '';
        public $fir2 = '';
        public $fir3 = '';
		public $fir4 = '';
        public $fir5 = '';
        private $tiprepfir='';
        private $codemp='';
        private $codfir = '';
        private $tipclafir = '';
        private $codusu = '';
        private $prefijo = '';
		private $coduniadm = '';
        private $codtipsol = '';
        private $tipordcom ='';
        private $codcla = '';
        private $nrofir = 0;
        
		public function __construct($tiprepfir, $codusu, $prefijo, $coduniadm, $codtipsol, $tipordcom)
        {
		$this->tiprepfir = $tiprepfir;
		$this->codusu = $codusu;
		$this->prefijo = $prefijo;
		$this->codunidadm = $coduniadm;
		$this->codtipsol = $codtipsol;
		$this->tipordcom = $tipordcom;                
                $this->nrofir = 0; 
                $this->codemp = $_SESSION["la_empresa"]["codemp"];
	}

        function verificarFirmasDinamicas()
        {
                require_once ("sigesp_lib_fabricadao.php");
                $resultado='';

                $criterio="codemp='".$this->codemp."' AND tiprepfir='".$this->tiprepfir."'";
                $daoFirmas = FabricaDao::CrearDAO('C','sss_firmantesdinamicos','',$criterio);		
                if($daoFirmas->codemp<>'')
                {
                        $this->existe = true;
                        $this->tipclafir = $daoFirmas->tipclafir;
                        $this->codfir = $daoFirmas->codfir;
                        $this->nrofir = $daoFirmas->nrofir;
                }
                unset($daoFirmas);
        }

        function cargarFirmasDinamicas()
        {
                require_once ("sigesp_lib_fabricadao.php");

                $this->verificarFirmasDinamicas();
                if ($this->existe)
                {
                    switch ($this->tipclafir)
                    {
                        case '000': //sin clasificacion
                            $this->codcla = '000';
                        break;

                        case '001': //control nro
                            $this->codcla = $this->prefijo;
                        break;
                    
                        case '002': //unidad administrativa
                            $this->codcla = $this->coduniadm;
                        break;
                    
                        case '003': //usuario
                            $this->codcla = $this->codusu;
                        break;

                        case '004': //tipo de solicitud
                            $this->codcla = $this->codtipsol;
                        break;

                        case '005': //tipo de orden de compra
                            $this->codcla = $this->tipordcom;
                        break;
                    }
                    
                    $criterio="codemp='".$this->codemp."' AND codfir='".$this->codfir."' AND tipclafir='".$this->tipclafir."' AND codcla='".$this->codcla."'";
                    $daoFirmas = FabricaDao::CrearDAO('C','sss_dt_firmantesdinamicos','',$criterio);		
                    if($daoFirmas->codemp<>'')
                    {
                            $this->existe = true;
                            switch ($this->nrofir)
                            {
                                case 1: 
                                    $this->fir1 = $daoFirmas->fir1;
                                    $this->fir2 = "";
                                    $this->fir3 = "";
                                    $this->fir4 = "";
                                    $this->fir5 = "";
                                break;

                                case 2: 
                                    $this->fir1 = $daoFirmas->fir1;
                                    $this->fir2 = $daoFirmas->fir2;
                                    $this->fir3 = "";
                                    $this->fir4 = "";
                                    $this->fir5 = "";
                                break;

                                case 3:
                                    $this->fir1 = $daoFirmas->fir1;
                                    $this->fir2 = $daoFirmas->fir2;
                                    $this->fir3 = $daoFirmas->fir3;
                                    $this->fir4 = "";
                                    $this->fir5 = "";
                                break;

                                case 4:
                                    $this->fir1 = $daoFirmas->fir1;
                                    $this->fir2 = $daoFirmas->fir2;
                                    $this->fir3 = $daoFirmas->fir3;
                                    $this->fir4 = $daoFirmas->fir4;
                                    $this->fir5 = "";
                                break;

                                case 5:
                                    $this->fir1 = $daoFirmas->fir1;
                                    $this->fir2 = $daoFirmas->fir2;
                                    $this->fir3 = $daoFirmas->fir3;
                                    $this->fir4 = $daoFirmas->fir4;
                                    $this->fir5 = $daoFirmas->fir5;
                                break;

                            }                                    
                    }
                    unset($daoFirmas);
                }
        }
}
?>