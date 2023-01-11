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

function catalogoCuentasspg(campo) {
	
	var reSpgCuenta = Ext.data.Record.create([
		{name: 'spg_cuenta'},    
		{name: 'denominacion'}
	]);
	
	var dsSpgCuenta =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSpgCuenta)
	});
	
	//aqui llenar ds
	var myJSONObject = {"oper": 'catcuentaspg'};	
	var ObjSon=JSON.stringify(myJSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_empresa.php',
		params : parametros,
		method: 'POST',
		success: function (resultado, request) {
			var datos = resultado.responseText;
			var datosJson = eval('(' + datos + ')');
			if(datosJson.raiz!=null) {
				dsSpgCuenta.loadData(datosJson);
			}		
		}
	});
	
	var formBusqueda = new Ext.FormPanel({
        labelWidth: 100,
        frame:true,
        title: 'B&#250;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 500,
		height:100,
        defaultType: 'textfield',
		items: [{
			fieldLabel: 'C&#243;digo',
			labelSeparator : '',
            name: 'codigo',
			id:'codigo',
			width: 100,
			autoCreate: {tag: 'input', type: 'text', maxlength: 25},
			changeCheck: function(){
				var valor = this.getValue();
				dsSpgCuenta.filter('spg_cuenta',valor);
			},							 
			initEvents : function() {
				AgregarKeyPress(this);
			}               
      	},{
      		fieldLabel: 'Denominaci&#243;n',
      		labelSeparator : '',
		    name: 'denominacion',
		    id:'denominacion',
		    width: 350,
		    autoCreate: {tag: 'input', type: 'text', maxlength: 254},
			changeCheck: function(){
				var valor = this.getValue();
				dsSpgCuenta.filter('denominacion',valor,true,false);
			},							 
			initEvents : function() {
				AgregarKeyPress(this);
			}
		}]
	});
	
	var gridSpgCuenta = new Ext.grid.GridPanel({
		width:760,
	 	height:350,
	 	tbar: formBusqueda,
	 	autoScroll:true,
     	border:true,
     	ds: dsSpgCuenta,
     	cm: new Ext.grid.ColumnModel([
     		{header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'spg_cuenta'},
          	{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denominacion'}
       	]),
       	stripeRows: true,
		viewConfig: {forceFit:true}
	});
	
	gridSpgCuenta.on({
		'celldblclick': {
			fn: function(){
				var registro = gridSpgCuenta.getSelectionModel().getSelected();
            	var strCuenta = Ext.getCmp(campo).getValue();
            	if(strCuenta.length < 230) {
            		strCuenta = strCuenta+','+registro.get('spg_cuenta');
            		Ext.getCmp(campo).setValue(strCuenta);
            	}
            	else {
            		Ext.Msg.show({
						title:'Error',
						msg: 'El campo ha llegado a su limite maximo de caracteres',
						buttons: Ext.Msg.OK,
						icon: Ext.MessageBox.ERROR
					});
            	}
                gridSpgCuenta.destroy();
		      	ventanaspgcuentas.destroy();			
			}
		}
	});

	var ventanaspgcuentas = new Ext.Window({
		title: 'Cat&#225;logo cuentas presupuestarias',
		autoScroll:true,
        width:800,
        height:430,
        modal: true,
        closable:false,
        plain: false,
        items:[gridSpgCuenta],
        buttons: [{
        	text:'Aceptar',  
            handler: function() {
            	var registro = gridSpgCuenta.getSelectionModel().getSelected();
            	var strCuenta = Ext.getCmp(campo).getValue();
            	strCuenta = strCuenta+','+registro.get(spg_cuenta);
            	Ext.getCmp(campo).setValue(strCuenta);
                gridSpgCuenta.destroy();
		      	ventanaspgcuentas.destroy();                      
			}
		},
		{
			text: 'Salir',
            handler: function(){
            	gridSpgCuenta.destroy();
		      	ventanaspgcuentas.destroy();
			}
		}]
	});
    
    ventanaspgcuentas.show();       
}