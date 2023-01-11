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

var dscuentabanco="";
var formbusquedacuentabanco="";
var gridcuentabanco="";
var ventanacuentabanco="";

function crearDsCuentaBanco()
{

	var registrocuentabanco = Ext.data.Record.create([
							{name: 'ctaban'},    
							{name: 'dencta'},
							{name: 'codban'},
							{name: 'nomban'},
							{name: 'codtipcta'},
							{name: 'nomtipcta'},
							{name: 'sc_cuenta'},
							{name: 'denominacion'},
							{name: 'ctabanext'},
							{name: 'fecapr'},
							{name: 'feccie'},
							{name: 'estact'},
							{name: 'codmon'},
							{name: 'denmon'}
							
						]);
	
	var objcuentabanco={"raiz":[{"ctaban":'',"dencta":''}]};
		
		dscuentabanco =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objcuentabanco),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registrocuentabanco  
			),
			data: objcuentabanco
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scb_cuentabanco.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objcuentabanco = eval('(' + datos + ')');
			if(objcuentabanco!='')
			{
				dscuentabanco.loadData(objcuentabanco);
			}
		}	
	})
}

function crearDataStoreCuentaFiltroBanco(codban)
{

	var registrocuentabanco = Ext.data.Record.create([
							{name: 'ctaban'},    
							{name: 'dencta'},
							{name: 'codban'},
							{name: 'nomban'},
							{name: 'codtipcta'},
							{name: 'nomtipcta'},
							{name: 'sc_cuenta'},
							{name: 'denominacion'},
							{name: 'ctabanext'},
							{name: 'fecapr'},
							{name: 'feccie'},
							{name: 'estact'},
							{name: 'codmon'},
							{name: 'denmon'}
							
						]);
	
	var objcuentabanco={"raiz":[{"codban":'',"nomban":'',"ctaban":'',"dencta":'',"codtipcta":'',"nomtipcta":''}]};
		
		dscuentabanco =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objcuentabanco),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registrocuentabanco  
			),
			data: objcuentabanco
	  	})	
		
		var JSONObject ={
			"oper": 'catalogofiltrobanco',
			"codban":codban
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scb_cuentabanco.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objcuentabanco = eval('(' + datos + ')');
			if(objcuentabanco!='')
			{
				dscuentabanco.loadData(objcuentabanco);
			}
		}	
	})
}



function actDsCuentaBanco(criterio,cadena)
{
	dscuentabanco.filter(criterio,cadena,true,false);
}


function crearFormBusquedaCuentaBanco()
{
		formbusquedacuentabanco = new Ext.FormPanel({
        labelWidth: 100, // label settings here cascade unless overridden
        frame:true,
        title: 'B&#250;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 600,
		height:130,
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'cod',
				id:'cod',
				width: 100,
				changeCheck: function(){
							var v = this.getValue();
							actDsCuentaBanco('ctaban',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							},							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}               
      			},{
			    fieldLabel: 'Denominaci&#243;n',
			    name: 'den',
			    id:'den',
			    width: 350,
				changeCheck: function(){
							var v = this.getValue();
							actDsCuentaBanco('dencta',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							},							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}
			    },{
			    fieldLabel: 'Banco',
			    name: 'banco',
			    id:'banco',
			    width: 200,
				changeCheck: function(){
							var v = this.getValue();
							actDsCuentaBanco('nomban',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							},							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}
			        }
			      ]
					});				  

}


