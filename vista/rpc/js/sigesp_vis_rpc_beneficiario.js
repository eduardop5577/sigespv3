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

var plbeneficiario      = null;  //instancia del formulario de beneficiario
var Actualizar          = null;
barraherramienta    = true;
var elimino = false;

Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//-------------------------------------------------------------------------------------------------------------------------	
	var objetodedxben={"raiz":[{"codded":'',"dended":''}]};	

	var reDeducciones = Ext.data.Record.create([
	                                            {name: 'dended'},
	                                            {name: 'codded'}
	                                            ]);

	var dsDeducciones =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDeducciones)
	});

	datastorededxproveliminada = new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(objetodedxben),		
		reader: new Ext.data.JsonReader({
			root: 'raiz',                
			id: "id"   
		},reDeducciones)
	});	
	
	var cmDeducciones = new Ext.grid.ColumnModel([
	                                              new Ext.grid.CheckboxSelectionModel(),
	                                              {header: "Codigo", width: 60, sortable: true, dataIndex: 'codded'},
	                                              {header: "Descripcion ", width: 100, sortable: true, dataIndex: 'dended'},                   
	                                              ]);

	//creando datastore y columnmodel para la grid de creditos
	gridDeducciones = new Ext.grid.EditorGridPanel({
		width:800,
		height:400,
		frame:true,
		title:"<H1 align='center'>Deducciones</H1>",
		style: 'position:absolute;left:15px;top:50px',
		autoScroll:true,
		border:true,
		ds: dsDeducciones,
		cm: cmDeducciones,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true},
		tbar:[{
				text:'Agregar Deducción',
				iconCls:'agregar',
				handler: agregarDeduccion,
			},
			'-',
			{
				text:'Eliminar fila',
				iconCls:'remover',
				handler: eliminar_grid_ded
			}]
	});


	//Creando el campo de cuentas contables para las solicitudes a pagar
	var reCuenta = Ext.data.Record.create([
			{name: 'sc_cuenta'},
			{name: 'denominacion'},
      		{name: 'status'} //campo obligatorio
	]);
		

	
	
		//-------------------------------------------------------------------------------------------------------------------------	
	//Creando el campo de banco sigecof
	var reg_banco_sigecof = Ext.data.Record.create([
                            {name: 'codbansig'},
                            {name: 'denbansig'}
                            ]);

	var dsbancosig =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reg_banco_sigecof)
	});

	var colmodelcatbancosig = new Ext.grid.ColumnModel([
                                {header: "<H1 align='center'>Código</H1>", width: 20, sortable: true,   dataIndex: 'codbansig'},
                                {header: "<H1 align='center'>Denominación</H1>", width: 40, sortable: true, dataIndex: 'denbansig'}
                                ]);
	//fin creando datastore y columnmodel para el catalogo de bancos sigecof

	//componente campocatalogo para el campo banco
	comcampocatbancosig = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Catalogo de Bancos Sigecof</H1>",
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		anchofieldset: 850,
		datosgridcat: dsbancosig,
		colmodelocat: colmodelcatbancosig, 
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_bansig'",
		arrfiltro:[{etiqueta:'Código',id:'codibansig',valor:'codbansig'},
		           {etiqueta:'Descripción',id:'desbansig',valor:'denbansig'}],
		           posicion:'position:absolute;left:80px;top:515px',
		           tittxt:'Banco SIGECOF',
		           idtxt:'codbansig',
		           campovalue:'codbansig',
		           anchoetiquetatext:145,
		           anchotext:130,
		           anchocoltext:0.34,
		           idlabel:'denbansig',
		           labelvalue:'denbansig',
		           anchocoletiqueta:0.55,
		           anchoetiqueta:250,
		           tipbus:'P',
		           binding:'C',
		           typeAhead: true,
		           binding:true,
		           allowBlank:true,
		           defaultvalue:'---',
	});
	//fin componente para el campo banco sigecof*/
	
	//Creando el campo de cuentas contables para las solicitudes a pagar
	var reg_cta_contable_pag = Ext.data.Record.create([
			{name: 'sc_cuenta'},
			{name: 'denominacion'}
	]);
	
	var dsctacontpag =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reg_cta_contable_pag)
	});
						
	var colmodelcatctacontpag = new Ext.grid.ColumnModel([
          	{header: "<H1 align='center'>Código</H1>", width: 20, sortable: true,   dataIndex: 'sc_cuenta'},
          	{header: "<H1 align='center'>Denominación</H1>", width: 40, sortable: true, dataIndex: 'denominacion'}
	]);
	//fin del campo de cuentas contables para las solicitudes a pagar
	
	//componente campocatalogo para el campo de cuentas contables para las solicitudes a pagar
	comcampocatctacontpag = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Catálogo de Cuentas Contables</H1>",
		anchoformbus: 450,
		altoformbus:140,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		anchofieldset: 1100,
		posbotbus: 470,
		datosgridcat: dsctacontpag,
		colmodelocat: colmodelcatctacontpag,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_ctacontpag'",
		arrfiltro:[{etiqueta:'Código',id:'sc_ccuenta',valor:'sc_cuenta',longitud:'25',ancho:200},
		           {etiqueta:'Descripción',id:'d_denominacion',valor:'denominacion',longitud:'254',ancho:250}],
		posicion:'position:absolute;left:80px;top:545px',
		tittxt:'(*)Cuenta Contable para el registro de las solicitudes por pagar',
		idtxt:'sc_cuenta',
		campovalue:'sc_cuenta',
		anchoetiquetatext:240,
		anchotext:120,
		anchocoltext:0.34,
		idlabel:'denominacion',
		labelvalue:'denominacion',
		anchocoletiqueta:0.45,
		anchoetiqueta:240,
		tipbus:'P',
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:false
	});
	//fin componente para el campo de cuentas contables para las solicitudes a pagar
	
	
