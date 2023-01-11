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

var fromReporteOpexEsp = null;
barraherramienta = true;
var fromProveedor = null;
var fromBeneficiario = null;
var fecha = new Date();

Ext.onReady(function() {
Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//--------------------------------------------------------------------------------------------

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
	
	//--------------------------------------------------------------------------------------------

	//componente catalogo de cuentas presupuestarias
	var reCuentaPre = Ext.data.Record.create([
        {name: 'spg_cuenta'}, //campo obligatorio                             
        {name: 'denominacion'}, //campo obligatorio
        {name: 'status'}, //campo obligatorio
    ]);
	
	comcampocatcuentadesde = new com.sigesp.vista.comCatalogoCuentaSPG({
		idComponente:'spgdesde',
		anchofieldset: 900,
		reCatalogo: reCuentaPre,
		rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_comprobante.php',
		parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
		posicion:'position:absolute;left:25px;top:10px', 
		tittxt:'Desde',
		idtxt:'cuentadesde',
		campovalue:'spg_cuenta',
		anchoetiquetatext:60,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'denodesde',
		labelvalue:'',
		anchocoletiqueta:0.35, 
		anchoetiqueta:0,
		binding:'',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false, 
	});
	
	comcampocatcuentahasta = new com.sigesp.vista.comCatalogoCuentaSPG({
		idComponente:'spghasta',
		anchofieldset: 900,
		reCatalogo: reCuentaPre,
		rutacontrolador:'../../controlador/spg/sigesp_ctr_spg_comprobante.php',
		parametros: "ObjSon={'operacion': 'buscarCuentasPresupuestarias'",
		posicion:'position:absolute;left:650px;top:10px', 
		tittxt:'Hasta',
		idtxt:'cuentahasta',
		campovalue:'spg_cuenta',
		anchoetiquetatext:60,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'denohasta',
		labelvalue:'',
		anchocoletiqueta:0.35, 
		anchoetiqueta:0,
		binding:'',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false, 
	});
	
	//--------------------------------------------------------------------------------------------

	//Creando el campo de proveedor
	var reProveedor = Ext.data.Record.create([
		{name: 'cod_pro'}, //campo obligatorio                             
		{name: 'nompro'}, //campo obligatorio
		{name: 'dirpro'}, //campo obligatorio
		{name: 'rifpro'}, //campo obligatorio
		{name: 'tipconpro'} //campo adicional
	]);
		
	//componente catalogo de proveedores
	comcampocatproveedordesde = new com.sigesp.vista.comCatalogoProveedor({
		idComponente:'provdesde',
		anchofieldset: 850,
		reCatalogo: reProveedor,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
		parametros: "ObjSon={'operacion': 'buscarProveedores'",
		posicion:'position:absolute;left:35px;top:10px', 
		tittxt:'Desde',
		idtxt:'cod_prodes',
		campovalue:'cod_pro',
		anchoetiquetatext:50,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'nomprodes',
		labelvalue:'',
		anchocoletiqueta:0.55, 
		anchoetiqueta:0,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	
	comcampocatproveedorhasta = new com.sigesp.vista.comCatalogoProveedor({
		idComponente:'provhasta',
		anchofieldset: 850,
		reCatalogo: reProveedor,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
		parametros: "ObjSon={'operacion': 'buscarProveedores'",
		posicion:'position:absolute;left:660px;top:10px', 
		tittxt:'Hasta',
		idtxt:'cod_prohas',
		campovalue:'cod_pro',
		anchoetiquetatext:50,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'nomprohas',
		labelvalue:'',
		anchocoletiqueta:0.55, 
		anchoetiqueta:0,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	//fin componente catalogo de proveedores
	
	//--------------------------------------------------------------------------------------------
	
	//Creando el campo de beneficiario
	var reBeneficiario = Ext.data.Record.create([
		{name: 'ced_bene'}, //campo obligatorio                             
		{name: 'nombene'}, //campo obligatorio
	]);
		
	//componente catalogo de proveedores
	comcampocatbeneficiariodesde = new com.sigesp.vista.comCatalogoBeneficiario({
		idComponente:'benedesde',
		anchofieldset: 850,
		reCatalogo: reBeneficiario,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
		parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
		posicion:'position:absolute;left:35px;top:10px', 
		tittxt:'Desde',
		idtxt:'ced_benedes',
		campovalue:'ced_bene',
		anchoetiquetatext:50,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'nombenedes',
		labelvalue:'',
		anchocoletiqueta:0.55, 
		anchoetiqueta:0,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	
	comcampocatbeneficiariohasta = new com.sigesp.vista.comCatalogoBeneficiario({
		idComponente:'benehasta',
		anchofieldset: 850,
		reCatalogo: reBeneficiario,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
		parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
		posicion:'position:absolute;left:660px;top:10px', 
		tittxt:'Hasta',
		idtxt:'ced_benehas',
		campovalue:'ced_bene',
		anchoetiquetatext:50,
		anchotext:100,
		anchocoltext:0.20, 
		idlabel:'nombenehas',
		labelvalue:'',
		anchocoletiqueta:0.55, 
		anchoetiqueta:0,
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'---',
		allowblank:false,
		numFiltroNoVacio: 1
	});
	//fin componente catalogo de proveedores
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromEstructura = new Ext.form.FieldSet({ 
			title:'',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 200+obtenerPosicion(),
			items: [{	
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:10px;top:15px',
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
					style: 'position:absolute;left:465px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 50,
							items: [fieldSetEstOrigenHasta.fieldSetEstPre]
						}]
					}]

		})
	
	//--------------------------------------------------------------------------------------------	
	
	var	fromCuentaPre = new Ext.form.FieldSet({
			xtype:"fieldset", 
			title:'Intervalo de Cuentas',
			style: 'position:absolute;left:10px;top:300px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 68,
			items: [comcampocatcuentadesde.fieldsetCatalogo,comcampocatcuentahasta.fieldsetCatalogo]

	})
	
	//--------------------------------------------------------------------------------------------

	var	fromIntervaloFechas = new Ext.form.FieldSet({
			title:'Intervalo de Fechas',
			style: 'position:absolute;left:10px;top:380px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:35px;top:10px',
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
					style: 'position:absolute;left:660px;top:10px',
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
	
	//--------------------------------------------------------------------------------------------

	var fromEstado = new Ext.form.FieldSet({
			title:'Estado Presupuestario',
			style: 'position:absolute;left:10px;top:450px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:10px',
					items: [{
							layout: "form",
							border: false,
							items: [{
									xtype: "radiogroup",
									fieldLabel: '',
									labelSeparator:"",	
									columns: [220,220,180,180],
									id:'rdEstPre',
									binding:true,
									hiddenvalue:'',
									defaultvalue:0,
									allowBlank:true,
									items: [
								        {boxLabel: 'Pre-Compromiso', name: 'estado',inputValue: 'PC'},
								        {boxLabel: 'Comprometido', name: 'estado', inputValue: 'CP',checked:true},
								        {boxLabel: 'Causar', name: 'estado', inputValue: 'CS'},
								        {boxLabel: 'Pagar', name: 'estado', inputValue: 'PG'}
									]					
								}]
						}]
				}]
			})
	
	//--------------------------------------------------------------------------------------------

	var fromMontos = new Ext.form.FieldSet({
			title:'Monto de la Operaci&#243;n',
			style: 'position:absolute;left:10px;top:520px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 68,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:40px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 45,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Desde',
									id: 'montodes',
									width: 140,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
									listeners:{
										'blur':function(objeto){
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
										},
										'specialKey':function(objeto){
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
										},
										'focus':function(objeto){
											var numero = formatoNumericoEdicion(objeto.getValue());
											objeto.setValue(numero);
										}
										}
									}]
							}]
			    	},
			    	{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:660px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 45,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Hasta',
									id: 'montohas',
									width: 140,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789.');"},
									listeners:{
										'blur':function(objeto){
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
										},
										'specialKey':function(objeto){
											var numero = objeto.getValue();
											valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
											objeto.setValue(valor);
										},
										'focus':function(objeto){
											var numero = formatoNumericoEdicion(objeto.getValue());
											objeto.setValue(numero);
										}
									}
									}]
							}]
			    	}]
	})
	
	//--------------------------------------------------------------------------------------------

	var fromDescripcion = new Ext.form.FieldSet({
			title:'Concepto de la Operaci&#243;n',
			style: 'position:absolute;left:10px;top:595px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 68,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:40px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 80,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Descripci&#243;n',
									id: 'descripcion',
									width: 355,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
								}]
							}]
    			}]
	})

	//--------------------------------------------------------------------------------------------

	var fromTipo = new Ext.form.FieldSet({
			title:'Proveedor/Beneficiario',
			style: 'position:absolute;left:10px;top:670px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:150px;top:10px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 20,
							items: [{
									xtype: "radiogroup",
									fieldLabel: '',
									labelSeparator:"",	
									columns: [200,200,200],
									id:'rdFormato',
									binding:true,
									hiddenvalue:'',
									defaultvalue:0,
									allowBlank:true,
									items: [{
											boxLabel: 'Proveedor',
											name: 'formato',
											inputValue: '0',
											listeners:{	
									        	'check': function (checkbox, checked){
										        	if(checked){
										        		fromProveedor.show();
										        		fromBeneficiario.hide();
										        	}
										        }
									        }
											},
									        {
											boxLabel: 'Beneficiario',
											name: 'formato', 
											inputValue: '1',
											listeners:{	
									        	'check': function (checkbox, checked){
										        	if(checked){
										        		fromProveedor.hide();
										        		fromBeneficiario.show();
										        	}
										        }
									        }
											},
									        {
											boxLabel: 'Ninguno',
											name: 'formato',
											inputValue: '2',
											checked:true,
											listeners:{	
									        	'check': function (checkbox, checked){
										        	if(checked){
										        		fromBeneficiario.hide();
										        		fromProveedor.hide();
										        	}
										        }
								        	}
											}]					
								}]
						}]
			}]
	})

	//--------------------------------------------------------------------------------------------

	fromProveedor = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:740px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[comcampocatproveedordesde.fieldsetCatalogo,comcampocatproveedorhasta.fieldsetCatalogo]
	})
	
	//--------------------------------------------------------------------------------------------

	fromBeneficiario = new Ext.form.FieldSet({
			title:'',
			style: 'position:absolute;left:10px;top:740px',
			border:true,
			width: 925,
			cls :'fondo',
			height: 58,
			items:[comcampocatbeneficiariodesde.fieldsetCatalogo,comcampocatbeneficiariohasta.fieldsetCatalogo]
	})

	//------------------------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(480)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteOpexEsp = new Ext.FormPanel({
		applyTo: 'formReporteOpexEsp',
		width:980, 
		height: 450,
		title: "<H1 align='center'>OPERACI&#211;N POR ESPECIFICA</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',   
		items: [fromEstructura,
		        fromCuentaPre,
		        fromIntervaloFechas,
		        fromEstado,
		        fromMontos,
		        fromDescripcion,
		        fromTipo,
		        fromProveedor,
		        fromBeneficiario
		        ]
	});	
	fromBeneficiario.hide();
	fromProveedor.hide();
	fromReporteOpexEsp.doLayout();
});	