function crearGridCuentaBanco()
{
	crearFormBusquedaCuentaBanco();
	crearDsCuentaBanco();
		 
	 gridcuentabanco = new Ext.grid.GridPanel({
	 width:915,
	 height:350,
	 tbar: formbusquedacuentabanco,
	 autoScroll:true,
     border:true,
     ds: dscuentabanco,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 120, sortable: true,   dataIndex: 'ctaban'},
          {header: "Denominaci&#243;n", width: 100, sortable: true, dataIndex: 'dencta'},
          {header: "Banco", width: 100, sortable: true, dataIndex: 'nomban'},
          {header: "Tipo de cuenta", width: 80, sortable: true, dataIndex: 'nomtipcta'},
          {header: "Cuenta contable", width: 80, sortable: true, dataIndex: 'sc_cuenta'},
          {header: "Denominaci&#243;n cuenta contable", width: 130, sortable: true, dataIndex: 'denominacion'},
          {header: "Apertura", width: 60, sortable: true, dataIndex: 'fecapr'},
          {header: "Cierre", width: 60, sortable: true, dataIndex: 'feccie'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
}

function crearGridCuentaBancoFiltro(codban, nomban,codctaban,denctaban,codtipcta,dentipcta)
{
	crearFormBusquedaCuentaBanco();
	crearDataStoreCuentaFiltroBanco(codban.getValue());
		 
	 gridcuentabanco = new Ext.grid.GridPanel({
	 width:915,
	 height:450,
	 tbar: formbusquedacuentabanco,
	 autoScroll:true,
     border:true,
     ds: dscuentabanco,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 120, sortable: true,   dataIndex: 'ctaban'},
          {header: "Denominaci&#243;n", width: 100, sortable: true, dataIndex: 'dencta'},
          {header: "Banco", width: 100, sortable: true, dataIndex: 'nomban'},
          {header: "Tipo de cuenta", width: 80, sortable: true, dataIndex: 'nomtipcta'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      },
      listeners:{ 'celldblclick' : function(grid,fila,columna,evento)
    	  							{
    	  								pasarDatosCuentaBancoFiltro(grid.getSelectionModel().getSelected(),codban, nomban,codctaban,denctaban,codtipcta,dentipcta);
    	  								grid.destroy();
    	  								ventanacuentabanco.destroy();
    	  							}
    	  
      }
      });            
}

function pasarDatosGridCuentaBanco(registro)
{
	
	Ext.getCmp('ctaban').setValue(registro.get('ctaban'));
	Ext.getCmp('dencta').setValue(registro.get('dencta'));
	Ext.getCmp('ctabanext').setValue(registro.get('ctabanext'));
	Ext.getCmp('codtipcta').setValue(registro.get('codtipcta'));
	Ext.getCmp('nomtipcta').setValue(registro.get('nomtipcta'));
	Ext.getCmp('codban').setValue(registro.get('codban'));
	Ext.getCmp('nomban').setValue(registro.get('nomban'));
	Ext.getCmp('codban').setValue(registro.get('codban'));
	Ext.getCmp('sc_cuenta').setValue(registro.get('sc_cuenta'));
	Ext.getCmp('denctascg').setValue(registro.get('denominacion'));
	Ext.getCmp('fecapr').setValue(registro.get('fecapr'));
	Ext.getCmp('codmon').setValue(registro.get('codmon'));
	Ext.getCmp('denmon').setValue(registro.get('denmon'));
        
	if(registro.get('feccie')=='01/01/1900'){
		Ext.getCmp('feccie').setValue('');	
	}
	else{
		Ext.getCmp('feccie').setValue(registro.get('feccie'));
	}
	Ext.getCmp('estact').setValue(registro.get('estact'));
		
	Actualizar=true;			
}

function pasarDatosCuentaBanco(registro,objcodigo,objnombre)
{
 objcodigo.setValue(registro.get('ctaban'));
 if(objnombre != null)
 {
	 switch(objnombre.getXtype())
	 {
		 case 'label': objnombre.setText(registro.get('dencta'));
		 break;
		 
		 case 'textfield': objnombre.setValue(registro.get('dencta'));
		 break;
	 }
 }

}

function pasarDatosCuentaBancoFiltro(registro,codban, nomban,codctaban,denctaban,codtipcta,dentipcta)
{
	
	if(codban != null)
	{
		switch(codban.getXType())
		{
			case 'textfield' : codban.setValue(registro.get('codban'));
			break;
			
			case 'label' : codban.setText(registro.get('codban'));
			break;
		}
	}
	
	if(nomban != null)
	{
		switch(nomban.getXType())
		{
			case 'textfield' : nomban.setValue(registro.get('nomban'));
			break;
			
			case 'label' : nomban.setText(registro.get('nomban'));
			break;
		}
	}
	
	if(codctaban != null)
	{
		switch(codctaban.getXType())
		{
			case 'textfield' : codctaban.setValue(registro.get('ctaban'));
			break;
			
			case 'label' : codctaban.setText(registro.get('ctaban'));
			break;
		}
	}
	
	if(denctaban != null)
	{
		switch(denctaban.getXType())
		{
			case 'textfield' : denctaban.setValue(registro.get('dencta'));
			break;
			
			case 'label' : denctaban.setText(registro.get('dencta'));
			break;
		}
	}
	
	if(codtipcta != null)
	{
		switch(codtipcta.getXType())
		{
			case 'textfield' : codtipcta.setValue(registro.get('codtipcta'));
			break;
			
			case 'label' : codtipcta.setText(registro.get('codtipcta'));
			break;
		}
	}
	
	if(dentipcta != null)
	{
		switch(dentipcta.getXType())
		{
			case 'textfield' : dentipcta.setValue(registro.get('nomtipcta'));
			break;
			
			case 'label' : dentipcta.setText(registro.get('nomtipcta'));
			break;
		}
	}
}

function mostrarCatalogoCuentaBanco()
{
				   crearGridCuentaBanco();
				   ObjetoFuente='definicion';
                   ventanacuentabanco = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo de Cuentas Bancarias',
		    		autoScroll:true,
                    width:950,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridcuentabanco],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridcuentabanco.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			//limpiarCampos();
	                    		pasarDatosGridCuentaBanco(registro);
	                    	break;
                    		case 'grid':
		                    	pasarDatosGridCuentaBanco(registro);
	                    	break;
                    		case 'objeto':
	                    		pasarDatosGridCuentaBanco(registro);
	                    	break;
 
                    		
                    	}          
                    	gridcuentabanco.destroy();
		      			ventanacuentabanco.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridcuentabanco.destroy();
		      			ventanacuentabanco.destroy();
                     }
                    }]
                    
                   });
                  ventanacuentabanco.show();       
 

 }

function mostrarCatalogoCuentaBancoFiltro(codban, nomban,codctaban,denctaban,codtipcta,dentipcta)
{
				   crearGridCuentaBancoFiltro(codban, nomban,codctaban,denctaban,codtipcta,dentipcta);
                   ventanacuentabanco = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Cuentas Bancarias',
		    		autoScroll:true,
                    width:950,
                    height:525,
                    resizable:false,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridcuentabanco],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridcuentabanco.getSelectionModel().getSelected();
                    	pasarDatosCuentaBancoFiltro(registro,codban, nomban,codctaban,denctaban,codtipcta,dentipcta);       
                    	gridcuentabanco.destroy();
		      			ventanacuentabanco.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridcuentabanco.destroy();
		      			ventanacuentabanco.destroy();
                     }
                    }]
                    
                   });
                  ventanacuentabanco.show();       
 

 }