//-------------------------------------------------------------------------------------------------------------------------	
	//Creando el campo de cuentas contables alterna para las solicitudes a pagar
	var reg_cta_contable_rec = Ext.data.Record.create([
			{name: 'sc_cuentarecdoc'},
			{name: 'denominacion_rec'}
	]);
	
	var dsctacontrec =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"},reg_cta_contable_rec)
	});
						
	var colmodelcatctacontrec = new Ext.grid.ColumnModel([
          	{header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'sc_cuentarecdoc'},
          	{header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'denominacion_rec'}
	]);
	//fin del campo de cuentas contables para las solicitudes a pagar
	
	//componente campocatalogo para el campo de cuentas contables para las solicitudes a pagar
	comcampocatctacontrec = new com.sigesp.vista.comCampoCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Cuentas Contables</H1>",
		anchoformbus: 450,
		altoformbus:140,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		anchofieldset: 1100,
		posbotbus: 470,
		datosgridcat: dsctacontrec,
		colmodelocat: colmodelcatctacontrec,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		parametros: "ObjSon={'operacion': 'catalogo_ctacontrec'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'sc_ccuentarecdoc',valor:'sc_cuentarecdoc',longitud:'25',ancho:200},
		           {etiqueta:'Descripci&#243;n',id:'d_denominacion_rec',valor:'denominacion_rec',longitud:'254',ancho:250}],
		posicion:'position:absolute;left:80px;top:580px',
		tittxt:' Cuenta Contable alterna de beneficiario',
		idtxt:'sc_cuentarecdoc',
		campovalue:'sc_cuentarecdoc',
		anchoetiquetatext:240,
		anchotext:120,
		anchocoltext:0.34,
		idlabel:'denominacion_rec',
		labelvalue:'denominacion_rec',
		anchocoletiqueta:0.45,
		anchoetiqueta:240,
		tipbus:'P',
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:true
	});
	//fin componente para el campo de cuentas contables alterna para las solicitudes a pagar

//-------------------------------------------------------------------------------------------------------------------------	
	
	
//	-------------------------------------------------------------------------------------------------------------------------
	//Combos relacionado con pais-estado-municipio-arroquia
	//Creacion del combo pais
	RecordDef = Ext.data.Record.create([
	                                    {name:'codpai'},
	                                    {name: 'despai'}
	                                    ]);
                

	DataStorePais =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({
			root: 'raiz',              
			id: "id"   
		},
		RecordDef
		)
	});

	ComboPais = new Ext.form.ComboBox({
		store :DataStorePais,
		fieldLabel:'País',
		displayField:'despai',
		valueField:'codpai',
		name: 'pais',
		id:'codpai',
		width:180,
		listWidth: 180, 
		typeAhead: true,
		selectOnFocus:true,
		binding:true,
		allowBlank:true,
		defaultvalue:'---',
		mode:'local',
		triggerAction:'all',
		listeners: {
			'change': function(combo, nuevovalor,antiguovalor)
			{
				if(nuevovalor != antiguovalor)
				{
					if(String(Ext.getCmp('codest').getValue()) != "")
					{
						Ext.getCmp('codest').setValue('');
						Ext.getCmp('codmun').setValue('');
						Ext.getCmp('codest').setValue('');
						Ext.getCmp('codpar').setValue('');
						Ext.getCmp('codcom').setValue('');
						Ext.getCmp('nomcom').setValue('');
						Ext.getCmp('codest').valor=0;
						Ext.getCmp('codmun').valor=0;
						Ext.getCmp('codpar').valor=0;
						codest="";
						codmun="";
						codpar="";
					}
				}
			}
		}
	})//Fin de combo pais

//	Creaciï¿½n del combo estado

	RecordDefes = Ext.data.Record.create([
	                                      {name: 'codpai'},
	                                      {name: 'codest'},
	                                      {name: 'desest'}
	                                      ]);

	DataStoreEstado =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
		},
		RecordDefes
		)				
	});
	Comboest = new Ext.form.ComboBox({
		store: DataStoreEstado,
		fieldLabel:'Estado',
		displayField:'desest',
		valueField:'codest',
		name: 'estado',
		width:180,
		listWidth: 180, 
		id:'codest',
		typeAhead: true,
		binding:true,
		defaultvalue:'---',
		allowBlank:true,
		selectOnFocus:true,
		mode:'local',
		triggerAction:'all',
		valor:'',
		listeners: {
			'change': function(combo, nuevovalor,antiguovalor)
			{
				if(nuevovalor != antiguovalor)
				{
					if(String(Ext.getCmp('codmun').getValue()) != "")
					{
						Ext.getCmp('codmun').setValue('');
						Ext.getCmp('codest').setValue('');
						Ext.getCmp('codpar').setValue('');
						Ext.getCmp('codcom').setValue('');
						Ext.getCmp('nomcom').setValue('');
						Ext.getCmp('codest').valor=0;
						Ext.getCmp('codmun').valor=0;
						Ext.getCmp('codpar').valor=0;
						codmun="";
						codpar="";
					}
				}
			}
		}
	})
	///fin combo estado

//	Creacion de combo municipio
	RecordDefmun = Ext.data.Record.create([
                   {name: 'codpai'},
                   {name: 'codest'},
                   {name: 'codmun'},
                   {name: 'denmun'}
                   ]);

	DataStoreMunicipio =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({
			root: 'raiz',               
			id: "id"   
		},
		RecordDefmun
		)
	});

	Combomun = new Ext.form.ComboBox({
		store:DataStoreMunicipio,
		fieldLabel:'Municipio',
		displayField:'denmun',
		valueField:'codmun',
		name: 'municipio',
		width:180,
		listWidth: 180, 
		id:'codmun',
		listWidth: 180,
		typeAhead: true,
		mode:'local',
		selectOnFocus:true,
		binding:true,
		defaultvalue:'---',
		allowBlank:true,
		triggerAction:'all',
		valor:'',
		listeners: {
			'change': function(combo, nuevovalor,antiguovalor)
			{
				if(nuevovalor != antiguovalor)
				{
					if(String(Ext.getCmp('codpar').getValue()) != "")
					{
						Ext.getCmp('codpar').setValue('');
						Ext.getCmp('codcom').setValue('');
						Ext.getCmp('nomcom').setValue('');
						Ext.getCmp('codpar').valor=0;
						codpar="";
					}
				}
			}
		}
	})
	//Fin de combo municipio

