/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var comcampocatproveedor = null;
var gridSolicitud = null;
var fromContabilizaSobasi = null;
barraherramienta = true;
//-------------------------------------------------------------------------------------------------------------------------	
Ext.onReady(function(){
	
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	//-------------------------------------------------------------------------------------------------------------------------	
	var Xpos = ((screen.width/2)-(300));
	plcontabilizaSobasi = new Ext.FormPanel({
		applyTo: 'formulario',
		width:613,
		height: 500,
		title: "<H1 align='center'>Contabilizaci&#243;n de Asignaci&#243;n</H1>",
		frame:true,
		autoScroll:false,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:15px;',
		items: [fromContabilizaSobasi,gridSolicitud]
	});	
	plcontabilizaSobasi.doLayout();
});

	var textCodPro = new Ext.form.TextField({
		labelSeparator:'',
		fieldLabel:'Proveedor',
		name:'proveedor',
		id:'cod_pro',
		width:100,
		binding:true,
		hiddenvalue:'',
		defaultvalue:'',
		allowBlank:true,
		autoCreate:{tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"}
	})
	
	var textNomPro = new Ext.form.TextField({
		labelSeparator:'',
		fieldLabel:'',
		name:'nompro',
		id:'nompro',
		width:230,
		readOnly:true,
		binding:true,
		hiddenvalue:'',
		defaultvalue:'',
		allowBlank:true,
		autoCreate:{tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789');"}
	})
	
	var reSolicitud = Ext.data.Record.create([
        {name: 'codasi'}, 
        {name: 'fecasi'},
        {name: 'obsasi'},
        {name: 'detasi'},
        {name: 'cod_pro'},
        {name: 'nompro'},
        {name: 'montotasi'},
        {name: 'estasi'},
        {name: 'desobr'},
        {name: 'codobr'}
    ]);

	var dsSolicitud =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSolicitud)
	});

	var cmSolicitud = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
	    {header: "<CENTER>Nro Asignaci&#243;n</CENTER>", width: 30, sortable: true, dataIndex: 'codasi'},
	    {header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecasi'},
	    {header: "<CENTER>Observaci&#243;n</CENTER>", width: 60, sortable: true, dataIndex: 'obsasi'},
	   
	]);
                  	
	//creando datastore y columnmodel para la grid de asignaciones de obra
	gridSolicitud = new Ext.grid.GridPanel({
		width:570,
		height:240,
		frame:true,
		title:"<H1 align='center'>Asignaciones por Contabilizar</H1>",
		style: 'position:absolute;left:15px;top:225px',
		autoScroll:true,
		border:true,
		ds: dsSolicitud,
		cm: cmSolicitud,
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
	
	// Creando la ventana emergente de los detalles
	gridSolicitud.on({
		'rowcontextmenu': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				
				//creando datastore y columnmodel para la grid de detalles presupuestarios
				var reMovBancario = Ext.data.Record.create([
				    {name: 'estructura'}, 
				    {name: 'estcla'},
				    {name: 'spg_cuenta'},
				    {name: 'denominacion'},
				    {name: 'monto'},
				    {name: 'disponibilidad'}
				]);
				
				var dsMovBancario =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reMovBancario)
				});
									
				var cmMovBancario = new Ext.grid.ColumnModel([
			        {header: "<CENTER>Estructura</CENTER>", width: 60, sortable: true, dataIndex: 'estructura'},
			        {header: "<CENTER>Estatus</CENTER>", width: 60, sortable: true, dataIndex: 'estcla',renderer:mostrarEstatusComCmp},
			        {header: "<CENTER>Cuenta</CENTER>", width: 60, sortable: true, dataIndex: 'spg_cuenta'},
			        {header: "<CENTER>Denominacion</CENTER>", width: 100, sortable: true, dataIndex: 'denominacion'},
			        {header: "<CENTER>Monto</CENTER>", width: 40, sortable: true, dataIndex: 'monto',renderer:formatoMontoGrid},
			        {header: "<CENTER>Disponibilidad</CENTER>", width: 45, sortable: true, dataIndex: 'disponibilidad',renderer:mostrarDisponibleComCmp} 
				]);
				//fin creando datastore y columnmodel para la grid de detalles presupuestarios
				
				//creando componente detalle comprobante
				var comMovBancario = new com.sigesp.vista.comDetalleComprobante({
					anchoVentana: 720,
					altoVentana: 500,
					anchoFormulario: 680,
					altoFormulario:150,
					arrCampos:[{
								tipo:'textfield',
								etiqueta:'Asignaci&#243;n',
								id:'ndoc',
								valor: registro.get('codasi'),
								ancho: 200 
								},
						        {	
								tipo:'textfield',
								etiqueta:'Fecha',
								id:'fmov',
								valor:registro.get('fecasi'),
								ancho: 100
								},
								{
								tipo:'textfield',
								etiqueta:'Descripci&#243;n',
								id:'cmov',
								valor:registro.get('obsasi'),
								ancho: 300
								},
								{
								tipo:'textfield',
								etiqueta:'Proveedor',
								id:'tip_mov',
								valor:registro.get('cod_pro')+" - "+registro.get('nompro'),
								ancho: 300
								},
							    {
								tipo:'textfield',
								etiqueta:'Obra Asociada',
								id:'b_mov',
								valor:registro.get('codobr')+" - "+registro.get('desobr'),
								ancho: 300
								},
								{
								tipo:'textfield',
								etiqueta:'Contabilizaci&#243;n',
								id:'promov',
								valor:'PRECOMPROMISO',
								ancho: 300
								}],
					tienePresupuesto:true,
					tieneContable:true,
					tituloGridPresupuestario:"<H1 align='center'>Detalle Presupuestario de Gasto</H1>",
					anchoGridPG :680,
					altoGridPG :150,
					dsPresupuestoGasto: dsMovBancario,
					cmPresupuestoGasto: cmMovBancario,
					rutaControlador:'../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
					paramPresupuesto: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'buscar_detalles_gasto',
																	  'codasi':registro.get('codasi'),
																	  'codcon':''}),  
				});
				//fin creando componente detalle comprobante
				comMovBancario.mostrarVentana();
			}
		}
	});

	var	fromContabilizaSobasi = new Ext.form.FieldSet({ 
		    title:'Datos de la Asignación',
		    style: 'position:absolute;left:15px;top:10px',
			border:true,
			width: 570,
			cls: 'fondo',
			height: 190,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:20px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 150,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Número de Asignaci&#243;n',
									id: 'codasi',
									width: 150,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									allowBlank:false,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '15'}
								}]
							}]
				}, 
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:50px',
				items: [{
						layout: "form",
						border: false,
						labelWidth: 150,
						items: [{
								xtype: 'textfield',
								labelSeparator :'',
								fieldLabel: 'Número de Obra',
								id: 'codobr',
								width: 150,
								binding:true,
								hiddenvalue:'',
								defaultvalue:'',
								allowBlank:false,
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '15'}
							}]
						}]
				}, 
				{
				style:'position:absolute;left:15px;top:80px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:150,
						items: [textCodPro]
					}]
				},
				{
				style:'position:absolute;left:280px;top:80px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						labelWidth:20,
						items: [textNomPro]
					}]
				},
				{
				style:'position:absolute;left:275px;top:80px',
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						items: [{
								xtype:"button",
								id:'btnBuscarBene',
								iconCls:'menubuscar',
								handler: function(boton)
								{
									CatalogoProveedores();
								}
							}]
					}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:110px',
				items: [{
						layout: "form",
						border: false,					
						labelWidth: 150,
						items: [{
								xtype:"datefield",
								fieldLabel:"Fecha de Asignaci&#243;n",
								labelSeparator :'',
								name:"fecasi",
								allowBlank:false,
								width:100,
								id:"fecasi",
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
						}]
				},
				{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:140px',
				items: [{
						layout: "form",
						border: false,					
						labelWidth: 150,
						items: [{
								xtype:"datefield",
								fieldLabel:"Fecha de Contabilizaci&#243;n",
								labelSeparator :'',
								name:"fechaconta",
								allowBlank:false,
								width:100,
								value: new Date().format('Y-m-d'),
								id:"fechaconta",
								autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
							}]
						}]
				}]
	})
	
	//Creacion del formulario


