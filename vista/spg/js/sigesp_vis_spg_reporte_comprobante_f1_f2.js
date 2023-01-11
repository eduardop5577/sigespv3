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

var fromReporteComprobantes = null; //variable para almacenar la instacia de objeto de formulario
barraherramienta = true;
var fecha = new Date();

Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//-------------------------------------------------------------------------------------
	
	//componente para el catalogo de cuentas presupuestarias
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
	//fin componente del catalogo de cuentas presupuestarias
	
	//Botones para la busqueda del intervalo de cuentas presupuestarias
	var botbusDesCuenta = new Ext.Button({
		id: 'botbusquedaDesdeCuenta',
		iconCls: 'menubuscar',
		disabled:true,
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
		disabled:true,
		style:'position:absolute;left:465px;top:10px',
		listeners:{
            'click' : function(boton){
				comcampocatcuentaspghasta.mostrarVentana();
           }
        }
	});	
	
	//-------------------------------------------------------------------------------------
	
	//componente para el catalogo de comprobantes
	var reCatComprobante = Ext.data.Record.create([
		{name: 'comprobante'}, //campo obligatorio                             
        {name: 'procede'}, //campo obligatorio
        {name: 'descripcion'}, //campo obligatorio
        {name: 'fecha'}, //campo obligatorio
        {name: 'cod_pro'}, //campo obligatorio
        {name: 'ced_bene'}, //campo obligatorio
        {name: 'monto'}, //campo obligatorio
	]);

	var comcampocatcomprobante = new com.sigesp.vista.comCatalogoComprobante({
		idComponente:'cmpuno',
		reCatalogo: reCatComprobante,
		valorStatus:'',
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcomprobante.php',
		parametros: "ObjSon={'operacion': 'buscarComprobantesPresupuestarios'",
		validarCuenta: false,
		soloCatalogo: true,
		arrSetCampo:[{campo:'txtComprobanteDesde',valor:'comprobante'},
		             {campo:'txtComprobanteHasta',valor:'comprobante'}],
		numFiltroNoVacio: 1,
		tipcom : 'SPG'
	});
	
	var comcampocatcmp = new com.sigesp.vista.comCatalogoComprobante({
		idComponente:'cmpdos',
		reCatalogo: reCatComprobante,
		valorStatus:'',
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcomprobante.php',
		parametros: "ObjSon={'operacion': 'buscarComprobantesPresupuestarios'",
		validarCuenta: true,
		soloCatalogo: true,
		arrSetCampo:[{campo:'txtComprobanteHasta',valor:'comprobante'}],
		numFiltroNoVacio: 1,
		tipcom : 'SPG'
	});
	//fin componente del catalogo de comprobantes
	
	//Botones para la busqueda del intervalo de comprobante
	var botbusDesCmp = new Ext.Button({
		id: 'botbusquedaDesde',
		iconCls: 'menubuscar',
		style:'position:absolute;left:215px;top:10px',
		listeners:{
            'click' : function(boton){
				comcampocatcomprobante.mostrarVentana();
           }
        }
	});	
	
	var botbusHasCmp = new Ext.Button({
		id: 'botbusquedaHasta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:465px;top:10px',
		listeners:{
            'click' : function(boton){
				comcampocatcmp.mostrarVentana();
           }
        }
	});	
	
	//-------------------------------------------------------------------------------------
	
	//componente para el catalogo de comprobante
	var comcampocatprocededesde = new com.sigesp.vista.comCatalogoComprobante({
		idComponente:'prouno',
		reCatalogo: reCatComprobante,
		valorStatus:'',
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcomprobante.php',
		parametros: "ObjSon={'operacion': 'buscarComprobantesPresupuestarios'",
		validarCuenta: false,
		soloCatalogo: true,
		arrSetCampo:[{campo:'txtProcedeDesde',valor:'procede'},
		             {campo:'txtProcedeHasta',valor:'procede'}],
		numFiltroNoVacio: 1
	});
	
	var comcampocatprocedehasta = new com.sigesp.vista.comCatalogoComprobante({
		idComponente:'prodos',
		reCatalogo: reCatComprobante,
		valorStatus:'',
		rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcomprobante.php',
		parametros: "ObjSon={'operacion': 'buscarComprobantesPresupuestarios'",
		validarCuenta: false,
		soloCatalogo: true,
		arrSetCampo:[{campo:'txtProcedeHasta',valor:'procede'}],
		numFiltroNoVacio: 1
	});
	//fin componente del catalogo de comprobantes
	
	//Botones para la busqueda de procedencia
	var botbusDesPro = new Ext.Button({
		id: 'botbusquedaProDesde',
		iconCls: 'menubuscar',
		style:'position:absolute;left:215px;top:10px',
		listeners:{
            'click' : function(boton){
				comcampocatprocededesde.mostrarVentana();
           }
        }
	});	

	var botbusHasPro = new Ext.Button({
		id: 'botbusquedaProHasta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:465px;top:10px',
		listeners:{
            'click' : function(boton){
            	comcampocatprocedehasta.mostrarVentana();
           }
        }
	});	
	
	//-------------------------------------------------------------------------------------
	
	fieldset = new Ext.form.FieldSet({
		width: 550,
		height: 70,
		title: 'Intervalo de Comprobante',
		style: 'position:absolute;left:190px;top:65px',
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
								id: 'txtComprobanteDesde',
								width: 140,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},botbusDesCmp,
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
								id: 'txtComprobanteHasta',
								width: 140,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},botbusHasCmp]
  	})
	
	//-------------------------------------------------------------------------------------
	
	fieldsetdos = new Ext.form.FieldSet({
		width: 550,
		height: 70,
		title: 'Intervalo de Procede',
		style: 'position:absolute;left:190px;top:135px',
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
								id: 'txtProcedeDesde',
								width: 140,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},botbusDesPro,
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
								id: 'txtProcedeHasta',
								width: 140,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
							}]
						}]
				},botbusHasPro]
  	})
	
	//-------------------------------------------------------------------------------------

	fieldsettres = new Ext.form.FieldSet({
		width: 550,
		height: 70,
		title: 'Intervalo de Cuentas',
		style: 'position:absolute;left:190px;top:205px',
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
	
	//-------------------------------------------------------------------------------------

	fieldsetcinco = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Intervalo de Fechas',
		style: 'position:absolute;left:190px;top:275px',
		cls :'fondo',
		items: [{
				layout:"column",
				defaults: {border: false},
				style: 'position:absolute;left:80px;top:10px',
				border:false,
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
	
	//-------------------------------------------------------------------------------------

	fieldsetseis = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Ordenado por',
		style: 'position:absolute;left:190px;top:335px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:30px;top:10px',
				items: [{
						layout: "form",
						border: false,
						items: [{
								xtype: "radiogroup",
								fieldLabel: '',
								labelSeparator:"",	
								columns: [200,200],
								id:'rdOrden',
								binding:true,
								hiddenvalue:'',
								defaultvalue:0,
								allowBlank:true,
								items: [{
										boxLabel: 'Natural',
										name: 'orden',
										inputValue: '1',
										checked:true
										},
								        {
										boxLabel: 'Cuenta',
										name: 'orden',
										inputValue: '0',
										listeners:{	
								        	'check': function (checkbox, checked){
									        	if(checked){
				//					        		Ext.getCmp('codartdes').setValue('');
				//					        		Ext.getCmp('codarthas').setValue('');
									        	}
								        	}
								        }
									}]
								}]
						}]
				}]
	})

	//-------------------------------------------------------------------------------------
	
	fieldsetcuatro = new Ext.form.FieldSet({
		width: 550,
		height: 58,
		title: 'Tipo Formato',
		style: 'position:absolute;left:190px;top:5px',
		cls :'fondo',
		items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:30px;top:10px',
				items: [{
						layout: "form",
						border: false,
						//labelWidth: 20,
						items: [{
								xtype: "radiogroup",
								fieldLabel: '',
								labelSeparator:"",	
								columns: [200,200],
								id:'rdFormato',
								binding:true,
								hiddenvalue:'',
								defaultvalue:0,
								allowBlank:true,
								items: [{
										boxLabel: 'Formato #1',
										name: 'formato',
										inputValue: '0',
										checked:true,
										listeners:{	
											'check': function (checkbox, checked){
												if(checked){
									        		Ext.getCmp('botbusquedaDesde').enable();
									        		Ext.getCmp('botbusquedaHasta').enable();
									        		Ext.getCmp('botbusquedaProDesde').enable();
									        		Ext.getCmp('botbusquedaProHasta').enable();
									        		Ext.getCmp('txtCuentasDesde').reset();
									        		Ext.getCmp('botbusquedaDesdeCuenta').disable();
									        		Ext.getCmp('txtCuentasHasta').reset();
									        		Ext.getCmp('botbusquedaHastaCuenta').disable();
									        	}
											}
										}
										},
										{
										boxLabel: 'Formato #2',
										name: 'formato',
										inputValue: '1',
										listeners:{	
								        	'check': function (checkbox, checked){
									        	if(checked){
									        		Ext.getCmp('txtComprobanteDesde').reset();
									        		Ext.getCmp('botbusquedaDesde').disable();
									        		Ext.getCmp('txtComprobanteHasta').reset();
									        		Ext.getCmp('botbusquedaHasta').disable();
									        		Ext.getCmp('txtProcedeDesde').reset();
									        		Ext.getCmp('botbusquedaProDesde').disable();
									        		Ext.getCmp('txtProcedeHasta').reset();
									        		Ext.getCmp('botbusquedaProHasta').disable();
									        		Ext.getCmp('botbusquedaDesdeCuenta').enable();
									        		Ext.getCmp('botbusquedaHastaCuenta').enable();
									        	}
											}
										}
										}]					
								}]
						}]
				}]
	})
	
	//--------------------------------------------------------------------------------------------
	
	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteComprobantes = new Ext.FormPanel({
		applyTo: 'formReporteComprobantes',
		width:965, //700
		height: 500,
		title: "<H1 align='center'>Listado de Comprobantes</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fieldset,fieldsetdos,fieldsettres,fieldsetcuatro,fieldsetcinco,fieldsetseis]
	});
	fromReporteComprobantes.doLayout();
});	