//	Creacion de combo parroquia
	RecordDefparroquia = Ext.data.Record.create([
                         {name: 'codpai'},
                         {name: 'codest'},
                         {name: 'codmun'},
                         {name: 'codpar'},
                         {name: 'denpar'}
                         ]);

	DataStoreParroquia =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({
			root: 'raiz',               
			id: "id"   
		},
		RecordDefparroquia			     
		)
	});

	Comboparroquia = new Ext.form.ComboBox({
		store: DataStoreParroquia,
		fieldLabel:'Parroquia',
		displayField:'denpar',
		valueField:'codpar',
		width:180,
		id:'codpar',
		listWidth: 180,
		typeAhead: true,
		selectOnFocus:true,
		binding:true,
		defaultvalue:'---',
		allowBlank:true,
		mode:'local',
		triggerAction:'all',
		valor:'',
		listeners: {
			'change': function(combo, nuevovalor,antiguovalor)
			{
				if(nuevovalor != antiguovalor)
				{
					if(String(Ext.getCmp('codcom').getValue()) != "")
					{
						Ext.getCmp('codcom').setValue('');
						Ext.getCmp('nomcom').setValue('');
					}
				}
			}
		}
	});
	//Fin de combo parroquia

	var myJSONObject ={
			"operacion": 'catalogocombopais'
	};	
	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function (resultado, request) { 
			var datos = resultado.responseText;  
			if(datos!='')
			{
				var DatosNuevo = eval('(' + datos + ')');
				DataStorePais.loadData(DatosNuevo);
			}

		}//fin de success
	});//fin de ajax request	
	//-------------------------------------------------------------------------------------------------------------------------

	//creando datastore y columnmodel para bancos
	var rebanco = Ext.data.Record.create([
	                                      {name: 'codban'},
	                                      {name: 'nomban'}
	                                      ]);

	var dsbanco =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},rebanco)
	});

	function buscarBancos(){

		var myJSONObject ={
				"operacion":"buscarbanco" 
		};

		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) { 
			var datosBanco = eval('(' + result.responseText + ')');
			dsbanco.loadData(datosBanco);
		},
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
		});		

	}

	buscarBancos(); 

	//creando datastore y columnmodel para el  tipo de cuentas
	var reCuentas = Ext.data.Record.create([
                    {name: 'codtipcta'},
                    {name: 'nomtipcta'}
                    ]);

	var dsCuentas =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuentas)
	});
	function tipoCuenta(){

		var myJSONObject ={
				"operacion":"buscarCuenta" 
		};

		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) { 
				var datosCuenta = eval('(' + result.responseText + ')');
				dsCuentas.loadData(datosCuenta);
			},
			failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
			}
		});		

	}
	tipoCuenta();

	//creando Simpledatastore y columnmodel para contribuyente
	var arregloContribuyente = [
	                            ['-','-- Seleccione --'],
	                            ['F','Formal'],
	                            ['O','Ordinario']
	                            ]; // Arreglo que contiene los Documentos que se pueden controlar

	var dataStoreContribuyente = new Ext.data.SimpleStore({
		fields: ['codigo', 'denominacion'],
		data : arregloContribuyente // Se asocian los documentos disponibles
	});