function irCancelar(){
	limpiarFormulario(fromContabilizaSobasi);
	gridSolicitud.store.removeAll();
}
function irAnular(){
	
}
function irProcesar(){
	grid = gridSolicitud.getSelectionModel().getSelections();
	fecha = Ext.getCmp('fechaconta').getValue().format('Y/m/d');
	cadenajson = "{'operacion':'contabilizar_sobasi','codsis':'"+sistema+"','nomven':'"+vista+"', 'feccon':'"+fecha+"', 'arrDetalle':[";
	total = grid.length;
	if(total>0)
	{
		for(var i=0; i<total; i++)
		{
			for (i=0; i<total; i++)
			{
				if (i==0) 
				{
					cadenajson += "{'codasi':'"+grid[i].get('codasi')+"','fecasi':'"+grid[i].get('fecasi')+"'," +
								   "'obsasi':'"+grid[i].get('obsasi')+"','estasi':'"+grid[i].get('estasi')+"'," +
					               "'montotasi':'"+grid[i].get('montotasi')+"'}";                
				}
				else {
					cadenajson += ",{'codasi':'"+grid[i].get('codasi')+"','fecasi':'"+grid[i].get('fecasi')+"'," +
									"'obsasi':'"+grid[i].get('obsasi')+"','estasi':'"+grid[i].get('estasi')+"'," +
									"'montotasi':'"+grid[i].get('montotasi')+"'}";                
				}
			}
		}
		cadenajson += "]}";	
		var parametros = 'ObjSon='+cadenajson;
		Ext.Ajax.request({
			url : '../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
			params : parametros,
			method: 'POST',
			success: function (resultado, request)
			{ 
				var resultado = resultado.responseText;
				var arrResultado = resultado.split("|");
				Ext.Msg.hide();
				//creando componente detalle comprobante
				var comResultado = new com.sigesp.vista.comResultadoIntegrador({
					tituloVentana: 'Resultado Contabilizaci&#243;n de Asignaciones',
					anchoLabel: 200,
					labelTotal:'Total asignaciones procesados',
					valorTotal: arrResultado[0],
					labelProcesada:'Total asignaciones contabilizados',
					valorProcesada:arrResultado[1],
					labelError:'Total asignaciones con error',
					valorError:arrResultado[2],
					tituloGrid:'Detalle de Resultados',
					dataDetalle:arrResultado[3]
				});
				//fin creando componente detalle comprobante
				
				comResultado.mostrarVentana();
				irCancelar();
			},
			failure: function (result,request) 
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
			}					
		});
	}
	else{
		Ext.MessageBox.show({
			title:'Mensaje',
			msg:'Debe seleccionar al menos un documento a procesar !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.INFO
		});
	}
}

