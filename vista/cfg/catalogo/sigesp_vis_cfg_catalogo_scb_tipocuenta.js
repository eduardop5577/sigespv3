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

var dstipocuenta="";
var formbusquedatipocuenta="";
var gridtipocuenta="";
var ventanatipocuenta="";


function crearDstipocuenta()
{

	registrotipocuenta = Ext.data.Record.create([
							{name: 'codtipcta'},    
							{name: 'nomtipcta'}
						]);
	
	var objtipocuenta={"raiz":[{"codtipcta":'',"nomtipcta":''}]};
		
		dstipocuenta =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objtipocuenta),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registrotipocuenta  
			),
			data: objtipocuenta
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scb_tipocuenta.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objtipocuenta = eval('(' + datos + ')');
			if(objtipocuenta!='')
			{
				dstipocuenta.loadData(objtipocuenta);
			}
		}	
	})
}

function actDsTipoCuenta(criterio,cadena)
{
	dstipocuenta.filter(criterio,cadena,true,false);
}


function crearFormBusquedaTipoCuenta()
{
		formbusquedatipocuenta = new Ext.FormPanel({
        labelWidth: 100, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'B&#250;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 500,
		height:100,
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'cod',
				id:'cod',
				width: 50,
				changeCheck: function(){
							var v = this.getValue();
							actDsTipoCuenta('codtipcta',v);
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
							actDsTipoCuenta('nomtipcta',v);
							if(String(v) !== String(this.startValue))
							{
								this.fireEvent('change', this, v, this.startValue);
							} 
							},							 
							initEvents : function()
							{
								AgregarKeyPress(this);
							}
			            }]
					});				  

}


function crearGridTipoCuenta()
{
	crearFormBusquedaTipoCuenta();
	crearDstipocuenta();
		 
	 gridtipocuenta = new Ext.grid.GridPanel({
	 width:760,
	 height:350,
	 tbar: formbusquedatipocuenta,
	 autoScroll:true,
     border:true,
     ds: dstipocuenta,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codtipcta'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'nomtipcta'}
       ]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}      
      });            
}

function pasarDatosGridTipoCuenta(registro)
{
	
	Ext.getCmp('codtipcta').setValue(registro.get('codtipcta'));
	Ext.getCmp('nomtipcta').setValue(registro.get('nomtipcta'));
		
				
} 

function mostrar_catalogo()
{
				   crearGridTipoCuenta();
				   ObjetoFuente='definicion';
                   ventanatipocuenta = new Ext.Window(
                   {
                    title: 'Cat&#225;logo Tipo de Cuentas',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridtipocuenta],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridtipocuenta.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			PasDatosGridDef(registro);
                                Actualizar=true;        
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(registro);
	                    	break;
 
                    		
                    	}          
                    	gridtipocuenta.destroy();
		      			ventanatipocuenta.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridtipocuenta.destroy();
		      			ventanatipocuenta.destroy();
                     }
                    }]
                    
                   });
                  ventanatipocuenta.show();       
 

 }
 
 function catalogoTipoCuenta()
{
				   crearGridTipoCuenta();
				   ObjetoFuente='definicion';
                   ventanatipocuenta = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo Tipo de Cuentas',
		    		autoScroll:true,
                    width:800,
                    height:450,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridtipocuenta],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridtipocuenta.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
	                    		pasarDatosGridTipoCuenta(registro);
								Actualizar=true;
	                    	break;
                    		case 'grid':
		                    	pasarDatosGridTipoCuenta(registro);
	                    	break;
                    		case 'objeto':
	                    		pasarDatosGridTipoCuenta(registro);
	                    	break;
 
                    		
                    	}          
                    	gridtipocuenta.destroy();
		      			ventanatipocuenta.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridtipocuenta.destroy();
		      			ventanatipocuenta.destroy();
                     }
                    }]
                    
                   });
                  ventanatipocuenta.show();       
 

 }