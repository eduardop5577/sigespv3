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

barraherramienta    = true; 
var registrocuenta        = '';
var gridIntegracionCuenta = '';
var banderaGrabar   	  = true;
var banderaEliminar       = true;
var banderaImprimir       = true;
var banderaCatalogo       = false;
var Actualizar            = null
var ruta			      = '../../controlador/cfg/sigesp_ctr_cfg_scg_integracion.php'; 	// Ruta del Controlador de la Pantalla

var registrocuenta = Ext.data.Record.create([
		{name: 'sig_cuenta'},     
		{name: 'denominacion'},
		{name: 'sc_cuenta'},
		{name: 'cueclaeco'},
		{name: 'cueoncop'}
]); // Se usa en la seleccion del registro en el Catalogo del Plan Unico de Recursos y Egresos

Ext.onReady(function(){

	function catRecurosEgresoIntegracion(){
		var registroPlanUnicoRecursoEgreso = Ext.data.Record.create([
		    {name: 'sig_cuenta'},    
		    {name: 'denominacion'}
		]);
		                                                 		                                 	
		var dsPlanUnicoRecursoEgreso =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz', id: "id"},registroPlanUnicoRecursoEgreso)
		});
		
		var formBusquedaPlanUnicoRecursoEgreso = new Ext.FormPanel({
	        labelWidth: 80,
	        frame:true,
	        title: 'B&uacute;squeda',
	        bodyStyle:'padding:5px 5px 0',
	        width: 630,
			height:100,
	        defaultType: 'textfield',
			items: [{
				fieldLabel: 'Cuenta',
	            name: 'codplanunicore',
				id:'codplanunicore',
				width:250,
				labelSeparator:'',
				autoCreate: {tag: 'input', type: 'text', maxlength: 25, onkeypress: "return keyRestrict(event,'0123456789');"},
				changeCheck: function() {
					var v = this.getValue();
					dsPlanUnicoRecursoEgreso.filter('sig_cuenta',v);
				},							 
				initEvents : function() {
					AgregarKeyPress(this);
				}               
	      	},{
	      		fieldLabel: 'Denominaci&#243;n',
				name: 'denplanunicore',
				id:'denplanunicore',
				width:500,
				labelSeparator:'',
				autoCreate: {tag: 'input', type: 'text', maxlength: 254, onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"},
				changeCheck: function() {
					var v = this.getValue();
					dsPlanUnicoRecursoEgreso.filter('denominacion',v,true,false);
				},							 
				initEvents : function() {
					AgregarKeyPress(this);
				}
			}]
		});
		
		var JSONObject = {"oper": 'catalogo',"estatus": 'C'};
		var ObjSon=JSON.stringify(JSONObject);
		var	parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
			url : '../../controlador/cfg/sigesp_ctr_cfg_scg_planunico.php',
			params : parametros,
			method: 'POST',
			success: function ( resultado, request) 
			{ 
				datos = resultado.responseText;
				var objetoPlanUnicoRecursoEgreso = eval('(' + datos + ')');
				if(objetoPlanUnicoRecursoEgreso!='')
				{
					dsPlanUnicoRecursoEgreso.loadData(objetoPlanUnicoRecursoEgreso);
				}
			}	
		});
		
		
		var gridRecursoEgreso = new Ext.grid.GridPanel({
			width:770,
			height:400,
			tbar: formBusquedaPlanUnicoRecursoEgreso,
			autoScroll:true,
		    border:true,
		    ds: dsPlanUnicoRecursoEgreso,
		    cm: new Ext.grid.ColumnModel([new Ext.grid.CheckboxSelectionModel({}),
		          {header: "Cuenta", width: 30, sortable: true,   dataIndex: 'sig_cuenta'},
		          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denominacion'}
		    ]),
		    sm: new Ext.grid.CheckboxSelectionModel({}),
		    stripeRows: true,
		    viewConfig: {forceFit:true}
		});
		
		
		var venCatRecursoEgreso = new Ext.Window({
			title: 'Cat&#225;logo de cuentas del plan &#250;nico de recursos y egresos',
			autoScroll:true,
	        width:785,
	        height:485,
	        modal: true,
	        closable:false,
	        plain: false,
	        items:[gridRecursoEgreso],
	        buttons: [{
						text:'Aceptar',  
				        handler: function(){
				        		var arrRegistro = gridRecursoEgreso.getSelectionModel().getSelections();
				        		for ( var int = 0; int < arrRegistro.length; int++) {
									var cuenta = arrRegistro[int];
									//if(validarExistenciaRegistroGrid(cuenta,gridIntegracionCuenta,'sig_cuenta','sig_cuenta',true)){
										var cuentaInt = new registrocuenta({
											'sig_cuenta':'',
											'denominacion':'',
											'sc_cuenta':''
										});
										gridIntegracionCuenta.store.insert(0,cuentaInt);
										cuentaInt.set('sig_cuenta',cuenta.get('sig_cuenta'));
										cuentaInt.set('denominacion',cuenta.get('denominacion'));
									//}
								}
				        		venCatRecursoEgreso.destroy();
							}
				       },
				       {
				      	text: 'Salir',
				        handler: function(){
				        	venCatRecursoEgreso.destroy();
				       	}
	                  }]
	    });
	      
	    venCatRecursoEgreso.show();
	}
	

    
    function creargrid(){
    	/*Crear datasore y cargar el casamiento existente*/
    	var dsIntegracion = new Ext.data.Store({
    		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registrocuenta)
    	});
    	
    	var JSONObject = {"oper": 'catalogo'}
    	var	ObjSon=JSON.stringify(JSONObject);
    	var	parametros = 'ObjSon='+ObjSon; 
    	Ext.Ajax.request({
    		url : '../../controlador/cfg/sigesp_ctr_cfg_scg_integracion.php',
    		params : parametros,
    		method: 'POST',
    		success: function ( resultado, request)
			{
    			var datos = resultado.responseText;
    			var objetointegracionctas = eval('(' + datos + ')');
    			if(objetointegracionctas!='')
                        {
    				dsIntegracion.loadData(objetointegracionctas);
    			}
    		}
    	});
    	/*Fin Crear datasore y cargar el casamiento existente*/
    	
    	/*Formulario para busqueda*/
    	function actualizarIntegracion(criterio,cadena){
    		dsIntegracion.filter(criterio,cadena,true,false);
		}
		
		var formBusquedaIntegracion = new Ext.FormPanel({
	        labelWidth: 150,
	        frame:true,
	        title: 'B&#250;squeda de Cuenta Presupuestaria',
	        bodyStyle:'padding:5px 5px 0',
	        width: 1000,
			height:100,
	        defaultType: 'textfield',
			items: [{
				fieldLabel: 'Cuenta Presupuestaria',
                id:'codcuentaint',
                labelSeparator:'',
				width:200,
				autoCreate: {tag: 'input', type: 'text', maxlength: 25},
				changeCheck: function(){
					var v = this.getValue();
					dsIntegracion.filter('sig_cuenta',v);
				},							 
				initEvents : function() {
					AgregarKeyPress(this);
				}               
      		},{
			    fieldLabel: 'Denominaci&#243;n',
			    id:'dencuentaint',
			    labelSeparator:'',
			    width:430,
			    autoCreate: {tag: 'input', type: 'text', maxlength: 254},
				changeCheck: function(){
					var v = this.getValue();
					dsIntegracion.filter('denominacion',v,true,false);
				},							 
				initEvents : function(){
					AgregarKeyPress(this);
				}
			}]
		});
    	/*Fin Fromulario para busqueda*/
		
    	var Xpos = ((screen.width/2)-(1200/2));
        var Ypos = 70;
    	
		gridIntegracionCuenta = new Ext.grid.GridPanel({
			width:1000,
		    height:440,
		    frame:true,
		    style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
		    title:'Integraci&#243;n de cuentas',
			applyTo: 'formulario_integracion_cta',
			bbar : formBusquedaIntegracion,
	       	ds: dsIntegracion,
	       	cm: new Ext.grid.ColumnModel([new Ext.grid.CheckboxSelectionModel({}),
	            {header: "Cuenta presupuestaria", width: 20, sortable: true, dataIndex: 'sig_cuenta'},
	            {header: "Denominaci&#243;n", width: 60, sortable: true, dataIndex: 'denominacion'},
	            {header: "Cuenta cont. Institucional", width: 20, setEditable: true, sortable: true, dataIndex: 'sc_cuenta'},
	            {header: "Cuenta cont. Oncop", width: 20, setEditable: true, sortable: true, dataIndex: 'cueoncop'},
	            {header: "Cuenta clasifi. Economico", width: 20, setEditable: true, sortable: true, dataIndex: 'cueclaeco'}
	        ]),
	       	sm: new Ext.grid.CheckboxSelectionModel({}),
			viewConfig: {forceFit:true},
	        columnLines: true,
	        tbar:[{
	            text:'Agregar cuenta presupuestaria',
	            tooltip:'Agregar cuenta presupuestaria',
	            iconCls:'agregar',
	            handler: catRecurosEgresoIntegracion
	        }, '-', {
	            text:'Agregar Cta. Cont. Institucional',
	            tooltip:'Agregar Cta. Cont. Institucional',
	            iconCls:'agregar',
	            id:'agregar',
				handler: function(){
					var arrRegCuentas = gridIntegracionCuenta.getSelectionModel().getSelections();
					if(arrRegCuentas.length > 0)
						mostrarCatalogoCuentaContableCasamiento('catalogocuentamovimiento',arrRegCuentas);
					else
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Debe seleccionar una cuenta presupuestaria',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
				} 		
	        }, '-', {
	            text:'Agregar Cta. Cont. Oncop',
	            tooltip:'Agregar Cta. Cont. Oncop',
	            iconCls:'agregar',
	            id:'agregar',
				handler: function(){
					var arrRegCuentas = gridIntegracionCuenta.getSelectionModel().getSelections();
					if(arrRegCuentas.length > 0)
						mostrarCatalogoCuentaContableOncop('catalogooncop',arrRegCuentas);
					else
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Debe seleccionar una cuenta presupuestaria',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
				} 		
	        }, '-', {
	            text:'Agregar Cta. Clasif. Economico',
	            tooltip:'Agregar Cta. Clasif. Economico',
	            iconCls:'agregar',
	            id:'agregar',
				handler: function(){
					var arrRegCuentas = gridIntegracionCuenta.getSelectionModel().getSelections();
					if(arrRegCuentas.length > 0)
						mostrarCatalogoCuentaContableClasificador('catalogoclasificadoreconomico',arrRegCuentas);
					else
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Debe seleccionar una cuenta presupuestaria',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
				} 		
	        }]
	    });
	}
    //llamado a funcion para pintar la grid ....
    creargrid();
});


