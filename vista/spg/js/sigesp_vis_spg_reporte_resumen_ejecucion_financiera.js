/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var fromReporteResEjeFin = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;


Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//--------------------------------------------------------------------------------------------

	//Creando los componentes para el catalogo de estructura
	fieldSetEstOrigenDesde = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		style:'position:absolute;left:15px;top:15px',
		mostrarDenominacion:false,
		idtxt:'comfsestdesde'
	});
	
	
	//--------------------------------------------------------------------------------------------

	//componente para el catalogo de cuentas presupuestarias
	var reCuentaPreDesde = Ext.data.Record.create([
        {name: 'spg_cuenta'}, //campo obligatorio                             
        {name: 'denominacion'}, //campo obligatorio
        {name: 'status'}, //campo obligatorio
    ]);
	
	comcampocatcuenta = new com.sigesp.vista.comCatalogoCuentaSPG({
		idComponente:'spgdesde',
		anchofieldset: 900,
		reCatalogo: reCuentaPreDesde,
		rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_comprobante.php',
		parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
		posicion:'position:absolute;left:100px;top:10px', 
		tittxt:'Cuenta',
		idtxt:'cuenta',
		campovalue:'spg_cuenta',
		anchoetiquetatext:50,
		anchotext:150,
		anchocoltext:0.24, 
		idlabel:'denominacion',
		labelvalue:'',
		anchocoletiqueta:0.35, 
		anchoetiqueta:0,
		binding:'',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false, 
	});

	//--------------------------------------------------------------------------------------------
	
	//Boton para la busqueda de cuentas presupuestarias
	var botbusDesOrdCom = new Ext.Button({
		id: 'botbusquedaDesde',
		iconCls: 'menubuscar',
		style:'position:absolute;left:310px;top:25px',
		listeners:{
            'click' : function(boton){
            	ventanaCatalogoFuenteFinan(boton.id)
           }
        }
	});
	
	//--------------------------------------------------------------------------------------------	
	
	fieldset = new Ext.form.FieldSet({
		width: 500,
		height: 300+obtenerPosicion(),
		title: '',
		style: 'position:absolute;left:70px;top:5px',
		border: false,
		items: [{	
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:10px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [fieldSetEstOrigenDesde.fieldSetEstPre]
					}]
				}]
		})

	//--------------------------------------------------------------------------------------------
	
	fieldsetdos = new Ext.form.FieldSet({
		width: 450,
		height: 70,
		title: 'Cuenta Presupuestaria',
		style: 'position:absolute;left:80px;top:'+(200+obtenerPosicion())+'px',
		cls :'fondo',
        items:[comcampocatcuenta.fieldsetCatalogo]
  	})

	
	//--------------------------------------------------------------------------------------------

	fieldsettres = new Ext.form.FieldSet({
		width: 450,
		height: 58,
		title: 'Hasta la Fecha',
		style: 'position:absolute;left:80px;top:'+(275+obtenerPosicion())+'px',
		cls :'fondo',
		items:[{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:125px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [{
								xtype: "datefield",
								labelSeparator :'',
								fieldLabel:"Fecha",
								name:'Hasta',
								id:'fecha',
								allowBlank:true,
								width:120,
								binding:true,
								defaultvalue:'1900-01-01',
								hiddenvalue:'',
								allowBlank:false,
								value:  new Date().format('d-m-Y'),
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
						}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteResEjeFin = new Ext.FormPanel({
		applyTo: 'formReporteResEjeFin',
		width:630, //700
		height: 450,
		title: "<H1 align='center'>Resumen de Ejecuci&#243;n Financiera de Gasto</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  
		items: [fieldset,fieldsetdos,fieldsettres]
	});	
	fromReporteResEjeFin.doLayout();
});	

	//--------------------------------------------------------------------------------------------

	function obtenerPosicion(){
		if(empresa['numniv']=='3'){
			return 0;
		}
		else{
			return 80;
		}
	}

	function irImprimir()
	{
		var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructuraFormato();
		var fecha = Ext.getCmp('fecha').getValue().format('d-m-Y');
		var spg_cuenta = Ext.getCmp('cuenta').getValue();
		for ( var i = 0; i < arrCodigosDesde.length; i++) {
			if(arrCodigosDesde[i]=="0000000000000000000000000" || arrCodigosDesde[i]=="--"){
				arrCodigosDesde[i]="";
			}	
		}
		if(empresa['estmodest']==1){
			var datosReporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
			+"&codestpro3="+arrCodigosDesde[2]+"&txtcuenta="+spg_cuenta
			+"&estcla="+arrCodigosDesde[5]+"&fecha="+fecha

		}else {
			var datosReporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
			+"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]
			+"&codestpro5="+arrCodigosDesde[4]+"&txtcuenta="+spg_cuenta
			+"&estcla="+arrCodigosDesde[5]+"&fecha="+fecha

		}
		pagina="reportes/sigesp_spg_rpp_ejecucion_financiera_presupuesto.php"+datosReporte;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}

