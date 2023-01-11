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

var fromReporteEjeCom = null; //varibale para almacenar la instacia de objeto de formulario
barraherramienta = true;
var comprobante = "";
var comprobanteProc ="";
var fecha = new Date();
var anio = fecha.getFullYear();
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	//-------------------------------------------------------------------------------------
	
	var botbuscarComprobante = new Ext.Button({
		id: 'botbusquedaHasta',
		iconCls: 'menubuscar',
		style:'position:absolute;left:460px;top:15px',
		listeners:{
	        'click' : function(boton){
	        	ventanaCatalogoComprobante()
	       }
	    }
	});	

	//-------------------------------------------------------------------------------------

	var	fromIntervaloFechas = new  Ext.form.FieldSet({
			title:'Intervalo de Fechas',
			style: 'position:absolute;left:10px;top:100px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 63,
			items:[{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:25px;top:15px',
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
									value: '01/01/'+anio,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
					},
					{
					layout:"column",
					defaults: {border: false},
					style: 'position:absolute;left:350px;top:15px',
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
	
	var	fromComprobante = new Ext.form.FieldSet({ 
			title:'Comprobante',
			style: 'position:absolute;left:10px;top:10px',
			border:true,
			width: 550,
			cls :'fondo',
			height: 70,
			items:[{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:10px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 35,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: '',
									id: 'txtComprobante',
									disabled:true,
									width: 140,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:165px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 30,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: '',
									id: 'txtProcedencia',
									disabled:true,
									width: 140,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
								}]
							}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:320px;top:15px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 30,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: '',
									id: 'txtFecha',
									disabled:true,
									width:100,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
								}]
							}]
					},botbuscarComprobante]
	})

	//------------------------------------------------------------------------------------------------------------

	//Creacion del formulario principal
	var Xpos = ((screen.width/2)-(300)); //375
	var Ypos = ((screen.height/2)-(650/2));
	fromReporteEjeCom = new Ext.FormPanel({
		applyTo: 'formReporteEjeCom',
		width:600, //700
		height: 220,
		title: "<H1 align='center'>Ejecuci&#243;n de Compromisos</H1>",
		frame:true,
		autoScroll:true,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:25px;', 
		items: [fromComprobante,
		        fromIntervaloFechas
		        ]
	});	
	fromReporteEjeCom.doLayout();
});	

//------------------------------------------------------------------------------------------------------------