//	Creacion del formulario
	var Xpos = ((screen.width/2)-(450));
	plbeneficiario = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 900,
		height: 500,
		title: "<H1 align='center'>Registro de Beneficiario</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
		items: [{
				xtype:"tabpanel",
				activeTab:0,
				deferredRender:false,
				enableTabScroll:true,
				autoScroll:true,
				width:885,
				border:false,
				frame:true,
				id:"tabfichaben",
				items:[{ 
						title:"Datos Básicos",
						labelWidth:150,
						layout:"form",
						frame:true,
						height:840,//Alto del Tab
						width:880,
						id:'tabficdatbas',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 200,
								items: [{
										xtype: 'label',
										text: 'Los campos en (*) son necesarios para incluir al Beneficiario',					
										id: 'label',
										width: 200							
									}]
								},
								{			
								layout: "column",
								defaults: {border: false},
								style: 'position:absolute;left:470px;top:5px',
								items: [{
										layout: "form",
										border: false,
										labelWidth: 150,
										items: [{
												xtype: 'textfield',
												labelSeparator :'',
												style:'font-weight: bold; border:none;background:#f1f1f1',
												fieldLabel: 'Fecha de Registro',
												id: 'fecregben',
												width: 100,
												value: obtenerFechaActual(),
												binding:true,
												hiddenvalue:'',
												defaultvalue:''											
											}]
										}]
								},
								{
								layout: "form",
								border: false,
								labelWidth: 130,
								columnWidth: 0.5,
								height:800, //Alto del contenido del Tab
								items: [{
										xtype:"fieldset", 
										title:'Datos Basicos del Beneficiario',
										style: 'position:absolute;left:60px;top:15px',
										border:true,
										width: 750,
										cls :'fondo',
										height: 350,
										items:[{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:25px;top:20px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 150,
														items:[{
																xtype: "radiogroup",
																fieldLabel: "Nacionalidad",
																labelSeparator:":",
																binding:true,
																hiddenvalue:'',
																defaultvalue:'',	
																columns: [200,200],
																id:'nacben',
																items: [
																        {boxLabel: 'Venezolano', name: 'nacionalidad',inputValue: 'V',checked:true},
																        {boxLabel: 'Extranjero', name: 'nacionalidad', inputValue: 'E'}
																        ]
															}]
													}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:25px;top:50px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 150,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: '(*)Cédula',
																name: 'cedula',
																id: 'ced_bene',
																width: 200,
																binding:true,
																hiddenvalue:'',
																defaultvalue:'',
																allowBlank:false,
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', length: '10', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"},
																listeners:{
																	'blur' : function(campo)
																	{
																	uf_verificar_cedula(campo.getValue());
																	}
																}
															}]
													}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:25px;top:80px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 150,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Pasaporte',
																name: 'pasasporte',
																id: 'numpasben',
																width: 200,
																binding:true,
																hiddenvalue:'',
																defaultvalue:'',
																allowBlank:true,
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"}
															}]
													}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:25px;top:110px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 150,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'R.I.F',
																name: 'rif',
																id: 'rifben',
																width: 200,
																binding:true,
																hiddenvalue:'',
																defaultvalue:'',
																autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '12'},
																allowBlank:true,
																listeners:{
																			'blur' : function(campo)
																			{
																				var regExPattern = /^[JGVEC]-\d{8}-\d$/
																				if (!campo.getValue().match(regExPattern)) 
																				{
																						Ext.Msg.show
																						({
																							title:'Advertencia',
																							msg: 'El formato del RIF es incorrecto, use [JGVEC]-[99999999]-[9]',
																							buttons: Ext.Msg.OK,
																							icon: Ext.MessageBox.WARNING
																						});
																				} 
																				else
																				{
																					uf_verificar_rif(campo.getValue());
																				}
																			}
																}
															}]
													}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:25px;top:140px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 150,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: '(*)Nombre',
																name: 'nombre',
																id: 'nombene',
																width: 350,
																binding:true,
																hiddenvalue:'',
																defaultvalue:'',
																allowBlank:false,
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzñ ABCDEFGHIJKLMNOPQRSTUVWXYZÑ 123456789');"}
						
															}]
														}]
												}, 
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:25px;top:170px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 150,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: '(*)Apellido',
																name: 'apellido',
																id: 'apebene',
																width: 350,
																binding:true,
																hiddenvalue:'',
																defaultvalue:'',
																allowBlank:false,
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzñ ABCDEFGHIJKLMNOPQRSTUVWXYZÑ 123456789 -/.&$,;:');"}
						
																}]
														}]
												},  
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:25px;top:200px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 150,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: '(*)Dirección',
																name: 'direccion',
																id: 'dirbene',
																width: 500,
																binding:true,
																hiddenvalue:'',
																defaultvalue:'',
																allowBlank:false,
																maxLength: 150,
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '500', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzñ ABCDEFGHIJKLMNOPQRSTUVWXYZÑ0123456789.;,#!@%&/\()¿?¡-+*[]{}');"}
															}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:25px;top:230px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 150,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Nro Teléfono fijo',
																name: 'telfijo',
																id: 'telbene',
																width: 200,
																binding:true,
																hiddenvalue:'',
																defaultvalue:'',
																allowBlank:true,
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789-');"}
															}]
													}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:25px;top:260px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 150,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'Nro de Celular',
																name: 'celular',
																id: 'celbene',
																width: 200,
																binding:true,
																hiddenvalue:'',
																defaultvalue:'',
																allowBlank:true,
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789-');"}
						
															}]
														}]
												},
												{
												layout: "column",
												defaults: {border: false},
												style: 'position:absolute;left:25px;top:290px',
												items: [{
														layout: "form",
														border: false,
														labelWidth: 150,
														items: [{
																xtype: 'textfield',
																labelSeparator :'',
																fieldLabel: 'E-mail',
																name: 'email',
																vtype:'email',
																id: 'email',
																width: 300,
																binding:true,
																hiddenvalue:'',
																defaultvalue:'',
																allowBlank:true,
																maxLength:100,
																autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzñ ABCDEFGHIJKLMNOPQRSTUVWXYZÑ0123456789.,@/\-');"}
															}]
														}]
												}]
										},
										{
										xtype:"fieldset", 
										title:'Informacion Adicional',
										style: 'position:absolute;left:60px;top:375px',
										border:true,
										width: 750,
										cls :'fondo',
										height: 260,
										items:[
										       //comcampocatcuentacontable.fieldsetCatalogo,
										       {
										       layout: "column",
									    	   border: false,
									    	   defaults: {border: false},
									    	   style: 'position:absolute;left:25px;top:20px',
									    	   items: [{
										    		   layout: "form",
										    		   border: false,
										    		   labelWidth: 150,			
										    		   items: [{
											    			   xtype:"combo",
											    			   store: dsbanco,
											    			   displayField:'nomban',
											    			   valueField:'codban',
											    			   id:"codban",
											    			   typeAhead: true,
											    			   mode: 'local',
											    			   triggerAction: 'all',
											    			   fieldLabel:'Banco',
											    			   listWidth:250,
											    			   editable:false,
											    			   width:200,
											    			   binding:true,
											    			   hiddenvalue:'',
											    			   defaultvalue:''	
											    		   }]
									    	   			}]
										       },
										       {
									    	   layout: "column",
									    	   border: false,
									    	   defaults: {border: false},
									    	   style: 'position:absolute;left:25px;top:50px',
									    	   items: [{
										    		   layout: "form",
										    		   border: false,
										    		   labelWidth: 150,			
										    		   items: [{
											    			   xtype:"combo",
											    			   store: dsCuentas,			      
											    			   displayField:'nomtipcta',
											    			   valueField:'codtipcta',
											    			   id:"codtipcta",
											    			   typeAhead: true,
											    			   mode: 'local',
											    			   triggerAction: 'all',
											    			   selectOnFocus:true,
											    			   fieldLabel:'Tipo de cuenta bancaria',
											    			   listWidth:250,
											    			   editable:false,
											    			   width:200,
											    			   binding:true,
											    			   hiddenvalue:'',
											    			   defaultvalue:''	
											    		   }]
									    	   		}]
										       }, 
										       {
									    	   layout: "column",
									    	   defaults: {border: false},
									    	   style: 'position:absolute;left:25px;top:80px',
									    	   items: [{
										    		   layout: "form",
										    		   border: false,
										    		   labelWidth: 150,
										    		   items: [{
											    			   xtype: 'textfield',
											    			   labelSeparator :'',
											    			   fieldLabel: 'Cuenta bancaria',
											    			   name: 'ctabancaria',
											    			   id: 'ctaban',
											    			   width: 300,
											    			   binding:true,
											    			   hiddenvalue:'',
											    			   defaultvalue:'',
											    			   allowBlank:true,
											    			   maxLength:50,
											    			   autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'0123456789.,-');"}
											    		   }]
									    	   			}]
										       }]
										},   
								       comcampocatbancosig.fieldsetCatalogo,
								       comcampocatctacontpag.fieldsetCatalogo,
									   comcampocatctacontrec.fieldsetCatalogo,
								       {
							    	   xtype:"fieldset", 
							    	   title:"Ubicación Geografica",
							    	   style: 'position:absolute;left:245px;top:650px',
							    	   border:true,
							    	   height:140,
							    	   cls:'fondo',
							    	   width:405,
							    	   items:[ComboPais,
							    	          Comboest,
							    	          Combomun,
							    	          Comboparroquia
							    	          ]
								       },
								       {
							    	   layout: "column",
							    	   border: false,
							    	   defaults: {border: false},
							    	   style: 'position:absolute;left:85px;top:495px',
							    	   items: [{
								    		   layout: "form",
								    		   border: false,
								    		   labelWidth: 150,			
								    		   items: [{
									    			   xtype:"combo",
									    			   store: dataStoreContribuyente,
									    			   displayField:'denominacion',
									    			   valueField:'codigo',
									    			   id:"tipconben",
									    			   typeAhead: true,
									    			   mode: 'local',
									    			   triggerAction: 'all',				
									    			   fieldLabel:'(*)Contribuyente',
									    			   listWidth:250,
									    			   editable:false,
									    			   width:200,
									    			   binding:true,
									    			   allowBlank:false,
									    			   hiddenvalue:'',
									    			   defaultvalue:''	
									    		   }]
						    	   			}]
								       }]

								}]
						},
						{
						title:"Deducciones",
						labelWidth:200,
						layout:"form",
						frame:true,
						height:820,//Alto del Tab
						width:880,
						id:'tabficdeducciones',
						tbar:[{
								text:'Grabar ',
								iconCls:'menuguardar',
								id:'guardardeduccion',
								handler: irGuardarDeduccion
						}],
						listeners:{
							'beforeshow': function(componente)
							{
								if(Ext.getCmp('nombene').getValue() == "")
								{
									Ext.Msg.alert('Mensaje','Debe seleccionar previamente un Beneficiario valido');
									Ext.getCmp('tabfichaben').activate('tabficdatbas');
									return false;
								}
								else
								{
									var ced_bene  = Ext.getCmp('ced_bene').getValue();
									Ext.getCmp('nom_label').setText(Ext.getCmp('nombene').getValue());
									obtenerMensaje('procesar','','Buscando Datos');
									var JSONObject = {
											'operacion' : 'buscarDeduccionesBeneficiarios',
											'ced_bene'  : ced_bene,										
									}

									var ObjSon = JSON.stringify(JSONObject);
									var parametros = 'ObjSon='+ObjSon; 
									Ext.Ajax.request({
										url : '../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
										params : parametros,
										method: 'POST',
										success: function ( resultado, request){
											Ext.Msg.hide();
											var datos = resultado.responseText;
											var objetoProveedores = eval('(' + datos + ')');
											if(objetoProveedores!='' || objetoProveedores.raiz!=''){
												gridDeducciones.store.loadData(objetoProveedores);
											}
											else {
												Ext.Msg.show({
													title:'Advertencia',
													msg: 'No se ha encontrado Beneficiarios',
													buttons: Ext.Msg.OK,
													icon: Ext.MessageBox.WARNING
												});  				
											}
										}	
									});
									return true;
								}
							}
						},
						items: [{
								layout: "form",
								border: false,
								labelWidth: 130,
								columnWidth: 0.5,
								height:800, //Alto del contenido del Tab
								items: [{					
										layout: "column",
										defaults: {border: false},
										style: 'position:absolute;left:15px;top:15px;font-size:14px;',
										items: [{
												layout: "form",
												border: false,
												labelWidth: 200,
												columnWidth: 0.5,
												items: [{
														xtype: 'label',
														id:'nom_label',	
														text: 'Proveedor',
														style:'font-weight: bold'
														}]
												}]
										},gridDeducciones]

								}]
						}]

				}]
	});	
	irNuevo();
	ComboPais.addListener('select',agregar_combo_estado);
	Comboest.addListener('select',agregar_combo_municipio);
	Combomun.addListener('select',agregar_combo_parroquia);
	Comboparroquia.addListener('select',function(combo,record,index){Comboparroquia.valor = codpar=record.get('codpar')});
});

