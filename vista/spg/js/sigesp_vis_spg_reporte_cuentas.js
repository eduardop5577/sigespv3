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

var fromReporteCuentas = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;
var fecha = new Date();

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//-------------------------------------------------------------------------------------

	fieldSetEstOrigenDesde = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
		titform: 'Estructura Presupuestaria Desde',
		mostrarDenominacion:false,
		sinFuente:false,
		sinCuenta:false,
		idtxt:'comfsestdesde',
		nofiltroest:'1'
	});

	fieldSetEstOrigenHasta = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
		titform: 'Estructura Presupuestaria Hasta',
		mostrarDenominacion:false,
		sinFuente:false,
		sinCuenta:false,
		idtxt:'comfsesthasta',
		nofiltroest:'1'
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos del estatus de la compra
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});
	
	//-------------------------------------------------------------------------------------
	
	//Creando el campo de cuenta contable
	var reCuentaContable = Ext.data.Record.create([
		{name: 'sc_cuenta'}, //campo obligatorio                             
		{name: 'denominacion'}, //campo obligatorio
		{name: 'status'}
	]);
		
	//componente catalogo de cuenta contable
	comcampocatcuentacontabledesde = new com.sigesp.vista.comCatalogoCuentaContable({
		idComponente:'spgdesde',
		anchofieldset: 900,
		validarCuenta:false,
                validarCuentaMayor:false,
		valorStatus: 'C',
		reCatalogo: reCuentaContable,
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcuentacontable.php',
		parametros: "ObjSon={'operacion': 'buscarCuentaContables'",
		posicion:'position:absolute;left:15px;top:15px', 
		tittxt:'Cuenta',
		idtxt:'cuenta_desde',
		campovalue:'sc_cuenta',
		anchoetiquetatext:50,
		anchotext:150,
		anchocoltext:0.24, 
		idlabel:'deno_desde',
		labelvalue:'denominacion',
		anchocoletiqueta:0.35, 
		anchoetiqueta:0,
		binding:'',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	
	//componente catalogo de cuenta contable
	comcampocatcuentacontablehasta = new com.sigesp.vista.comCatalogoCuentaContable({
		idComponente:'spghasta',
		anchofieldset: 900,
		validarCuenta:false,
                validarCuentaMayor:false,
		valorStatus: 'C',
		reCatalogo: reCuentaContable,
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcuentacontable.php',
		parametros: "ObjSon={'operacion': 'buscarCuentaContables'",
		posicion:'position:absolute;left:270px;top:15px', 
		tittxt:'Cuenta',
		idtxt:'cuenta_hasta',
		campovalue:'sc_cuenta',
		anchoetiquetatext:50,
		anchotext:150,
		anchocoltext:0.24, 
		idlabel:'deno_hasta',
		labelvalue:'denominacion',
		anchocoletiqueta:0.35, 
		anchoetiqueta:0,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});

	//--------------------------------------------------------------------------------------------

	fieldset = new Ext.form.FieldSet({
		width: 930,
		height: 275+obtenerPosicion(),
		title: '',
		style: 'position:absolute;left:5px;top:5px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:5px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [fieldSetEstOrigenDesde.fsEstructura]
					}]
				},
				{	
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:470px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [fieldSetEstOrigenHasta.fsEstructura]
					}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	fieldsetdos = new Ext.form.FieldSet({
		width: 550,
		height: 70,
		title: 'Intervalo de Cuentas Contables',
		style: 'position:absolute;left:190px;top:'+(280+obtenerPosicion())+'px',
		cls :'fondo',
		items: [comcampocatcuentacontabledesde.fieldsetCatalogo,comcampocatcuentacontablehasta.fieldsetCatalogo]	

	})
	
	//--------------------------------------------------------------------------------------------
	
	fieldsettres = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Tipo de Impresion',
		style: 'position:absolute;left:190px;top:'+(355+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:150px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [{
								xtype: 'combo',
								fieldLabel: '',
								labelSeparator :'',
								id: 'tipoimp',
								store : stOpcimp,
								editable : false,
								displayField : 'col',
								valueField : 'tipo',
								typeAhead : true,
								triggerAction : 'all',
								mode : 'local',
								emptyText:'PDF',
								listWidth:150,
								width:150
							}]
						}]
				}]
	})

	//--------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteCuentas = new Ext.FormPanel({
		applyTo: 'formReporteCuentas',
		width:965, //700
		height: 500,
		title: "<H1 align='center'>Listado de Cuentas Presupuestarias</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  
		items: [fieldset,fieldsetdos,fieldsettres/*fromTipoImpresion*/]
	});	
	fromReporteCuentas.doLayout();
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
	var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
	var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
	var opcionimp = 'P';
	var valido = true;
	var cuecondesde = Ext.getCmp('cuenta_desde').getValue();
	var cueconhasta = Ext.getCmp('cuenta_hasta').getValue();
	
	if(arrCodigosDesde[6] > arrCodigosHasta[6]){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por fuente de financiamiento no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(arrCodigosDesde[7] > arrCodigosHasta[7]){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por cuenta no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
		
	if(arrCodigosDesde[0]!='0000000000000000000000000') {
		if(!fieldSetEstOrigenDesde.validarEstructuraCompleta()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe seleccionar toda la estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if(!fieldSetEstOrigenHasta.validarEstructuraCompleta()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	
	if(arrCodigosHasta[0]!='0000000000000000000000000') {
		if(!fieldSetEstOrigenHasta.validarEstructuraCompleta()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe seleccionar toda la estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if(!fieldSetEstOrigenDesde.validarEstructuraCompleta()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	
	if((arrCodigosDesde[6]=="" && arrCodigosHasta[6]!="") || (arrCodigosDesde[6]!="" && arrCodigosHasta[6]=="")){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Fuente de Financiamiento !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((arrCodigosDesde[7]=="" && arrCodigosHasta[7]!="") || (arrCodigosDesde[7]!="" && arrCodigosHasta[7]=="")){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Cuenta Presupuestaria !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((cuecondesde=="" && cueconhasta!="") || (cuecondesde!="" && cueconhasta=="")){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Cuenta Contable !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(cuecondesde>cueconhasta){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El rango de Busqueda por Cuenta Contable no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(valido){
		var Datosreporte='';
		if(Ext.getCmp('tipoimp').getValue()!=''){
			opcionimp=Ext.getCmp('tipoimp').getValue();
		}
		for(var i = 0; i < arrCodigosDesde.length; i++) {
			if(arrCodigosDesde[i]=="0000000000000000000000000" || arrCodigosDesde[i]=="--"){
				arrCodigosDesde[i]="";
			}
			if(arrCodigosHasta[i]=="0000000000000000000000000" || arrCodigosHasta[i]=="--"){
				arrCodigosHasta[i]="";
			}		
		}
		if(empresa['estmodest']==1){
			Datosreporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
			              +"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
			              +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
			              +"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
			              +"&cuentascg_desde="+cuecondesde+"&cuentascg_hasta="+cueconhasta
			              +"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
			              +"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5];
		}
		else{
			Datosreporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
                          +"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
                          +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
			              +"&codestpro4="+arrCodigosDesde[3]+"&codestpro5="+arrCodigosDesde[4]
			              +"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]
                          +"&txtcuentades="+arrCodigosDesde[7]+"&txtcuentahas="+arrCodigosHasta[7]
                          +"&cuentascg_desde="+cuecondesde+"&cuentascg_hasta="+cueconhasta
                          +"&txtcodfuefindes="+arrCodigosDesde[6]+"&txtcodfuefinhas="+arrCodigosHasta[6]
                          +"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5];
		}
		imprimir(Datosreporte,opcionimp);
	}
}

function imprimir(datos,opcion)
{	
	if(opcion=='P'){
		var pagina = "reportes/sigesp_spg_rpp_cuentas.php"+datos;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
	else{
		var pagina = "reportes/sigesp_spg_rpp_cuentas_excel.php"+datos;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}