function irImprimir()
{
	pantalla    = "reporte/sigesp_vis_cfg_rep_integracion.php";
	window.open(pantalla,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
};

function irGuardar()
{
	var numDatos = gridIntegracionCuenta.store.getModifiedRecords();
	var reg = "{'oper':'incluirvarios','codmenu':"+codmenu+",'datos':[";
	for(var i=0;i<=numDatos.length-1;i++) {
		var sig_cuenta = numDatos[i].get('sig_cuenta');
		var sc_cuenta  = numDatos[i].get('sc_cuenta');
		if(sc_cuenta == '') {
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Debe asignarle una cuenta contable, a la cuenta '+sig_cuenta,
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.WARNING
			});
			
			return false;
		}
		
		if(i==0)
                {
			reg = reg + "{'sig_cuenta':'" + numDatos[i].get('sig_cuenta') +"','sc_cuenta':'" + numDatos[i].get('sc_cuenta')+"',";
			reg = reg + " 'cueclaeco':'" + numDatos[i].get('cueclaeco') +"','cueoncop':'" + numDatos[i].get('cueoncop')+"'}";
		}	
		else
                {
			reg = reg + ",{'sig_cuenta':'" + numDatos[i].get('sig_cuenta') +"','sc_cuenta':'" + numDatos[i].get('sc_cuenta')+"', ";
			reg = reg + " 'cueclaeco':'" + numDatos[i].get('cueclaeco') +"','cueoncop':'" + numDatos[i].get('cueoncop')+"'}";
		}		
	}
	reg = reg + "]}";
	var Obj= eval('(' + reg + ')');
	var ObjSon=JSON.stringify(Obj);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scg_integracion.php',
		params : parametros,
		method: 'POST',
		success: function ( resultad, request ) { 
	        var respuesta = resultad.responseText;
	       	if(respuesta == '1'){
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Registro(s) incluido(s) con &#233;xito',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
				gridIntegracionCuenta.store.commitChanges();
				gridIntegracionCuenta.store.sort('sig_cuenta');
				gridIntegracionCuenta.getSelectionModel().clearSelections(); 
			}
	       	else {
	       		Ext.Msg.show({
					title:'Mensaje',
					msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
	       	}
		},
		failure: function ( result, request) {
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Ha ocurrido un error de conexion, por favor comuniquese con el administrador del sistema',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			}); 
		} 
	});
}

