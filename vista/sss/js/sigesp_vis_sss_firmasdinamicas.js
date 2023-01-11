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

var cambiar = false;
var panel      = '';
var pantalla   = 'firmasdinamicas';
var ruta = '../../controlador/sss/sigesp_ctr_sss_firmasdinamicas.php';
var gridDetalle   = '';
var dsdetalle = '';
var arrFirmas	= new Array();
var arrEliminar = new Array();
var toteliminar = 0;
var DatosNuevo = {'raiz':[{'codfir':'','codcla':'','tipclafir':'','nombre':'','fir1':'','fir2':'','fir3':'','fir4':'','fir5':''}]};	
barraherramienta    = true;
var RecordDef = "";
var Actualizar = null;
Ext.onReady
(
	function()
	{
	    Ext.QuickTips.init();
		Ext.form.Field.prototype.msgTarget = 'side';
		var agregar = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregar,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar Clasificacion'
		});
		
		var quitar = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitar,
			iconCls: 'bmenuquitar',
        	tooltip: 'Quitar Clasificacion'
		});

		RecordDef = Ext.data.Record.create
		([
			{name: 'codfir'}, 
			{name: 'codcla'},
			{name: 'tipclafir'},
			{name: 'nombre'},
			{name: 'fir1'}, 
			{name: 'fir2'},
			{name: 'fir3'},
			{name: 'fir4'}, 
			{name: 'fir5'}
		]);
		
		dsdetalle =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevo),
		reader: new Ext.data.JsonReader({
			root: 'raiz',               
			id: 'id'   
			},
				  RecordDef
			),
			data: DatosNuevo
			});
		
		gridDetalle = new Ext.grid.EditorGridPanel({
				width:1000,
				autoScroll:true,
				border:true,
				ds: dsdetalle,
				cm: new Ext.grid.ColumnModel([new Ext.grid.CheckboxSelectionModel(),
					{header: 'Código', width: 50, sortable: true,   dataIndex: 'codcla'},
					{header: 'Nombre', width: 100, sortable: true, dataIndex: 'nombre'},
					{header: 'Firma 1', width: 150, sortable: true, dataIndex: 'fir1',id:'fir1',
                                            editor: new Ext.form.TextField({
                                            allowBlank: false,
                                             autoCreate: {tag: 'input', type: 'text', setEditable: true, maxLength: 254, autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890- ');"}
                                         })},
					{header: 'Firma 2', width: 150, sortable: true, dataIndex: 'fir2',id:'fir2',
                                            editor: new Ext.form.TextField({
                                            allowBlank: true,
                                             autoCreate: {tag: 'input', type: 'text', setEditable: true, maxLength: 254, autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890- ');"}
                                         })},
					{header: 'Firma 3', width: 150, sortable: true, dataIndex: 'fir3',id:'fir3',
                                            editor: new Ext.form.TextField({
                                            allowBlank: true,
                                             autoCreate: {tag: 'input', type: 'text', setEditable: true, maxLength: 254, autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890- ');"}
                                         })},
					{header: 'Firma 4', width: 150, sortable: true, dataIndex: 'fir4',id:'fir4',
                                            editor: new Ext.form.TextField({
                                            allowBlank: true,
                                             autoCreate: {tag: 'input', type: 'text', setEditable: true, maxLength: 254, autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890- ');"}
                                         })},
					{header: 'Firma 5', width: 150, sortable: true, dataIndex: 'fir5',id:'fir5',
                                            editor: new Ext.form.TextField({
                                            allowBlank: true,
                                             autoCreate: {tag: 'input', type: 'text', setEditable: true, maxLength: 254, autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890- ');"}
                                         })}
				]),
                                sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
				viewConfig: {
								forceFit:true
							},
				autoHeight:true,
				stripeRows: true
		});
            
		var datosReporte={'raiz':[{'tiprepfir':'SEP','nomrepfir':'Solicitud de ejecución presupuestaria'},
					   {'tiprepfir':'SOC','nomrepfir':'Ordenes de Compra'},
					   {'tiprepfir':'CXP','nomrepfir':'Solicitud de Pago'},
					   {'tiprepfir':'SCB','nomrepfir':'Cheque Voucher'}]};
		
		recordReporte = Ext.data.Record.create([
				{name: 'tiprepfir'},     
				{name: 'nomrepfir'}
		]);					
		dsReporte =  new Ext.data.Store({
				proxy: new Ext.data.MemoryProxy(datosReporte),
				reader: new Ext.data.JsonReader(
				{
					root: 'raiz',               
					id: 'id'   
				},
				recordReporte
				),
				data: datosReporte			
			 });
			 			 
		var datosClasificacion={'raiz':[{'tipclafir':'001','nomclafir':'Control numero'},
					   {'tipclafir':'002','nomclafir':'Unidad ejecutora'},
					   {'tipclafir':'003','nomclafir':'Usuario'},
					   {'tipclafir':'004','nomclafir':'Tipo de SEP'},
                                           {'tipclafir':'005','nomclafir':'Tipo Orden'}]};
		
		recordClasificacion = Ext.data.Record.create([
				{name: 'tipclafir'},     
				{name: 'nomclafir'}
		]);					
		dsClasificacion =  new Ext.data.Store({
				proxy: new Ext.data.MemoryProxy(datosClasificacion),
				reader: new Ext.data.JsonReader(
				{
					root: 'raiz',               
					id: 'id'   
				},
				recordClasificacion
				),
				data: datosClasificacion			
			 });

		var datosNroFir={'raiz':[{'nrofir':'1'},
					   {'nrofir':'2'},
					   {'nrofir':'3'},
					   {'nrofir':'4'},
                                           {'nrofir':'5'}]};
		
		recordNroFir = Ext.data.Record.create([
				{name: 'nrofir'}
		]);
                
		dsNroFir =  new Ext.data.Store({
				proxy: new Ext.data.MemoryProxy(datosNroFir),
				reader: new Ext.data.JsonReader(
				{
					root: 'raiz',               
					id: 'id'   
				},
				recordNroFir
				),
				data: datosNroFir			
			 });
            
		Xpos = ((screen.width/2)-(1100/2)); 
		Ypos = ((screen.height/2)-(600/2));
		//Panel con los componentes del formulario
		panel = new Ext.FormPanel({
                labelWidth: 75,
                title: 'Firmas Dinámicas',
                bodyStyle:'padding:5px 5px 5px',
                width: 1050,
		style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
	  	tbar: [],
                defaults: {width: 1050},		   
		items:[{
			   	xtype:'fieldset',
				title:'Datos',
				id:'fsDefiniciones',
                                autoHeight:true,
				autoWidth:true,
				cls :'fondo',	
                                items:[{
                                        xtype:'textfield',
                                        fieldLabel:'Código',
					name:'Código',
					id:'txtcodfir',
					disabled: true,
                                        autoCreate: {tag: 'input', type: 'text', size: '6', autocomplete: 'off', maxlength: '4', onkeypress: "return keyRestrict(event,'1234567890');"},
					width:80                                        
                                        },
                                        {
                                        xtype:'textfield',
                                        fieldLabel:'Denominacion',
					name:'Denominacion',
					id:'txtdenfir',
					disabled: false,
                                        autoCreate: {tag: 'input', type: 'text', size: '350', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890- ');"},
					width:350                                        
                                        }]
                                },{
					xtype:'fieldset',
					title:'Datos del Sistema',
					id:'fsSistema',
					autoHeight:true,
					autoWidth:true,
					cls :'fondo',	
					items:[{			   
                                                xtype:'combo',
                                                fieldLabel:'Reporte',
                                                name:'Reporte',
                                                id:'cmbReporte',
                                                emptyText:'Seleccione',
                                                displayField:'nomrepfir',
                                                valueField:'tiprepfir',
                                                typeAhead: true,
                                                mode: 'local',
                                                triggerAction: 'all',
                                                store: dsReporte,
                                                width:250,
                                                listeners: {
                                                    'change': function(combo, nuevovalor,antiguovalor)
                                                    {
                                                                if(nuevovalor == "")
                                                                {
                                                                    datosClasificacion={'raiz':[{'tipclafir':'000','nomclafir':'Ninguno'}]};
                                                                }
                                                                if(nuevovalor == "SEP")
                                                                {
                                                                    datosClasificacion={'raiz':[{'tipclafir':'000','nomclafir':'Ninguno'},
                                                                                                {'tipclafir':'001','nomclafir':'Control numero'},
                                                                                                {'tipclafir':'002','nomclafir':'Unidad ejecutora'},
                                                                                                {'tipclafir':'003','nomclafir':'Usuario'},
                                                                                                {'tipclafir':'004','nomclafir':'Tipo de SEP'}]};
                                                                }
                                                                if(nuevovalor == "SOC")
                                                                {
                                                                    datosClasificacion={'raiz':[{'tipclafir':'000','nomclafir':'Ninguno'},
                                                                                                {'tipclafir':'001','nomclafir':'Control numero'},
                                                                                                {'tipclafir':'005','nomclafir':'Tipo Orden'}]};
                                                                }
                                                                if(nuevovalor == "CXP")
                                                                {
                                                                    datosClasificacion={'raiz':[{'tipclafir':'000','nomclafir':'Ninguno'},
                                                                                                {'tipclafir':'001','nomclafir':'Control numero'},
                                                                                                {'tipclafir':'003','nomclafir':'Usuario'}]};
                                                                }
                                                                if(nuevovalor == "SCB")
                                                                {
                                                                    datosClasificacion={'raiz':[{'tipclafir':'000','nomclafir':'Ninguno'},
                                                                                                {'tipclafir':'003','nomclafir':'Usuario'}]};
                                                                }
                                                                dsClasificacion.loadData(datosClasificacion);
                                                                Ext.getCmp('cmbClasificacion').setValue('000');
                                                                gridDetalle.store.removeAll();
                                                                gridDetalle.store.loadData(DatosNuevo);
                                                    }
                                                }
                                              },
                                              {			   
                                                xtype:'combo',
                                                fieldLabel:'Clasificacion',
                                                name:'Clasificacion',
                                                id:'cmbClasificacion',
                                                emptyText:'Seleccione',
                                                displayField:'nomclafir',
                                                valueField:'tipclafir',
                                                typeAhead: true,
                                                mode: 'local',
                                                triggerAction: 'all',
                                                store: dsClasificacion,
                                                width:250,
                                                listeners: {
                                                    'change': function(combo, nuevovalor,antiguovalor)
                                                    {
                                                                gridDetalle.store.removeAll();
                                                                gridDetalle.store.loadData(DatosNuevo);
                                                    }
                                                }                                                
                                              },
                                              {
                                                xtype:'combo',
                                                fieldLabel:'Nro firmantes',
                                                name:'NroFirmantes',
                                                id:'cmbNroFirmantes',
                                                emptyText:'Seleccione',
                                                displayField:'nrofir',
                                                valueField:'nrofir',
                                                typeAhead: true,
                                                mode: 'local',
                                                triggerAction: 'all',
                                                store: dsNroFir,
                                                width:100	
					      }]
				},{
					xtype:'panel',
					width:1050,
					title:'Detalle Firmantes',
					tbar: [agregar,quitar],
					contentEl:'grid-detalle'
			}]
		});
		panel.render(document.body);
	
		//llamada a la función
		gridDetalle.render('grid-detalle');
                irNuevo();
	}
);	//FIN

		
	function irAgregar()
	{
            var reDetalle = Ext.data.Record.create
            ([
                {name: 'codigo'}, 
                {name: 'nombre'}
            ]);
            
            var dsDetalle =  new Ext.data.Store({
                        reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reDetalle)
              });

            var chk = new Ext.grid.CheckboxSelectionModel({});

                var cmDetalle = new Ext.grid.ColumnModel([
                                chk,									   
                    {header: "C&#243;digo", width: 40, sortable: true, dataIndex: 'codigo'},
                    {header: "Denominaci&#243;n ", width: 100, sortable: true, dataIndex: 'nombre'}          
                ]);  

            gridVentanaDetalle = new Ext.grid.EditorGridPanel({
                                width:550,
                                height:325,
                                frame:true,
                                title:"",
                                style: 'position:absolute;left:10px;top:5px',
                                autoScroll:true,
                                border:true,
                                ds: dsDetalle,
                                cm: cmDetalle,
                                stripeRows: true,
                                sm: new Ext.grid.CheckboxSelectionModel({}),
                                viewConfig: {forceFit:true}
                });
                
            ventanaDetalle = new Ext.Window(
            {
                title: "<H1 align='center'>Cat&#225;logo</H1>",
                        autoScroll:true,
                width:575,
                height:400,
                modal: true,
                closable:false,
                plain: false,
                items:[gridVentanaDetalle],
                buttons: [{
                            text:'Aceptar',  
                                handler: function()
                                {
                                        arregloDetalle = gridVentanaDetalle.getSelectionModel().getSelections();
                                        for (i=0; i<arregloDetalle.length; i++)
                                        {
                                            arrDetalle = gridDetalle.getStore();
                                            if (arrDetalle.length > 0)
                                            {
                                                if (validarExistenciaRegistroGrid(arregloDetalle[i],gridDetalle,'codigo','codigo',true))
                                                {
                                                        pasarDatosGridDetalle(gridDetalle,arregloDetalle[i]);
                                                }
                                            }
                                            else
                                            {
                                                pasarDatosGridDetalle(gridDetalle,arregloDetalle[i]);
                                            }
                                        }
                                        gridVentanaDetalle.destroy();
                                        ventanaDetalle.destroy();
                                }
                          },
                          {
                            text: 'Salir',
                                 handler: function()
                                 {
                                        gridVentanaDetalle.destroy();
                                        ventanaDetalle.destroy();
                                }
                          }]
            });
            if (Ext.getCmp('cmbClasificacion').getValue() == '000')
            {
                detalle = new RecordDef
                ({
                        'codfir':'',
                        'codcla':'',
                        'tipclafir':'',
                        'nombre':'',
                        'fir1':'',
                        'fir2':'',
                        'fir3':'',
                        'fir4':'',
                        'fir5':''
                });
                gridDetalle.store.insert(0,detalle);
                detalle.set('codfir',Ext.getCmp('txtcodfir').getValue());
                detalle.set('tipclafir',Ext.getCmp('cmbClasificacion').getValue());
                detalle.set('codcla','000');
                detalle.set('nombre','Ninguno');
            }
            if (Ext.getCmp('cmbClasificacion').getValue() == '001')
            {
                ventanaDetalle.show();
                obtenerMensaje('procesar','','Buscando Datos');
                var JSONObject = {
                                'oper' : 'buscarControlNumero',
                                'codsis' : Ext.getCmp('cmbReporte').getValue()
                }			
                var ObjSon = JSON.stringify(JSONObject);
                var parametros = 'objdata='+ObjSon; 
                Ext.Ajax.request({
                        url : '../../controlador/sss/sigesp_ctr_sss_firmasdinamicas.php',
                        params : parametros,
                        method: 'POST',
                        success: function ( resultado, request){
                                Ext.Msg.hide();
                                var datos = resultado.responseText;
                                var objeto= eval('(' + datos + ')');
                                if(objeto!='' || objeto.raiz!=''){
                                        gridVentanaDetalle.store.loadData(objeto);
                                }
                                else {
                                        Ext.Msg.show({
                                                title:'Advertencia',
                                                msg: 'No se han encontrado datos',
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.WARNING
                                        });  				
                                }
                        }	
                });	
            }
            if (Ext.getCmp('cmbClasificacion').getValue() == '002')
            {
                ventanaDetalle.show();
                obtenerMensaje('procesar','','Buscando Datos');
                var JSONObject = {
                                'oper' : 'buscarUnidadEjecutora'
                }			
                var ObjSon = JSON.stringify(JSONObject);
                var parametros = 'objdata='+ObjSon; 
                Ext.Ajax.request({
                        url : '../../controlador/sss/sigesp_ctr_sss_firmasdinamicas.php',
                        params : parametros,
                        method: 'POST',
                        success: function ( resultado, request){
                                Ext.Msg.hide();
                                var datos = resultado.responseText;
                                var objeto= eval('(' + datos + ')');
                                if(objeto!='' || objeto.raiz!=''){
                                        gridVentanaDetalle.store.loadData(objeto);
                                }
                                else {
                                        Ext.Msg.show({
                                                title:'Advertencia',
                                                msg: 'No se han encontrado datos',
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.WARNING
                                        });  				
                                }
                        }	
                });	   
            }            
            if (Ext.getCmp('cmbClasificacion').getValue() == '003')
            {
                ventanaDetalle.show();
                obtenerMensaje('procesar','','Buscando Datos');
                var JSONObject = {
                                'oper' : 'buscarUsuario'
                }			
                var ObjSon = JSON.stringify(JSONObject);
                var parametros = 'objdata='+ObjSon; 
                Ext.Ajax.request({
                        url : '../../controlador/sss/sigesp_ctr_sss_firmasdinamicas.php',
                        params : parametros,
                        method: 'POST',
                        success: function ( resultado, request){
                                Ext.Msg.hide();
                                var datos = resultado.responseText;
                                var objeto= eval('(' + datos + ')');
                                if(objeto!='' || objeto.raiz!=''){
                                        gridVentanaDetalle.store.loadData(objeto);
                                }
                                else {
                                        Ext.Msg.show({
                                                title:'Advertencia',
                                                msg: 'No se han encontrado datos',
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.WARNING
                                        });  				
                                }
                        }	
                });	   
            }            
            if (Ext.getCmp('cmbClasificacion').getValue() == '004')
            {
                ventanaDetalle.show();
                obtenerMensaje('procesar','','Buscando Datos');
                var JSONObject = {
                                'oper' : 'buscarTipoSep'
                }			
                var ObjSon = JSON.stringify(JSONObject);
                var parametros = 'objdata='+ObjSon; 
                Ext.Ajax.request({
                        url : '../../controlador/sss/sigesp_ctr_sss_firmasdinamicas.php',
                        params : parametros,
                        method: 'POST',
                        success: function ( resultado, request){
                                Ext.Msg.hide();
                                var datos = resultado.responseText;
                                var objeto= eval('(' + datos + ')');
                                if(objeto!='' || objeto.raiz!=''){
                                        gridVentanaDetalle.store.loadData(objeto);
                                }
                                else {
                                        Ext.Msg.show({
                                                title:'Advertencia',
                                                msg: 'No se han encontrado datos',
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.WARNING
                                        });  				
                                }
                        }	
                });	   
            }            
            if (Ext.getCmp('cmbClasificacion').getValue() == '005')
            {
                ventanaDetalle.show();
                obtenerMensaje('procesar','','Buscando Datos');
                var JSONObject = {
                                'oper' : 'buscarTipoSoc'
                }			
                var ObjSon = JSON.stringify(JSONObject);
                var parametros = 'objdata='+ObjSon; 
                Ext.Ajax.request({
                        url : '../../controlador/sss/sigesp_ctr_sss_firmasdinamicas.php',
                        params : parametros,
                        method: 'POST',
                        success: function ( resultado, request){
                                Ext.Msg.hide();
                                var datos = resultado.responseText;
                                var objeto= eval('(' + datos + ')');
                                if(objeto!='' || objeto.raiz!=''){
                                        gridVentanaDetalle.store.loadData(objeto);
                                }
                                else {
                                        Ext.Msg.show({
                                                title:'Advertencia',
                                                msg: 'No se han encontrado datos',
                                                buttons: Ext.Msg.OK,
                                                icon: Ext.MessageBox.WARNING
                                        });  				
                                }
                        }	
                });	   
            }            
        }			

        function pasarDatosGridDetalle(grid,registro)
        {
            detalle = new RecordDef
            ({
                    'codfir':'',
                    'codcla':'',
                    'tipclafir':'',
                    'nombre':'',
                    'fir1':'',
                    'fir2':'',
                    'fir3':'',
                    'fir4':'',
                    'fir5':''
            });
            grid.store.insert(0,detalle);
            detalle.set('codfir',Ext.getCmp('txtcodfir').getValue());
            detalle.set('tipclafir',Ext.getCmp('cmbClasificacion').getValue());
            detalle.set('codcla',registro.get('codigo'));
            detalle.set('nombre',registro.get('nombre'));
        }
			
	function irQuitar()
	{
		var claveseleccionada = gridDetalle.selModel.selections.keys;
		if(claveseleccionada.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar el registro?', borrarRegistro);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}
	}
	
	function borrarRegistro(btn) 
	{
		if (btn=='yes') 
		{
			var filaseleccionada = gridDetalle.getSelectionModel().getSelected();
			if (filaseleccionada)
			{
				detalleelim    = gridDetalle.getSelectionModel().getSelected().get('tipclafir');
				arrEliminar[toteliminar] = detalleelim;
				toteliminar++;
				gridDetalle.store.remove(filaseleccionada);
				Ext.Msg.alert('Exito','Registro eliminado');				
			}
		} 
	}
	
	function limpiarCampos() 
	{		 
		Actualizar= null;
                Ext.getCmp('txtcodfir').setValue('');
		Ext.getCmp('txtdenfir').setValue('');
		Ext.getCmp('cmbReporte').setValue('Seleccione');
		Ext.getCmp('cmbClasificacion').setValue('Seleccione');	
		Ext.getCmp('cmbNroFirmantes').setValue('Seleccione');
                gridDetalle.store.removeAll();
		gridDetalle.store.loadData(DatosNuevo);
		gridDetalle.store.commitChanges();
		for (i=0; i<=arrFirmas.length; i++)
		{
			arrFirmas.pop();			
		}
		for (i=0; i<=arrEliminar.length; i++)
		{
			arrEliminar.pop();			
		}
	}

	function irCancelar()
	{
		limpiarCampos();
		arrEliminar = new Array();
		toteliminar = 0;
		cambiar = false;
                irNuevo();
	}
        
        function irNuevo()
        {
            limpiarCampos();
            var myJSONObject ={
		    'sistema': sistema,
                    'vista': vista,
                    'oper':'nuevo'
            };

            var ObjSon=Ext.util.JSON.encode(myJSONObject);
            var parametros = 'objdata='+ObjSon;
            Ext.Ajax.request({
            url : '../../controlador/sss/sigesp_ctr_sss_firmasdinamicas.php',
            params : parametros,
            method: 'POST',
            success: function ( result, request) 
            { 
                    datos = result.responseText;
                    var codigo = eval('(' + datos + ')');
                    if(codigo != "")
                    {
                            Ext.getCmp('txtcodfir').setValue(codigo);
                            gridDetalle.store.removeAll();
                    }
            }	
            })
            
        }

	function irGuardar()
	{
		valido=true;
                operacion = "actualizar";
                if(Actualizar == null)
                {
                    operacion = "incluir";
                } 
		if((!tbactualizar)&&(cambiar))
		{
			valido=false;
			Ext.MessageBox.alert('Error','No tiene permiso para Modificar.');
		}
		else if (Ext.getCmp('txtcodfir').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Debe tener un codigo de firma.');
		}
		else if (Ext.getCmp('txtdenfir').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Debe colocar una denominacion de la firma.');
		}
		else if (Ext.getCmp('cmbReporte').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Debe seleccionar un reporte.');
		}
		else if (Ext.getCmp('cmbClasificacion').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Debe seleccionar una clasificacion.');
		}
		else if (Ext.getCmp('cmbNroFirmantes').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Debe seleccionar un nro de firmantes.');
		}
		else 
                {
                        codfir = Ext.getCmp('txtcodfir').getValue();
                        denfir = Ext.getCmp('txtdenfir').getValue();
                        tiprepfir = Ext.getCmp('cmbReporte').getValue();
                        tipclafir = Ext.getCmp('cmbClasificacion').getValue();
                        nrofir = Ext.getCmp('cmbNroFirmantes').getValue();
                        first = true;
                        arrFirmas = gridDetalle.getStore();
                        cadenaDetalle= ",datosFirmas:[";
                        arrFirmas.each(function (registroGrid)
                        {
                            if (registroGrid.get('codcla') != '')
                            {
                                if(first)
                                {
                                        cadenaDetalle = cadenaDetalle + "{'codfir':'"+codfir+"','tipclafir': '"+tipclafir+"','codcla':'"+ registroGrid.get('codcla')+ "','fir1':'"+ registroGrid.get('fir1')+ "','fir2':'"+ registroGrid.get('fir2')+ "','fir3':'"+ registroGrid.get('fir3')+ "','fir4':'"+ registroGrid.get('fir4')+ "','fir5':'"+ registroGrid.get('fir5')+ "'}";
                                        first = false;
                                }
                                else 
                                {
                                        cadenaDetalle = cadenaDetalle + ",{'codfir':'"+codfir+"','tipclafir': '"+tipclafir+"','codcla':'"+ registroGrid.get('codcla')+ "','fir1':'"+ registroGrid.get('fir1')+ "','fir2':'"+ registroGrid.get('fir2')+ "','fir3':'"+ registroGrid.get('fir3')+ "','fir4':'"+ registroGrid.get('fir4')+ "','fir5':'"+ registroGrid.get('fir5')+ "'}";
                                }
                            }
                        });
                        cadenaDetalle= cadenaDetalle + "]";
			if (first)
			{
                            Ext.Msg.alert('Mensaje','Debe Registrar firmantes.');
                        }
                        else
                        {
                            obtenerMensaje('procesar','','Guardando Datos');
                            var cadenaJson = "{'oper': '"+operacion+"','codfir':'"+codfir+"','denfir':'"+denfir+"','tiprepfir': '"+tiprepfir+"','tipclafir': '"+tipclafir+"','nrofir': '"+nrofir+"','sistema': '"+sistema+"','vista': '"+vista+"' ";				
                            cadenaJson=cadenaJson + cadenaDetalle + ',datosEliminar:[';
                            total = arrEliminar.length;
                            if (total>0)
                            {
				for (i=0; i < total; i++)
				{
					if (i==0)
					{
						cadenaJson = cadenaJson +"{'codfir':'"+codfir+"','tipclafir': '"+tipclafir+"','codcla':'"+ arrEliminar[i]+ "'}";
					}
					else
					{
						cadenaJson = cadenaJson +",{'codfir':'"+codfir+"','tipclafir': '"+tipclafir+"','codcla':'"+ arrEliminar[i]+ "'}";
					}
				}			
                            }
                            cadenaJson = cadenaJson + ']}';
                            objdata= eval('(' + cadenaJson + ')');	
                            objdata=JSON.stringify(objdata);
                            parametros = 'objdata='+objdata; 
                            Ext.Ajax.request({
                            url : ruta,
                            params : parametros,
                            method: 'POST',
                            success: function (resultado, request)
                            { 
                                    datos = resultado.responseText;
                                    Ext.MessageBox.alert('Mensaje', datos);
                                    Ext.Msg.hide();
                                    var datajson = eval('(' + datos + ')');
                                    if (datajson.raiz.valido==true)
                                    {	
                                            Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
                                            irCancelar();  
                                    }
                                    else
                                    {
                                            Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
                                    }
                            },
                            failure: function (result,request) 
                            { 
                                    Ext.Msg.hide();
                                    Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
                            }					
                            });
                        }
		}	
	}

        function irBuscar()
	{
		var arreglotxt     = new Array('txtcodfir','txtdenfir','cmbReporte','cmbClasificacion','cmbNroFirmantes');		
		var arreglovalores = new Array('codfir','denfir','tiprepfir','tipclafir','nrofir');			
		objCatalogo = new catalogoFirmas();
		objCatalogo.mostrarCatalogoFirmas(arreglotxt, arreglovalores);
	}
	
	function irEliminar()
	{
		var Result;
		Ext.MessageBox.confirm('Confirmar', '¿Desea eliminar la firma?', Result);
		function Result(btn)
		{
			if(btn=='yes')
			{ 
				if (Ext.getCmp('txtcodfir').getValue()=='')
				{
					Ext.Msg.alert('Mensaje','Debe seleccionar una firma');
				}
				else if (Actualizar)
				{
					obtenerMensaje('procesar','','Eliminando Datos');
					codfir = Ext.getCmp('txtcodfir').getValue();
					var objdata ={
						'oper': 'eliminar', 
						'codfir':codfir,
						'sistema': sistema,
						'vista': vista
					};	
					objdata=JSON.stringify(objdata);
					parametros = 'objdata='+objdata;
					Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',
					success: function ( resultado, request )
					{ 
						datos = resultado.responseText;
						Ext.Msg.hide();
						var datajson = eval('(' + datos + ')');
						if (datajson.raiz.valido==true)
						{
							Ext.MessageBox.alert('Mensaje',datajson.raiz.mensaje);
							irCancelar();	  
						}
						else
						{
							Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
						}
					},
					failure: function ( result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', 'Error al procesar la información'); 
					} 
					});
				}
                                else
                                {
                                    Ext.Msg.alert('Mensaje','Debe seleccionar una firma');
                                }
			}
		};		
	}	
	