function irDescargar(){
	
}

function irImprimir(){
	
}
//----------------------------------------------------------------------------------------------------------------------------------
function CatalogoProveedores()
{
	var reVentana = Ext.data.Record.create([
            {name: 'cod_pro'},
            {name: 'nompro'},
            {name: 'dirpro'},
            {name: 'rifpro'},
            {name: 'fecvenrnc'},
    ]);

	var dsVentana =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reVentana)
	});

	var cmVentana = new Ext.grid.ColumnModel([
	        {header: "<H1 align='center'>C&#243;digo</H1>", width: 40, sortable: true, dataIndex: 'cod_pro'},
	        {header: "<H1 align='center'>Nombre</H1>", width: 50, sortable: true, dataIndex: 'nompro'},
	        {header: "<H1 align='center'>Direcci&#243;n</H1>", width: 50, sortable: true, dataIndex: 'dirpro'},
	        {header: "<H1 align='center'>RIF</H1>", width: 40, sortable: true, dataIndex: 'rifpro'},
	        {header: "<H1 align='center'>Fecha Reg. Nac. Contratista</H1>", width: 40, sortable: true, dataIndex: 'fecvenrnc'}
	]);

	gridVentanaProveedor = new Ext.grid.GridPanel({
		width:650,
		height:245,
		frame:true,
		title:"",
		style: 'position:absolute;left:10px;top:215px',
		autoScroll:true,
		border:true,
		ds: dsVentana,
		cm: cmVentana,
		stripeRows: true,
		viewConfig: {forceFit:true}
	});

	gridVentanaProveedor.on({
		'rowdblclick': {
			fn: function(grid, numFila, evento){
				var registro = grid.getStore().getAt(numFila);
				if(registro!= undefined){
					Ext.getCmp('cod_pro').setValue(registro.get('cod_pro'));
					Ext.getCmp('nompro').setValue(registro.get('nompro'));
					gridVentanaProveedor.destroy();
					ventanaEstructura.destroy();
				}
				else{
					Ext.MessageBox.show({
						title:'Mensaje',
						msg:'Debe seleccionar al menos un registro a procesar',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
			}
		}
	});

	var formVentanaCatalogo= new Ext.FormPanel({
		width: 685,
		height: 475,
		title: '',
		style: 'position:absolute;left:10px;top:10px',
		frame: true,
		autoScroll:false,
		items: [{
				xtype:"fieldset", 
				title:'Datos del Proveedor',
				style: 'position:absolute;left:10px;top:10px',
				border:true,
				height:185,
				cls: 'fondo',
				width:650,
				items:[{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:15px;top:15px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 80,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'C&#243;digo',
										name: 'codigo',
										id: 'codi_pro',									
										width: 150,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'',
										autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '20', onkeypress: "return keyRestrict(event,'0123456789');"},
										changeCheck: function(){
											var textvalor = this.getValue();
											dsVentana.filter('cod_pro',textvalor,true);
											if(String(textvalor) !== String(this.startValue)){
												this.fireEvent('change', this, textvalor, this.startValue);
											} 
										}, 
										initEvents: function(){
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
								labelWidth: 80,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Nombre',
										name: 'nombre',
										id: 'nombpro',
										width: 300,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'',
										changeCheck: function(){
											var v = this.getValue();
											act_data_store_proveedores('nompro',v);
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
								labelWidth: 80,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Direcci&#243;n',
										name: 'direccion',
										id: 'direcpro',
										width: 450,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'',
										changeCheck: function(){
											var v = this.getValue();
											act_data_store_proveedores('dirpro',v);
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
						style: 'position:absolute;left:15px;top:105px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 80,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'R.I.F',
										name: 'rif',
										id: 'rifprov',
										width: 150,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'',
										autoCreate: {tag: 'input', type: 'text', size: '15', autocomplete: 'off', maxlength: '12'},
										changeCheck: function(){
											var v = this.getValue();
											act_data_store_proveedores('rifpro',v);
											if(String(v) !== String(this.startValue)){
												this.fireEvent('change', this, v, this.startValue);
											} 
										},							 
										initEvents : function(){
											AgregarKeyPress(this);
										},
									}]
								}]
						},
						{	
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:300px;top:109px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 400,
								items: [{
										xtype: 'label',
										text: 'El formato correcto del RIF es: [JGVE]-[99999999]-[9]',
										disabled: true
									}]
							}]
						},
						{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:15px;top:135px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 80,
								items: [{
										xtype: 'datefield',
										labelSeparator :'',
										fieldLabel: 'Fecha Desde',
										name: 'fecdesde',
										id: 'fecdes',
										width: 150,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'1900-01-01',
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"},
										changeCheck: function(){
											var v = this.getValue();
											act_data_store_proveedores('fecvenrnc',v);
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
						style: 'position:absolute;left:300px;top:135px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 80,
								items: [{
										xtype: 'datefield',
										labelSeparator :'',
										fieldLabel: 'Fecha Hasta',
										name: 'fechasta',
										id: 'fechas',
										width: 150,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'1900-01-01',
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"},
										changeCheck: function(){
											var v = this.getValue();
											act_data_store_proveedores('fecvenrnc',v);
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
						style: 'position:absolute;left:560px;top:135px',
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
										handler: function()
										{
											if((Ext.getCmp('codi_pro').getValue() == '') && (Ext.getCmp('nombpro').getValue() == '') 
													&& (Ext.getCmp('direcpro').getValue() == '') && (Ext.getCmp('rifprov').getValue() == '')
													&& (Ext.getCmp('fecdes').getValue() == '') && (Ext.getCmp('fechas').getValue() == ''))
											{
												Ext.Msg.show({
													title:'Mensaje',
													msg:'Debe seleccionar al menos un par&#225;metro de b&#250;squeda',
													buttons: Ext.Msg.OK,
													icon: Ext.MessageBox.INFO
												});
											}
											else{
												obtenerMensaje('procesar','','Buscando Datos');
					
												var codpro  = Ext.getCmp('codi_pro').getValue();
												var nompro  = Ext.getCmp('nombpro').getValue();
												var dirpro  = Ext.getCmp('direcpro').getValue();
												var rifpro  = Ext.getCmp('rifprov').getValue();
												if ((Ext.getCmp('fecdes').getValue() != '') && (Ext.getCmp('fechas').getValue() != '')){
													var fecdes  = Ext.getCmp('fecdes').getValue().format('Y-m-d');
													var fechas  = Ext.getCmp('fechas').getValue().format('Y-m-d');
												}
												else{
													var fecdes = Ext.getCmp('fecdes').getValue();
													var fechas  = Ext.getCmp('fechas').getValue();
												}
					
												var JSONObject = {
														'operacion'	: 'catalogo',
														'codi_pro'  : codpro,
														'nombpro'   : nompro,
														'direcpro' 	: dirpro,
														'rifprov'   : rifpro,
														'fecdes'   	: fecdes,
														'fechas' 	: fechas
												}			
												var ObjSon = JSON.stringify(JSONObject);
												var parametros = 'ObjSon='+ObjSon; 
												Ext.Ajax.request({
													url : '../../controlador/rpc/sigesp_ctr_rpc_proveedor.php',
													params : parametros,
													method: 'POST',
													success: function ( resultado, request)
													{
														Ext.Msg.hide();
														var datos = resultado.responseText;
														var objetoProveedor = eval('(' + datos + ')');
														if(objetoProveedor!=''){
															if(objetoProveedor!='0'){
																if(objetoProveedor.raiz == null || objetoProveedor.raiz ==''){
																	Ext.MessageBox.show({
																		title:'Advertencia',
																		msg:'No existen datos para mostrar',
																		buttons: Ext.Msg.OK,
																		icon: Ext.MessageBox.WARNING
																	});
																}
																else{
																	gridVentanaProveedor.store.loadData(objetoProveedor);
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
													},
													failure: function (result,request) 
													{ 
														Ext.MessageBox.alert('Error', 'Error de comunicación con el Servidor'); 
													}	
												});
												}
										}
									}]
							}]
					}]
		},gridVentanaProveedor]  
	});
	var ventanaEstructura = new Ext.Window({
		width:700,
		height:550,
		border:false,
		modal: true,
		closable:false,
		frame:true,
		title:"<H1 align='center'>Cat&#225;logo de Proveedores</H1>",
		items:[formVentanaCatalogo],
		buttons:[{
				text:'Aceptar',  
				handler: function(){
					var registro = gridVentanaProveedor.getSelectionModel().getSelected();	        	
					if(registro!= undefined){
						Ext.getCmp('cod_pro').setValue(registro.get('cod_pro'));
						Ext.getCmp('nompro').setValue(registro.get('nompro'));
						gridVentanaProveedor.destroy();
						ventanaEstructura.destroy();
					}
					else{
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

	function act_data_store_proveedores(criterio,cadena){
		dsVentana.filter(criterio,cadena);
	}
	ventanaEstructura.show();

}
function irBuscar(){
	obtenerMensaje('procesar','','Buscando Datos');
	//buscar asignaciones
	var codasi   = Ext.getCmp('codasi').getValue();
	var codobr  = Ext.getCmp('codobr').getValue();
	var fecasi	 = Ext.getCmp('fecasi').getValue();
	var cod_pro	 = Ext.getCmp('cod_pro').getValue();

	var JSONObject = {
			'operacion'   : 'buscar_sobasi',
			'codasi'      : codasi,
			'codobr'      : codobr,
			'fecasi'      : fecasi,		
			'cod_pro'     : cod_pro,
			'estatus'     : '0'
	}
	var ObjSon = JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/mis/sigesp_ctr_mis_integracionsob.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){
			Ext.Msg.hide();
			var datos = resultado.responseText;
			var objetoSobasi = eval('(' + datos + ')');
			if(objetoSobasi!=''){
				if(objetoSobasi!='0'){
					if(objetoSobasi.raiz == null || objetoSobasi.raiz ==''){
						Ext.MessageBox.show({
							title:'Advertencia',
							msg:'No existen datos para mostrar',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.WARNING
		 				});
						gridSolicitud.store.removeAll();
					}
					else{
						gridSolicitud.store.loadData(objetoSobasi);
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
		},
		failure: function (result,request) 
		{ 
			Ext.Msg.hide();
			Ext.MessageBox.alert('Error', 'Error de comunicacion con el Servidor'); 
		}
	});
}
