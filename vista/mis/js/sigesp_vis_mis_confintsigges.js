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

var plConfiguracion = null;
var gridCuenta = null;

function buscarNumero() {
	var myJSONObject = {"operacion":"BUS_NUM"};
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/mis/sigesp_ctr_mis_confintsigges.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) {
			var numero = result.responseText;
			Ext.getCmp('numcon').setValue(numero);
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
		}
	});
}

function catalogoCuenta(regCueEnl, estOriDes, bdOrigen, bdDestino) {
	var bd = '';
	var campoCuenta = '';
	
	if(estOriDes == 'O'){
		bd = bdOrigen;
		campoCuenta = 'cueori';
	}
	else {
		bd = bdDestino;
		campoCuenta = 'cuedes';
	}
	
	//registro cuenta
	var reCuenta = Ext.data.Record.create([
	    {name: 'codcuenta'},
	    {name: 'dencuenta'}
	]);
	
	var dsCuenta =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuenta)
	});
	
	var formFiltroCuenta = new Ext.FormPanel({
		labelWidth: 90,
        frame:true,
        title: 'Filtrar cuenta',
        bodyStyle:'padding:5px 5px 0',
        height:150,
        width:600,
        items: [{
        	xtype: 'textfield',
			fieldLabel: 'C&#243;digo',
			labelSeparator : '',
            id:'codcue',
			width: 120,
			autoCreate: {tag: 'input', type: 'text', maxlength: 25},
			changeCheck: function(){
				var valor = this.getValue();
				dsCuenta.filter('codcuenta',valor,true,false);
			},							 
			initEvents : function() {
				AgregarKeyPress(this);
			}	             
  		},{
  			xtype: 'textfield',
  			fieldLabel: 'Descripci&#243;n',
  			labelSeparator : '',
		    id:'dencue',
		    width: 400,
		    autoCreate: {tag: 'input', type: 'text', maxlength: 254},
		   	changeCheck: function(){
				var valor = this.getValue();
				dsCuenta.filter('dencuenta',valor,true,false);
			},							 
			initEvents : function() {
				AgregarKeyPress(this);
			}
		},{
			xtype: 'button',
		   	fieldLabel: '',
		   	id: 'btbuscar',
		   	text: 'Buscar',
		   	style:'position:absolute;left:450px;top:80px;',
		   	iconCls: 'menubuscar',
		   	handler: function(){
		   		obtenerMensaje('procesar','','Buscando Datos');
		   					
	   			var JSONObject = {
	   				'operacion' : 'BUS_CUE',
   					'codcue' : Ext.getCmp('codcue').getValue(),
   					'dencue' : Ext.getCmp('dencue').getValue(),
   					'bdbus'  : bd,
   					'codfon' : Ext.getCmp('codfon').getValue(),
   				}
	   				
			   	var ObjSon = JSON.stringify(JSONObject);
   				var parametros = 'ObjSon='+ObjSon; 
   				Ext.Ajax.request({
   					url : '../../controlador/mis/sigesp_ctr_mis_confintsigges.php',
   					params : parametros,
   					method: 'POST',
   					success: function ( resultado, request){
   						Ext.Msg.hide();
   						var datos = resultado.responseText;
   						var objData = eval('(' + datos + ')');
   						if(objData!=''){
   							if(objData.raiz == null || objData.raiz ==''){
   								Ext.MessageBox.show({
					 				title:'Advertencia',
					 				msg:'No existen datos para mostrar',
					 				buttons: Ext.Msg.OK,
					 				icon: Ext.MessageBox.WARNING
					 			});
							}
							else {
								dsCuenta.loadData(objData);
							}
   						}
   					}//fin del success	
   				});//fin del ajax request
		   	}
		}]
	});
	
	//Grid de catalogo cunetas
	var gridCatCuenta = new Ext.grid.GridPanel({
		width:770,
	 	height:370,
	 	tbar: formFiltroCuenta,
	    ds: dsCuenta,
	    cm: new Ext.grid.ColumnModel([
            {header: "C&#243;digo", width: 15, sortable: true, dataIndex: 'codcuenta'},
            {header: "Descripci&#243;n", width: 30, sortable: true, dataIndex: 'dencuenta'}
        ]),
       	sm: new Ext.grid.CheckboxSelectionModel({}),
		viewConfig: {forceFit:true},
        columnLines: true
    });
	
	gridCatCuenta.on({
		'celldblclick': {
			fn: function () {
				var regCuenta = gridCatCuenta.getSelectionModel().getSelected();
				regCueEnl.set(campoCuenta,regCuenta.get('codcuenta'));
            	gridCatCuenta.destroy();
            	venCatCuenta.destroy();
			}
		}
	});
	
	//Ventana de catalogo contratos
	var venCatCuenta = new Ext.Window({
		title: "<H1 align='center'>Cuentas Contables</H1>",
		width:800,
	    height:450,
        modal: true,
        closable:false,
        plain: false,
        items:[gridCatCuenta],
        buttons: [{
        	text:'Aceptar',  
            handler: function() {
            	var regCuenta = gridCatCuenta.getSelectionModel().getSelected();
            	regCueEnl.set(campoCuenta,regCuenta.get('codcuenta'));
            	gridCatCuenta.destroy();
            	venCatCuenta.destroy();                      
            }
        },{
        	text: 'Salir',
            handler: function() {
            	gridCatCuenta.destroy();
            	venCatCuenta.destroy();
            }
        }]
	});
	venCatCuenta.show();
}

barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//combo leer
    var sistema = [['SIGESP', 'S'], ['GESTOR', 'G']]
    var stSistema = new Ext.data.SimpleStore({
        fields: ['descripcion', 'valor'],
        data: sistema
    });
    
    var cmbLeer = new Ext.form.ComboBox({
		store: stSistema,
		fieldLabel:'Leer en',
		labelSeparator: '',
		displayField:'descripcion',
		valueField:'valor',
        id:'baslec',
        //listWidth : 250,
        forceSelection: true,  
        typeAhead: true,
        mode: 'local',
        binding:true,
        editable: false,
        triggerAction: 'all'
	});
    //fin combo leer
    
    //combo escribir
    var cmbEscribir = new Ext.form.ComboBox({
		store: stSistema,
		fieldLabel:'Escribir en',
		labelSeparator: '',
		displayField:'descripcion',
		valueField:'valor',
        id:'basesc',
        //listWidth : 250,
        forceSelection: true,  
        typeAhead: true,
        mode: 'local',
        binding:true,
        editable: false,
        triggerAction: 'all'
	});
    //fin combo escribir
    
    //combo movimineto banco
    var movimiento = [['Ninguno', 'NI'], ['Nota Credito', 'NC'], ['Nota Debito', 'ND']]
    var stMovimiento = new Ext.data.SimpleStore({
        fields: ['descripcion', 'valor'],
        data: movimiento
    });
    
    var cmbMovBanco = new Ext.form.ComboBox({
		store: stMovimiento,
		fieldLabel:'Movimiento Banco',
		labelSeparator: '',
		displayField:'descripcion',
		valueField:'valor',
        id:'movban',
        forceSelection: true,  
        typeAhead: true,
        mode: 'local',
        binding:true,
        editable: false,
        triggerAction: 'all'
	});
    //fin combo movimineto banco
    
    //creando datastore y columnmodel para el catalogo fondos
	var reFondo = Ext.data.Record.create([
		{name: 'cod_agencia'},
		{name: 'nombre'}
	]);
	
	var dsFondo =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reFondo)
	});
						
	var cmFondo = new Ext.grid.ColumnModel([
	    {header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'cod_agencia'},
		{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'nombre'}
	]);
	//fin creando datastore y columnmodel para el catalogo fondos
	
	//componente campocatalogo para el campo fondos
	var comtcFondo = new com.sigesp.vista.comCampoCatalogo({
		titvencat: 'Fondo',
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 350,
		anchoven: 500,
		altoven: 420,
		anchofieldset:850,
		datosgridcat: dsFondo,
		colmodelocat: cmFondo,
		rutacontrolador:'../../controlador/mis/sigesp_ctr_mis_confintsigges.php',
		parametros: "ObjSon={'operacion': 'OBT_FON'",
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codfondo',valor:'cod_agencia'},
				   {etiqueta:'Denominaci&#243;n',id:'denfondo',valor:'nombre'}],
		posicion:'position:absolute;left:5px;top:65px',
		tittxt:'Fondo',
		idtxt:'codfon',
		campovalue:'cod_agencia',
		anchoetiquetatext:130,
		anchotext:130,
		anchocoltext:0.40,
		idlabel:'denfon',
		labelvalue:'nombre',
		anchocoletiqueta:0.53,
		anchoetiqueta:250,
		tipbus:'P',
		binding:'C',
		hiddenvalue:'',
		defaultvalue:'',
		allowblank:true
	});
	//fin componente campocatalogo para el campo fondos
	
	//combo operacion
    var operacion = [['Debe', 'D'], ['Haber', 'H']]
    var stOperacion = new Ext.data.SimpleStore({
        fields: ['descripcion', 'valor'],
        data: operacion
    });
    
    var cmbOpeOri = new Ext.form.ComboBox({
		store: stOperacion,
		displayField:'descripcion',
		valueField:'valor',
        forceSelection: true,  
        typeAhead: true,
        mode: 'local',
        binding:true,
        editable: false,
        triggerAction: 'all'
	});
    
    var cmbOpeDes = new Ext.form.ComboBox({
		store: stOperacion,
		displayField:'descripcion',
		valueField:'valor',
        forceSelection: true,  
        typeAhead: true,
        mode: 'local',
        binding:true,
        editable: false,
        triggerAction: 'all'
	});
    //fin combo operacion
	
	//registro y store de la grid de tareas
	reCuenta = Ext.data.Record.create([
	    {name: 'cueori'},
	    {name: 'cueopo'},
	    {name: 'codopo'},
	    {name: 'cuedes'},
	    {name: 'codopd'},
	    {name: 'estbdt'}
	]);
	
	var dsCuenta =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCuenta)
	});
	
	//Grid de cuenta
	gridCuenta = new Ext.grid.EditorGridPanel({
		title: "<H1 align='center'>Enlace Cuentas Contables</H1>",
		width:700,
	    height:230,
	    frame:true,
	    style: 'position:absolute;left:65px;top:210px',
	    ds: dsCuenta,
       	cm: new Ext.grid.ColumnModel([
       	    new Ext.grid.CheckboxSelectionModel(),                          
			{
				header: "Cuenta Origen",
			    width: 80,
			    sortable: true,
			    dataIndex: 'cueori',
			    editor: new Ext.form.TextField({
			        allowBlank: false,
			        enableKeyEvents: true,
			        readOnly: true,
			        listeners: {
			            'keypress': function(Obj, e){
			            	Ext.Msg.hide();
			                var whichCode = e.keyCode;
			                if (whichCode == 38) {
			                	//catalogo de cuenta origen
			                }
			            }
			        }
			    })
			},{
				header: "Operaci&#243;n Origen", 
				width: 40, 
				sortable: true, 
				dataIndex: 'codopo',
				editor : cmbOpeOri
			},{
				header: "Cuenta Destino",
			    width: 80,
			    sortable: true,
			    dataIndex: 'cuedes',
			    editor: new Ext.form.TextField({
			        allowBlank: false,
			        enableKeyEvents: true,
			        readOnly: true,
			        listeners: {
			            'keypress': function(Obj, e){
			            	Ext.Msg.hide();
			                var whichCode = e.keyCode;
			                if (whichCode == 38) {
			                	//catalogo de cuenta destino
			                }
			            }
			        }
			    })
			},{
				header: "Operaci&#243;n Destino", 
				width: 40, 
				sortable: true, 
				dataIndex: 'codopd',
				editor : cmbOpeDes
			}
        ]),
       	sm: new Ext.grid.CheckboxSelectionModel({}),
		viewConfig: {forceFit:true},
        columnLines: true,
        tbar:[{
            text:'Agregar cuenta',
            tooltip:'Agregar cuenta contable y su enlace  ',
            iconCls:'agregar',
            handler: function(){
            	var cuenta = new reCuenta({
					'cueori':'',
					'codopo':'',
					'cuedes':'',
					'codopd':'',
					'estbdt':'N'
				});
            	gridCuenta.store.add(cuenta);
            }
        }, '-', {
            text:'Eliminar cuenta',
            tooltip:'Eliminar cuenta contable y su enlace ',
            iconCls:'remover',
            handler: function() {
            	Ext.Msg.show({
	        		title:'Confirmar',
	     		   	msg: 'Desea eliminar este registro?',
	     		   	buttons: Ext.Msg.YESNO,
	     		   	icon: Ext.MessageBox.QUESTION,
	     		   	fn: function(btn) {
	     		   		if (btn == 'yes') {
	     		   			var regEliminar = gridCuenta.getSelectionModel().getSelected();
	     		   			//if(gridTareas.store.getCount() > 1){
		                		if(regEliminar.get('estbdt') == 'N') {
		                			gridCuenta.store.remove(regEliminar);
		                		}
		                		else {
		                			var myJSONObject = {
		                				"operacion":"ELI_CUE",
		                				"cueori":regEliminar.get('cueori'),
		                				"codopo":regEliminar.get('codopo'),
		                				"cuedes":regEliminar.get('cuedes'),
		                				"codopd":regEliminar.get('codopd'),
		                				"numcon":Ext.getCmp('numcon').getValue()
		                			};
		                			var ObjSon=Ext.util.JSON.encode(myJSONObject);
		                			var parametros ='ObjSon='+ObjSon;
		                			Ext.Ajax.request({
		                				url: '../../controlador/mis/sigesp_ctr_mis_confintsigges.php',
		                				params: parametros,
		                				method: 'POST',
		                				success: function ( result, request ) {
		                					var respuesta = result.responseText;
		                					if (respuesta == 1) {
		                						gridCuenta.store.remove(regEliminar);
		                					}
		                					else {
		                						Ext.Msg.show({
		    	    	    						title:'Mensaje',
		    	    	    						msg: 'Ocurrio un error al tratar de eliminar la cuenta',
		    	    	    						buttons: Ext.Msg.OK,
		    	    	    						icon: Ext.MessageBox.ERROR
		    	    	    					});
		                					}
		                					
		                				},
		                				failure: function ( result, request){ 
		                						Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
		                				}
		                			});
		                		}
		                	/*}
		                	else {
		                		Ext.Msg.show({
		    						title:'Mensaje',
		    						msg: 'La actividad debe contener al menos una tarea realizada',
		    						buttons: Ext.Msg.OK,
		    						icon: Ext.MessageBox.WARNING
		    					});
		                	}*/
	     		   		}
	     		   	}
            	});
            } 		
        }]
    });
	
	gridCuenta.on('cellclick', function(grid, rowIndex, columnIndex, e) {
		var record = grid.getStore().getAt(rowIndex);
		var campo  = grid.getColumnModel().getDataIndex(columnIndex);
		var bdOrigen  = Ext.getCmp('baslec').getValue();
		var	bdDestino = Ext.getCmp('basesc').getValue();
		if(campo == 'cueori' && record.get('estbdt') != 'S') {
			catalogoCuenta(record, 'O', bdOrigen, bdDestino);
		}
		else if (campo == 'cuedes' && record.get('estbdt') != 'S') {
			catalogoCuenta(record, 'D', bdOrigen, bdDestino);
		}
	});
	
	//PANEL PRINCIPAL CONFIGURACION INTERFAZ SIGESP - GESTOR
	plConfiguracion = new Ext.FormPanel({
		title: "<H1 align='center'>Configuraci&#243;n Interfaz SIGESP - GESTOR</H1>",
		style: 'position:relative;top:25px;left:100px', 
		height: 480,
		width: 850,
	   	applyTo:'formConfIntSigGes',
	   	frame: true,
	   	items:[{
			xtype: 'hidden',
			id: 'catalogo',
			value:'0'
		},{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:10px',
			items: [{
				layout: "form",
				border: false,
				labelWidth: 130,
				items: [{
					xtype:'textfield',
					fieldLabel:'N&#250;mero',
					style:'font-weight: bold; border:none;background:#f1f1f1',
					id:'numcon',
					width:150,
					labelSeparator:'',
					binding:true,
					hiddenvalue:'',
					defaultvalue:'',
					allowBlank:false
				}]
			}]
		},{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:40px',
			items: [{
				layout: "form",
				border: false,
				labelWidth: 130,
				items: [{
					xtype:'textfield',
					fieldLabel:'Descripci&#243;n',
					id:'descon',
					width:500,
					autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '255'},
					labelSeparator:'',
					binding:true,
					hiddenvalue:'',
					defaultvalue:'',
					allowBlank:false
				}]
			}]
		},comtcFondo.fieldsetCatalogo,
		{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:110px',
			items: [{
				width: 400,
				layout: "form",
				border: false,
				labelWidth: 130,
				items: [cmbLeer]
			},{
				width: 400,
				layout: "form",
				border: false,
				labelWidth: 100,
				style: 'padding-left:15px',
				items: [cmbEscribir]
			}]
		},{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:140px',
			items: [{
				layout: "form",
				border: false,
				labelWidth: 130,
				items: [cmbMovBanco]
			}]
		},{
			layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:170px',
			items: [{
				layout: "form",
				border: false,
				labelWidth: 200,
				items: [{
		            xtype: "checkbox",
		            fieldLabel: "Obviar cuentas no configuradas",
		            labelSeparator: '',
		            id: 'obvcue',
		           	inputValue: '1',
		           	defaultvalue:'0',
		           	binding:true
		        }]
			}]
		},gridCuenta]
	});
});
buscarNumero();