function irEliminar()
{
	var resulado;
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este(os) registro(s)&#63;', resultado);
	function resultado(btn) {
		if(btn=='yes') {
			var numDatos = gridIntegracionCuenta.getSelectionModel().getSelections();
			var reg = "{'oper':'eliminarvarios','codmenu':"+codmenu+",'datos':[";
			for(var i=0;i<=numDatos.length-1;i++)
			{	
				if(i==0)
				{
					reg = reg + "{'sig_cuenta':'" + numDatos[i].get('sig_cuenta') +"','sc_cuenta':'" + numDatos[i].get('sc_cuenta')+"'}";
				}	
				else
				{
					reg = reg + ",{'sig_cuenta':'" + numDatos[i].get('sig_cuenta') +"','sc_cuenta':'" + numDatos[i].get('sc_cuenta')+"'}";
				}		
			}
			reg = reg + "]}";
			
			var Obj = eval('(' + reg + ')');
			var ObjSon = JSON.stringify(Obj);
			var parametros = 'ObjSon='+ObjSon; 
			
			Ext.Ajax.request({
				url : '../../controlador/cfg/sigesp_ctr_cfg_scg_integracion.php',
				params : parametros,
				method: 'POST',
				success: function ( resultad, request ){ 
					var datos = resultad.responseText;
					var resultado = datos.split("|");
					if(resultado[1] != '0')
					{
						var mensaje = "Fueron eliminado(s) "+resultado[1]+" registro(s)  con &#233;xito <br><br>";
						if(resultado[0] != "")
						{
							var erroreli  = resultado[0].split(",");
							for(var j=0;j<=erroreli.length-1;j++)
							{
								mensaje = mensaje + " La cuenta "+erroreli[j]+" tiene movimientos no puede ser eliminada <br>" 
							}
							
							for(var i=0;i<=numDatos.length-1;i++)
							{
								var validoQuitar = true
								for(var j=0;j<=erroreli.length-1;j++)
								{
									if (erroreli[j]==numDatos[i].get('sig_cuenta').trim())
									{
										validoQuitar = false;
									}
								}
								if(validoQuitar)
								{
									gridIntegracionCuenta.store.remove(numDatos[i]);
								}
							}
						}
						else
						{
							for(var i=0;i<=numDatos.length-1;i++)
							{
								gridIntegracionCuenta.store.remove(numDatos[i]);
							}
						}
						Ext.Msg.show({
							title:'Mensaje',
							msg: mensaje,
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.INFO
						});
						gridIntegracionCuenta.store.sort('sig_cuenta');
						gridIntegracionCuenta.getSelectionModel().clearSelections();
					}
					else
					{
						Ext.Msg.show({
							title:'Mensaje',
							msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo, la cuenta puede tener movimientos.',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.ERROR
						});
					}
				},
				failure: function ( result, request){
					Ext.Msg.show({
						title:'Mensaje',
						msg: 'Ha ocurrido un error de conexion, por favor comuniquese con el administrador del sistema',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					}); 
				} 
		    });
		}
	}
}

function irNuevo()
{
	Ext.getCmp('codcuentaint').setValue('');
	Ext.getCmp('dencuentaint').setValue('');
	var JSONObject = {"oper": 'catalogo'}
	var	ObjSon=JSON.stringify(JSONObject);
	var	parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scg_integracion.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request)
		{
			var datos = resultado.responseText;
			var objetointegracionctas = eval('(' + datos + ')');
			if(objetointegracionctas!='')
			{
				gridIntegracionCuenta.getStore().loadData(objetointegracionctas);
			}
		}
	});
	
}

function irCancelar()
{
	irNuevo();
}