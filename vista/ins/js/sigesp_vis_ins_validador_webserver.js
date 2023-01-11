Ext.onReady(function(){
	Ext.BLANK_IMAGE_URL = 'base/librerias/js/ext/resources/images/default/s.gif';
	
	//Datos del combo gestor BD
	var gestor = [ [ 'MySql', 'M' ], 
	               [ 'PostgreSQL', 'P' ],
	               [ 'ORACLE', 'O' ] ];
	
	var stGestor = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : gestor
	});
	
	//funcion para mostrar imagen de estado de parametro
	function mostrarEstado(estado) {
		if(estado==1){
			return '<img src="../../base/imagenes/aceptar.png" style="border-style:none" />';
		}
		else{
			return '<img src="../../base/imagenes/cancelar.png" style="border-style:none" />';
		}
	}
	
	//Creando datastore y columnmodel para la grid de parametros
	var reParametro = Ext.data.Record.create([
	    {name: 'codigo'},
	    {name: 'parametro'},
	    {name: 'estado'}
	]);
	
	var dsParametro =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reParametro)
	});
						
	var cmParametro = new Ext.grid.ColumnModel([
	    {header: "<H1 align='center'>Codigo</H1>", width: 10, sortable: true, dataIndex: 'codigo'},                                        
		{header: "<H1 align='center'>Parametro</H1>", width: 80, sortable: true, dataIndex: 'parametro'},
        {header: "<H1 align='center'>Estado</H1>", width: 10, sortable: true, dataIndex: 'estado',renderer:mostrarEstado}
	]);
	
	                                    	
	
	//Creando grid para los parametros a validar
	var gridParametros = new Ext.grid.GridPanel({
 		width:900,
 		height:300,
		frame:true,
		style: 'position:absolute;left:10px;top:100px',
		autoScroll:true,
		border:true,
 		ds: dsParametro,
   		cm: cmParametro,
		stripeRows: true,
  		viewConfig: {forceFit:true}
	});

	
	//Formulario principal
	var forDataUsuario = new Ext.FormPanel({
		title: "<H1 align='center'>Validador de Web Server (SIGESP)</H1>",
		width: 1000,
		height: 500,
		frame: true,
		style:'position:absolute;margin-left:30px;margin-top:45px;',
		autoScroll:true,
		applyTo: 'formValidador',
		items: [{
			layout: "column",
			items: [{
				layout: "form",
				items: [{
					xtype: 'combo',
					labelSeparator :'',
					fieldLabel: 'Gestor BD',
					id: 'gestor',
					store : stGestor,
					editable : false,
					displayField : 'etiqueta',
					valueField : 'valor',
					typeAhead : true,
					triggerAction : 'all',
					mode : 'local',
			        emptyText:'----Seleccione----',
			   	    listWidth:150,
			        width:150
				}]
			}]
		},{
			layout: "column",
			style: 'position:absolute;left:700px;top:50px',
			items: [{
				layout: "form",
				items: [{
					xtype:'button',
					//iconCls: 'menubuscar',
					text:'Validar Web Server',
					id:'Validar',
					handler:function(){
						var gestorSel = Ext.getCmp('gestor').getValue();
						if(gestorSel!=''){
							var myJSONObject = {
								"operacion":"validar",
								"gestor":gestorSel
							};
									
							var ObjSon=Ext.util.JSON.encode(myJSONObject);
							var parametros ='ObjSon='+ObjSon;
							Ext.Ajax.request({
								url: '../../controlador/ins/sigesp_ctr_ins_validador_webserver.php',
								params: parametros,
								method: 'POST',
								success: function ( result, request ) {
									var parametros = result.responseText;
									var objetoParametro = eval('(' + parametros + ')');
									if(objetoParametro!='') {
										gridParametros.store.loadData(objetoParametro);
									}	
								},
								failure: function ( result, request){ 
									Ext.MessageBox.alert('Error', 'Ocurrio un error en el servicio notifique al administrador del sistema'); 
								}//fin del success
							});//fin del ajax request
						}
						else {
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'Debe seleccionar el manejador de base de datos con el cual se conectara el sistema',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
						}
					}
				}]
			}]
		},
		gridParametros]
	});
	
	
  	
});	

