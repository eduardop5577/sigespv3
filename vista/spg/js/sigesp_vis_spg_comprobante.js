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

var fromComprobantePresupuestario = null; //varibale para almacenar la instacia de objeto de formulario 
var gridDetPresupuestario = null;
var fieldsetTotales = null;
var fieldsetTotCont = null;
var gridDetContables = null;
var gridComprobante = null;
var Actualizar = null;
var cmboperacion = null;
var operacion = true;
var contador = 0;
barraherramienta    = true;
var prefijocmp = null;
//var comcampocatcuentacontable = null;

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
                                        gridDetPresupuestario.store.removeAll();
                                        gridDetContables.store.removeAll();
                                        gridDetContables.hide();
                                        Ext.getCmp('totaldeb').setValue('');
                                        Ext.getCmp('totaldeb').setValue('');
                                        Ext.getCmp('totalhab').setValue('');
                                        Ext.getCmp('diferencia').setValue('');
                                        Ext.getCmp('totalpre').setValue();
                                        fieldsetTotCont.hide();
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
		idComponente:'spgprouno',
		reCatalogo: reCatProveedor,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatproveedor.php',
		parametros: "ObjSon={'operacion': 'buscarProveedores'",
		soloCatalogo: true,
		arrSetCampo:[{campo:'cod_pro',valor:'cod_pro'},
				     {campo:'nompro',valor:'nompro'}],
		numFiltroNoVacio: 1
	});
	
	//-------------------------------------------------------------------------------------------------------------------------	

	//componente catalogo de beneficiarios
	var reCatBeneficiario = Ext.data.Record.create([
		{name: 'ced_bene'}, //campo obligatorio                             
		{name: 'nombene'},  //campo obligatorio
		{name: 'apebene'},  //campo obligatorio
		{name: 'dirbene'}   //campo obligatorio
	]);

	var comcampocatbeneficiario = new com.sigesp.vista.comCatalogoBeneficiario({
		idComponente:'spgbenuno',
		reCatalogo: reCatBeneficiario,
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_comcatbeneficiario.php',
		parametros: "ObjSon={'operacion': 'buscarBeneficiarios'",
		soloCatalogo: true,
		arrSetCampo:[{campo:'cod_pro',valor:'ced_bene'},
				     {campo:'nompro',valor:'nombene'}],
		numFiltroNoVacio: 1
	});
	
	//-------------------------------------------------------------------------------------------------------------------------	

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
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//Datos de las operaciones
	var operaciones = [ ['PRE-COMPROMISO', 'PC'], 
	            	  ['COMPROMISO SIMPLE', 'CS'],
	            	  ['COMPROMISO Y GASTO CAUSADO', 'CG'],
	            	  ['GASTO CAUSADO', 'GC'],
	            	  ['GASTO CAUSADO Y PAGO', 'CP'],
	            	  ['PAGO', 'PG'],
			  ['COMPROMISO,CAUSADO Y PAGADO', 'CCP']];
	
	var stOperaciones = new Ext.data.SimpleStore({
		fields : [ 'col', 'tipo' ],
		data : operaciones 
	});

	//creando objeto combo destino
	var cmbOperaciones = new Ext.form.ComboBox({
		store : stOperaciones,
		fieldLabel : 'Operaci&#243;n ',
		labelSeparator : '',
		editable : false,
		displayField : 'col',
		valueField : 'tipo',
		id : 'operaciones',
		binding:true,
		hiddenvalue:'',
		defaultvalue:'NS',
		allowBlank:true,
		width:250,
		typeAhead: true,
		emptyText:'---Seleccione---',
		triggerAction:'all',
		forceselection:true,
		binding:true,
		mode:'local',
		listeners: {
			'select': function(){
				if(this.getValue()=='CG' || this.getValue()=='GC' || this.getValue()=='CP' || this.getValue()=='CCP'){
					gridDetPresupuestario.store.removeAll();
					Ext.getCmp('totalpre').setValue();
					fromComprobantePresupuestario.add(gridDetContables);
					fromComprobantePresupuestario.add(fieldsetTotCont);
					gridDetContables.show();
					fieldsetTotCont.show();
					gridDetContables.store.removeAll();
					Ext.getCmp('totaldeb').setValue('');
					Ext.getCmp('totalhab').setValue('');
					Ext.getCmp('diferencia').setValue('');
					fieldsetTotCont.show();
					fromComprobantePresupuestario.doLayout();
				}
				else{
					if(gridDetContables!=null){
						gridDetPresupuestario.store.removeAll();
						Ext.getCmp('totalpre').setValue();
						gridDetContables.store.removeAll();
						gridDetContables.hide();
						Ext.getCmp('totaldeb').setValue('');
						Ext.getCmp('totalhab').setValue('');
						Ext.getCmp('diferencia').setValue('');
						fieldsetTotCont.hide();
					}
				}
			}
		}
	});
	
	//-----------------------------------------------------------------------------------------------
	
	//creando datastore y columnmodel para la grid de los detalles contables
	var reDetPresupuestario = Ext.data.Record.create([
	    {name: 'spg_cuenta'},
            {name: 'ctaban'},
            {name: 'codban'},
	    {name: 'sc_cuenta'},
	    {name: 'status'},
	    {name: 'codestpro1'},
	    {name: 'codestpro2'},
	    {name: 'codestpro3'},
	    {name: 'codestpro4'},
	    {name: 'codestpro5'},
	    {name: 'estcla'},
	    {name: 'operacion'},
	    {name: 'procede_doc'},
	    {name: 'documento'},
	    {name: 'descripcion'},
	    {name: 'monto'},
            {name: 'codestpro'},
            {name: 'codfuefin'}
	]);
	
	var dsDetPresupuestario =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetPresupuestario)
	});
						
	var cmDetPresupuestario = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
        {header: "<CENTER>Cuenta</CENTER>", width: 50, align: 'center', sortable: true, dataIndex: 'spg_cuenta'},
        {header: "<CENTER>Programatico</CENTER>", width: 50, sortable: true, dataIndex: 'codestpro'},
        {header: "<CENTER>Fuente <br> Financiamiento</CENTER>", width: 50, sortable: true, dataIndex: 'codfuefin'},
        {header: "<CENTER>Documento</CENTER>", width: 70, sortable: true, dataIndex: 'documento', align: 'center'},
	    {header: "<CENTER>Descripci&#243;n</CENTER>", type: 'float', width: 40, align: 'center', sortable: true, dataIndex: 'descripcion'},
		{header: "<CENTER>Procede</CENTER>", width: 30, sortable: true, dataIndex: 'procede_doc', align: 'center'},
		{header: "<CENTER>Operaci&#243;n</CENTER>", type: 'float', width: 40, align: 'center', sortable: true, dataIndex: 'operacion'},
		{header: "<CENTER>Monto</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'monto'}
	]);
	//fin del datastore y columnmodel para la grid de bienes
	
	//creando grid para los detalles de bienes
	gridDetPresupuestario = new Ext.grid.EditorGridPanel({
 		width:950,
 		height:200,
		frame:true,
		title:"<H1 align='center'>Detalle Presupuestario</H1>",
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		style: 'position:absolute;left:10px;top:200px',
		autoScroll:true,
 		border:true,
 		ds: dsDetPresupuestario,
	   	cm: cmDetPresupuestario,
	   	stripeRows: true,
	  	viewConfig: {forceFit:true},
	  	tbar:[{
		        text:'Agregar Detalle Presupuestario',
		        tooltip:'Agregar Cuenta',
		        iconCls:'agregar',
		        id: 'btagrebie',
		        handler: function(){
					if(Ext.getCmp('comprobante').getValue()=='' || Ext.getCmp('descripcion').getValue()=='' || Ext.getCmp('operaciones').getValue()==''){
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Debe llenar los campos Operaci&#243;n, Comprobante, y Descripci&#243;n!!!',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
					else{
						AgregarPresupuesto();
					}
				}
	  		},
	  		{
			text:'Eliminar Detalle Presupuestario',
			tooltip:'Eliminar Detalle',
			iconCls:'remover',
			id:'btelibie',
			handler: function(){
				arreglo = gridDetPresupuestario.getSelectionModel().getSelections();
				arregloCon = gridDetContables.getStore();
				if(arreglo.length >0){
					for(var i = arreglo.length - 1; i >= 0; i--){
						for(var j=arregloCon.getCount()-1; j>=0; j--){
		  					if(arregloCon.getAt(j).get('sc_cuenta')==arreglo[i].get('sc_cuenta')){
		  						if(arregloCon.getAt(j).get('monto')==arreglo[i].get('monto')){
		  							gridDetContables.getStore().remove(arregloCon.getAt(j));
		  						}
		  						else{
		  							var monto = parseFloat(ue_formato_operaciones(arreglo[i].get('monto')));
		  							var montoaux = parseFloat(ue_formato_operaciones(arregloCon.getAt(j).get('monto')));
		  							if(monto>=montoaux){
		  								var total = monto-montoaux;
		  							}
		  							else{
		  								var total = montoaux-monto;
		  							}
		  							//total = abs(total);
		  							arreglo[i].set('monto',formatoNumericoMostrar(total,2,'.',',','','','-',''));
		  						}
		  						acumularTotalContable();
		  					}
		  				}
						gridDetPresupuestario.getStore().remove(arreglo[i]);
					}
				}
				else{
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Debe seleccionar el registro a Eliminar!!!',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.INFO
					});
				}
				acumularTotalGasto(gridDetPresupuestario,'totalpre');
			}
		}]
	});
	
	//-----------------------------------------------------------------------------------------------
	
	//creando datastore y columnmodel para la grid de los detalles contables
	var reDetContables = Ext.data.Record.create([
	    {name: 'codban'},
		{name: 'ctaban'},
	    {name: 'sc_cuenta'},
	    {name: 'status'},
	    {name: 'procede_doc'},
	    {name: 'documento'},
	    {name: 'descripcion'},
	    {name: 'monto'},
	    {name: 'operacion'}
	]);
	
	var dsDetContables =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetContables)
	});
						
	var cmDetContables = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),
        {header: "<CENTER>Cuenta</CENTER>", width: 60, align: 'center', sortable: true, dataIndex: 'sc_cuenta'},
        {header: "<CENTER>Descripci&#243;n</CENTER>", width: 80, sortable: true, dataIndex: 'descripcion'},
        {header: "<CENTER>Procede</CENTER>", width: 30, sortable: true, dataIndex: 'procede_doc', align: 'center'},
		{header: "<CENTER>Documento</CENTER>", width: 40, sortable: true, dataIndex: 'documento', align: 'center'},
		{header: "<CENTER>Operaci&#243;n</CENTER>", width: 40, align: 'center', sortable: true, dataIndex: 'operacion'/*, renderer: MostrarOperacion*/},
		{header: "<CENTER>Monto</CENTER>", width: 40, align: 'right', sortable: true, dataIndex: 'monto'},
		
	]);
	//fin del datastore y columnmodel para la grid de bienes
	
	//creando grid para los detalles de bienes
	gridDetContables = new Ext.grid.EditorGridPanel({
 		width:950,
 		height:200,
		frame:true,
		title:"<H1 align='center'>Detalle Contable</H1>",
		sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		style: 'position:absolute;left:10px;top:480px',
		autoScroll:true,
 		border:true,
 		ds: dsDetContables,
   		cm: cmDetContables,
   		stripeRows: true,
  		viewConfig: {forceFit:true},
		tbar:[{
		        text:'Agregar Detalle Contable',
		        tooltip:'Agregar Cuenta',
		        iconCls:'agregar',
		        id: 'btagrebie',
		        handler: function(){
					if(Ext.getCmp('comprobante').getValue()=='' || Ext.getCmp('descripcion').getValue()=='' || Ext.getCmp('operaciones').getValue()==''){
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Debe llenar los campos Operaci&#243;n, Comprobante, y Descripci&#243;n!!!',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
					else{
						contador++;
						AgregarCuentas(contador,gridDetContables,'SPGCMP');
					}
				}
				},
				{
				text:'Eliminar Detalle Contable',
				tooltip:'Eliminar Detalle',
				iconCls:'remover',
				id:'btelibie',
				handler: function(){
					arreglo = gridDetContables.getSelectionModel().getSelections();
					//arregloPre = gridDetPresupuestario.getStore();
					if(arreglo.length >0){
						for(var i = arreglo.length - 1; i >= 0; i--){
							//SE QUITO ENLACEN ENTRE DETALLE CONTABLE Y PRESUPUESTARIO AL ELIMINAR CASO 15562
							/*for(var j=arregloPre.getCount()-1; j>=0; j--){
								if(arregloPre.getAt(j).get('sc_cuenta')==arreglo[i].get('sc_cuenta')){
									gridDetPresupuestario.getStore().remove(arregloPre.getAt(j));
								}
								acumularTotalGasto(gridDetPresupuestario,'totalpre');
							}*/
							gridDetContables.getStore().remove(arreglo[i]);
						}
					}
					else{
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Debe seleccionar el registro a Eliminar!!!',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
				acumularTotalContable();
				}
			}]
	});
	
	//-------------------------------------------------------------------------------------------------------------------------	

	//Creando el formulario de los totales contables 
	fieldsetTotCont = new Ext.form.FieldSet({
		width: 950,
		height: 55,
		title: 'Totales Contables',
		style: 'position:absolute;left:10px;top:690px',
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
				laberWidth: 90,
				style:"padding-left:15px",
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
	//fin creando formulario de totales
	
	//-------------------------------------------------------------------------------------------------------------------------	

	//Creando el formulario del total de gasto
	fieldsetTotales = new Ext.form.FieldSet({
		width: 300,
		height: 60,
		title: 'Total Presupuestario',
		style: 'position:absolute;left:655px;top:410px',
		cls :'fondo',
		items: [{
				layout:"column",
				border:false,
				items: [{
						layout:"form",
						border:false,
						items: [{
								xtype:"textfield",
								fieldLabel: 'Total',
								labelStyle :'font-weight: bold; width: 60px; text-align: right',
								style:'font-weight: bold; text-align: right; border:none; background:#f1f1f1',
								labelSeparator:'',
								readOnly:true,
								id:'totalpre',
								allowBlank:true,
								width:165,
								binding:true,
								defaultvalue:'0',
								hiddenvalue:'',
							}]
						}]
				}]
	});
	//fin creando formulario de totales
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//Creando formulario principal 
	var Xpos = ((screen.width/2)-(500));
	var Ypos = ((screen.height/2)-(650/2));
	fromComprobantePresupuestario = new Ext.FormPanel({
		title: "<H1 align='center'>Comprobante Presupuestario</H1>",
		applyTo: 'formulario',
		width: 1000,
		height: 510,
		style: 'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',  //'position:absolute;margin-left:'+Xpos+'px;margin-top:45px;',
		frame: true,
		autoScroll:true,
		items: [{
				xtype:"fieldset", 
			    title:'Datos del Comprobante',
			    style: 'position:absolute;left:10px;top:5px',
			    border:true,
			    width: 950,
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
										value: 'SPGCMP',
										readOnly: true,
										allowBlank:false,
										width:80,
										binding:true,
										defaultvalue:'',
										hiddenvalue:'',
									}]
								}]
			    		},
			    		{
				    	layout:"column",
					    defaults: {border: false},
					    style: 'position:absolute;left:380px;top:50px',
					    border:false,
						items:[{
								layout:"form",
								border:false,
								labelWidth:80,
								items:[cmbOperaciones]
							}]
			    		},
			    		{
				    	layout:"column",
					    defaults: {border: false},
					    style: 'position:absolute;left:600px;top:20px',
					    border:false,
						items:[{
								layout:"form",
								border:false,
								labelWidth:80,
								items:[{
										xtype:"textfield",
										labelSeparator :'',
										fieldLabel:"Compromiso",
										name:'Compromiso',
										id:'numconcom',
										readOnly: true,
										allowBlank:true,
										width:120,
										binding:true,
										defaultvalue:'000000000000000',
										hiddenvalue:'',
									}]
								}]
			    		},
			    		{
				    	layout:"column",
					    defaults: {border: false},
					    style: 'position:absolute;left:300px;top:20px',
					    border:false,
						items:[{
								layout:"form",
								border:false,
								labelWidth:80,
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
                                                                        layout:"form",
                                                                            border:false,
                                                                                labelWidth:10,
                                                                                items:[{
                                                                                                xtype: 'textfield',
                                                                                                labelSeparator :'',
                                                                                                fieldLabel: '',
                                                                                                id: 'comprobante',
                                                                                                autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-._');"},
                                                                                                width: 150,
                                                                                                formatonumerico:false,
                                                                                                binding:true,
                                                                                                hiddenvalue:'',
                                                                                                defaultvalue:'',
                                                                                                allowBlank:false,
                                                                                                listeners:{
                                                                                                        'blur' : function(campo)
                                                                                                        {
                                                                                                                llenarCampoNumdoc(campo.getValue());
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
						    			  autoCreate: {tag: 'textarea', type: 'text', size: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.;,!@%&/\()?-+*[]{}');"},
						    		  }]
				    	  			}]
			    		},
			    		{
				    	  layout: "column",
				    	  defaults: {border: false},
				    	  style: 'position:absolute;left:740px;top:50px',
				    	  items: [{
					    		  layout: "form",
					    		  border: false,
					    		  labelWidth: 30,
					    		  items: [{
						    			  xtype: 'checkbox',
						    			  labelSeparator :'',
						    			  boxLabel:'Rendici&#243;n de Fondos',
						    			  fieldLabel: '',
						    			  id: 'estrenfon',
						    			  inputValue:1,
						    			  binding:true,
						    			  hiddenvalue:'',
						    			  defaultvalue:'0',
						    			  allowBlank:true
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
										width: 310
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
						}]
		},gridDetPresupuestario,fieldsetTotales]
	});
	llenarCmbPrefijos();
	
}); //fin creando formulario principal con parametros de busqueda y grid de modificaciones
	
	//-------------------------------------------------------------------------------------------------------------------------	
	
	//Funcion que llama al catalogo de comprobante presupuestario
	function irBuscar()
	{
		CatalogoComprobante();
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que guardar el comprobante presupuestario
	function irGuardar()
	{
		if(Ext.getCmp('procede').getValue()!='SPGCMP')
		{
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'No puede editar un comprobante, que no fue generado por este modulo !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
		}
		else
		{
			var cadjson = '';
		    var valido = true;
		    var aux = formatoNumericoMostrar(0,2,'.',',','','','-','');
		    var montotpre = 0;
		    var montotcon = 0;
		    gridDetPresupuestario.store.each(function (reDetPre){
		    	var monto = parseFloat(ue_formato_operaciones(reDetPre.get('monto')));
		    	monto = Math.abs(monto);
		    	montotpre += monto;
		    })
		    gridDetContables.store.each(function (reDetCon){
		    	if(reDetCon.get('operacion')=='D'){
		    		var monto = parseFloat(ue_formato_operaciones(reDetCon.get('monto')));
		    		montotcon += monto;
		    	}
		    })
		    if(gridDetContables.store.getCount()>0){
			    if(valido){
			    	if(Ext.getCmp('diferencia').getValue()!=aux){
				    	Ext.Msg.show({
							title:'Mensaje',
							msg: 'Asiento Contable Descuadrado !!!',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.ERROR
						});
				    	valido = false;
				    }
			    }
		    }
		    
		    if(montotpre == 0){
		    	Ext.Msg.show({
					title:'Mensaje',
					msg: 'El monto del comprobante debe ser distinto de cero',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
		    }
		    
		    var evento = 'INSERT';
		    if(Actualizar!=null){
		    	var evento = 'UPDATE';
		    }
	        cadjson = "{'operacion':'guardar','codsis':'"+sistema+"','nomven':'"+vista+"','evento':'"+evento+"',"+getJsonFormulario(fromComprobantePresupuestario);
	        cadjson += ",'detallesPresupuestario':[";	
	        if(valido) {
	        	var numDetalle = 0;
	    		gridDetPresupuestario.store.each(function (reDetPre){
			    		var monto = parseFloat(ue_formato_operaciones(reDetPre.get('monto')));
						if(monto!=0)
						{
							if(numDetalle==0)
							{
								cadjson +="{'spg_cuenta':'"+reDetPre.get('spg_cuenta')+"','procede_doc':'"+reDetPre.get('procede_doc')+"'," +
										   "'documento':'"+reDetPre.get('documento')+"','operacion':'"+reDetPre.get('operacion')+"'," +
										   "'codfuefin':'"+reDetPre.get('codfuefin')+"','codestpro1':'"+reDetPre.get('codestpro1')+"'," +
										   "'codestpro2':'"+reDetPre.get('codestpro2')+"','codestpro3':'"+reDetPre.get('codestpro3')+"'," +
										   "'codestpro4':'"+reDetPre.get('codestpro4')+"','codestpro5':'"+reDetPre.get('codestpro5')+"'," +
										   "'estcla':'"+reDetPre.get('estcla')+"','descripcion':'"+reDetPre.get('descripcion')+"'," +
										   "'monto':'"+reDetPre.get('monto')+"'}";
							}
							else
							{
								cadjson +=",{'spg_cuenta':'"+reDetPre.get('spg_cuenta')+"','procede_doc':'"+reDetPre.get('procede_doc')+"'," +
										   "'documento':'"+reDetPre.get('documento')+"','operacion':'"+reDetPre.get('operacion')+"'," +
										   "'codfuefin':'"+reDetPre.get('codfuefin')+"','codestpro1':'"+reDetPre.get('codestpro1')+"'," +
										   "'codestpro2':'"+reDetPre.get('codestpro2')+"','codestpro3':'"+reDetPre.get('codestpro3')+"'," +
										   "'codestpro4':'"+reDetPre.get('codestpro4')+"','codestpro5':'"+reDetPre.get('codestpro5')+"'," +
										   "'estcla':'"+reDetPre.get('estcla')+"','descripcion':'"+reDetPre.get('descripcion')+"'," +
										   "'monto':'"+reDetPre.get('monto')+"'}";
							}
							numDetalle++;
						}
	    		});
	    		cadjson += "],'detallesContable':[";
	    		var numDetalle = 0;
	    		if(gridDetContables.store.getCount()>0)
				{
	    			gridDetContables.store.each(function (reDetCon){
			    		var monto = parseFloat(ue_formato_operaciones(reDetCon.get('monto')));
						if(monto!=0)
						{
							if(numDetalle==0)
							{
								cadjson +="{'sc_cuenta':'"+reDetCon.get('sc_cuenta')+"','procede_doc':'"+reDetCon.get('procede_doc')+"'," +
										   "'documento':'"+reDetCon.get('documento')+"','debhab':'"+reDetCon.get('operacion')+"'," +
										   "'descripcion':'"+reDetCon.get('descripcion')+"','monto':'"+reDetCon.get('monto')+"'}";
							}
							else
							{
								cadjson +=",{'sc_cuenta':'"+reDetCon.get('sc_cuenta')+"','procede_doc':'"+reDetCon.get('procede_doc')+"'," +
										   "'documento':'"+reDetCon.get('documento')+"','debhab':'"+reDetCon.get('operacion')+"'," +
										   "'descripcion':'"+reDetCon.get('descripcion')+"','monto':'"+reDetCon.get('monto')+"'}";
							}
							numDetalle++;
						}
		    		});
	    		}
	        }
	        cadjson += "]}";
	        if(valido){
	        	try{
	        		var objjson = Ext.util.JSON.decode(cadjson);
	        		if(typeof(objjson) == 'object'){
	        			obtenerMensaje('procesar','','Procesando Informaci&#243;n');
	        			var parametros = 'ObjSon=' + cadjson;
	        			Ext.Ajax.request({
	        				url : '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
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
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que limpia la pantalla para generar un nuevo comprobante presupuestario
	function irNuevo()
	{
		limpiarFormulario(fromComprobantePresupuestario);
		gridDetPresupuestario.store.removeAll();
		gridDetContables.store.removeAll();
		gridDetContables.hide();
		llenarCmbPrefijos();
		Ext.getCmp('totaldeb').setValue('');
		Ext.getCmp('totalhab').setValue('');
		Ext.getCmp('diferencia').setValue('');
		fieldsetTotCont.hide();
		Actualizar = null;
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que elimina el comprobante presupuestario
	function irEliminar(){
		if(Ext.getCmp('procede').getValue()!='SPGCMP'){
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'No puede editar un comprobante, que no fue generado por este modulo !!!',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.INFO
			});
		}
		else{
			var aux = formatoNumericoMostrar(0,2,'.',',','','','-','');
			var valido = true;
			function respuesta(btn){
				if(btn=='yes'){
					obtenerMensaje('procesar','','Procesando Informaci&#243;n');
					var cadjson = "{'operacion':'eliminar','codsis':'"+sistema+"','nomven':'"+vista+"',"+getJsonFormulario(fromComprobantePresupuestario);
					cadjson += "}";
			        if(valido){
			        	try{
			        		var objjson = Ext.util.JSON.decode(cadjson);
			        		if(typeof(objjson) == 'object'){
			        			var parametros = 'ObjSon=' + cadjson;
			        			Ext.Ajax.request({
			        				url : '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
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
			if(Actualizar){
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
	function acumularTotalContable()
	{
		var totaldebe = 0;
		var totalhaber = 0;
		
		gridDetContables.store.each(function (reDetCon){
			if(reDetCon.get('operacion')=='D'){
				montodebe = parseFloat(ue_formato_operaciones(reDetCon.get('monto')));
				totaldebe = totaldebe+montodebe;
			}
			else if(reDetCon.get('operacion')=='H'){
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

	function acumularTotalGasto(grid,campo){
		var monto = 0;
		var montotal = 0;
		grid.store.each(function (reDet){
			var monto = reDet.get('monto');
			monto = parseFloat(ue_formato_operaciones(monto));
			montotal += monto; 
			monto = 0;
	    }); //totalpresupuesto
		Ext.getCmp(campo).setValue(formatoNumericoMostrar(montotal,2,'.',',','','','-',''));
	}
	
	//-------------------------------------------------------------------------------------------------------------------------		 

	//Funcion que retorna la descripcion de la operacion del comprobante
	function MostrarOperacion(valor){
		if(valor=='D'){
			return 'Debe';
		}
		else if(valor=='H'){
			return 'Haber';
		}
	}
	
	//-------------------------------------------------------------------------------------------------------------------------	

	//Funcion que valida si el comprobante y la descripcion estan llenos para poder agregar los detalles contables
	function validarDocumentoDescripcion(){
		var unidadOk = true;
		if(Ext.getCmp('comprobante').getValue()=='' || Ext.getCmp('descripcion').getValue()==''){
			unidadOk = false;
		}
		return unidadOk;
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
			idComponente:'spgprodos',
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
			idComponente:'spgbendos',
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
	//Creacion del combo procedencia
	var reProcedencia = Ext.data.Record.create([
          {name: 'procede'},
          {name: 'desproc'}
     ]);

	var dsProcedencia =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "procede"},reProcedencia)			
	});
	
	var CmbProcedencia = new Ext.form.ComboBox({
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
		    {name: 'total'},
		    {name: 'numconcom'}
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
			{header: "<CENTER>Monto</CENTER>", type: 'float', width: 40, align: 'right', sortable: true, dataIndex: 'total'},
			{header: "<CENTER>Compromiso</CENTER>", width: 60, align: 'center', sortable: true, dataIndex: 'numconcom'},
		]);
		//fin del datastore y columnmodel para la grid de bienes
		
		//creando grid para los detalles de bienes
		gridComprobante = new Ext.grid.GridPanel({
	 		width:780,
	 		height:250,
			frame:true,
			title:"",
			style: 'position:absolute;left:15px;top:200px',
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
					title:'Datos del Comprobante',
					style: 'position:absolute;left:15px;top:10px',
					border:true,
					cls: 'fondo',
					width: 780,
					height: 300,
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
											autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"},
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
							style: 'position:absolute;left:380px;top:15',
							items: [{
									layout: "form",
									border: false,
									labelWidth: 100,
									items: [{
											xtype: 'textfield',
											labelSeparator :'',
											fieldLabel: 'Compromiso',
											id: 'numcompromiso',									
											width: 170,
											autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '15', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');"}
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
						    style: 'position:absolute;left:15px;top:50px',  //520/20
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
											width:130,
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
						    style: 'position:absolute;left:300px;top:50px', //520/50
						    border:false,
							items:[{
									layout:"form",
									border:false,
									labelWidth:50,
									items:[{
											xtype:"datefield",
											labelSeparator :'',
											fieldLabel:"Hasta",
											name:'Fecha',
											id:'fechasta',
											allowBlank:false,
											width:130,
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
							style: 'position:absolute;left:15px;top:110px',
							items: [{
									layout: "form",
									border: true,
									items: [CmbProcedencia]
									}]
							},gridComprobante,
							{
							layout:"column",
							defaults: {border: false},
							style: 'position:absolute;left:670px;top:135px', 
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
									   					'operacion'   : 'buscarComprobantesPresupuestarios',
									   					'comprobante' : Ext.getCmp('numcomprobante').getValue(),
									   					'numconcom' : Ext.getCmp('numcompromiso').getValue(),
									   					'procede'     : Ext.getCmp('procedencia').getValue(),
									   					'tipo'        : Ext.getCmp('combodestino').getValue(),
									   					'provben'     : Ext.getCmp('catcodpro').getValue(),
									   					'fecdesde'    : Ext.getCmp('fecdesde').getValue().format('Y-m-d'),
									   					'fechasta'    : Ext.getCmp('fechasta').getValue().format('Y-m-d'),
									   					'filtro'      : 'TODOS'
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
																		dsComprobante.loadData(objCmp);
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
		formVentanaCatalogo.add(gridComprobante);
	    var ventanaEstructura = new Ext.Window({
	    	width:850, 
	        height:600,
	        closable:true,
	    	border:false,
	    	modal: true,
	    	frame:true,
	    	title:"<H1 align='center'>Cat&#225;logo de Comprobantes</H1>",
	    	items:[formVentanaCatalogo], 
	    });
	    
	    ventanaEstructura.show();
	    
	    //function que setea los datos en el formulario principal
	    function aceptar(registro)
	    {
	    	setDataFrom(fromComprobantePresupuestario,registro);
                prefijocmp = registro.get('comprobante');
                prefijocmp = prefijocmp.substring(0,6);
                if (registro.get('tipo_destino')=='P')
                {
                    Ext.getCmp('cod_pro').setValue(registro.get('cod_pro'));                                    
                }
                else
                {
                    Ext.getCmp('cod_pro').setValue(registro.get('ced_bene'));                                    
                }
                Ext.getCmp('prefijo').setValue(prefijocmp);
	    	Actualizar=true;
	    	buscarDetallesPresupuestarios(registro);
	    	buscarDetallesContables(registro);
	    	Ext.getCmp('totalpre').setValue(registro.get('total'));
			gridComprobante.destroy();
			ventanaEstructura.destroy();
	    }
	    
	    function buscarDetallesPresupuestarios(registro)
	    {
	    	var reDetPre = Ext.data.Record.create([
	    	    {name: 'spg_cuenta'},
	    	    {name: 'codestpro1'},
	    	    {name: 'codestpro2'},
	    	    {name: 'codestpro3'},
	    	    {name: 'codestpro4'},
	    	    {name: 'codestpro5'},
	    	    {name: 'estcla'},
	    	    {name: 'operacion'},
	    	    {name: 'procede_doc'},
	    	    {name: 'documento'},
	    	    {name: 'descripcion'},
	    	    {name: 'monto'},
	    	    {name: 'codestpro'},
	    	    {name: 'codfuefin'}
	    	]);
	    	obtenerMensaje('procesar','','Buscando Datos');
			//Buscar los detalles contables
			var JSONObject = {
				'operacion'   : 'buscarDetallesPresupuestario',
				'comprobante' : registro.get('comprobante'),
				'procede'     : registro.get('procede'),
				'fecha'       : registro.get('fecha'),
				'codban'      : registro.get('codban'),
				'ctaban'      : registro.get('ctaban')
			}
			var ObjjSon=Ext.util.JSON.encode(JSONObject);
  			var parametros ='ObjSon='+ObjjSon;
  			Ext.Ajax.request({
  				url: '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
  				params: parametros,
  				method: 'POST',
  				success: function ( result, request ) { 
					Ext.Msg.hide();
					var resultado = result.responseText;
					var objDatos = eval('(' + resultado + ')');
					var datos = objDatos.raiz;
					if (objDatos != ""){
						var codigo = '';
						gridDetPresupuestario.store.removeAll();
                                                var oper = datos[0].operacion;
						Ext.getCmp('operaciones').setValue(oper);
						for(var j = 0; j < datos.length; j++){
							var detpreInt = new reDetPre({
								'spg_cuenta' :datos[j].spg_cuenta,
								'operacion'  :datos[j].operacion,
								'procede_doc':datos[j].procede_doc,
								'codestpro1' :datos[j].codestpro1,
								'codestpro2' :datos[j].codestpro2,
								'codestpro3' :datos[j].codestpro3,
							    'codestpro4' :datos[j].codestpro4,
							    'codestpro5' :datos[j].codestpro5,
							    'estcla'     :datos[j].estcla,
							    'codestpro'  :datos[j].codestpro,
							    'documento'  :datos[j].documento,
							    'descripcion':datos[j].descripcion,
							    'monto'      :datos[j].monto,
							    'codfuefin'  :datos[j].codfuefin
							});
							gridDetPresupuestario.store.insert(0,detpreInt);
						}
					}
				},
  				failure: function ( result, request){ 
  						Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
  				}//fin del success
  			});//fin del ajax request
	    }
	    
	    function buscarDetallesContables(registro)
	    {
    		var reDetCon = Ext.data.Record.create([
   	    	    {name: 'sc_cuenta'},
   	    	    {name: 'procede_doc'},
   	    	    {name: 'documento'},
   	    	    {name: 'descripcion'},
   	    	    {name: 'monto'},
   	    	    {name: 'debhab'}
   	    	]);
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
   			var ObjjSon=Ext.util.JSON.encode(JSONObject);
     			var parametros ='ObjSon='+ObjjSon;
     			Ext.Ajax.request({
     				url: '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
     				params: parametros,
     				method: 'POST',
     				success: function ( result, request ) { 
   						Ext.Msg.hide();
   						var resultado = result.responseText;
   						var objDatos = eval('(' + resultado + ')');
   						var datos = objDatos.raiz;
   						if (objDatos != ""){
   							gridDetContables.store.removeAll();
   							Ext.getCmp('totaldeb').setValue('');
   							Ext.getCmp('totalhab').setValue('');
   							Ext.getCmp('diferencia').setValue('');
   							fromComprobantePresupuestario.add(gridDetContables);
   							fromComprobantePresupuestario.add(fieldsetTotCont);
   							gridDetContables.show();
   							fieldsetTotCont.show();
   							fromComprobantePresupuestario.doLayout();
   							var montodeb = 0;
   							var montohab = 0;
   							for(var j = 0; j < datos.length; j++){
   								var detconInt = new reDetCon({
   									'sc_cuenta'  :datos[j].sc_cuenta,
   									'operacion'  :datos[j].debhab,
   									'procede_doc':datos[j].procede_doc,
   								    'documento'  :datos[j].documento,
   								    'descripcion':datos[j].descripcion,
   								    'monto'      :datos[j].monto,
   								});
   								if(datos[j].debhab=='D'){
   									var monto = parseFloat(datos[j].monto);
   									montodeb += monto; 
   								}
   								else{
   									var monto = parseFloat(datos[j].monto);
   									montohab += monto; 
   								}
   								gridDetContables.store.insert(0,detconInt);
   							}
   							acumularTotalContable();
   						}
   						else{
   							gridDetContables.store.removeAll();
   							gridDetContables.hide();
   							Ext.getCmp('totaldeb').setValue('');
   							Ext.getCmp('totalhab').setValue('');
   							Ext.getCmp('diferencia').setValue('');
   							fieldsetTotCont.show();
   						}
   					},
     				failure: function ( result, request){ 
     						Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
     				}//fin del success
     			});//fin del ajax request
	    }
	    	
	}
	
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//INICIO DEL FORMULARIO AGREGAR PRESUPUESTO//
	function AgregarPresupuesto()
	{
		var fieldSetEstOrigen = new com.sigesp.vista.comFSEstructuraFuenteCuenta({
			titform: 'Estructura Presupuestaria',
			mostrarDenominacion:true,
			sinFuente:false,
			sinCuenta:false,
			idtxt:'comfsest',
			datosocultos:1,
			camposocultos:['sc_cuenta']
		});
		//agregarListenersEstructura(fieldSetEstructura);
		
		//Creacion del formulario de agregar presupuesto
		var frmAgregarPresupuesto = new Ext.FormPanel({
			width: 870,
			height: 530, 
			style: 'position:absolute;left:5px;top:0px',
			frame: true,
			autoScroll:false,
			items:[{
					xtype:"fieldset", 
					title:'Datos del Documento',
					border:true,
					width: 850,
					height: 180,
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
											id:'agrdocgasto',	
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
										id:'catdesgasto',									
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
										id:'catprogasto',										
										width: 185,
										readOnly:true,
										value:'SPGCMP'
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
								items: [{
										xtype:'textfield',
										labelSeparator:'',
										fieldLabel:'Operaci&#243;n',
										name:'operacion',
										id:'opecmp',										
										width: 185,
										readOnly:true,
										value:Ext.getCmp('operaciones').getValue()
									}]
								}]
						},
						{
						xtype: 'hidden',
						id: 'sc_cuenta',
						binding:true,
						hiddenvalue:'',
						defaultvalue:''
						},
						{
						style:'position:absolute;left:15px;top:135px',
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
										id:'catmongasto',											
										width: 185,
										autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'0123456789.-');"},
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
				},fieldSetEstOrigen.fsEstructura]  
		});

		var ventanaAgregarPresupuesto = new Ext.Window({
			title: "<H1 align='center'>Entrada de Comprobante de Gastos</H1>",
			width:880,
			x:10,
			height:590, 
			modal: true,
			closable:false,
			plain: false,
			frame:true,
			items:[frmAgregarPresupuesto],
			buttons: [{
				text:'Aceptar',  
				handler: function(){
					var arrCodigos = fieldSetEstOrigen.obtenerArrayEstructura();
					var estructura = fieldSetEstOrigen.obtenerEstructuraFormato();
					if(Ext.getCmp('agrdocgasto').getValue()=='' || Ext.getCmp('catdesgasto').getValue()=='' ||
					   Ext.getCmp('catmongasto').getValue()==''	|| Ext.getCmp('catmongasto').getValue()=='0,00'	|| Ext.getCmp('codcuentacomfsest').getValue()==''){
						Ext.Msg.show({
							title:'Mensaje',
							msg:'Debe completar todos los datos',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
					}
					else{
						var reDetGas = Ext.data.Record.create([
					   	    {name: 'sc_cuenta'}, 
						   	{name: 'spg_cuenta'},
						   	{name: 'denominacion'},
						   	{name: 'procede'},
						   	{name: 'operacion'},
						   	{name: 'documento'},
						   	{name: 'monto'},
						]);
						var detgasInt = new reDetGas({
							'sc_cuenta':Ext.getCmp('sc_cuenta').getValue(),
							'codfuefin':arrCodigos[6],
							'spg_cuenta':arrCodigos[7],
							'documento':Ext.getCmp('agrdocgasto').getValue(),
							'descripcion':Ext.getCmp('catdesgasto').getValue(),
							'procede_doc':Ext.getCmp('catprogasto').getValue(),
							'operacion':Ext.getCmp('opecmp').getValue(),
							'monto':Ext.getCmp('catmongasto').getValue(),
							'codestpro':estructura,
							'codestpro1':arrCodigos[0],
							'codestpro2':arrCodigos[1],
							'codestpro3':arrCodigos[2],
							'codestpro4':arrCodigos[3],
							'codestpro5':arrCodigos[4],
							'estcla':arrCodigos[5],
						});
						var entro=false;
						if(gridDetPresupuestario.getStore().getCount()==0){
							gridDetPresupuestario.store.insert(0,detgasInt);
						}
						else{
							gridDetPresupuestario.store.each(function (reDetGas){
								if(reDetGas.get('spg_cuenta')==arrCodigos[7] && reDetGas.get('codestpro1')==arrCodigos[0] &&
										reDetGas.get('codestpro2')==arrCodigos[1] && reDetGas.get('codestpro3')==arrCodigos[2] &&
										reDetGas.get('codestpro4')==arrCodigos[3] && reDetGas.get('codestpro5')==arrCodigos[4] &&
										reDetGas.get('estcla')==arrCodigos[5] && reDetGas.get('documento')==Ext.getCmp('agrdocgasto').getValue()){
									Ext.Msg.show({
										 title:'Mensaje',
										 msg: 'El Detalle Presupuestario ya existe...',
										 buttons: Ext.Msg.OK,
										 icon: Ext.MessageBox.INFO
									 });
									entro=true;
								}
							})
							if(!entro){
								gridDetPresupuestario.store.insert(0,detgasInt);
							}
						}
						
						if(!entro){
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
							var campo = Ext.getCmp('operaciones').getValue();
							if(campo=='CG' || campo=='GC' || campo=='CP' || campo=='CCP'){
								var monto = Ext.util.Format.substr(Ext.getCmp('catmongasto').getValue(),0,1);
								var montocont = Ext.getCmp('catmongasto').getValue();
								var operacion = 'D';
								if(monto=='-'){
									operacion = 'H';
									montocont = Ext.util.Format.substr(Ext.getCmp('catmongasto').getValue(),1,10);
								}
								var detconInt = new reDetCon({
									'sc_cuenta':Ext.getCmp('sc_cuenta').getValue(),
									'documento':Ext.getCmp('agrdocgasto').getValue(),
									'descripcion':Ext.getCmp('catdesgasto').getValue(),
									'procede_doc':Ext.getCmp('catprogasto').getValue(),
									'operacion':operacion,
									'monto':montocont,
								});
								if(gridDetContables.getStore().getCount()==0){
									gridDetContables.store.insert(0,detconInt);
								}
								else{
									var existe=false;
									var total = 0;
									gridDetContables.store.each(function (reDetCon){
										if(reDetCon.get('sc_cuenta')==Ext.getCmp('sc_cuenta').getValue() && operacion==reDetCon.get('operacion')){
											total = parseFloat(ue_formato_operaciones(reDetCon.get('monto')));
											montocont = parseFloat(ue_formato_operaciones(montocont));
											reDetCon.set('monto',formatoNumericoMostrar(total+montocont,2,'.',',','','','-',''));  
											existe=true;
										}
									})
									if(!existe){
										gridDetContables.store.insert(0,detconInt);
									}
								}
								acumularTotalContable();
							}
						}
						acumularTotalGasto(gridDetPresupuestario,'totalpre');
						//Se desactivo cerrar la ventana en aceptar caso mantis 16609
						//ventanaAgregarPresupuesto.close();
					}	
				}
			},
			{
		   		text: 'Salir',
	   			handler:function(){
					ventanaAgregarPresupuesto.close();
	   		    }
	   		}]
		});
		ventanaAgregarPresupuesto.show();
	}
	//FIN DEL FORMULARIO AGREGAR PRESUPUESTO//
    
	//Funcion que para buscar el consecutivo nrocomprobante
	function NroComprobante(prefijo)
	{
            if (Actualizar==null)
            {
		if(!tbadministrativo)
		{
			var myJSONObject = {
				"operacion" :'verificar_prefijo',
				"procede" :'SPGCMP'	
			};
			var ObjSon= JSON.stringify(myJSONObject);
			var parametros ='ObjSon='+ObjSon;
			Ext.Ajax.request({
				url: '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
				params: parametros,
				method: 'POST',
				success: function ( result, request )
				{ 
		    		var prefijo = result.responseText;
	    			if((prefijo == "1")&&(!tbadministrativo))
					{
						Ext.getCmp('comprobante').setDisabled(true);
					}
		    		else
					{
						Ext.getCmp('comprobante').setDisabled(false);
		    		}
				}
			});
		}

		var myJSONObject = {
			"operacion" :'cargar_nrodocumento',
			"procede" :'SPGCMP',
                        "prefijo" : prefijo                        
		};
		var ObjSon= JSON.stringify(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request )
			{ 
	    		var numdoc = result.responseText;
    			if(numdoc == "-2")
				{
	    			Ext.Msg.show({
	    				title:'Mensaje',
	    				msg: 'El sistema tiene configurado el uso de prefijo y este usuario no tiene uno asignado !!!',
	    				buttons: Ext.Msg.OK,
	    				fn: function(){ location.href = 'sigesp_vis_spg_inicio.html'},
	    				icon: Ext.MessageBox.INFO
	    			});
	    		}
	    		else if (numdoc != "-1")
				{
	    			Ext.getCmp('comprobante').setValue(numdoc);
	    		}
			}
		});
            }
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
                        url : '../../controlador/spg/sigesp_ctr_spg_comprobante.php',
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
