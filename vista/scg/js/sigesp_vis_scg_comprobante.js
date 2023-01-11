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

var fromComprobanteContable = null; //varibale para almacenar la instacia de objeto de formulario 
var gridDetContables = null;
var Actualizar = null;
var cmboperacion = null;
var procesando = false;
barraherramienta    = true;
var cont_cat = 0; 
var componente = 0; 
var dsDetalles = null;
var gridDetalles = null;
var prefijocmp = null;

Ext.onReady(function() {
	
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
		Ext.QuickTips.init();
		Ext.Ajax.timeout=36000000000;
	//-------------------------------------------------------------------------------------------------------------------------	

    		//Creacion del combo prefijo
		var rePrefijo = Ext.data.Record.create([
	          {name: 'prefijo'}
	     ]);

		dsPrefijo =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "prefijo"},rePrefijo)			
		});
		
		CmbPrefijo = new Ext.form.ComboBox({
			store: dsPrefijo,
			labelSeparator :'',
			fieldLabel:' Comprobante',
			displayField:'prefijo',
			valueField:'prefijo',
			name: 'prefijo',
			width:80,
			listWidth: 80, 
			id:'prefijo',
			typeAhead: true,
			binding:true,
			defaultvalue:'---',
			emptyText:'Prefijo',
			allowBlank:true,
			selectOnFocus:true,
			mode:'local',
			triggerAction:'all',
			valor:'',
                        listeners: {'select': function()
					{
                                            if (Actualizar == null)
                                            {
                                		gridDetContables.store.removeAll();
                                                NroComprobante(this.getValue());
                                            }
                                            else
                                            {
                                                Ext.getCmp('prefijo').setValue(prefijocmp);
                                            }
					}
                        }
		});
		//Fin combo prefijo

    
	//componente catalogo de proveedores
	var reCatProveedor = Ext.data.Record.create([
		{name: 'cod_pro'}, //campo obligatorio                             
		{name: 'nompro'},  //campo obligatorio
		{name: 'dirpro'},  //campo obligatorio
		{name: 'rifpro'}   //campo obligatorio
	]);

	var comcampocatproveedor = new com.sigesp.vista.comCatalogoProveedor({
		idComponente:'scguno',
		reCatalogo: reCatProveedor,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
		parametros: "ObjSon={'operacion': 'buscarProveedores'",
		soloCatalogo: true,
		arrSetCampo:[{campo:'cod_pro',valor:'cod_pro'},
				     {campo:'nompro',valor:'nompro'}],
		numFiltroNoVacio: 1
	});
	
	//componente catalogo de beneficiarios
	var reCatBeneficiario = Ext.data.Record.create([
		{name: 'ced_bene'}, //campo obligatorio                             
		{name: 'nombene'},  //campo obligatorio
		{name: 'apebene'},  //campo obligatorio
		{name: 'dirbene'}   //campo obligatorio
	]);

	var comcampocatbeneficiario = new com.sigesp.vista.comCatalogoBeneficiario({
		idComponente:'scguno',
		reCatalogo: reCatBeneficiario,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
		parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
		soloCatalogo: true,
		arrSetCampo:[{campo:'cod_pro',valor:'ced_bene'},
                             {campo:'nompro',valor:'nombene'}],
		numFiltroNoVacio: 1
	});
	
	//creando store para el combo destino
	var destino = [
	    ['Proveedor','P'],
		['Beneficiario','B']
	]; 

	var stdestino = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : destino
	});
	//fin creando store para el combo destino 

	//creando objeto combo destino
	var cmbdestino = new Ext.form.ComboBox({
		store : stdestino,
		fieldLabel : 'Destino ',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'tipo_destino',
		binding:true,
		hiddenvalue:'',
		defaultvalue:'-',
		allowBlank:true,
		width:130,
		typeAhead: true,
		emptyText:'Seleccione',
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local',
		listeners: {
			'select': function(valor){
				if(valor.getValue()=="P") {
					comcampocatproveedor.mostrarVentana();
				}
				else{
					comcampocatbeneficiario.mostrarVentana();
				}
			}
		}
	});
	
	//-----------------------------------------------------------------------------------------------
	
	//creando datastore y columnmodel para la grid de los detalles contables
	var reDetContables = Ext.data.Record.create([
	    {name: 'codban'},
		{name: 'ctaban'},
		{name: 'canart'},
	    {name: 'sc_cuenta'},
	    {name: 'denominacion'},
	    {name: 'status'},
	    {name: 'procede_doc'},
	    {name: 'documento'},
	    {name: 'descripcion'},
	    {name: 'monto'},
	    {name: 'debhab'}
	    
	]);
	
	var dsDetContables =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetContables)
	});
						
	var cmDetContables = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
        {header: "<CENTER>Cuenta</CENTER>", width: 60, align: 'center', sortable: true, dataIndex: 'sc_cuenta'},
        {header: "<CENTER>Descripci&#243;n</CENTER>", width: 80, sortable: true, dataIndex: 'descripcion',editor: new Ext.form.TextField({allowBlank: false})},
        {header: "<CENTER>Procede</CENTER>", width: 30, sortable: true, dataIndex: 'procede_doc', align: 'center'},
		{header: "<CENTER>Documento</CENTER>", width: 40, sortable: true, dataIndex: 'documento', align: 'center',editor: new Ext.form.TextField({allowBlank: false})},
		{header: "<CENTER>Operaci&#243;n</CENTER>", type: 'float', width: 40, align: 'center', sortable: true, dataIndex: 'debhab', renderer: MostrarValor},
		{header: "<CENTER>Monto</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'monto'},
		
	]);
	//fin del datastore y columnmodel para la grid de bienes
	
	//creando grid para los detalles de bienes
	gridDetContables = new Ext.grid.EditorGridPanel({
 		width:900,
 		height:300,
		frame:true,
		title:"<H1 align='center'>Detalles Contables</H1>",
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		style: 'position:absolute;left:10px;top:200px',
		autoScroll:true,
 		border:true,
 		ds: dsDetContables,
   		cm: cmDetContables,
   		stripeRows: true,
  		viewConfig: {forceFit:true},
		tbar:[{
	        text:'Agregar Cuenta Contable',
	        tooltip:'Agregar Cuenta',
	        iconCls:'agregar',
	        id: 'btagrebie',
	        handler: function()
			{
				AgregarContable();
			}
		},{
			text:'Eliminar Detalle Contable',
			tooltip:'Eliminar Detalle',
			iconCls:'remover',
			id:'btelibie',
			handler: function()
			{
				arreglo = gridDetContables.getSelectionModel().getSelections();
				if(arreglo.length >0)
				{
					for(var i = arreglo.length - 1; i >= 0; i--)
					{
						gridDetContables.getStore().remove(arreglo[i]);
					}
				}
				else
				{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Debe seleccionar el registro a Eliminar!!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
				acumularTotales();
			}		
		},
			{
	        text:'Agregar Cuentas Masivas',
	        tooltip:'Agregar Cuentas Masivas',
	        iconCls:'procesar',
	        id: 'btagrelote',
	        handler: function()
				{
					AgregarEnLote();
				}
			},
			{
	        text:'Comprobante Carga Masiva',
	        tooltip:'Comprobante Carga Masiva',
	        iconCls:'procesar',
	        id: 'btcmplote',
	        handler: function()
				{
					AgregarEnLote2();
				}
			}			
		]
	});
	
	//-------------------------------------------------------------------------------------
	
	//Metodo que realiza cambios despues de editar la grid de los detalles contables
	gridDetContables.on('afteredit', function(Obj){
		acumularTotales();
	});
	
	//-------------------------------------------------------------------------------------------------------------------------	

	var fieldsetTotales = new Ext.form.FieldSet({
		width: 900,
		height: 55,
		title: 'Totales',
		style: 'position:absolute;left:10px;top:510px',
		cls :'fondo',
		items: [{
			layout:"column",
			border:false,
			items: [{
				layout:"form",
				border:false,
				laberWidth: 90,
				items: [{
					xtype:"textfield",
					fieldLabel: 'Total Debe',
					labelStyle :'font-weight: bold',
					style:'font-weight: bold; border:none; background:#f1f1f1',
					labelSeparator:'',
					readOnly:true,
					id:'totaldeb',
					width:150
				}]
			},{
				layout:"form",
				border:false,
				laberWidth: 90,
				style:"padding-left:15px",
				items: [{												
						xtype:"textfield",
						fieldLabel: 'Total Haber',
						labelStyle :'font-weight: bold',
						style:'font-weight: bold; border:none; background:#f1f1f1',
						readOnly:true,
						labelSeparator:'',
						id:'totalhab',
						width:150
					}]
			},{
				layout:"form",
				border:false,
				style:"padding-left:15px",
				laberWidth: 90,
				items: [{
						xtype:"textfield",
						fieldLabel: 'Diferencia',
						labelStyle :'font-weight: bold',
						style:'font-weight: bold; border:none; background:#f1f1f1',
						readOnly:true,
						labelSeparator:'',
						id:'diferencia',
						width:150
					}]
			}]
		}]
	});
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//Creando formulario principal 
	var Xpos = ((screen.width/2)-(475));
	var Ypos = ((screen.height/2)-(330));
	fromComprobanteContable = new Ext.form.FormPanel({
		title: "<H1 align='center'>Comprobantes Contables</H1>",
		applyTo: 'formComprobanteContable',
		width: 950,
		height: 600,
		id: 'formdos',
		style: 'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		frame: true,
		autoScroll:true,
		items: [{
				xtype:"fieldset", 
			    title:'Datos del Comprobante',
			    style: 'position:absolute;left:10px;top:5px',
			    border:true,
			    width: 900,
			    cls :'fondo',
			    height: 190,
			    items: [{
			    		layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:15px;top:20px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 100,
								items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Procedencia',
										id: 'procede',
										value: 'SCGCMP',
										readOnly: true,
										allowBlank:false,
										width:100,
										binding:true,
										defaultvalue:'',
										hiddenvalue:'',
									}]
								}]
					    },
					    {
				    	layout:"column",
					    defaults: {border: false},
					    style: 'position:absolute;left:550px;top:20px',
					    border:false,
						items:[{
								layout:"form",
								border:false,
								labelWidth:50,
								items:[{
										xtype:"datefield",
										labelSeparator :'',
										fieldLabel:"Fecha",
										name:'Fecha',
										id:'fecha',
										allowBlank:false,
										width:100,
										binding:true,
										defaultvalue:'1900-01-01',
										hiddenvalue:'',
										value: new Date().format('d-m-Y'),
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
									}]
								}]
					    },
					    {
				    	layout:"column",
					    defaults: {border: false},
					    style: 'position:absolute;left:15px;top:50px',
					    border:false,
					    items:[{
						    	layout:"column",
							    border:false,
								labelWidth:100,
								items:[{
                                                                                layout: "form",
                                                                                border: true,
                                                                                labelWidth: 100,
                                                                                items: [CmbPrefijo]
                                                                        },
                                                                        {
                                                                            layout: "form",
                                                                            border: false,
                                                                            labelWidth: 10,
                                                                            items: [{
                                                                                            xtype: 'textfield',
                                                                                            labelSeparator :'',
                                                                                            id: 'comprobante',
                                                                                            autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
                                                                                            width: 150,
                                                                                            formatonumerico:false,
                                                                                            binding:true,
                                                                                            hiddenvalue:'',
                                                                                            defaultvalue:'',
                                                                                            allowBlank:false,
                                                                                            readOnly:false,
                                                                                            listeners:{
                                                                                                    'blur' : function(campo)
                                                                                                    {
                                                                                                            llenarCampoNumdoc(campo.getValue());
                                                                                                            actualizarDocumento();
                                                                                                    }
                                                                                            }
                                                                                        }]
                                                                            }]
					    		}]
					    },
					    {
				    	layout:"column",
				    	defaults: {border: false},
				    	style: 'position:absolute;left:15px;top:80px',
				    	border:false,
				    	items:[{
				    		  	layout:"form",
				    		  	border:false,
				    		  	labelWidth:100,
				    		  	items:[{
					    			  xtype: 'textarea',
					    			  labelSeparator :'',
					    			  fieldLabel: 'Descripci&#243;n',
					    			  id: 'descripcion',
					    			  width: 700,
					    			  row: 2,
					    			  binding:true,
					    			  hiddenvalue:'',
					    			  defaultvalue:'',
					    			  allowBlank:false,
					    			  autoCreate: {tag: 'textarea', type: 'text', size: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@()?-+*');"},
					    		  }]
				    	  	}]
					   },
					   {
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:15px;top:140px',
						items: [{
								layout: "form",
								border: true,
								labelWidth: 100,
								items: [cmbdestino]
								},
								{
								layout: "form",
								border: false,
								labelWidth: 10,
								items: [{
										xtype: 'textfield',
										fieldLabel: '',
										labelSeparator :'',
										id: 'cod_pro',
										disabled:true,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'----------',
										allowBlank:true,
										width: 130
									}]
								},
								{
								layout: "form",
								border: false,
								labelWidth: 10,
								items: [{
										xtype: 'textfield',
										fieldLabel: '',
										labelSeparator :'',
										id: 'nompro',
										disabled:true,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'Ninguno',
										allowBlank:true,
										width: 400
									}]
								}]
					   },
		   			{
					xtype: 'hidden',
					id: 'ctaban',
					binding:true,
					hiddenvalue:'',
					defaultvalue:'-------------------------'
		   			},
					{
					xtype: 'hidden',
					id: 'codban',
					binding:true,
					hiddenvalue:'',
					defaultvalue:'---'
					},
					{
					xtype: 'hidden',
					id: 'codfuefin',
					binding:true,
					hiddenvalue:'',
					defaultvalue:'--'
					},
					{
					xtype: 'hidden',
					id: 'monto',
					binding:true,
					hiddenvalue:'',
					value:''
					}]
		},gridDetContables,fieldsetTotales]
	});	
        llenarCmbPrefijos();
}); //fin creando formulario principal con parametros de busqueda y grid de modificaciones
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//Funcion que llama al catalogo de comprobantes contables
	function irBuscar( )
        {
		CatalogoComprobante();
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que guardar el comprobante contable
	function irGuardar()
	{
		if (!procesando)
		{
			procesando=true;
			if(Ext.getCmp('procede').getValue() != "SCGCMP")
			{
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'No puede editar un comprobante, que no fue generado por este modulo !!!',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
			else
			{
				var cadjson = '';
				var valido = true;
				var aux = formatoNumericoMostrar(0,2,'.',',','','','-','');
			   
				if(Ext.getCmp('diferencia').getValue()=='')
				{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Debe completar todo el detalle contable para guardar el comprobante !!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
					valido = false;
				}
				if(Ext.getCmp('totaldeb').getValue()==aux && Ext.getCmp('totaldeb').getValue()==aux)
				{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Debe completar todo el detalle contable para guardar el comprobante !!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
					valido = false;
				}
				if(Ext.getCmp('diferencia').getValue()!=aux)
				{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'La diferenica del detalle contable debe ser igual a 0,00 !!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
					valido = false;
				}
				var evento = 'INSERT';
				if(Actualizar!=null)
				{
					var evento = 'UPDATE';
				}
				cadjson = "{'operacion':'guardar','codsis':'"+sistema+"','nomven':'"+vista+"','evento':'"+evento+"',"+getJsonFormulario(fromComprobanteContable);
				cadjson += ",'detallesContables':[";	
				if(valido)
				{
					var numDetalle = 0;
					gridDetContables.store.each(function (reDetCon){
						if(numDetalle==0){
							cadjson +="{'sc_cuenta':'"+reDetCon.get('sc_cuenta')+"','procede_doc':'"+reDetCon.get('procede_doc')+"'," +
									   "'documento':'"+reDetCon.get('documento')+"','debhab':'"+reDetCon.get('debhab')+"'," +
									   "'descripcion':'"+reDetCon.get('descripcion')+"','monto':'"+reDetCon.get('monto')+"'}";
						}
						else{
							cadjson +=",{'sc_cuenta':'"+reDetCon.get('sc_cuenta')+"','procede_doc':'"+reDetCon.get('procede_doc')+"'," +
									   "'documento':'"+reDetCon.get('documento')+"','debhab':'"+reDetCon.get('debhab')+"'," +
									   "'descripcion':'"+reDetCon.get('descripcion')+"','monto':'"+reDetCon.get('monto')+"'}";
						}
						numDetalle++;
					});
				}
				cadjson += "]}";
				if(valido)
				{
					try
					{
						var objjson = Ext.util.JSON.decode(cadjson);
						if(typeof(objjson) == 'object')
						{
							var parametros = 'ObjSon=' + cadjson;
							Ext.Ajax.request({
								url : '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
								params : parametros,
								method: 'POST',
								success: function ( result, request)
								{
									datos = result.responseText;
									Ext.Msg.hide();
									var datajson = eval('(' + datos + ')');
									if(datajson.raiz.valido==true)
									{	
										Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
										irNuevo();
									}
									else
									{
										Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
									}
									procesando=false;									
								}
							});
						}
					}	
					catch(e)
					{
						alert('Verifique los datos, esta insertando caracteres invalidos '+e);
						procesando=false;
					}
				}
			}
		}
		else
		{
			Ext.MessageBox.alert('Error', 'Espere un momento el sistema esta procesando.');
		}		
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que limpia la pantalla para generar un nuevo comprobante contable
	function irNuevo()
        {
		limpiarFormulario(fromComprobanteContable);
		gridDetContables.store.removeAll();
		Actualizar = null;
                llenarCmbPrefijos();		
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que elimina el comprobante contable
	function irEliminar()
        {
		if(Ext.getCmp('procede').getValue() != "SCGCMP")
                {
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'No se puede eliminar comprobante, que no fue generado por este modulo !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			});
		}
		else {
			var aux = formatoNumericoMostrar(0,2,'.',',','','','-','');
			var valido = true;
			function respuesta(btn)
                        {
				if(btn=='yes')
                                {
					var cadjson = "{'operacion':'eliminar','codsis':'"+sistema+"','nomven':'"+vista+"',"+getJsonFormulario(fromComprobanteContable);
					cadjson += "}";
			        if(valido)
                                {
			        	try
                                        {
			        		var objjson = Ext.util.JSON.decode(cadjson);
			        		if(typeof(objjson) == 'object')
                                                {
			        			var parametros = 'ObjSon=' + cadjson;
			        			Ext.Ajax.request({
			        				url : '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
			        				params : parametros,
			        				method: 'POST',
			        				success: function ( result, request){
				        				datos = result.responseText;
										Ext.Msg.hide();
										var datajson = eval('(' + datos + ')');
										if(datajson.raiz.valido==true)
										{	
											Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
											irNuevo();
										}
										else
										{
											Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
										}
			        		    	}
			        			});
			        		}
			        	}	
			        	catch(e){
			        		alert('Verifique los datos, esta insertando caracteres invalidos '+e);
			        	}
			        }
				}
			}	
			if(Actualizar)
                        {
				Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta); 
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
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 
	
	//Funcion que acumula los totales de la grid de detalle contable
	function acumularTotales()
	{
		var totaldebe = 0;
		var totalhaber = 0;
		
		gridDetContables.store.each(function (reDetCon){
			if(reDetCon.get('debhab')=='D'){
				montodebe = parseFloat(ue_formato_operaciones(reDetCon.get('monto')));
				totaldebe = totaldebe+montodebe;
			}
			else if(reDetCon.get('debhab')=='H'){
				montohaber = parseFloat(ue_formato_operaciones(reDetCon.get('monto')));
				totalhaber = totalhaber+montohaber;
			}
		})
		
				
		resta=totaldebe-totalhaber;
		Ext.getCmp('totaldeb').setValue(formatoNumericoMostrar(totaldebe,2,'.',',','','','-',''));
		Ext.getCmp('totalhab').setValue(formatoNumericoMostrar(totalhaber,2,'.',',','','','-',''));
		Ext.getCmp('diferencia').setValue(formatoNumericoMostrar(resta,2,'.',',','','','-',''));
	}
	//-------------------------------------------------------------------------------------------------------------------------	

	//Funcion que valida si el comprobante y la descripcion estan llenos para poder agregar los detalles contables
	function validarDocumentoDescripcion()
        {
		var unidadOk = true;
		if(Ext.getCmp('comprobante').getValue()=='' || Ext.getCmp('descripcion').getValue()==''){
			unidadOk = false;
		}
		return unidadOk;
	}
	
	//-------------------------------------------------------------------------------------------------------------------------	
	function MostrarValor(valor)
        {
		if(valor=='D'){
			return 'Debe';
		}
		else{
			return 'Haber';
		}
	}	
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//Funcion que completa el comprbante con ceros para alcanzar la longitud maxima
	function llenarCampoNumdoc(campo)
	{
		var myJSONObject = {
				"operacion" :'llenar_documento',
				"numdoc"    : campo
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) 
			{ 
	    		var numdoc = result.responseText;
	    		if (numdoc.length != 0)
	    		{
	    			Ext.getCmp('comprobante').setValue(numdoc);
	    		}
			}
		});
	}
	//----------------------------------------------------------------------------------------------------------------------------------

	//Funcion que completa el comprbante con ceros para alcanzar la longitud maxima
	function NroComprobante(prefijo)
	{
            if (Actualizar==null)
            {
                Ext.getCmp('comprobante').setValue('');    
                var myJSONObject = {
				"operacion" :'cargar_nrodocumento',
                                "prefijo" : prefijo
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) 
			{ 
                            var numdoc = result.responseText;
                            if(numdoc == "0000000000000-2")
                            {
	    			Ext.Msg.show({
	    				title:'Mensaje',
	    				msg: 'El sistema tiene configurado el uso de prefijo y este usuario no tiene uno asignado !!!',
	    				buttons: Ext.Msg.OK,
	    				fn: function(){ location.href = 'sigesp_vis_scg_inicio.html'},
	    				icon: Ext.MessageBox.INFO
	    			});
                            }
                            else if (numdoc != "0000000000000-1")
                            {
                                    Ext.getCmp('comprobante').setValue(numdoc);
                            }
			}
		});
            }
	}
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//Funcion que completa el comprbante con ceros para alcanzar la longitud maxima
	function CatalogoComprobante()
        {
		//componente catalogo de proveedores
		var reCatProv = Ext.data.Record.create([
			{name: 'cod_pro'}, //campo obligatorio                             
			{name: 'nompro'},  //campo obligatorio
			{name: 'dirpro'},  //campo obligatorio
			{name: 'rifpro'}   //campo obligatorio
		]);

		var comcampocatprov = new com.sigesp.vista.comCatalogoProveedor({
			idComponente:'scgdos',
			reCatalogo: reCatProv,
			rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
			parametros: "ObjSon={'operacion': 'buscarProveedores'",
			soloCatalogo: true,
			arrSetCampo:[{campo:'catcodpro',valor:'cod_pro'},
					     {campo:'catnom_pro',valor:'nompro'}],
			numFiltroNoVacio: 1
		});
		
		//componente catalogo de beneficiarios
		var reCatBene = Ext.data.Record.create([
			{name: 'ced_bene'}, //campo obligatorio                             
			{name: 'nombene'},  //campo obligatorio
			{name: 'apebene'},  //campo obligatorio
			{name: 'dirbene'}   //campo obligatorio
		]);

		var comcampocatbene = new com.sigesp.vista.comCatalogoBeneficiario({
			idComponente:'scgdos',
			reCatalogo: reCatBene,
			rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
			parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
			soloCatalogo: true,
			arrSetCampo:[{campo:'catcodpro',valor:'ced_bene'},
					     {campo:'catnom_pro',valor:'nombene'}],
			numFiltroNoVacio: 1
		});
		
		//creando store para el combo destino
		var destinoProBen = [
		    ['Proveedor','P'],
			['Beneficiario','B']
		]; 

		var stdestinoProBen = new Ext.data.SimpleStore({
			fields : [ 'etiqueta', 'valor' ],
			data : destinoProBen
		});
		//fin creando store para el combo destino 

		//creando objeto combo destino
		var cmbdestinoProBen = new Ext.form.ComboBox({
			store : stdestinoProBen,
			fieldLabel : 'Tipo ',
			labelSeparator : '',
			editable : false,
			displayField : 'etiqueta',
			valueField : 'valor',
			id : 'combodestino',
			binding:true,
			hiddenvalue:'',
			defaultvalue:'-',
			allowBlank:false,
			width:130,
			typeAhead: true,
			emptyText:'Seleccione',
			triggerAction:'all',
			forceselection:true,
			binding:true,
			mode:'local',
			listeners: {
				'select': function(valor){
					if(valor.getValue()=="P") {
						comcampocatprov.mostrarVentana();
					}
					else{
						comcampocatbene.mostrarVentana();
					}
				}
			}
		});
		
		//Creacion del combo procedencia
		var reProcedencia = Ext.data.Record.create([
	          {name: 'procede'},
	          {name: 'desproc'}
	     ]);

		dsProcedencia =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "procede"},reProcedencia)			
		});
		
		CmbProcedencia = new Ext.form.ComboBox({
			store: dsProcedencia,
			labelSeparator :'',
			fieldLabel:'Procedencia',
			displayField:'desproc',
			valueField:'procede',
			name: 'procedencia',
			width:350,
			listWidth: 350, 
			id:'procedencia',
			typeAhead: true,
			binding:true,
			defaultvalue:'---',
			emptyText:'----Seleccione----',
			allowBlank:true,
			selectOnFocus:true,
			mode:'local',
			triggerAction:'all',
			valor:''
		});
		//Fin combo procedencia
		
		//creando datastore y columnmodel para la grid de los comprobantes contables
		var reComprobante = Ext.data.Record.create([
		    {name: 'comprobante'},
			{name: 'procede'},
			{name: 'descripcion'},
		    {name: 'fecha'},
		    {name: 'codban'},
		    {name: 'ctaban'},
		    {name: 'tipo_destino'},
		    {name: 'cod_pro'},
		    {name: 'ced_bene'},
		    {name: 'monto'}
		    
		]);
		
		var dsComprobante =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reComprobante)
		});
							
		var cmComprobante = new Ext.grid.ColumnModel([
	        {header: "<CENTER>Comprobante</CENTER>", width: 60, align: 'center', sortable: true, dataIndex: 'comprobante'},
	        {header: "<CENTER>Descripci&#243;n</CENTER>", width: 60, sortable: true, dataIndex: 'descripcion'},
	        {header: "<CENTER>Procede</CENTER>", width: 30, sortable: true, dataIndex: 'procede', align: 'center'},
			{header: "<CENTER>Fecha</CENTER>", width: 30, sortable: true, dataIndex: 'fecha', align: 'center'},
			{header: "<CENTER>Proveedor</CENTER>", type: 'float', width: 40, align: 'center', sortable: true, dataIndex: 'cod_pro'},
			{header: "<CENTER>Beneficiario</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'ced_bene'},
			{header: "<CENTER>Monto</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'monto'},
		]);
		//fin del datastore y columnmodel para la grid de bienes
		
		//creando grid para los detalles de bienes
		gridComprobante = new Ext.grid.GridPanel({
	 		width:780,
	 		height:250,
			frame:true,
			title:"",
			style: 'position:absolute;left:15px;top:190px',
			autoScroll:true,
	 		border:true,
	 		ds: dsComprobante,
	   		cm: cmComprobante,
	   		stripeRows: true,
	  		viewConfig: {forceFit:true}
		});
		
		//Metodo que realiza cambios despues de editar la grid de los comprobantes
		gridComprobante.on({
			'rowdblclick': {
				fn: function(grid, numFila, evento){
					var registro = grid.getStore().getAt(numFila);
					aceptar(registro);
	 		    }
			}
		});
		
		var	formVentanaCatalogo = new Ext.FormPanel({
			width: 830,
			height: 480,
			style: 'position:absolute;left:5px;top:10px',
			frame: true,
			autoScroll:false,
			items: [{
					xtype:"fieldset", 
					title:'Datos del Comprobante Contable',
					style: 'position:absolute;left:15px;top:10px',
					border:true,
					cls: 'fondo',
					width: 780,
					height: 170,
					items:[{
							layout: "column",
							defaults: {border: false},
							style: 'position:absolute;left:15px;top:20px',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 100,
									items: [{
											xtype: 'textfield',
											labelSeparator :'',
											fieldLabel: 'Comprobante',
											id: 'numcomprobante',									
											width: 170,
											autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15'},
											changeCheck: function(){
												var textvalor = this.getValue();
												dsComprobante.filter('comprobante',textvalor,true);
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
							style: 'position:absolute;left:15px;top:80px',
							items: [{
									layout: "form",
									border: true,
									labelWidth: 100,
									items: [cmbdestinoProBen]
									},
									{
									layout: "form",
									border: false,
									labelWidth: 10,
									items: [{
											xtype: 'textfield',
											fieldLabel: '',
											labelSeparator :'',
											id: 'catcodpro',
											disabled:true,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'----------',
											allowBlank:true,
											width: 130
										}]
									},
									{
									layout: "form",
									border: false,
									labelWidth: 10,
									items: [{
											xtype: 'textfield',
											fieldLabel: '',
											labelSeparator :'',
											id: 'catnom_pro',
											disabled:true,
											binding:true,
											hiddenvalue:'',
											defaultvalue:'Ninguno',
											allowBlank:true,
											width: 310
										}]
									}]
							},
							{
					    	layout:"column",
						    defaults: {border: false},
						    style: 'position:absolute;left:520px;top:20px',
						    border:false,
							items:[{
									layout:"form",
									border:false,
									labelWidth:100,
									items:[{
											xtype:"datefield",
											labelSeparator :'',
											fieldLabel:"Fecha Desde",
											name:'Fecha',
											id:'fecdesde',
											allowBlank:false,
											width:100,
											binding:true,
											defaultvalue:'1900-01-01',
											hiddenvalue:'',
											value: new Date().format('01-m-Y'),
											autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
										}]
									}]
							},
							{
					    	layout:"column",
						    defaults: {border: false},
						    style: 'position:absolute;left:520px;top:50px',
						    border:false,
							items:[{
									layout:"form",
									border:false,
									labelWidth:100,
									items:[{
											xtype:"datefield",
											labelSeparator :'',
											fieldLabel:"Hasta",
											name:'Fecha',
											id:'fechasta',
											allowBlank:false,
											width:100,
											binding:true,
											defaultvalue:'1900-01-01',
											hiddenvalue:'',
											value: new Date().format('d-m-Y'),
											autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10', onkeypress: "return keyRestrict(event,'0123456789/');"}
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
									labelWidth: 100,
									items: [CmbProcedencia]
									}]
							},gridComprobante,
							{
							layout:"column",
							defaults: {border: false},
							style: 'position:absolute;left:670px;top:115px', 
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
												if(Ext.getCmp('fecdesde').getValue().format('Y-m-d')<=Ext.getCmp('fechasta').getValue().format('Y-m-d')){
													obtenerMensaje('procesar','','Buscando Datos');
								   					
							   						//Buscar ordenes de compra
									   				var JSONObject = {
									   					'operacion'   : 'buscarComprobantesContables',
									   					'comprobante' : Ext.getCmp('numcomprobante').getValue(),
									   					'procede'     : Ext.getCmp('procedencia').getValue(),
									   					'tipo'        : Ext.getCmp('combodestino').getValue(),
									   					'provben'     : Ext.getCmp('catcodpro').getValue(),
									   					'fecdesde'    : Ext.getCmp('fecdesde').getValue().format('Y-m-d'),
									   					'fechasta'    : Ext.getCmp('fechasta').getValue().format('Y-m-d'),
									   				}
									   				
									   				var ObjSon = JSON.stringify(JSONObject);
									   				var parametros = 'ObjSon='+ObjSon; 
									   				Ext.Ajax.request({
									   					url : '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
									   					params : parametros,
									   					method: 'POST',
									   					success: function ( resultado, request){
									   						Ext.Msg.hide();
									   						var datos = resultado.responseText;
									   						var objCmp = eval('(' + datos + ')');
									   						if(objCmp!=''){
									   							if(objCmp!='0'){
									   								if(objCmp.raiz == null || objCmp.raiz ==''){
									   									Ext.MessageBox.show({
														 					title:'Advertencia',
														 					msg:'No existen datos para mostrar',
														 					buttons: Ext.Msg.OK,
														 					icon: Ext.MessageBox.WARNING
														 				});
																		gridComprobante.store.removeAll();
																	}
																	else{
									   									gridComprobante.store.loadData(objCmp);
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
												else{
													Ext.MessageBox.show({
										 				title:'Mensaje',
										 				msg:'El rango de fechas no es correcto !!!',
										 				buttons: Ext.Msg.OK,
										 				icon: Ext.MessageBox.WARNING
										 			});
												}
											}
										}]
									}]
							},
							{
							layout:"column",
							defaults:{border: false},
							style:'position:absolute;left:600px;top:420px', 
							border:false,
							items:[{
									buttons: [{
										text:'Aceptar',  
										handler: function()
										{
											var registro = gridComprobante.getSelectionModel().getSelected();	
										    aceptar(registro);
										}
									},
									{
										text: 'Salir',
										handler: function()
										{
											ventanaEstructura.destroy();
										}
									}]
								}]
							}]
					}]
		});
		llenarCmbProcedencia();
		formVentanaCatalogo.add(gridComprobante);
		
	    var ventanaEstructura = new Ext.Window({
	    	width:850, 
	        height:520,
	        closable:true,
	    	border:false,
	    	modal: true,
	    	frame:true,
	    	title:"<H1 align='center'>Cat&#225;logo de Comprobantes Contables</H1>",
	    	items:[formVentanaCatalogo], 
	    });
	    
	    ventanaEstructura.show();
	    
	    //Funcion que agrega los datos al combo procedencia
	    function llenarCmbProcedencia()
            {
			var myJSONObject ={
					"operacion": 'buscarProcedencia',
			};	
			var ObjSon=JSON.stringify(myJSONObject);
			var parametros = 'ObjSon='+ObjSon; 
			Ext.Ajax.request({
				url : '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
				params : parametros,
				method: 'POST',
				success: function (resultado, request) { 
					var datosest = resultado.responseText;
					if(datosest!='')
					{
						var DatosEst = eval('(' + datosest + ')');
					}
					dsProcedencia.loadData(DatosEst);
				}//fin del success
			});//fin del ajax request	
		}

            
	    //function que setea los datos en el formulario principal
	    function aceptar(registro)
	    {
	    	setDataFrom(fromComprobanteContable,registro);
                prefijocmp = registro.get('comprobante');
                prefijocmp = prefijocmp.substring(0,6);
                Ext.getCmp('prefijo').setValue(prefijocmp);
	    	Actualizar=true;
			obtenerMensaje('procesar','','Buscando Datos');
			//Buscar los detalles contables
			var JSONObject = {
				'operacion'   : 'buscarDetallesContables',
				'comprobante' : registro.get('comprobante'),
				'procede'     : registro.get('procede'),
				'fecha'       : registro.get('fecha'),
				'codban'      : registro.get('codban'),
				'ctaban'      : registro.get('ctaban')
			}
			
			var ObjSon = JSON.stringify(JSONObject);
			var parametros = 'ObjSon='+ObjSon; 
			Ext.Ajax.request({
				url : '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
				params : parametros,
				method: 'POST',
				success: function ( resultado, request){
					Ext.Msg.hide();
					var datos = resultado.responseText;
					var objCmp = eval('(' + datos + ')');
					if(objCmp!=''){
						if(objCmp!='0'){
							if(objCmp.raiz == null || objCmp.raiz ==''){
								Ext.MessageBox.show({
				 					title:'Advertencia',
				 					msg:'No existen datos para mostrar',
				 					buttons: Ext.Msg.OK,
				 					icon: Ext.MessageBox.WARNING
								});
								gridDetContables.store.removeAll();
							}
							else{
								gridDetContables.store.loadData(objCmp);
								acumularTotales();
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
			gridComprobante.destroy();
			ventanaEstructura.destroy();
		}
	}
	
	//INICIO DEL FORMULARIO CONTABLE//
	function AgregarContable()
	{	
		cont_cat++; 
		if(Ext.getCmp('comprobante').getValue()=='' || Ext.getCmp('descripcion').getValue()==''){
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe llenar los campos Comprobante y Descripci&#243;n!!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
		}
		else{
			//Creando el campo de cuenta contable
			var reCuentaContable = Ext.data.Record.create([
				{name: 'sc_cuenta'}, //campo obligatorio                             
				{name: 'denominacion'}, //campo obligatorio
				{name: 'status'}
			]);
				
			//componente catalogo de proveedores
			var comcampocatcuentacontable = new com.sigesp.vista.comCatalogoCuentaContable({
				idComponente:'scg'+cont_cat,
				anchofieldset: 900,
				validarCuenta:true,
				valorStatus: '',
				reCatalogo: reCuentaContable,
				rutacontrolador:'../../controlador/scg/sigesp_ctr_scg_comcatcuentacontable.php',
				parametros: "ObjSon={'operacion': 'buscarCuentaContables'",
				posicion:'position:absolute;left:5px;top:125px', 
				tittxt:'Cuenta Contable',
				idtxt:'cuenta_con',
				campovalue:'sc_cuenta',
				anchoetiquetatext:215,
				anchotext:150,
				anchocoltext:0.43, 
				idlabel:'deno_cuenta',
				labelvalue:'denominacion',
				anchocoletiqueta:0.35, 
				anchoetiqueta:300,
				binding:'C',
				hiddenvalue:'',
				defaultvalue:'---',
				allowblank:false,
				numFiltroNoVacio: 1
			});
			//fin componente catalogo de proveedores
			
			//creando store para la operacion
			var operacontable = [['Debe','D'],
			                     ['Haber','H']
		                  		]; // Arreglo que contiene los Documentos que se pueden controlar
			
			var stoperacontable = new Ext.data.SimpleStore({
				fields : ['etiqueta','valor'],
				data : operacontable
			});
			//fin creando store para el combo operacion
		
			//creando objeto combo operacion
			var cmboperacontable = new Ext.form.ComboBox({
				store : stoperacontable,
				fieldLabel : 'Operaci&#243;n',
				labelSeparator : '',
				editable : false,
				emptyText:'Debe',
				displayField : 'etiqueta',
				valueField : 'valor',
				id : 'codopecont', // falta colocar el campo id correctamente
				width : 150,
				typeAhead : true,
				triggerAction : 'all',
				forceselection : true,
				binding : true,
				mode : 'local'
			});
		
			//Creacion del formulario de agregar contable
			var frmAgregarContable = new Ext.FormPanel({
				width: 870,
				height: 235, 
				style: 'position:absolute;left:5px;top:0px',
				frame: true,
				autoScroll:false,
				items:[{
						xtype:"fieldset", 
						title:'Datos del Documento',
						border:true,
						width: 850,
						height: 210,
						cls: 'fondo',
						items:[{
								style:'position:absolute;left:15px;top:15px',
								layout:"column",
								defaults:{border: false},
								items: [{
										layout:"form",
										border:false,
										labelWidth:215,
										items: [{
												xtype:'textfield',
												labelSeparator:'',
												fieldLabel:'Documento',
												name:'docgasto',
												id:'agrdoccon',	
												autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
												width: 185,
												value:Ext.getCmp('comprobante').getValue(),
											}]
										}]
								},
								{
								style:'position:absolute;left:15px;top:45px',
								layout:"column",
								defaults:{border: false},
								items: [{
										layout:"form",
										border:false,
										labelWidth:215,
										items: [{
												xtype:'textfield',
												labelSeparator:'',
												fieldLabel:'Descripci&#243;n',
												autoCreate: {tag: 'input', type: 'text', size: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@%&/\()?-+*[]{}');"},
												name:'desgasto',
												id:'catdescon',									
												width: 600,
												value:Ext.getCmp('descripcion').getValue(),
											}]
										}]
								},
								{
								style:'position:absolute;left:15px;top:75px',
								layout:"column",
								defaults:{border: false},
								items: [{
										layout:"form",
										border:false,
										labelWidth:215,
										items: [{
												xtype:'textfield',
												labelSeparator:'',
												fieldLabel:'Procedencia',
												name:'progasto',
												id:'catprocon',										
												width: 185,
												readOnly:true,
												value:'SCGCMP'
											}]
										}]
								},
								{
								style:'position:absolute;left:15px;top:105px',
								layout:"column",
								defaults:{border: false},
								items: [{
										layout:"form",
										border:false,
										labelWidth:215,
										items: [cmboperacontable]
										}]
								},comcampocatcuentacontable.fieldsetCatalogo,
								{
								style:'position:absolute;left:15px;top:165px',
								layout:"column",
								defaults:{border: false},
								items: [{
										layout:"form",
										border:false,
										labelWidth:215,
										items: [{
												xtype:'textfield',
												labelSeparator:'',
												fieldLabel:'Monto',
												name:'mongasto',
												id:'catmoncon',											
												width: 185,
												autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'0123456789.');"},
												listeners:{
													'blur':function(objeto)
													{
													var numero = objeto.getValue();
													valor = formatoNumericoMostrar(objeto.getValue(),2,'.',',','','','-','');
													objeto.setValue(valor);
													},
													'focus':function(objeto)
													{
														var numero = formatoNumericoEdicion(objeto.getValue());
														objeto.setValue(numero);
													}
												}
											}]
										}]
								}]
						}]  
			});
			
			var ventanaAgregarContable = new Ext.Window({
				title: "<H1 align='center'>Entrada de Movimientos Contables</H1>",
				y:10,
				width:880,
				height:300, 
				modal: true,
				closable:false,
				plain: false,
				frame:true,
				items:[frmAgregarContable],
				buttons: [{
							text:'Aceptar',
							handler:function(){
								var operacion = 'D';
								if(Ext.getCmp('codopecont').getValue()!=''){
									operacion = Ext.getCmp('codopecont').getValue();
								}
								
								if(Ext.getCmp('agrdoccon').getValue()=='' || Ext.getCmp('catdescon').getValue()=='' ||
									Ext.getCmp('catmoncon').getValue()==''	||  Ext.getCmp('cuenta_con').getValue()==''){
									Ext.Msg.show({
										title:'Mensaje',
										msg:'Debe completar todos los datos',
										buttons: Ext.Msg.OK,
										icon: Ext.MessageBox.INFO
									});
								}
								else{
									if (Ext.getCmp('catmoncon').getValue()=='0,00') {
										Ext.Msg.show({
											title:'Mensaje',
											msg:'El monto del monvimiento debe ser distinto de 0,00',
											buttons: Ext.Msg.OK,
											icon: Ext.MessageBox.INFO
										});
									} 
									else {
										var reDetCon = Ext.data.Record.create([
											{name: 'codban'},
											{name: 'ctaban'},
											{name: 'canart'},
											{name: 'sc_cuenta'},
											{name: 'status'},
											{name: 'procede_doc'},
											{name: 'documento'},
											{name: 'descripcion'},
											{name: 'monto'},
											{name: 'debhab'}
										]);
										var documento = Ext.getCmp('agrdoccon').getValue();
										var documento = documento.replace("&", " ");	
										
										var descripcion = Ext.getCmp('catdescon').getValue();
										var descripcion = descripcion.replace("&", " ");	
										
										var detconInt = new reDetCon({
											'sc_cuenta':Ext.getCmp('cuenta_con').getValue(),
											'documento':documento,
											'descripcion':descripcion,
											'procede_doc':Ext.getCmp('catprocon').getValue(),
											'debhab':operacion,
											'monto':Ext.getCmp('catmoncon').getValue(),
										});
									
										if(gridDetContables.getStore().getCount()==0){
											gridDetContables.store.insert(0,detconInt);
										}
										else{
											var existe=false;
											gridDetContables.store.each(function (reDetCon){
												if(reDetCon.get('sc_cuenta')==Ext.getCmp('cuenta_con').getValue() && 
												   operacion==reDetCon.get('debhab') &&
												   Ext.getCmp('agrdoccon').getValue()==reDetCon.get('documento')){
													var total = parseFloat(ue_formato_operaciones(reDetCon.get('monto')));
													var montocont = parseFloat(ue_formato_operaciones(Ext.getCmp('catmoncon').getValue()));
													reDetCon.set('monto',formatoNumericoMostrar(total+montocont,2,'.',',','','','-',''));  
													existe=true;
												}
											})
											if(!existe){
												gridDetContables.store.insert(0,detconInt);
											}
										}
										acumularTotales();
										//se comento el cerrar de la ventana solicitado en caso 16609
										//ventanaAgregarContable.close();	
									}
								}
							}	
						},
						{
							text: 'Salir',
							handler:function(){
								ventanaAgregarContable.close();
							}
						}]
				});
			ventanaAgregarContable.show();
		}
	}
	
	function actualizarDocumento()
	{
		if(gridDetContables.store.getCount() > 0)
		{
			var comprobante = Ext.getCmp('comprobante').getValue();
			gridDetContables.store.each(function (reDetCon)
			{
				reDetCon.set('documento', comprobante);
			});
		}
	}

	function AgregarEnLote()
	{	
		var myJSONObject = {
				"operacion" :'comprobantemasivo'
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) 
			{ 
	    		var datos = result.responseText;
				if (datos == 1) 
				{
					componente++; 
					if(Ext.getCmp('comprobante').getValue()=='' || Ext.getCmp('descripcion').getValue()=='' || Ext.getCmp('fecha').getValue()=='')
					{
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Debe llenar los campos Comprobante, Descripci&#243;n  y Fecha!!!',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
					else
					{
						//creando datastore y columnmodel para la grid de los detalles contables
						var reDetalles = Ext.data.Record.create([
							{name: 'sc_cuenta'},
							{name: 'monto'},
							{name: 'debhab'}
							
						]);
						
						dsDetalles =  new Ext.data.Store({
							reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetalles)
						});
											
						var cmDetalles = new Ext.grid.ColumnModel([
							{header: "<CENTER>Cuenta</CENTER>", width: 100, align: 'center', sortable: true, dataIndex: 'sc_cuenta'},
							{header: "<CENTER>Operaci&#243;n</CENTER>", type: 'float', width: 60, align: 'center', sortable: true, dataIndex: 'debhab'},
							{header: "<CENTER>Monto</CENTER>", type: 'float', width: 60, align: 'right', sortable: true, dataIndex: 'monto'}
						]);
						//fin del datastore y columnmodel para la grid de bienes
						
						//creando grid para los detalles de bienes
						gridDetalles = new Ext.grid.EditorGridPanel({
							width:686,
							height:300,
							frame:true,
							title:"<H1 align='center'>Detalles Contables</H1>",
							sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
							style: 'position:absolute;left: 7px;top:125px',
							autoScroll:true,
							border:true,
							ds: dsDetalles,
							cm: cmDetalles,
							stripeRows: true,
							viewConfig: {forceFit:true}
						});
						
						var formCargarArchivo = new Ext.FormPanel({
							fileUpload: true,
							width: 700,
							frame: true,
							title: 'Cargar archivo de datos',
							autoHeight: true,
							bodyStyle: 'padding: 0px 10px 0 100px;',
							labelWidth: 50,
							defaults:
							{
								anchor: '50%',
								allowBlank: false,
								msgTarget: 'side',
								labelSeparator:''
							},
							items: [{
								xtype: 'fileuploadfield',
								id: 'archivo',
								emptyText: 'Selecione el archivo de datos',
								fieldLabel: 'Archivo',
								buttonText: '...'
							}],
							buttons: [{
								text: 'Cargar',
								handler: function(){
									if(formCargarArchivo.getForm().isValid())
									{
										formCargarArchivo.getForm().submit({
											url: '../../controlador/scg/sigesp_ctr_scg_cargararchivo.php',
											waitMsg: 'Cargando el archivo...',
											success: function(formCargarArchivo, o)
											{
												CargarGrid();
											}
										});
									}
								}
							},{
								text: 'Cancelar',
								handler: function()
								{
									formCargarArchivo.getForm().reset();
								}
							}]
						});
						
						var ventanaAgregarLote = new Ext.Window({
							title: "<H1 align='center'>Entrada de Movimientos Contables en lote</H1>",
							y:10,
							width:700,
							height:500, 
							modal: true,
							closable:false,
							plain: false,
							frame:true,
							items:[formCargarArchivo,gridDetalles],
							buttons: [{
										text:'Aceptar',
										handler:function()
										{
											gridDetalles.store.each(
												function (DetCon)
												{
													documento = Ext.getCmp('comprobante').getValue();
													documento = documento.replace("&", " ");	
													descripcion = Ext.getCmp('descripcion').getValue();
													descripcion = descripcion.replace("&", " ");	
													operacion = DetCon.get('debhab');
													sc_cuenta = DetCon.get('sc_cuenta');
													monto = DetCon.get('monto');
													procede_doc = Ext.getCmp('procede').getValue();
			
													if(sc_cuenta=='' || operacion=='')
													{
														Ext.Msg.show({
															title:'Mensaje',
															msg:'Debe completar todos los datos, cuenta y operacion',
															buttons: Ext.Msg.OK,
															icon: Ext.MessageBox.INFO
														});
													}
													else
													{
														if (monto=='0,00')
														{
															Ext.Msg.show({
																title:'Mensaje',
																msg:'El monto del monvimiento debe ser distinto de 0,00',
																buttons: Ext.Msg.OK,
																icon: Ext.MessageBox.INFO
															});
														} 
														else
														{
															var reDetCon = Ext.data.Record.create([
																{name: 'codban'},
																{name: 'ctaban'},
																{name: 'canart'},
																{name: 'sc_cuenta'},
																{name: 'status'},
																{name: 'procede_doc'},
																{name: 'documento'},
																{name: 'descripcion'},
																{name: 'monto'},
																{name: 'debhab'}
															]);
																					
															var detconInt = new reDetCon({
																'sc_cuenta':sc_cuenta,
																'documento':documento,
																'descripcion':descripcion,
																'procede_doc':procede_doc,
																'debhab':operacion,
																'monto':monto
															});
															if(gridDetContables.getStore().getCount()==0)
															{
																gridDetContables.store.insert(0,detconInt);
															}
															else
															{
																var existe=false;
																gridDetContables.store.each(function (reDetCon)
																{
																	if((reDetCon.get('sc_cuenta')==sc_cuenta) && (reDetCon.get('debhab') == operacion) && (reDetCon.get('documento')== documento))
																	{
																		var total = parseFloat(ue_formato_operaciones(reDetCon.get('monto')));
																		var montocont = parseFloat(ue_formato_operaciones(monto));
																		reDetCon.set('monto',formatoNumericoMostrar(total+montocont,2,'.',',','','','-',''));  
																		existe=true;
																	}
																})
																if(!existe)
																{
																	gridDetContables.store.insert(0,detconInt);
																}
															}
															acumularTotales();
														}
													}
												});
											ventanaAgregarLote.close();	
										}
									},
									{
										text: 'Salir',
										handler:function(){
											ventanaAgregarLote.close();
										}
									}]
							});
						ventanaAgregarLote.show();
					}
				}
				else
				{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'No se tiene la configuraci�n para utilizar este proceso.',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
			}
		});
	}

	function AgregarEnLote2()
	{	
		var myJSONObject = {
				"operacion" :'comprobantemasivo2'
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) 
			{ 
	    		var datos = result.responseText;
				if (datos == 1) 
				{
					componente++; 
					if(Ext.getCmp('comprobante').getValue()=='' || Ext.getCmp('descripcion').getValue()=='' || Ext.getCmp('fecha').getValue()=='')
					{
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Debe llenar los campos Comprobante, Descripci&#243;n  y Fecha!!!',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
					else
					{
						//creando datastore y columnmodel para la grid de los detalles contables
						var reDetalles = Ext.data.Record.create([
							{name: 'sc_cuenta'},
							{name: 'monto'},
							{name: 'descripcion'},
							{name: 'documento'},
							{name: 'debhab'}
							
						]);
						
						dsDetalles =  new Ext.data.Store({
							reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetalles)
						});
											
						var cmDetalles = new Ext.grid.ColumnModel([
							{header: "<CENTER>Cuenta</CENTER>", width: 100, align: 'center', sortable: true, dataIndex: 'sc_cuenta'},
							{header: "<CENTER>Descripci&#243;n</CENTER>", type: 'float', width: 60, align: 'center', sortable: true, dataIndex: 'descripcion'},
							{header: "<CENTER>Documento</CENTER>", type: 'float', width: 60, align: 'center', sortable: true, dataIndex: 'documento'},
							{header: "<CENTER>Operaci&#243;n</CENTER>", type: 'float', width: 60, align: 'center', sortable: true, dataIndex: 'debhab'},
							{header: "<CENTER>Monto</CENTER>", type: 'float', width: 60, align: 'right', sortable: true, dataIndex: 'monto'}
						]);
						//fin del datastore y columnmodel para la grid de bienes
						
						//creando grid para los detalles de bienes
						gridDetalles = new Ext.grid.EditorGridPanel({
							width:686,
							height:300,
							frame:true,
							title:"<H1 align='center'>Detalles Contables</H1>",
							sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
							style: 'position:absolute;left: 7px;top:125px',
							autoScroll:true,
							border:true,
							ds: dsDetalles,
							cm: cmDetalles,
							stripeRows: true,
							viewConfig: {forceFit:true}
						});
						
						var formCargarArchivo = new Ext.FormPanel({
							fileUpload: true,
							width: 700,
							frame: true,
							title: 'Cargar archivo de datos',
							autoHeight: true,
							bodyStyle: 'padding: 0px 10px 0 100px;',
							labelWidth: 50,
							defaults:
							{
								anchor: '50%',
								allowBlank: false,
								msgTarget: 'side',
								labelSeparator:''
							},
							items: [{
								xtype: 'fileuploadfield',
								id: 'archivo',
								emptyText: 'Selecione el archivo de datos',
								fieldLabel: 'Archivo',
								buttonText: '...'
							}],
							buttons: [{
								text: 'Cargar',
								handler: function(){
									if(formCargarArchivo.getForm().isValid())
									{
										formCargarArchivo.getForm().submit({
											url: '../../controlador/scg/sigesp_ctr_scg_cargararchivoexcel.php',
											waitMsg: 'Cargando el archivo...',
											success: function(formCargarArchivo, o)
											{
												CargarGrid2();
											}
										});
									}
								}
							},{
								text: 'Cancelar',
								handler: function()
								{
									formCargarArchivo.getForm().reset();
								}
							}]
						});
						
						var ventanaAgregarLote = new Ext.Window({
							title: "<H1 align='center'>Entrada de Movimientos Contables en lote</H1>",
							y:10,
							width:700,
							height:500, 
							modal: true,
							closable:false,
							plain: false,
							frame:true,
							items:[formCargarArchivo,gridDetalles],
							buttons: [{
										text:'Aceptar',
										handler:function()
										{
											ventanaAgregarLote.close();
											Ext.MessageBox.alert('Error', 'Error al procesar la Informacion'); 
											gridDetalles.store.each(
												function (DetCon)
												{
													documento = DetCon.get('documento');
													documento = documento.replace("&", " ");	
													
													descripcion = DetCon.get('descripcion');
													descripcion = descripcion.replace("&", " ");	
													operacion = DetCon.get('debhab');
													sc_cuenta = DetCon.get('sc_cuenta');
													monto = DetCon.get('monto');
													documento2 = Ext.getCmp('comprobante').getValue();
													procede_doc = Ext.getCmp('procede').getValue();
													if(documento=='')
													{
														documento=documento2;	
													}
													if(sc_cuenta=='' || operacion=='')
													{
														Ext.Msg.show({
															title:'Mensaje',
															msg:'Debe completar todos los datos, cuenta y operacion',
															buttons: Ext.Msg.OK,
															icon: Ext.MessageBox.INFO
														});
													}
													else
													{
														if (monto=='0,00')
														{
															Ext.Msg.show({
																title:'Mensaje',
																msg:'El monto del monvimiento debe ser distinto de 0,00',
																buttons: Ext.Msg.OK,
																icon: Ext.MessageBox.INFO
															});
														} 
														else
														{
															var reDetCon = Ext.data.Record.create([
																{name: 'codban'},
																{name: 'ctaban'},
																{name: 'canart'},
																{name: 'sc_cuenta'},
																{name: 'status'},
																{name: 'procede_doc'},
																{name: 'documento'},
																{name: 'descripcion'},
																{name: 'monto'},
																{name: 'debhab'}
															]);
																					
															var detconInt = new reDetCon({
																'sc_cuenta':sc_cuenta,
																'documento':documento,
																'descripcion':descripcion,
																'procede_doc':procede_doc,
																'debhab':operacion,
																'monto':monto
															});
															if(gridDetContables.getStore().getCount()==0)
															{
																gridDetContables.store.insert(0,detconInt);
															}
															else
															{
																var existe=false;
																gridDetContables.store.each(function (reDetCon)
																{
																	if((reDetCon.get('sc_cuenta')==sc_cuenta) && (reDetCon.get('debhab') == operacion) && (reDetCon.get('documento')== documento))
																	{
																		var total = parseFloat(ue_formato_operaciones(reDetCon.get('monto')));
																		var montocont = parseFloat(ue_formato_operaciones(monto));
																		reDetCon.set('monto',formatoNumericoMostrar(total+montocont,2,'.',',','','','-',''));  
																		existe=true;
																	}
																})
																if(!existe)
																{
																	gridDetContables.store.insert(0,detconInt);
																}
															}
															acumularTotales();
														}
													}
												});
												Ext.Msg.hide();
										}
									},
									{
										text: 'Salir',
										handler:function(){
											ventanaAgregarLote.close();
										}
									}]
							});
						ventanaAgregarLote.show();
					}
				}
				else
				{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'No se tiene la configuraci�n para utilizar este proceso.',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
			}
		});
	}
	//Funcion que completa el comprbante con ceros para alcanzar la longitud maxima
	function CargarGrid()
	{
		var myJSONObject = 
		{
				"operacion" :'cargar_archivo', 
				"fecha" :  Ext.getCmp('fecha').getValue()
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) 
			{ 
	    		var datos = result.responseText;
				var objetoSCG = eval('(' + datos + ')');
				if(objetoSCG!='')
				{
					if(objetoSCG!='0')
					{
						if(objetoSCG.raiz == null || objetoSCG.raiz =='')
						{
							Ext.MessageBox.show({
								title:'Advertencia',
								msg:'No existen datos para mostrar',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.WARNING
							});
							gridDetalles.store.removeAll();
						}
						else
						{
							dsDetalles.loadData(objetoSCG);
						}
					}
				}
			}
		});
	}

	function CargarGrid2()
	{
		var myJSONObject = 
		{
				"operacion" :'cargar_archivo2', 
				"fecha" :  Ext.getCmp('fecha').getValue()
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) 
			{ 
	    		var datos = result.responseText;
				var objetoSCG = eval('(' + datos + ')');
				if(objetoSCG!='')
				{
					if(objetoSCG!='0')
					{
						if(objetoSCG.raiz == null || objetoSCG.raiz =='')
						{
							Ext.MessageBox.show({
								title:'Advertencia',
								msg:'No existen datos para mostrar',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.WARNING
							});
							gridDetalles.store.removeAll();
						}
						else
						{
							dsDetalles.loadData(objetoSCG);
						}
					}
				}
			}
		});
	}

	function irImprimir()
	{
		var comprobante = Ext.getCmp('comprobante').getValue();
		var procede = Ext.getCmp('procede').getValue();
		window.open("reportes/sigesp_scg_rfs_comprobante.php?comprobante="+comprobante+"&procede="+procede,"menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
	}

        //Funcion que agrega los datos al combo prefijos
        function llenarCmbPrefijos()
	{
            if (Actualizar==null)
            {
                var myJSONObject ={
                                "operacion": 'buscarPrefijosUsuarios',
                };	
                var ObjSon=JSON.stringify(myJSONObject);
                var parametros = 'ObjSon='+ObjSon; 
                Ext.Ajax.request({
                        url : '../../controlador/scg/sigesp_ctr_scg_comprobante_contable.php',
                        params : parametros,
                        method: 'POST',
                        success: function (resultado, request)
                        { 
                                var datosest = resultado.responseText;
                                var prefijo = "";
                                if(datosest!='')
                                {
                                        prefijo = datosest.substring(21, 27);
                                        var DatosEst = eval('(' + datosest + ')');
                                }
                                dsPrefijo.loadData(DatosEst);                        
                                Ext.getCmp('prefijo').setValue(prefijo);
                                NroComprobante(prefijo);
                        }//fin del success
                });//fin del ajax request
            }
	}

//FIN DEL FORMULARIO CONTABLE//