function agregar_combo_estado(par,rec) {
	ComboPais.valor = codpai = rec.get('codpai');
	var myJSONObject ={
			"operacion": 'catalogocomboestado',
			"codpai":codpai
	};	
	var ObjSon = JSON.stringify(myJSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function (resultado, request) { 
			var datos = resultado.responseText;
			if(datos!=''){
				var DatosNuevo = eval('(' + datos + ')');
			}
			DataStoreEstado.loadData(DatosNuevo);
		}
	});	
}

function agregar_combo_municipio(par,rec){
	ComboPais.valor = codpai = rec.get('codpai');
	Comboest.valor  = codest = rec.get('codest');
	var myJSONObject ={
			"operacion": 'catalogocombomuni',
			"codpai":codpai,
			"codest":codest
	};	
	var ObjSon = JSON.stringify(myJSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function (resultado, request) { 
			var datos = resultado.responseText;
			if(datos!=''){
				var DatosNuevo = eval('(' + datos + ')');
			}
			DataStoreMunicipio.loadData(DatosNuevo);
		}
	});	
}
function agregar_combo_parroquia(par,rec){
	ComboPais.valor = codpai = rec.get('codpai');
	Comboest.valor  = codest = rec.get('codest');
	Combomun.valor  = codmun = rec.get('codmun');
	var myJSONObject ={
			"operacion": 'catalogocomboparroquia',
			"codpai":codpai,
			"codest":codest,
			"codmun":codmun
	};	
	var ObjSon = JSON.stringify(myJSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function (resultado, request) { 
			var datos = resultado.responseText;
			if(datos!='') {
				var DatosNuevo = eval('(' + datos + ')');
			}
			DataStoreParroquia.loadData(DatosNuevo);
		}
	});	
}

function irCancelar(){
	limpiarFormulario(plbeneficiario);
}

function irNuevo(){
	limpiarFormulario(plbeneficiario);
	Ext.getCmp('ced_bene').enable();
	Ext.getCmp('codpai').setValue('---seleccione---');
	Ext.getCmp('codest').setValue('---seleccione---');
	Ext.getCmp('codmun').setValue('---seleccione---');
	Ext.getCmp('codest').setValue('---seleccione---');
	Ext.getCmp('codpar').setValue('---seleccione---');
	gridDeducciones.store.removeAll();
}

function deshabilitarCedula()
{
	Ext.getCmp('ced_bene').disable();
	buscardenoEstado();
	buscardenoMunicipio();
	buscardenoParroquia();
}

function uf_verificar_rif(campo){
	var myJSONObject = {
			"operacion":'verificar_rif',
			"rifben" : campo
	};
	var ObjSon= JSON.stringify(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) 
		{ 
			var codigo = result.responseText;
			if (codigo.length != 0)
			{
				Ext.Msg.show({
					title:'Advertencia',
					msg: 'El RIF ya pertenece a otro Beneficiario!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.WARNING
				});
				Ext.getCmp('rifben').setValue('');
			}
		}
	});
}

function uf_verificar_cedula(campo){
	var myJSONObject = {
			"operacion":'verificar_cedula',
			"ced_bene" : campo
	};
	var ObjSon= JSON.stringify(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) 
		{ 
			var codigo = result.responseText;
			if (codigo.length != 0)
			{
				Ext.Msg.show({
					title:'Advertencia',
					msg: 'La Cédula ya pertenece a otro Beneficiario!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.WARNING
				});
				Ext.getCmp('ced_bene').setValue('');
			}
		}
	});
}

