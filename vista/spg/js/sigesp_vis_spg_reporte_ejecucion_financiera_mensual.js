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

var fromReporteEjePreMen = null; //varibale para almacenar la instacia de objeto de formulario 
barraherramienta = true;
var fieldSetEstOrigenHasta = null;
var fieldSetEstOrigenDesde = null;
var	fromFechaMensual = null;
var	fromFechaBiMensual = null;
var	fromFechaTrimestral = null;
var	fromRangoFecha = null;
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
	
	//Datos para el formato de impresion
	var opcimp = [ [ 'PDF', 'P' ], 
	               [ 'EXCEL', 'E' ]/*,
	               [ 'GR&#225;FICOS', 'G' ]*/];
	
	var stOpcimp = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcimp
	});
	
	//-------------------------------------------------------------------------------------
	
	//Datos del nivel de cuentas
	var nivelcuentas = [ [ '1', '1' ], 
	                     [ '2', '2' ],
			             [ '3', '3' ],
			             [ '4', '4' ],
			             [ '5', '5' ],
			             [ '6', '6' ],
			             [ '7', '7' ]];
	
	var stNivelcuentas = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : nivelcuentas
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
	
	var	fromEstructura = new Ext.form.FieldSet({ 
			title:'',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 930,
			cls :'fondo',
			height: 275+obtenerPosicion(),
			items: [{	
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:10px;top:15px',
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
					style: 'position:absolute;left:470px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [fieldSetEstOrigenHasta.fsEstructura]
						}]
					}]
		})
	
	//--------------------------------------------------------------------------------------------
	
	var	fromNivelCuentas = new Ext.form.FieldSet({ 
			title:'Nivel de Cuentas',
			style: 'position:absolute;left:10px;top:370px',
			border:true,
			width: 930,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:30px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: 'combo',
									fieldLabel: 'Nivel',
									labelSeparator :'',
									id: 'nivelCtas',
									store : stNivelcuentas,
									editable : false,
									displayField : 'col',
									valueField : 'tipo',
									triggerAction : 'all',
									mode : 'local',
									emptyText:'----Seleccione----',
									listWidth:150,
									width:150,
								}]
							}]
					}]
			})
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromNivelReporte = new Ext.form.FieldSet({ 
			title:'Organizaci&#243;n de las Fechas',
			style: 'position:absolute;left:10px;top:435px',
			border:true,
			width: 930,
			cls :'fondo',
			height: 58,
			items:[{
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
									columns: [200,200,200,220],
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
														fromFechaMensual.show();
														fromFechaBiMensual.hide();
														fromFechaTrimestral.hide();
                                                                                                                fromRangoFecha.hide();
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
														fromFechaMensual.hide();
														fromFechaBiMensual.show();
														fromFechaTrimestral.hide();
                                                                                                                fromRangoFecha.hide();
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
														fromFechaMensual.hide();
														fromFechaBiMensual.hide();
														fromFechaTrimestral.show();
                                                                                                                fromRangoFecha.hide();
													}
												}
											}
											},
											{
											boxLabel: 'Rango de Fecha',
											name: 'nivel_reporte',
											inputValue: '3',
											listeners:{	
												'check': function (checkbox, checked){
													if(checked){
														fromFechaMensual.hide();
														fromFechaBiMensual.hide();
														fromFechaTrimestral.hide();
                                                                                                                fromRangoFecha.show();
													}
												}
											}
											}]
									}]
							}]
			}]
	})

	//--------------------------------------------------------------------------------------------

	fromFechaMensual = new Ext.form.FieldSet({ 
			title:'',
			style: 'position:absolute;left:10px;top:570px',
			border:true,
			width: 930,
			cls :'fondo',
			height: 48,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:80px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 70,
							items: [combomensual]
						}]
					}]
	})
	
	//--------------------------------------------------------------------------------------------

	fromFechaBiMensual = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:570px',
			border:true,
			width: 930,
			cls :'fondo',
			height: 48,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:80px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 70,
							items: [combobimensual]
						}]
					}]
	})
	
	//--------------------------------------------------------------------------------------------

	fromFechaTrimestral = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:570px',
			border:true,
			width: 930,
			cls :'fondo',
			height: 48,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:80px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 70,
							items: [combotrimestral]
						}]
			}]
		})

	//--------------------------------------------------------------------------------------------	
	
    fromRangoFecha = new Ext.form.FieldSet({
                title:'',
                style: 'position:absolute;left:10px;top:570px',
                border:true,
                width: 930,
                cls :'fondo',
                height: 48,
		items: [{
				layout:"column",
				defaults: {border: false},
				style: 'position:absolute;left:80px;top:15px',
				items:[{
						layout:"form",
						border:false,
						labelWidth:50,
						items:[{
								xtype:"datefield",
								labelSeparator :'',
								fieldLabel:"Desde",
								name:'Desde',
								id:'dtFechaDesde',
								allowBlank:true,
								width:100,
								binding:true,
								defaultvalue:'1900-01-01',
								hiddenvalue:'',
								allowBlank:false,
								value: '01/01/'+fecha.getFullYear(),
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
						}]
				},
				{
				layout:"column",
				defaults: {border: false},
				style: 'position:absolute;left:300px;top:10px',
				border:false,
				items:[{
						layout:"form",
						border:false,
						labelWidth:50,
						items:[{
								xtype:"datefield",
								labelSeparator :'',
								fieldLabel:"Hasta",
								name:'Hasta',
								id:'dtFechaHasta',
								allowBlank:true,
								width:100,
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
        
        
	var	fromTipoGrafico = new Ext.form.FieldSet({ 
			title:'Gr&#225;ficos',
			style: 'position:absolute;left:10px;top:600px',
			border:true,
			width: 930,
			cls :'fondo',
			height: 48,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:150px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [{
									xtype: "radiogroup",
									fieldLabel: '',
									labelSeparator:"",	
									columns: [100,100],
									id:'graficoReporte',
									binding:true,
									hiddenvalue:'',
									defaultvalue:0,
									allowBlank:true,
									items: [
									        {boxLabel: 'Torta', name: 'tipo_grafico',inputValue: '1',checked:true},
									        {boxLabel: 'Barras', name: 'tipo_grafico', inputValue: '0'}
									        ]
								}]
							}]
				}]
	})

	//--------------------------------------------------------------------------------------------
	
	var	fromTipoImpresion = new Ext.form.FieldSet({
			title:'Tipo de Impresion',
			style: 'position:absolute;left:10px;top:500px',
			border:true,
			width: 930,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:60px;top:10px',
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
									width:150,
									listeners: {
										'select': function(){	
											if(this.getValue()=='G'){
												fromTipoGrafico.show();
												//DESBLOQUEAR
											}
											else{
												fromTipoGrafico.hide();//BLOQUEAR
											}
										}
									}
								}]
							}]
					}]
	})
	
	//--------------------------------------------------------------------------------------------
	
	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteEjePreMen = new Ext.FormPanel({
		applyTo: 'formReporteEjePreMen',
		width:970, //700
		height: 500,
		title: "<H1 align='center'>Ejecuci&#243;n Presupuestaria Mensual de Gasto</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  
		items: [fromEstructura,
		        fromNivelCuentas,
		        fromNivelReporte,
		        fromFechaMensual,
		        fromFechaBiMensual,
		        fromFechaTrimestral,
                        fromRangoFecha,
		        fromTipoImpresion,
		        fromTipoGrafico]
	});	
	fromFechaBiMensual.hide();
	fromTipoGrafico.hide();  
	fromFechaTrimestral.hide();
        fromRangoFecha.hide();
	fromReporteEjePreMen.doLayout();
});	

	//-------------------------------------------------------------------------------------------------------------------------	

	function irImprimir(){
		
		var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
		var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
		var opcionimp = 'P';
		var valido = true;
                var fechaReporteDesde = fecha.format('Y-m-d');
                var fechaReporteHasta = fecha.format('Y-m-d');
                if(Ext.getCmp('tipper').items.items[3].checked)
                {                
                    var fechaReporteDesde = Ext.getCmp('dtFechaDesde').getValue().format('Y-m-d');
                    var fechaReporteHasta = Ext.getCmp('dtFechaHasta').getValue().format('Y-m-d');
                }
                 
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
		if((arrCodigosDesde[0]=='0000000000000000000000000' && arrCodigosHasta[0]!='0000000000000000000000000') || (arrCodigosDesde[0]!='0000000000000000000000000' && arrCodigosHasta[0]=='0000000000000000000000000')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if((arrCodigosDesde[1]=='0000000000000000000000000' && arrCodigosHasta[1]!='0000000000000000000000000') || (arrCodigosDesde[1]!='0000000000000000000000000' && arrCodigosHasta[1]=='0000000000000000000000000')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if((arrCodigosDesde[2]=='0000000000000000000000000' && arrCodigosHasta[2]!='0000000000000000000000000') || (arrCodigosDesde[2]!='0000000000000000000000000' && arrCodigosHasta[2]=='0000000000000000000000000')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if(empresa['estmodest']==2){
			if((arrCodigosDesde[3]=='0000000000000000000000000' && arrCodigosHasta[3]!='0000000000000000000000000') || (arrCodigosDesde[3]!='0000000000000000000000000' && arrCodigosHasta[3]=='0000000000000000000000000')){
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			if((arrCodigosDesde[4]=='0000000000000000000000000' && arrCodigosHasta[4]!='0000000000000000000000000') || (arrCodigosDesde[4]!='0000000000000000000000000' && arrCodigosHasta[4]=='0000000000000000000000000')){
				valido = false;
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		}
		if((arrCodigosDesde[5]=="" && arrCodigosHasta[5]!="") || (arrCodigosDesde[5]!="" && arrCodigosHasta[5]=="")){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Estrutura !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		if (Ext.getCmp('dtFechaDesde').getValue() > Ext.getCmp('dtFechaHasta').getValue())
		{
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Por favor verifique el intervalo de fechas !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		
		if(valido){
			var tipper = 1;
			var periodo = '01';
			if(Ext.getCmp('tipoimp').getValue()!=''){
				opcionimp=Ext.getCmp('tipoimp').getValue();
			}
			for ( var i = 0; i < arrCodigosDesde.length; i++) {
				if(arrCodigosDesde[i]=="0000000000000000000000000" || arrCodigosDesde[i]=="--"){
					arrCodigosDesde[i]="";
				}
				if(arrCodigosHasta[i]=="0000000000000000000000000" || arrCodigosHasta[i]=="--"){
					arrCodigosHasta[i]="";
				}		
			}
			var nivel = Ext.getCmp('nivelCtas').getValue();
			if(nivel==""){
				nivel='s1';

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
			if(Ext.getCmp('tipper').items.items[3].checked){
				tipper = 4;
				periodo = '';
			}			if(empresa['estmodest']==1){
				var datosReporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
				                  +"&codestpro3="+arrCodigosDesde[2]+"&codestpro1h="+arrCodigosHasta[0]
						  +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
				                  +"&cmbnivel="+nivel+"&txtcuentades="+arrCodigosDesde[7]
				                  +"&txtcuentahas="+arrCodigosHasta[7]+"&tipper="+tipper+"&periodo="+periodo
				                  +"&tperiodo="+""+"&txtcodfuefindes="+arrCodigosDesde[6]
				                  +"&txtcodfuefinhas="+arrCodigosHasta[6]+"&estclades="+arrCodigosDesde[5]
						  +"&estclahas="+arrCodigosHasta[5]+"&fechaReporteDesde="+fechaReporteDesde
                                                  +"&fechaReporteHasta="+fechaReporteHasta
			}
			else{
				var datosReporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
                                  +"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]
				                  +"&codestpro5="+arrCodigosDesde[4]+"&codestpro1h="+arrCodigosHasta[0]
				                  +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
						          +"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]
                                  +"&cmbnivel="+nivel+"&txtcuentades="+arrCodigosDesde[7]
                                  +"&txtcuentahas="+arrCodigosHasta[7]+"&tipper="+tipper+"&periodo="+periodo
                                  +"&tperiodo="+""+"&txtcodfuefindes="+arrCodigosDesde[6]
                                  +"&txtcodfuefinhas="+arrCodigosHasta[6]+"&estclades="+arrCodigosDesde[5]
				                  +"&estclahas="+arrCodigosHasta[5]+"&fechaReporteDesde="+fechaReporteDesde
                                                  +"&fechaReporteHasta="+fechaReporteHasta
			}
			imprimir(datosReporte,opcionimp);
		}
	}
	
	function obtenerPosicion(){
		if(empresa['numniv']=='3'){
			return 0;
		}
		else{
			return 80;
		}
	}

	function imprimir(datos,opcion)
	{
		if(opcion=='G'){
			if(Ext.getCmp('graficoReporte').items.items[0].checked){
				pagina = "reportes/sigesp_spg_rpp_ejecucion_financiera_mensual_torta.php"+datos;
			}
			else{
				pagina = "reportes/sigesp_spg_rpp_ejecucion_financiera_mensual_barra.php"+datos;
			}
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		}
		else{
			if(opcion=='P'){
				variable = 'EJECUCION MENSUAL';
				valor = 'sigesp_spg_rpp_ejecucion_financiera_mensual.php';
			}
			else{
				variable = 'EJECUCION MENSUAL XLS';
				valor = 'sigesp_spg_rpp_ejecucion_financiera_mensual_excel.php';
			}
			var myJSONObject =
			{
				'operacion'   : 'buscarFormato',
				'sistema'	  : 'SPG',
				'seccion'     : 'REPORTE',
				'variable'    : variable,
				'valor'		  : valor,
				'tipo'		  : 'C'
			};	
			var ObjSon=Ext.util.JSON.encode(myJSONObject);
			var parametros ='ObjSon='+ObjSon;
			Ext.Ajax.request(
			{
				url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
				params: parametros,
				method: 'POST',
				success: function (result, request)
				{ 
					formato = result.responseText;	
					pagina = "reportes/"+formato+datos;
					window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
				},
				failure: function (result, request){ 
					Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
				}
			})
		}
	}
	