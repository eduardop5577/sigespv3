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

var fromReporteEjeFinCau = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;
var	fieldsetcinco = null;
var	fieldsetseis = null;
var	fieldsetsiete = null;

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//-------------------------------------------------------------------------------------

	fieldSetEstOrigenDesde = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		style:'position:absolute;left:15px;top:15px',
		mostrarDenominacion:false,
		idtxt:'comfsestdesde'
	});
	
	fieldSetEstOrigenHasta = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		style:'position:absolute;left:15px;top:15px',
		mostrarDenominacion:false,
		idtxt:'comfsesthasta'
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos para el formato de impresion
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]/*,
	               [ 'GR&#225;FICOS', 'G' ]*/];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});

	//--------------------------------------------------------------------------------------------
	
	//componente catalogo de cuentas presupuestarias
	var reCuentaPresupuestaria = Ext.data.Record.create([
        {name: 'spg_cuenta'}, //campo obligatorio                             
        {name: 'denominacion'}, //campo obligatorio
        {name: 'status'}, //campo obligatorio
    ]);

	var comcampocatcuentaspgdesde = new com.sigesp.vista.comCatalogoCuentaSPG({
		idComponente:'spgdesde',
		reCatalogo: reCuentaPresupuestaria,
		rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_comprobante.php',
		parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
		soloCatalogo: true,
		valorStatus: '',
		arrSetCampo:[{campo:'txtCuentasDesde',valor:'spg_cuenta'},
		             {campo:'txtCuentasHasta',valor:'spg_cuenta'}]
	});
	
	var comcampocatcuentaspghasta = new com.sigesp.vista.comCatalogoCuentaSPG({
		idComponente:'spghasta',
		reCatalogo: reCuentaPresupuestaria,
		rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_comprobante.php',
		parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
		soloCatalogo: true,
		valorStatus: '',
		arrSetCampo:[{campo:'txtCuentasHasta',valor:'spg_cuenta'}]
	});
	//fin componente catalogo de cuentas presupuestarias
	
	//Botones para la busqueda del intervalo de cuenta presupuestaria
	var botbusDesCuenta = new Ext.Button({
		id: 'botbusquedaDesdeCuenta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:215px;top:10px',
		listeners:{
            'click' : function(boton){
				comcampocatcuentaspgdesde.mostrarVentana();
           }
        }
	});
	
	var botbusHasCuenta = new Ext.Button({
		id: 'botbusquedaHastaCuenta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:465px;top:10px',
		listeners:{
            'click' : function(boton){
				comcampocatcuentaspghasta.mostrarVentana();
           }
        }
	});
	
	//-------------------------------------------------------------------------------------	
	
	//Datos de la fecha mensual
	var fechaMensual = [ [ 'Enero', '01' ], 
	                     [ 'Febrero', '02' ],
			             [ 'Marzo', '03' ],
			             [ 'Abril', '04' ],
			             [ 'Mayo', '05' ],
			             [ 'Junio', '06' ],
			             [ 'Julio', '07' ],
			             [ 'Agosto', '08' ],
			             [ 'Septiembre', '09' ],
			             [ 'Octubre', '10' ],
			             [ 'Noviembre', '11' ],
			             [ 'Diciembre', '12' ]];
	
	var stfechaMensual = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : fechaMensual
	});
	
	var combomensual = new Ext.form.ComboBox({
		fieldLabel: 'Mensual',
		labelSeparator :'',
		id: 'fechamensual',
		store : stfechaMensual,
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		triggerAction : 'all',
		mode : 'local',
		emptyText:'Enero',
		listWidth:150,
		width:150,
	})
	
	//-------------------------------------------------------------------------------------	
	
	//Datos de la fecha bi-mensual
	var fechaBiMensual = [ [ 'Enero-Febrero', '0102' ], 
	                       [ 'Febrero-Marzo', '0203' ],
			               [ 'Marzo-Abril', '0304' ],
			               [ 'Abril-Mayo', '0405' ],
			               [ 'Mayo-Junio', '0506' ],
			               [ 'Junio-Julio', '0607' ],
			               [ 'Julio-Agosto', '0708' ],
			               [ 'Agosto-Septiembre', '0809' ],
			               [ 'Septiembre-Octubre', '0910' ],
			               [ 'Octubre-Noviembre', '1011' ],
			               [ 'Noviembre-Diciembre', '1112' ]];
	
	var stfechaBiMensual = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : fechaBiMensual
	});

	var combobimensual = new Ext.form.ComboBox({
		fieldLabel: 'Bi-Mensual',
		labelSeparator :'',
		id: 'fechabimensual',
		store : stfechaBiMensual,
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		triggerAction : 'all',
		mode : 'local',
		emptyText:'Enero-Febrero',
		listWidth:150,
		width:150,
	})
	
	//-------------------------------------------------------------------------------------	
	
	//Datos de la fecha trimestral
	var fechaTrimestral = [ [ 'Enero-Marzo', '010203' ], 
	                        [ 'Abril-Junio', '040506' ],
			                [ 'Julio-Septiembre', '070809' ],
			                [ 'Octubre-Diciembre', '101112' ]];
	
	var stfechaTrimestral = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : fechaTrimestral
	});

	var combotrimestral = new Ext.form.ComboBox({
		fieldLabel: 'Trimestral',
		labelSeparator :'',
		id: 'fechatrimestral',
		store : stfechaTrimestral,
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		triggerAction : 'all',
		mode : 'local',
		emptyText:'Enero-Marzo',
		listWidth:150,
		width:150,
	})
	
	//--------------------------------------------------------------------------------------------

	fieldset = new Ext.form.FieldSet({
		width: 925,
		height: 200+obtenerPosicion(),
		title: '',
		style: 'position:absolute;left:5px;top:5px',
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
				},
				{	
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:465px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [fieldSetEstOrigenHasta.fieldSetEstPre]
					}]
				}]
	})

	//--------------------------------------------------------------------------------------------
	
	fieldsetdos = new Ext.form.FieldSet({
		width: 550,
		height: 70,
		title: 'Intervalo de Cuentas',
		style: 'position:absolute;left:190px;top:'+(205+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:25px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 35,
						items: [{
								xtype: 'textfield',
								labelSeparator :'',
								fieldLabel: 'Desde',
								id: 'txtCuentasDesde',
								width: 140,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
					}]
				},botbusDesCuenta,
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:280px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 30,
						items: [{
								xtype: 'textfield',
								labelSeparator :'',
								fieldLabel: 'Hasta',
								id: 'txtCuentasHasta',
								width: 140,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},botbusHasCuenta]
  	})
	
	//--------------------------------------------------------------------------------------------

	fieldsetocho = new Ext.form.FieldSet({
		width: 700,
		height: 58,
		title: 'Organizaci&#243;n de las Fechas',
		style: 'position:absolute;left:90px;top:'+(275+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 50,
						items: [{
								xtype: "radiogroup",
								fieldLabel: '',
								labelSeparator:"",	
								columns: [200,200,200],
								id:'tipper',
								binding:true,
								hiddenvalue:'',
								defaultvalue:0,
								allowBlank:true,
								items: [{
								        boxLabel: 'Mensual',
								        name: 'nivel_reporte',
								        inputValue: '0',
								        checked:true,
								        listeners:{	
								        	'check': function (checkbox, checked){
												if(checked){
									        		fieldsetcinco.show();
									        		fieldsetseis.hide();
									        		fieldsetsiete.hide();
												}
								        	}
								        }
										},
								        {
										boxLabel: 'Bi-Mensual',
										name: 'nivel_reporte',
										inputValue: '1',
										listeners:{	
								        	'check': function (checkbox, checked){
									        	if(checked){
									        		fieldsetcinco.hide();
									        		fieldsetseis.show();
									        		fieldsetsiete.hide();
									        	}
							        		}
										}
										},
								        {
										boxLabel: 'Trimestral',
										name: 'nivel_reporte', 
										inputValue: '2',
										listeners:{	
								        	'check': function (checkbox, checked){
									        	if(checked){
									        		fieldsetcinco.hide();
									        		fieldsetseis.hide();
									        		fieldsetsiete.show();
									        	}
									        }
								        }
										}]
							}]
					}]
		}]
	})
	
	//--------------------------------------------------------------------------------------------

	fieldsetcinco = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: '',
		style: 'position:absolute;left:190px;top:'+(340+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:150px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 70,
						items: [combomensual]
					}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------

	fieldsetseis = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: '',
		style: 'position:absolute;left:190px;top:'+(340+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:150px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 70,
						items: [combobimensual]
					}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------

	fieldsetsiete = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: '',
		style: 'position:absolute;left:190px;top:'+(340+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:150px;top:10px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 70,
						items: [combotrimestral]
					}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------
	
	fieldsetcuatro = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Tipo de Impresion',
		style: 'position:absolute;left:190px;top:'+(460+obtenerPosicion())+'px',
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
	
	fieldsettres = new Ext.form.FieldSet({
		width: 700,
		height: 58,
		title: 'Nivel de Reporte',
		style: 'position:absolute;left:90px;top:'+(400+obtenerPosicion())+'px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:150px;top:10px',
				items: [{
						layout: "form",
						border: false,
						items: [{
								xtype: "radiogroup",
								fieldLabel: '',
								labelSeparator:"",	
								columns: [200,200],
								id:'nivelReport',
								binding:true,
								hiddenvalue:'',
								defaultvalue:0,
								allowBlank:true,
								items: [{
							        	boxLabel: 'Consolidado',
							        	name: 'nivel_reporte',
							        	inputValue: '1',
							        	checked:true
							        	},
								        {
						        		boxLabel: 'Detallado', 
						        		name: 'nivel_reporte',
						        		inputValue: '0'
						        		}]
								}]
						}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteEjeFinCau = new Ext.FormPanel({
		applyTo: 'formReporteEjeFinCau',
		width:965, //700
		height: 500,
		title: "<H1 align='center'>Ejecuci&#243;n Financiera de Presupuesto de Gasto - Causado</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fieldset,fieldsetdos,fieldsettres,fieldsetcuatro,fieldsetcinco,fieldsetseis,fieldsetsiete,fieldsetocho]
	});	
	fieldsetseis.hide();
	fieldsetsiete.hide();
	fromReporteEjeFinCau.doLayout();
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

function irImprimir(){

	var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
	var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
	var opcionimp = 'P';
	var valido = true;
	var cuentadesde = Ext.getCmp('txtCuentasDesde').getValue();
	var cuentahasta = Ext.getCmp('txtCuentasHasta').getValue();

	if(arrCodigosDesde[0] != '0000000000000000000000000') {
		if (!fieldSetEstOrigenDesde.validarEstructura()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar la estructura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if (!fieldSetEstOrigenHasta.validarEstructura()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	
	if(arrCodigosHasta[0] != '0000000000000000000000000') {
		if (!fieldSetEstOrigenHasta.validarEstructura()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar la estructura  !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if (!fieldSetEstOrigenDesde.validarEstructura()) {
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	
	if(cuentadesde>cuentahasta){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El rango de Busqueda por Cuenta presupuestaria no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((cuentadesde=="" && cuentahasta!="") || (cuentadesde!="" && cuentahasta==""))
	{
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Cuenta presupuestaria !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(valido){
		var datosReporte="";
		var tipper = 1;
		var periodo = '01';
		var consolidado = 1;
		if(Ext.getCmp('tipoimp').getValue()!=''){
			opcionimp=Ext.getCmp('tipoimp').getValue();
		}
		if(Ext.getCmp('tipper').items.items[0].checked){
			if(Ext.getCmp('fechamensual').getValue()!=''){
				periodo = Ext.getCmp('fechamensual').getValue();
			}
		}
		if(Ext.getCmp('tipper').items.items[1].checked){
			tipper = 2;
			periodo = '0102';
			if(Ext.getCmp('fechabimensual').getValue()!=''){
				periodo = Ext.getCmp('fechabimensual').getValue();
			}
		}
		if(Ext.getCmp('tipper').items.items[2].checked){
			tipper = 3;
			periodo = '010203';
			if(Ext.getCmp('fechatrimestral').getValue()!=''){
				periodo = Ext.getCmp('fechatrimestral').getValue();
			}
		}
		if(Ext.getCmp('nivelReport').items.items[1].checked){  
			consolidado = 0;
		}
		for ( var i = 0; i < arrCodigosDesde.length; i++) {
			if(arrCodigosDesde[i]=="0000000000000000000000000" || arrCodigosDesde[i]=="--"){
				arrCodigosDesde[i]="";
			}

			if(arrCodigosHasta[i]=="0000000000000000000000000" || arrCodigosHasta[i]=="--"){
				arrCodigosHasta[i]="";
			}		
		}
		if(empresa['estmodest']==1){
			var datosReporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
			+"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
			+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
			+"&txtcuentades="+cuentadesde+"&txtcuentahas="+cuentahasta
			+"&tipper="+tipper+"&periodo="+periodo+"&tperiodo="+""
			+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5]
			+"&consolidado="+consolidado;
		}
		else{
			var datosReporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
			+"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]
			+"&codestpro5="+arrCodigosDesde[4]+"&codestpro1h="+arrCodigosHasta[0]
			+"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
			+"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]
			+"&txtcuentades="+cuentadesde+"&txtcuentahas="+cuentahasta
			+"&tipper="+tipper+"&periodo="+periodo+"&tperiodo="+""
			+"&estclades="+arrCodigosDesde[5]+"&estclahas="+arrCodigosHasta[5]
			+"&consolidado="+consolidado;
		}
		imprimir(datosReporte,opcionimp);
	}
}

function imprimir(datos,opcion)
{	 
	if(opcion=='P'){
		pagina="reportes/sigesp_spg_rpp_ejecucion_financiera_gasto_causado.php"+datos;
	}
	else{
		pagina="reportes/sigesp_spg_rpp_ejecucion_financiera_gasto_causado_excel.php"+datos;
	}
	window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
}