function buscardenoEstado()
{
	var codpai = Ext.getCmp('codpai').getValue();
	var codest = Ext.getCmp('codest').getValue();
	var myJSONObject ={
			"operacion": 'denom_estado',
			"codpai":codpai
	};	
	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function (resultado, request) { 
			datos = resultado.responseText;
			if(datos!='')
			{
				var DatosNuevo = eval('(' + datos + ')');
			}
			DataStoreEstado.loadData(DatosNuevo);
			Ext.getCmp('codest').setValue(codest);
		}
	})	
}

function buscardenoMunicipio()
{
	var codpai = Ext.getCmp('codpai').getValue();
	var codest = Ext.getCmp('codest').getValue();
	var codmun = Ext.getCmp('codmun').getValue();
	var myJSONObject ={
			"operacion": 'denom_municipio',
			"codpai":codpai,
			"codest":codest
	};	
	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function (resultado, request) { 
			datos = resultado.responseText;
			if(datos!='')
			{
				var DatosNuevo = eval('(' + datos + ')');
			}
			DataStoreMunicipio.loadData(DatosNuevo);
			Ext.getCmp('codmun').setValue(codmun);
		}
	})	
}

function buscardenoParroquia()
{
	var codpai = Ext.getCmp('codpai').getValue();
	var codest = Ext.getCmp('codest').getValue();
	var codmun = Ext.getCmp('codmun').getValue();
	var codpar = Ext.getCmp('codpar').getValue();
	var myJSONObject ={
			"operacion": 'denom_parroquia',
			"codpai":codpai,
			"codest":codest,
			"codmun":codmun
	};	
	ObjSon=JSON.stringify(myJSONObject);
	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
		params : parametros,
		method: 'POST',
		success: function (resultado, request) { 
			datos = resultado.responseText;
			if(datos!='')
			{
				var DatosNuevo = eval('(' + datos + ')');
			}
			DataStoreParroquia.loadData(DatosNuevo);
			Ext.getCmp('codpar').setValue(codpar);
		}
	})	
}

function irBuscar()
{
	ventanaCatalogo();

	function ventanaCatalogo(){			
		var reVentana = Ext.data.Record.create([
                        {name: 'ced_bene'},
                        {name: 'nombene'},
                        {name: 'apebene'},
                        {name: 'dirbene'},
                        {name: 'sc_cuenta'},
                        {name: 'denominacion'},
                        {name: 'rifben'},
                        {name: 'telbene'},
                        {name: 'celbene'},
                        {name: 'email'},
                        {name: 'codpai'},
                        {name: 'codest'},
                        {name: 'codmun'},
                        {name: 'codpar'},
                        {name: 'codbansig'},
                        {name: 'denbansig'},
                        {name: 'codban'},
                        {name: 'ctaban'},
                        {name: 'fecregben'},
                        {name: 'nacben'},
                        {name: 'numpasben'},	
                        {name: 'codtipcta'},
                        {name: 'nomtipcta'},
                        {name: 'tipconben'},
						{name: 'sc_cuentarecdoc'},
						{name: 'denominacion_rec'}
						
                        ]);

		var dsVentana =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reVentana)
		});

		var cmVentana = new Ext.grid.ColumnModel([
		                                      new Ext.grid.CheckboxSelectionModel(),
		                                      {header: "<H1 align='center'>Código</H1>", width: 40, sortable: true, dataIndex: 'ced_bene'},
		                                      {header: "<H1 align='center'>Nombre</H1>", width: 50, sortable: true, dataIndex: 'nombene'},
		                                      {header: "<H1 align='center'>Apellido</H1>", width: 50, sortable: true, dataIndex: 'apebene'},
		                                      {header: "<H1 align='center'>Dirección</H1>", width: 50, sortable: true, dataIndex: 'dirbene'}
		                                      ]);

		gridVentanaBeneficiario = new Ext.grid.EditorGridPanel({
			width:550,
			height:200,
			frame:true,
			title:"",
			style: 'position:absolute;left:20px;top:150px',
			autoScroll:true,
			border:true,
			ds: dsVentana,
			cm: cmVentana,
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:true}),
			stripeRows: true,
			viewConfig: {forceFit:true}
		});

		var formVentanaCatalogo= new Ext.FormPanel({
			width: 590,
			height: 380,
			title: '',
			style: 'position:absolute;left:5px;top:10px',
			frame: true,
			autoScroll:false,
			items: [{
					xtype:"fieldset", 
					title:'Datos del Beneficiario',
					style: 'position:absolute;left:20px;top:10px',
					border:true,
					height:125,
					width:550,
					cls: 'fondo',
					items:[{
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:15px;top:15px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 50,
									items: [{
											xtype: 'textfield',
											labelSeparator :'',
											fieldLabel: 'Código',
											name: 'codigo',
											id: 'cedulabene',									
											width: 150,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'',
											autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"},
											changeCheck: function(){
												var v = this.getValue();
												act_data_store_beneficiarios('ced_bene',v);
												if(String(v) !== String(this.startValue)){
													this.fireEvent('change', this, v, this.startValue);
												} 
											},							 
											initEvents : function(){
												AgregarKeyPress(this);
											}
										}]
								}]
							},
							{
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:15px;top:45px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 50,
									items: [{
											xtype: 'textfield',
											labelSeparator :'',
											fieldLabel: 'Nombre',
											name: 'nombre',
											id: 'nombrebene',
											width: 350,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'',
											changeCheck: function(){
												var v = this.getValue();
												act_data_store_beneficiarios('nombene',v);
												if(String(v) !== String(this.startValue)){
													this.fireEvent('change', this, v, this.startValue);
												} 
											},							 
											initEvents : function(){
												AgregarKeyPress(this);
											}
										}]
									}]
							},
							{
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:15px;top:75px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 50,
									items: [{
											xtype: 'textfield',
											labelSeparator :'',
											fieldLabel: 'Apellido',
											name: 'apellido',
											id: 'apellidobene',
											width: 350,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'',
											changeCheck: function(){
												var v = this.getValue();
												act_data_store_beneficiarios('apebene',v);
												if(String(v) !== String(this.startValue)){
													this.fireEvent('change', this, v, this.startValue);
												} 
											},							 
											initEvents : function(){
												AgregarKeyPress(this);
											}
										}]
									}]
							},
							{
							layout:"column",
							defaults: {border: false},
							style: 'position:absolute;left:450px;top:75px',
							border:false,
							items:[{
									layout:"form",
									border:false,
									items:[{					
											xtype: 'button',
											labelSeparator :'',
											fieldLabel: '',
											id: 'btnBuscarBene',
											text: 'Buscar',
											width: 300,
											height: 300,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'',
											iconCls: 'menubuscar',
											handler: function(){
												if ((Ext.getCmp('cedulabene').getValue() == '') && (Ext.getCmp('nombrebene').getValue() == '') && (Ext.getCmp('apellidobene').getValue() == '')){
													Ext.Msg.show({
														title:'Mensaje',
														msg:'Debe seleccionar al menos un parámetro de búsqueda',
														buttons: Ext.Msg.OK,
														icon: Ext.MessageBox.INFO
													});
												}
												else{
													obtenerMensaje('procesar','','Buscando Datos');
					
													var ced_bene  = Ext.getCmp('cedulabene').getValue();
													var nombene  = Ext.getCmp('nombrebene').getValue();
													var apebene  = Ext.getCmp('apellidobene').getValue();
					
													var JSONObject = {
															'operacion'    : 'buscarBeneficiarios',
															'cedulabene'   : ced_bene,
															'nombrebene'   : nombene,
															'apellidobene' : apebene
													}			
													var ObjSon = JSON.stringify(JSONObject);
													var parametros = 'ObjSon='+ObjSon; 
													Ext.Ajax.request({
														url : '../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
														params : parametros,
														method: 'POST',
														success: function ( resultado, request){
															Ext.Msg.hide();
															var datos = resultado.responseText;
															var objetoProveedores = eval('(' + datos + ')');
															if(objetoProveedores!=''){
																if(objetoProveedores!='0'){
																	if(objetoProveedores.raiz == null || objetoProveedores.raiz ==''){
																		Ext.MessageBox.show({
																			title:'Advertencia',
																			msg:'No existen datos para mostrar',
																			buttons: Ext.Msg.OK,
																			icon: Ext.MessageBox.WARNING
																		});
																	}
																	else{
																		gridVentanaBeneficiario.store.loadData(objetoProveedores);
																	}
																}
																else{
																	Ext.MessageBox.show({
																		title:'Advertencia',
																		msg:'Debe configurar en Empresa los digitos de las cuentas de gastos',
																		buttons: Ext.Msg.OK,
																		icon: Ext.MessageBox.WARNING
																	});
																}
															}
														}	
													});
												}
										}
										}]
									}]
							}]
					},gridVentanaBeneficiario]  
			});
		var ventanaEstructura = new Ext.Window({
			width:600,
			height:450,
			border:false,
			modal: true,
			closable:false,
			frame:true,
			title:"<H1 align='center'>Catálogo de Beneficiarios</H1>",
			items:[formVentanaCatalogo],
			buttons:[{
						text:'Aceptar',  
						handler: function(){
							var registro = gridVentanaBeneficiario.getSelectionModel().getSelected();	        	
							if(registro!= undefined)
							{
								gridDeducciones.store.removeAll();
								setDataFrom(plbeneficiario,registro);
								gridVentanaBeneficiario.destroy();
								ventanaEstructura.destroy();
								deshabilitarCedula();
								Actualizar = 1;
							}
							else {
							Ext.MessageBox.show({
								title:'Mensaje',
								msg:'Debe seleccionar al menos un registro a procesar',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
							}
					  }
					},
					{
						text: 'Salir',
						handler: function(){
							ventanaEstructura.destroy();
						}
					}] 	
		});
		function act_data_store_beneficiarios(criterio,cadena)
		{
			dsVentana.filter(criterio,cadena);
		}
		ventanaEstructura.show();
	}
}