function irNuevo() {
	limpiarFormulario(plConfiguracion);
	gridCuenta.store.removeAll();
	buscarNumero();
}

function irBuscar() {
	irNuevo();
	function cargarCuentas() {
		var myJSONObject = {"operacion":"OBT_CUE",
							"numcon":Ext.getCmp('numcon').getValue()};
		var ObjSon=Ext.util.JSON.encode(myJSONObject);
		var parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/mis/sigesp_ctr_mis_confintsigges.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) {
				var datos = result.responseText;
				var objData = eval('(' + datos + ')');
				gridCuenta.store.loadData(objData);
				Ext.getCmp('catalogo').setValue('1');
			},
			failure: function ( result, request){ 
					Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
			}
		});
	}
	
	var reConfiguracion = Ext.data.Record.create([
		{name: 'numcon'},
		{name: 'descon'},
		{name: 'codfon'},
		{name: 'movban'},
		{name: 'baslec'},
		{name: 'basesc'},
		{name: 'obvcue'},
		{name: 'dupori'}
		
	]);
	
	var dsConfiguracion =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reConfiguracion)
	});
						
	var cmConfiguracion = new Ext.grid.ColumnModel([
        {header: "N&#250;mero", width: 20, sortable: true,   dataIndex: 'numcon'},
        {header: "Descripci&#243;n", width: 40, sortable: true, dataIndex: 'descon'}
    ]);
	
	
	var comCatConfiguracion = new com.sigesp.vista.comCatalogo({
		titvencat: 'Catalogo de Configuraci&#243;n de Interfaz',
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 430,
		datosgridcat: dsConfiguracion,
		colmodelocat: cmConfiguracion,
		arrfiltro:[{etiqueta:'N&#250;mero',id:'numconf',valor:'numcon'},
				   {etiqueta:'Descripci&#243;n',id:'desconf',valor:'descon'}],
		rutacontrolador:'../../controlador/mis/sigesp_ctr_mis_confintsigges.php',
		parametros: "ObjSon={'operacion': 'BUS_CON'",
		tipbus:'P',
		setdatastyle:'F',
		formulario:plConfiguracion,
		onAceptar:true,
		fnOnAceptar: cargarCuentas
	});
	
	comCatConfiguracion.mostrarVentana();
}

