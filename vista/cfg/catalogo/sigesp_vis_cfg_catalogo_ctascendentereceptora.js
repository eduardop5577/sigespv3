/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* al catalogo de cuentas presupuestarias cedentes-receptoras  
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

function mostrarCatalogoCtascedentesreceptoras(tipocta){
	
	var reCtacedentereceptora = Ext.data.Record.create([
		{name: 'codcuenta'},    
		{name: 'denominacion'}
	]);
	
	var dsCtacedentereceptora =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reCtacedentereceptora)
	});	
	
	var myJSONObject = {
		"operacion": 'catalogo'
	}
	
	var ObjSon=JSON.stringify(myJSONObject);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_catctacedentereceptora.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) {
			var datos = resultado.responseText;
			var dataobjeto = eval('(' + datos + ')');
			if(dataobjeto != '') {
				if(dataobjeto.raiz == null || dataobjeto.raiz ==''){
					Ext.MessageBox.show({
 						title:'Advertencia',
 						msg: 'No existen datos para mostrar',
 						buttons: Ext.Msg.OK,
 						icon: Ext.MessageBox.WARNING
 					});
				}
				else{
					dsCtacedentereceptora.loadData(dataobjeto);
				}
			}
		}	
	});
	
	var formBusquedaCtacedentereceptora = new Ext.FormPanel({
        labelWidth: 100, // label settings here cascade unless overridden
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 500,
		height: 100,
        defaultType: 'textfield',
		items: [{
			fieldLabel: 'C&#243;digo',
            id:'codcta',
            labelSeparator: '',
            width: 60,
            autoCreate: {tag: 'input', type: 'text', maxlength: 3},
			changeCheck: function(){
				var v = this.getValue();
				dsCtacedentereceptora.filter('codcuenta',v);
				if(String(v) !== String(this.startValue))
				{
					this.fireEvent('change', this, v, this.startValue);
				} 
			},							 
			initEvents : function(){
				AgregarKeyPress(this);
			}               
      	},{
      		fieldLabel: 'Denominaci&#243;n',
            id:'ctaden',
            labelSeparator: '',
            width: 300,
            autoCreate: {tag: 'input', type: 'text', maxlength: 254},
			changeCheck: function(){
				var v = this.getValue();
				dsCtacedentereceptora.filter('denominacion',v,true,false);
				if(String(v) !== String(this.startValue)){
					this.fireEvent('change', this, v, this.startValue);
				} 
			},							 
			initEvents : function(){
				AgregarKeyPress(this);
			}               
		}]
	});				  

	var gridCtacedentereceptora = new Ext.grid.GridPanel({
		width:500,
	 	height:370,
	 	tbar: formBusquedaCtacedentereceptora,
	 	autoScroll:true,
     	border:true,
     	ds: dsCtacedentereceptora,
     	cm: new Ext.grid.ColumnModel([
          	{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codcuenta'},
          	{header: "Descripcion", width: 80, sortable: true, dataIndex: 'denominacion'}
        ]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}
	});
	
	gridCtacedentereceptora.on({
		'celldblclick': {
			fn: function(){
				var ctascedente=Ext.getCmp('ctaspgced').getValue();
				var ctasreceptora=Ext.getCmp('ctaspgrec').getValue();
				if(tipocta=='C'){
					var registro=gridCtacedentereceptora.getSelectionModel().getSelected();
					if(ctascedente==''&&ctascedente!=','){
						//validando si la cuenta es receptora
						var arrcuentarec = ctasreceptora.split(",");
						for (var i = arrcuentarec.length - 1; i >= 0; i--){
							if(arrcuentarec[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas receptoras');
								return false;
							}
						}
						Ext.getCmp('ctaspgced').setValue(registro.get('codcuenta'));
					}else{
						//validando si la cuenta es receptora
						var arrcuentarec = ctasreceptora.split(",");
						for (var i = arrcuentarec.length - 1; i >= 0; i--){
							if(arrcuentarec[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas receptoras');
								return false;
							}
						}
						//validando si la cuenta ya es cedente
						var arrcuentaced = ctascedente.split(",");
						for (var i = arrcuentaced.length - 1; i >= 0; i--){
							if(arrcuentaced[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas cedentes');
								return false;
							}
						}
						ctascedente=ctascedente+","+registro.get('codcuenta');
						Ext.getCmp('ctaspgced').setValue(ctascedente);
					}
				}
				else if(tipocta=='R'){
					var registro=gridCtacedentereceptora.getSelectionModel().getSelected();
					if(ctasreceptora==''){
						//validando si la cuenta es cedente
						var arrcuentaced = ctascedente.split(",");
						for (var i = arrcuentaced.length - 1; i >= 0; i--){
							if(arrcuentaced[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas cedentes');
								return false;
							}
						}
						Ext.getCmp('ctaspgrec').setValue(registro.get('codcuenta'));
					}else{
						//validando si la cuenta es cedente
						var arrcuentaced = ctascedente.split(",");
						for (var i = arrcuentaced.length - 1; i >= 0; i--){
							if(arrcuentaced[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas cedentes');
								return false;
							}
						}
						
						//validando si la cuenta ya es receptora
						var arrcuentarec = ctasreceptora.split(",");
						for (var i = arrcuentarec.length - 1; i >= 0; i--){
							if(arrcuentarec[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas receptoras');
								return false;
							}
						}
						
						ctasreceptora=ctasreceptora+","+registro.get('codcuenta');
						Ext.getCmp('ctaspgrec').setValue(ctasreceptora);
					}
				}
				gridCtacedentereceptora.destroy();
				ventanaCatCtacedentereceptora.destroy();			
			}
		}
	});
	
	var ventanaCatCtacedentereceptora = new Ext.Window({
		title: 'Cat&#225;logo de Cuentas Presupuestarias Cedentes y Receptoras',
		autoScroll:true,
        width:600,
        height:460,
        modal: true,
        closable:false,
        plain: false,
        items:[gridCtacedentereceptora],
        buttons: [{
			text:'Aceptar',
			handler: function(){
				var ctascedente=Ext.getCmp('ctaspgced').getValue();
				var ctasreceptora=Ext.getCmp('ctaspgrec').getValue();
				if(tipocta=='C'){
					var registro=gridCtacedentereceptora.getSelectionModel().getSelected();
					if(ctascedente==''&&ctascedente!=','){
						//validando si la cuenta es receptora
						var arrcuentarec = ctasreceptora.split(",");
						for (var i = arrcuentarec.length - 1; i >= 0; i--){
							if(arrcuentarec[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas receptoras');
								return false;
							}
						}
						Ext.getCmp('ctaspgced').setValue(registro.get('codcuenta'));
					}else{
						//validando si la cuenta es receptora
						var arrcuentarec = ctasreceptora.split(",");
						for (var i = arrcuentarec.length - 1; i >= 0; i--){
							if(arrcuentarec[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas receptoras');
								return false;
							}
						}
						//validando si la cuenta ya es cedente
						var arrcuentaced = ctascedente.split(",");
						for (var i = arrcuentaced.length - 1; i >= 0; i--){
							if(arrcuentaced[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas cedentes');
								return false;
							}
						}
						ctascedente=ctascedente+","+registro.get('codcuenta');
						Ext.getCmp('ctaspgced').setValue(ctascedente);
					}
				}
				else if(tipocta=='R'){
					var registro=gridCtacedentereceptora.getSelectionModel().getSelected();
					if(ctasreceptora==''){
						//validando si la cuenta es cedente
						var arrcuentaced = ctascedente.split(",");
						for (var i = arrcuentaced.length - 1; i >= 0; i--){
							if(arrcuentaced[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas cedentes');
								return false;
							}
						}
						Ext.getCmp('ctaspgrec').setValue(registro.get('codcuenta'));
					}else{
						//validando si la cuenta es cedente
						var arrcuentaced = ctascedente.split(",");
						for (var i = arrcuentaced.length - 1; i >= 0; i--){
							if(arrcuentaced[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas cedentes');
								return false;
							}
						}
						
						//validando si la cuenta ya es receptora
						var arrcuentarec = ctasreceptora.split(",");
						for (var i = arrcuentarec.length - 1; i >= 0; i--){
							if(arrcuentarec[i]==registro.get('codcuenta')){
								Ext.MessageBox.alert('Advertencia','El grupo '+registro.get('codcuenta')+' fue asignado como cuentas receptoras');
								return false;
							}
						}
						
						ctasreceptora=ctasreceptora+","+registro.get('codcuenta');
						Ext.getCmp('ctaspgrec').setValue(ctasreceptora);
					}
				}
				gridCtacedentereceptora.destroy();
				ventanaCatCtacedentereceptora.destroy();
			}
		},
		{
			text: 'Salir',
         	handler: function(){
         		gridCtacedentereceptora.destroy();
				ventanaCatCtacedentereceptora.destroy();
         	}
	 	}]
	});
    ventanaCatCtacedentereceptora.show();       
 }