function irGuardar(){
	var cadjson = '';
	if (Ext.getCmp('codpai').getValue()=='---seleccione---')
	{
		Ext.getCmp('codpai').setValue('---');	
	}
	if (Ext.getCmp('codest').getValue()=='---seleccione---')
	{
		Ext.getCmp('codest').setValue('---');	
	}
	if (Ext.getCmp('codmun').getValue()=='---seleccione---')
	{
		Ext.getCmp('codmun').setValue('---');	
	}
	if (Ext.getCmp('codest').getValue()=='---seleccione---')
	{
		Ext.getCmp('codest').setValue('---');	
	}
	if (Ext.getCmp('codpar').getValue()=='---seleccione---')
	{
		Ext.getCmp('codpar').setValue('---');	
	}
	if ((Ext.getCmp('codban').getValue()=='') || (Ext.getCmp('codban').getValue()=='   '))
	{
		Ext.getCmp('codban').setValue('---');	
	}
	if(Actualizar == null)
	{
		if(validarObjetos('ced_bene','10','novacio|numero')!='0')
		{
			cadjson      = getItems(plbeneficiario,'incluir','N',null,null);
		}
	} 
	else
	{
		cadjson      = getItems(plbeneficiario,'actualizar','N',null,null);
	}	
	try 
	{
		if(cadjson!='')
		{
			var objjson = Ext.util.JSON.decode(cadjson);
		}
		if (typeof(objjson) == 'object') 
		{
			var parametros = 'ObjSon=' + cadjson;
			Ext.Ajax.request({ 
				url : '../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
				params : parametros,
				method: 'POST',
				success: function ( result, request){
					var codigo = result.responseText;
					codigo = codigo.trim();
					if(codigo == '1'){
						Ext.Msg.show({
							title:'Mensaje',
							msg: exitoguardar,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
						limpiarFormulario(plbeneficiario);
						Actualizar=null;
					}
					else
					{
						Ext.Msg.show({
							title:'Mensaje',
							msg: errorguardar,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.ERROR
						});
					}
				}	
			});
		}
	}	
	catch(e){
		//alert('Verifique los datos, esta insertando caracteres invalidos '+e);
	}	
}

function irEliminar(){

	function respuesta(btn){
		if(btn=='yes'){
			var cadjson = getItems(plbeneficiario,'eliminar','N',null,null);
			try {
				var objjson = Ext.util.JSON.decode(cadjson);
				if (typeof(objjson) == 'object') {
					var parametros = 'ObjSon=' + cadjson;
					Ext.Ajax.request({
						url : '../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
						params : parametros,
						method: 'POST',
						success: function ( result, request){
							var codigo = result.responseText;
							if(String(codigo) == '1'){
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro eliminado con éxito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
								});
								limpiarFormulario(plbeneficiario);
								Actualizar=null;
							}
							else if(String(codigo) == '2'){
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Beneficiario asociado a otros registros, no puede ser Eliminado <br>',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
							}
							else{
								Ext.Msg.show({
									title:'Mensaje',
									msg: 'Error al tratar de eliminar el registro <br>',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
							}							
						}	
					});
				}
			}
			catch(e){
				alert('error'+e);
			}
		}
	}	
	if(Actualizar){
		Ext.MessageBox.confirm('Confirmar', '¿Desea eliminar este registro?', respuesta);
	}
	else{
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El registro debe estar guardado para poder eliminarlo, verifique por favor',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		}); 
	}
}

function agregarDeduccion(){
	//crear_grid_catalogoplanunicorefiltro('catalogogastos');				   

	var reDeduccion = Ext.data.Record.create
	([
	  {name: 'codded'}, 
	  {name: 'dended'}
	  ]);

	var dsDeduccion =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDeduccion)
	});

	var chk = new Ext.grid.CheckboxSelectionModel({});

	var cmDeduccion = new Ext.grid.ColumnModel([
	                                            chk,									   
	                                            {header: "Código", width: 40, sortable: true, dataIndex: 'codded'},
	                                            {header: "Denominación ", width: 50, sortable: true, dataIndex: 'dended'}          
	                                            ]);


	//creando datastore y columnmodel para la grid de cambio de estatus de proveedor
	gridVentanaDeduccion = new Ext.grid.EditorGridPanel({
		width:550,
		height:325,
		frame:true,
		title:"<H1 align='center'>Deducciones</H1>",
		style: 'position:absolute;left:10px;top:5px',
		autoScroll:true,
		border:true,
		ds: dsDeduccion,
		cm: cmDeduccion,
		stripeRows: true,
		sm: new Ext.grid.CheckboxSelectionModel({}),
		viewConfig: {forceFit:true}
	});

	ventanaDeduccion = new Ext.Window({
		title: 'Catálogo de Deducciones disponibles',
		autoScroll:true,
		width:575,
		height:400,
		modal: true,
		closable:false,
		plain: false,
		items:[gridVentanaDeduccion],
		buttons: [{
			text:'Aceptar',  
			handler: function()
			{
				arreglodeduccion = gridVentanaDeduccion.getSelectionModel().getSelections();
				for (i=0; i<arreglodeduccion.length; i++)
				{
					if (validarExistenciaRegistroGrid(arreglodeduccion[i],gridDeducciones,'codded','codded',true))
					{
						pasarDatosGridDeduccion(gridDeducciones,arreglodeduccion[i]);
					}
				}
				gridVentanaDeduccion.destroy();
				ventanaDeduccion.destroy();
			}
			},
			{
			text: 'Salir',
			handler: function()
			{
				gridVentanaDeduccion.destroy();
				ventanaDeduccion.destroy();
			}
			}]
	});
	ventanaDeduccion.show();

	var ced_bene  = Ext.getCmp('ced_bene').getValue();
	obtenerMensaje('procesar','','Buscando Datos');
	var JSONObject = {
			'operacion' : 'buscarDeduccionesDisp','ced_bene' : ced_bene
	}			
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoProveedores = eval('(' + datos + ')');
			if(objetoProveedores!='' || objetoProveedores.raiz!=''){
				gridVentanaDeduccion.store.loadData(objetoProveedores);
			}
			else {
				Ext.Msg.show({
					title:'Advertencia',
					msg: 'No se han encontrado especialidades disponibles',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.WARNING
				});  				
			}
		}	
	});	
}