function irGuardar() {
	var strJsonConfiguracion = getJsonFormulario(plConfiguracion);
	var dataCuenta = gridCuenta.getStore(); 
	if(dataCuenta.getCount() > 0) {
    	var arrCampos = [{etiqueta:'Cuenta origen', campo:'cueori', tipo:'s', requerido: true},
    	                 {etiqueta:'Operaci&#243;n origen', campo:'codopo', tipo:'s', requerido: true},
    	                 {etiqueta:'Cuenta destino', campo:'cuedes', tipo:'s', requerido: true},
    	                 {etiqueta:'Operaci&#243;n destino', campo:'codopd', tipo:'s', requerido: true}];
    	var strJsonGrid = getJsonGrid(dataCuenta, arrCampos);
    	if(strJsonGrid != false) {
    		var operacion = '';
    		if (Ext.getCmp('catalogo').getValue() == '0') {
    			operacion = 'INS_CON';
    		}
    		else {
    			operacion = 'MOD_CON';
    		}
    		var strJsonConCue = "{'operacion':'"+operacion+"',"+strJsonConfiguracion+",'arrConf':"+strJsonGrid+"}";
    		var objjson = Ext.util.JSON.decode(strJsonConCue);
    		if (typeof(objjson) == 'object') {
    			var parametros ='ObjSon='+strJsonConCue;
	        	Ext.Ajax.request({
	        		url: '../../controlador/mis/sigesp_ctr_mis_confintsigges.php',
	        		params: parametros,
	        		method: 'POST',
	        		success: function ( result, request ) {
	        			var respuesta = result.responseText;
						var datajson = eval('(' + respuesta + ')');
						if(datajson.raiz.valido==true){
							Ext.Msg.show({
	    						title:'Mensaje',
	    						msg: datajson.raiz.mensaje,
	    						buttons: Ext.Msg.OK,
	    						icon: Ext.MessageBox.INFO
	    					});
							irNuevo();
						}
						else {
							Ext.Msg.show({
	    						title:'Mensaje',
	    						msg: datajson.raiz.mensaje,
	    						buttons: Ext.Msg.OK,
	    						icon: Ext.MessageBox.ERROR
	    					});
						}
	        		},
	        		failure: function ( result, request){ 
	        				Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
	        		}
	        	});
    		}
    	}
    }
	else {
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Debe agregar al menos una cuenta a configurar',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.WARNING
		});
	}
}