//------------------------------------------------------------------------------------------------------------

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
	var valido = true;
	var tipoproben = '-';
	var codprobendes = '';
	var codprobenhas = '';
	var orden = 'CP';
	var arrCodigosDesde = fieldSetEstOrigenDesde.obtenerArrayEstructura();
	var arrCodigosHasta = fieldSetEstOrigenHasta.obtenerArrayEstructura();
	var cuentadesde = Ext.getCmp('cuentadesde').getValue();
	var cuentahasta = Ext.getCmp('cuentahasta').getValue();
	var montodesde = Ext.getCmp('montodes').getValue();
	var montohasta = Ext.getCmp('montohas').getValue();
    var fecdes = Ext.getCmp('dtFechaDesde').getValue().format('Y-m-d');
    var fechas = Ext.getCmp('dtFechaHasta').getValue().format('Y-m-d');
    var radiouno= Ext.getCmp('rdEstPre'); 
    var concepto = Ext.getCmp('descripcion').getValue();
	
    if(radiouno.items.items[0].checked){
		orden = radiouno.items.items[0].inputValue;
	}
    if(radiouno.items.items[2].checked){
		orden = radiouno.items.items[0].inputValue;
	}
    if(radiouno.items.items[3].checked){
		orden = radiouno.items.items[0].inputValue;
	}
	if(fecdes>fechas){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por Fecha no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(montodes!='' && montohas!=''){
		montouno = parseFloat(ue_formato_operaciones(montodes));
		montodos = parseFloat(ue_formato_operaciones(montohas));
		if(montouno>montodos){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'El Rango de Busqueda por Monto de la Operaci&#243;n no es correcto !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	if((montodes=='' && montohas!='') || (montodes!='' && montohas=='')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Monto de la Operaci&#243;n!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(cuentadesde>cuentahasta){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El rango de Busqueda por Cuenta Presupuestaria no es correcto!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if((cuentadesde=='' && cuentahasta!='') || (cuentadesde!='' && cuentahasta=='')){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe completar el rango de Busqueda por Cuenta Presupuestaria!!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	
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
	
	if(Ext.getCmp('rdFormato').items.items[0].checked){
		tipoproben = 'P';
		codprobendes = Ext.getCmp('cod_prodes').getValue();
		codprobenhas = Ext.getCmp('cod_prohas').getValue();
		if((codprobendes!='' && codprobenhas=='') || (codprobendes=='' && codprobenhas!='')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Proveedor!!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	if(Ext.getCmp('rdFormato').items.items[1].checked){
		tipoproben = 'B';
		codprobendes = Ext.getCmp('ced_benedes').getValue();
		codprobenhas = Ext.getCmp('ced_benehas').getValue();
		if((codprobendes!='' && codprobenhas=='') || (codprobendes=='' && codprobenhas!='')){
			valido = false;
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe completar el rango de Busqueda por Beneficiario!!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
	if(valido){
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
						      +"&txtfecdes="+fecdes+"&rborden="+orden+"&txtfechas="+fechas
						      +"&txtprvbendes="+codprobendes+"&txtprvbenhas="+codprobenhas
						      +"&tipoprvben="+tipoproben+"&txtmondes="+montodesde+"&txtmonhas="+montohasta
						      +"&txtconcepto="+concepto+"&estclades="+arrCodigosDesde[5]
						      +"&estclahas="+arrCodigosHasta[5];
	  	}
		else{
	  		var datosReporte = "?codestpro1="+arrCodigosDesde[0]+"&codestpro2="+arrCodigosDesde[1]
				              +"&codestpro3="+arrCodigosDesde[2]+"&codestpro4="+arrCodigosDesde[3]
	  		                  +"&codestpro5="+arrCodigosDesde[4]+"&codestpro1h="+arrCodigosHasta[0]
						      +"&codestpro2h="+arrCodigosHasta[1]+"&codestpro3h="+arrCodigosHasta[2]
	  		                  +"&codestpro4h="+arrCodigosHasta[3]+"&codestpro5h="+arrCodigosHasta[4]
				              +"&txtcuentades="+cuentadesde+"&txtcuentahas="+cuentahasta
						      +"&txtfecdes="+fecdes+"&rborden="+orden+"&txtfechas="+fechas
						      +"&txtprvbendes="+codprobendes+"&txtprvbenhas="+codprobenhas
						      +"&tipoprvben="+tipoproben+"&txtmondes="+montodesde+"&txtmonhas="+montohasta
						      +"&txtconcepto="+concepto+"&estclades="+arrCodigosDesde[5]
						      +"&estclahas="+arrCodigosHasta[5];
	  	}
		var pagina = "reportes/sigesp_spg_rpp_operacion_por_especifica.php"+datosReporte;
		window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
	}
}