function pasarDatosGridDeduccion(grid,registro){
	var registrodeduxgrid = Ext.data.Record.create([
	                                                {name: 'codded'},     
	                                                {name: 'dended'}
	                                                ]);

	dedxpro = new registrodeduxgrid
	({
		'codded':'',
		'dended':''
	});
	grid.store.insert(0,dedxpro);
	dedxpro.set('codded',registro.get('codded'));
	dedxpro.set('dended',registro.get('dended'));
}

function eliminar_grid_ded() {
	arreglodedxprov = gridDeducciones.getSelectionModel().getSelections();
	if (arreglodedxprov.length >0)
	{
		for (var i = arreglodedxprov.length - 1; i >= 0; i--)
		{
			gridDeducciones.getStore().remove(arreglodedxprov[i]);
			if(!arreglodedxprov[i].isModified('sig_cuenta'))
			{
				datastorededxproveliminada.add(arreglodedxprov[i]);
			}
			elimino = true;
		}
	}
}

function irGuardarDeduccion()
{
	valido=true;
	arrDedxbene = gridDeducciones.getStore();
	var first = true;

	if(Ext.getCmp('nombene').getValue()==''){
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe cargar un Proveedor para procesar la información',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
	else{
		var cadenaJson = "{'operacion':'guardar_dedxbene', 'ced_bene':'"+Ext.getCmp('ced_bene').getValue()+"','arrDedIncluir':[";
		arrDedxbene.each(function (registroGrid)
		{
			if(first)
			{
				cadenaJson = cadenaJson + "{'codded':'"+registroGrid.get('codded')+"'," +
				" 'dended':'"+registroGrid.get('dended')+"'}";
				first = false;
			}
			else 
			{
				cadenaJson = cadenaJson + ",{'codded':'"+registroGrid.get('codded')+"'," +
				" 'dended':'"+registroGrid.get('dended')+"'}";
			}
		});
		cadenaJson = cadenaJson + "]}";

		var parametros = 'ObjSon='+cadenaJson;
		Ext.Ajax.request({
			url : '../../controlador/rpc/sigesp_ctr_rpc_beneficiario.php',
			params : parametros,
			method: 'POST',
			success: function ( resultad, request ){ 
				var respuesta = resultad.responseText;
				respuesta = respuesta.split("|");
				var msjError = '';
				if(respuesta[0]=='1'){
					Ext.MessageBox.show({
						title:'Mensaje',
						msg: 'La información fue procesada exitosamente'+msjError,
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
				else{
					if(elimino){
						Ext.MessageBox.show({
							title:'Mensaje',
							msg: 'La deducción fue eliminada exitosamente'+msjError,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
					else{
						if(gridDeducciones.getStore().getCount()==0){
							Ext.MessageBox.show({
								title:'Mensaje',
								msg: 'No existe datos, para procesar la información'+msjError,
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
						}
						else{
							Ext.MessageBox.show({
								title:'Error',
								msg: 'Ha ocurrido un error procesando la información '+msjError,
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.ERROR
							});
						}
					}
				}
			
			},
			failure: function ( result, request){ 
				Ext.MessageBox.alert('Error','Ha ocurrido un error en la operación, por favor intente de nuevo'); 
			} 
		});
	}
}

