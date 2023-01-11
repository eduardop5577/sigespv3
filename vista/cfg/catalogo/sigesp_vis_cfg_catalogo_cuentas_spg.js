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

function catalogoCuentasspg(operacion)
{
	
	var reSpgCuenta = Ext.data.Record.create([
		{name: 'spg_cuenta'},    
		{name: 'denominacion'}
	]);
	
	var dsSpgCuenta =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reSpgCuenta)
	});
	
	var formbusquedaspg = new Ext.FormPanel({
        labelWidth: 90,
        frame:true,
        title: 'B&#250;squeda',
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
				dsSpgCuenta.filter('spg_cuenta',valor,true,false);
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
				dsSpgCuenta.filter('denominacion',valor,true,false);
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
	   				'oper'   : operacion,
   					'codcue' : Ext.getCmp('codcue').getValue(),
   					'dencue' : Ext.getCmp('dencue').getValue()
   				}
	   				
			   	var ObjSon = JSON.stringify(JSONObject);
   				var parametros = 'ObjSon='+ObjSon; 
   				Ext.Ajax.request({
   					url : '../../controlador/cfg/sigesp_ctr_cfg_cuentas_spg.php',
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
								dsSpgCuenta.loadData(objData);
							}
   						}
   					}//fin del success	
   				});//fin del ajax request
		   	}
		}]
	});
	
	var gridSpgCuenta = new Ext.grid.GridPanel({
		width:770,
	 	height:370,
	 	tbar: formbusquedaspg,
	 	autoScroll:true,
     	border:true,
     	ds: dsSpgCuenta,
     	cm: new Ext.grid.ColumnModel([
     		{header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'spg_cuenta'},
          	{header: "Denominaci&#243;n", width: 70, sortable: true, dataIndex: 'denominacion'}
       	]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}
	});
	
	gridSpgCuenta.on({
		'celldblclick': {
			fn: function(){
				var registro = gridSpgCuenta.getSelectionModel().getSelected();
            	Ext.getCmp('spg_cuenta').setValue(registro.get('spg_cuenta'));
				Ext.getCmp('denominacionspg').setValue(registro.get('denominacion'));
				
				Actualizar=true;
                gridSpgCuenta.destroy();
		      	ventanaSpgCuenta.destroy();			
			}
		}
	});
	
    var ventanaSpgCuenta = new Ext.Window({
    	title: 'Cat&#225;logo cuentas presupuestarias',
		autoScroll:true,
        width:800,
        height:450,
        modal: true,
        closable:false,
        plain: false,
        items:[gridSpgCuenta],
        buttons: [{
        	text:'Aceptar',  
            handler: function() {
            	var registro = gridSpgCuenta.getSelectionModel().getSelected();
            	Ext.getCmp('spg_cuenta').setValue(registro.get('spg_cuenta'));
				Ext.getCmp('denominacionspg').setValue(registro.get('denominacion'));
				
				Actualizar=true;
          		gridSpgCuenta.destroy();
		      	ventanaSpgCuenta.destroy();                      
			}
		},
		{
        	text: 'Salir',
            handler: function() {
            	gridSpgCuenta.destroy();
		      	ventanaSpgCuenta.destroy();
			}
		}]
	});
    ventanaSpgCuenta.show();       
}