function irEliminar() {
	Ext.Msg.show({
		title:'Confirmar',
		   	msg: 'Desea eliminar este registro?',
		   	buttons: Ext.Msg.YESNO,
		   	icon: Ext.MessageBox.QUESTION,
		   	fn: function(btn) {
		   		if (btn == 'yes') {
		   			var myJSONObject = {"operacion":"ELI_CON", "numcon":Ext.getCmp('numcon').getValue()};
					var ObjSon=Ext.util.JSON.encode(myJSONObject);
					var parametros ='ObjSon='+ObjSon;
					Ext.Ajax.request({
						url: '../../controlador/mis/sigesp_ctr_mis_confintsigges.php',
						params: parametros,
						method: 'POST',
						success: function ( result, request ) {
							var respuesta = result.responseText;
	    					if (respuesta == 1) {
	    						Ext.Msg.show({
		    						title:'Mensaje',
		    						msg: 'La configuracion fue eliminada exitosamente',
		    						buttons: Ext.Msg.OK,
		    						icon: Ext.MessageBox.INFO
		    					});
	    						irNuevo();
							}
							else {
								Ext.Msg.show({
		    						title:'Mensaje',
		    						msg: 'Ocurrio un error al tratar de eliminar la configuracion',
		    						buttons: Ext.Msg.OK,
		    						icon: Ext.MessageBox.ERROR
		    					});
							}
						},
						failure: function ( result, request){ 
								Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
						}
					});
		   		}
		   	}
	});
}

function irCancelar() {
	irNuevo();
}