//--------------------------------------------------------------------------------------------

function irImprimir()
{
	var valido = true;
	if((Ext.getCmp('txtComprobanteDesde').getValue()=='' && Ext.getCmp('txtComprobanteHasta').getValue()!='') ||  (Ext.getCmp('txtComprobanteDesde').getValue()!='' && Ext.getCmp('txtComprobanteHasta').getValue()=='')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe Completar el Rango de Busqueda por Comprobante !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(Ext.getCmp('txtComprobanteDesde').getValue()!='' && Ext.getCmp('txtComprobanteHasta').getValue()!=''){
		if(Ext.getCmp('txtComprobanteDesde').getValue()>Ext.getCmp('txtComprobanteHasta').getValue()){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El Rango de Busqueda por Comprobante no es correcto !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	if((Ext.getCmp('txtProcedeDesde').getValue()=='' && Ext.getCmp('txtProcedeHasta').getValue()!='') ||  (Ext.getCmp('txtProcedeDesde').getValue()!='' && Ext.getCmp('txtProcedeHasta').getValue()=='')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe Completar el Rango de Busqueda por Procedencia !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(Ext.getCmp('txtProcedeDesde').getValue()!='' && Ext.getCmp('txtProcedeHasta').getValue()!=''){
		if(Ext.getCmp('txtProcedeDesde').getValue()>Ext.getCmp('txtProcedeHasta').getValue()){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El Rango de Busqueda por procedencia no es correcto !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	if((Ext.getCmp('txtCuentasDesde').getValue()=='' && Ext.getCmp('txtCuentasHasta').getValue()!='') ||  (Ext.getCmp('txtCuentasDesde').getValue()!='' && Ext.getCmp('txtCuentasHasta').getValue()=='')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe Completar el Rango de Busqueda por Cuenta Presupuestaria !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(Ext.getCmp('txtCuentasDesde').getValue()!='' && Ext.getCmp('txtCuentasHasta').getValue()!=''){
		if(Ext.getCmp('txtCuentasDesde').getValue()>Ext.getCmp('txtCuentasHasta').getValue()){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El Rango de Busqueda por Cuenta Presupuestaria no es correcto !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	if((Ext.getCmp('dtFechaDesde').getValue()=='' && Ext.getCmp('dtFechaHasta').getValue()!='') ||  (Ext.getCmp('dtFechaDesde').getValue()!='' && Ext.getCmp('dtFechaHasta').getValue()=='')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe Completar el Rango de Busqueda por Fecha !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(Ext.getCmp('dtFechaDesde').getValue()!='' && Ext.getCmp('dtFechaHasta').getValue()!=''){
		if(Ext.getCmp('dtFechaDesde').getValue()>Ext.getCmp('dtFechaHasta').getValue()){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El Rango de Busqueda por Fecha no es correcto !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	if(valido){
		if(Ext.getCmp('rdFormato').items.items[0].checked){
			var orden = '';
			var cmpdesde = Ext.getCmp('txtComprobanteDesde').getValue();
			var cmphasta = Ext.getCmp('txtComprobanteHasta').getValue();
			var procededesde = Ext.getCmp('txtProcedeDesde').getValue();
			var procedehasta = Ext.getCmp('txtProcedeHasta').getValue();
			var fechadesde = Ext.getCmp('dtFechaDesde').getValue().format('d-m-Y');
			var fechahasta = Ext.getCmp('dtFechaHasta').getValue().format('d-m-Y');
			if(Ext.getCmp('rdOrden').items.items[0].checked){
				orden='N';
			}
			else{
				orden='C';
			}
			var datosReporte = "?txtcompdes="+cmpdesde+"&txtcomphas="+cmphasta+"&txtprocdes="+procededesde
			+"&txtprochas="+procedehasta+"&txtfecdes="+fechadesde+"&rborden="+orden
			+"&txtfechas="+fechahasta;
			buscarformato('COMPROBANTE_FORMATO1','sigesp_spg_rpp_comprobante_formato1.php',datosReporte,'1');
		}
		else{
			var orden = '';
			var cuentadesde = Ext.getCmp('txtCuentasDesde').getValue();
			var cuentahasta = Ext.getCmp('txtCuentasHasta').getValue();
			var fechadesde = Ext.getCmp('dtFechaDesde').getValue().format('d-m-Y');
			var fechahasta = Ext.getCmp('dtFechaHasta').getValue().format('d-m-Y');
			if(Ext.getCmp('rdOrden').items.items[0].checked){
				orden='N';
			}
			else{
				orden='C';
			}
			var datosReporte = "?txtcuentades="+cuentadesde+"&txtcuentahas="+cuentahasta
			+"&txtfecdes="+fechadesde+"&rborden="+orden+"&txtfechas="+fechahasta;
			buscarformato('COMPROBANTE_FORM2','sigesp_spg_rpp_comprobante_formato2.php',datosReporte,'2');
		}
	}
}

function buscarformato(variable,nombreArchivo,datosReporte,tipoformato)
{
	if(tipoformato=='1'){
		var myJSONObject =
		{
				'operacion': 'buscarFormato',
				'sistema'  : 'SPG',
				'seccion'  : 'REPORTE',
				'variable' : variable,
				'valor'	   : nombreArchivo,
				'tipo'	   : 'C'
		};	
		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/spg/sigesp_ctr_spg_mod_comprobante.php',
			params: parametros,
			method: 'POST',
			success: function (result, request)
			{ 
				formato = result.responseText;	
				pagina="reportes/"+formato+datosReporte;
				window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
			},
			failure: function (result, request){ 
				Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
			}
		});
	}
	else{
		pagina="reportes/sigesp_spg_rpp_comprobante_formato2.php"+datosReporte;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}