function ventanaCatalogoComprobante()
{	
	//Datos del tipo de procedencia
	var opcproc = [[ 'Traspaso', 'SPGTRA' ], 
	               [ 'Rectificaciones', 'SPGREC' ], 
	               [ 'Insubsistencia', 'SPGINS' ], 
	               [ 'Cr&#233;dito Adicional', 'SPGCRA' ]];

	var stOpcProc = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : opcproc
	});
	
	//Creando datastore y columnmodel para el catalogo de comprobante
	var reVentana = Ext.data.Record.create([
	          {name: 'comprobante'}, 
	          {name: 'descripcion'},
	          {name: 'procede'},
	          {name: 'fecha'}
	]);
		                                        	
	var dsVentana =  new Ext.data.Store({
	         reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reVentana)
	});
		                                						
	var cmVentana = new Ext.grid.ColumnModel([ 
	         new Ext.grid.CheckboxSelectionModel,
		     {header: "<H1 align='center'>Comprobante</H1>", width: 30, sortable: true, dataIndex: 'comprobante'},
		     {header: "<H1 align='center'>Descripci&#243;n</H1>", width: 85, sortable: true, dataIndex: 'descripcion'},
		     {header: "<H1 align='center'>Procede</H1>", width: 20, sortable: true, dataIndex: 'procede'},
		     {header: "<H1 align='center'>Fecha</H1>", width: 20, sortable: true, dataIndex: 'fecha'},
	]);
		 
	//Creando grid para el catalogo de comprobante
	var gridVentanaCatalogoComprobante = new Ext.grid.GridPanel({
		width:650,
		height:220,
		frame:true,
		style: 'position:absolute;left:13px;top:170px',
		autoScroll:true,
		border:true,
		ds: dsVentana,
		cm: cmVentana,
		sm: new Ext.grid.CheckboxSelectionModel({singleSelect:true}),
		stripeRows: true,
		viewConfig: {forceFit:true}
	});
		
	var	formVentanaCatalogo = new Ext.FormPanel({
		width: 690,
		height: 410,
		style: 'position:absolute;left:5px;top:10px',
		frame: true,
		autoScroll:false,
		items: [{
				xtype:"fieldset", 
				title:'Cat&#225;logo de Comprobantes',
				style: 'position:absolute;left:15px;top:10px',
				border:true,
				cls: 'fondo',
				width: 650,
				height: 140,
				items:[{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:40px;top:20px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 90,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Comprobante',
										id: 'txtcmp',									
										width: 150,
										autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"},
										changeCheck: function(){
											var textvalor = this.getValue();
											dsVentana.filter('txtcodigo',textvalor,true);
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
						style: 'position:absolute;left:40px;top:100px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 90,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Compromiso',
										id: 'txtnumconcom',									
										width: 150,
										autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'0123456789');"}
									}]
							}]
						},						{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:350px;top:20px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 90,
								items: [{
										xtype: 'combo',
										fieldLabel: 'Procedencia',
										labelSeparator :'',
										id: 'cmbprocede',
										editable : false,
										displayField : 'col',
										valueField : 'tipo',
										typeAhead : true,
										triggerAction : 'all',
										width: 150,
										mode : 'local',
										emptyText:'Traspaso',
										store : stOpcProc,
										changeCheck: function(){
											var textvalor = this.getValue();
											dsVentana.filter('tipbieser',textvalor,true);
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
						layout:"column",
						defaults: {border: false},
						style: 'position:absolute;left:40px;top:60px',
						border:false,
						items:[{
								layout:"form",
								border:false,
								labelWidth:90,
								items:[{
									xtype:"datefield",
									labelSeparator :'',
									fieldLabel:"Desde",
									name:'Desde',
									id:'dtFechaInicial',
									allowBlank:true,
									width:100,
									binding:true,
									defaultvalue:'1900-01-01',
									hiddenvalue:'',
									allowBlank:false,
									value: '01/01/'+anio,
									autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
								}]
							}]
						},
						{
						layout:"column",
						defaults: {border: false},
						style: 'position:absolute;left:350px;top:60px',
						border:false,
						items:[{
								layout:"form",
								border:false,
								labelWidth:90,
								items:[{
										xtype:"datefield",
										labelSeparator :'',
										fieldLabel:"Hasta",
										name:'Hsta',
										id:'dtFechaFinal',
										allowBlank:true,
										width:100,
										binding:true,
										defaultvalue:'1900-01-01',
										hiddenvalue:'',
										allowBlank:false,
										value: new Date().format('d-m-Y'),
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
									}]
								}]
						},
						{
						layout:"column",
						defaults: {border: false},
						style: 'position:absolute;left:525px;top:95px', 
						border:false,
						items:[{
								layout:"form",
								border:false,
								items:[{
										xtype: 'button',
										fieldLabel: '',
										id: 'btagregar',
										text: 'Buscar',
										iconCls: 'menubuscar',
										handler: function(){
											if(Ext.getCmp('dtFechaInicial').getValue()>Ext.getCmp('dtFechaFinal').getValue()){
							    				Ext.MessageBox.show({
								 					title:'Advertencia',
								 					msg:'El rango de fechas no es valido!!!',
								 					buttons: Ext.Msg.OK,
								 					icon: Ext.MessageBox.WARNING
								 				});
							    			}
											else{
												obtenerMensaje('procesar','','Buscando Datos');
							   					
						   						//Buscar comprobante
								   				var JSONObject = {
													'operacion'     : 'buscarComprobantesPresupuestarios',
													'comprobante'   : Ext.getCmp('txtcmp').getValue(),
													'numconcom'     : Ext.getCmp('txtnumconcom').getValue(),
													'procede'		: Ext.getCmp('cmbprocede').getValue(),
													'fecdesde'		: Ext.getCmp('dtFechaInicial').getValue().format('d/m/Y'),
													'fechasta'		: Ext.getCmp('dtFechaFinal').getValue().format('d/m/Y'),
									   				'filtro'        : 'EJECUCION_COMPROMISO'
								   				}
								   				
								   				var ObjSon = JSON.stringify(JSONObject);
								   				var parametros = 'ObjSon='+ObjSon; 
								   				Ext.Ajax.request({
								   					url : '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
								   					params : parametros,
								   					method: 'POST',
								   					success: function ( resultado, request){
								   						Ext.Msg.hide();
								   						var datos = resultado.responseText;
								   						var objOrdCom = eval('(' + datos + ')');
								   						if(objOrdCom!=''){
								   							if(objOrdCom!='0'){
								   								if(objOrdCom.raiz == null || objOrdCom.raiz ==''){
								   									Ext.MessageBox.show({
													 					title:'Advertencia',
													 					msg:'No existen datos para mostrar',
													 					buttons: Ext.Msg.OK,
													 					icon: Ext.MessageBox.WARNING
													 				});
																}
																else{
								   									gridVentanaCatalogoComprobante.store.loadData(objOrdCom);
																}
								   							}
								   							else{
								   								Ext.MessageBox.show({
													 				title:'Advertencia',
													 				msg:'',
													 				buttons: Ext.Msg.OK,
													 				icon: Ext.MessageBox.WARNING
													 			});
								   							}
								   						}
								   					}//fin del success	
								   				});//fin del ajax request
											}
										}
									}]
								}]
						}]
				},gridVentanaCatalogoComprobante]
	});
	
    var ventanaEstructura = new Ext.Window({
    	width:700, 
        height:480,
    	border:false,
    	modal: true,
    	closable:false,
    	frame:true,
    	title:"<H1 align='center'>Cat&#225;logo de Comprobantes</H1>",
    	items:
    		[formVentanaCatalogo],
    		buttons:[{
       			text:'Aceptar',  
       	        handler: function(){
    				var registro = gridVentanaCatalogoComprobante.getSelectionModel().getSelected();	        	
    				if(registro!= undefined){
						Ext.getCmp('txtComprobante').setValue(registro.get('comprobante'));
						Ext.getCmp('txtProcedencia').setValue(registro.get('procede'));
						Ext.getCmp('txtFecha').setValue(registro.get('fecha'));
						gridVentanaCatalogoComprobante.destroy();
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
    ventanaEstructura.show();			
}

function irImprimir(){
	var valido = true;
    var fecdes = Ext.getCmp('dtFechaDesde').getValue().format('Y-m-d');
    var fechas = Ext.getCmp('dtFechaHasta').getValue().format('Y-m-d');
	
	if(fecdes>fechas){
		valido = false;
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'El Rango de Busqueda por Fecha no es correcto !!!',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});
	}
	if(valido){
		comprobante = Ext.getCmp('txtComprobante').getValue();
		procede = Ext.getCmp('txtProcedencia').getValue();
		fecha = Ext.getCmp('txtFecha').getValue();
		if(comprobante!="" && procede!="" && fecha!=""){
			var datos = "?txtcomprobante="+comprobante+"&txtprocede="+procede
					   +"&txtfecha="+fecha+"&txtfecdes="+fecdes+"&txtfechas="+fechas
					   +"&tipoformato="+"0";
			imprimir(datos);
		}
		else{
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Por Favor Seleccione un Comprobante.... !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
	}
}

function imprimir(datos)
{
	var myJSONObject =
	{
		'operacion'   : 'buscarFormato',
		'sistema'	  : 'SPG',
		'seccion'     : 'REPORTE',
		'variable'    : 'COMPROMISOS',
		'valor'		  : 'sigesp_spg_rpp_ejecucion_compromisos.php',
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
			var pagina = "reportes/"+formato+datos;
			window.open(pagina,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,resizable=yes,location=no");
		},
		failure: function (result, request){ 
			Ext.MessageBox.alert('Error', 'error al accesar al sistema.'); 
		}
	})
}