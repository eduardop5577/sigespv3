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

var dsConcepto="";
var formBusquedaConcepto="";
var gridConcepto="";

function crearDataStoreConcepto()
{

	registroConcepto = Ext.data.Record.create([
							{name: 'codconsep'},    
							{name: 'denconsep'},
							{name: 'monconsepe'},
							{name: 'spg_cuenta'},
							{name: 'denominacion'},
							{name: 'obsconesp'}						
						]);
	
	var objetoConcepto={"raiz":[{"codconsep":'',"denconsep":'',"monconsepe":'',"spg_cuenta":'',"denominacion":'',"obsconesp":''}]};
		
		dsConcepto =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoConcepto),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroConcepto 
			),
			data: objetoConcepto
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_sep_concepto.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoConcepto = eval('(' + datos + ')');
			if(objetoConcepto!='')
			{
				dsConcepto.loadData(objetoConcepto);
			}
		}	
	})
}


function actDataStoreConcepto(criterio,cadena)
{
	dsConcepto.filter(criterio,cadena,true,false);
}


function crearFormBusqueda()
{
		formBusquedaConcepto = new Ext.FormPanel({
        labelWidth: 90, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 400,
		height:100,
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'concepto',
				id:'conce',
				width:100,
				changeCheck: function(){
							var v = this.getValue();
							actDataStoreConcepto('codconsep',v);
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
			    name: 'descripcion concepto',
			    id:'den',
			    width:230,
				changeCheck: function(){
							var v = this.getValue();
							actDataStoreConcepto('denconsep',v);
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

function crearGridConcepto()
{
	crearFormBusqueda();
	crearDataStoreConcepto();
		 
	 gridConcepto = new Ext.grid.GridPanel({
	 width:770,
	 height:300,
	 tbar: formBusquedaConcepto,
	 autoScroll:true,
     border:true,
     ds: dsConcepto,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codconsep'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denconsep'},
          {header: "Monto", width: 30, sortable: true,   dataIndex: 'monconsepe'},
          {header: "Cuenta", width: 30, sortable: true,   dataIndex:'spg_cuenta'}
        ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
} 

function pasarDatosGridConcepto(registro)
{
	Ext.getCmp('codconsep').setValue(registro.get('codconsep'));
	Ext.getCmp('denconsep').setValue(registro.get('denconsep'));
	Ext.getCmp('monconsepe').setValue(registro.get('monconsepe'));
	Ext.getCmp('spg_cuenta').setValue(registro.get('spg_cuenta'));
	Ext.getCmp('denominacionspg').setValue(registro.get('denominacion'));
	Ext.getCmp('obsconesp').setValue(registro.get('obsconesp'));	
	Actualizar=true;			
}


function mostrar_catalogo()
{
	crearGridConcepto();
	ObjetoFuente='definicion';
    ventanacatConcepto = new Ext.Window(
    	{
        title: 'Cat&#225;logo de conceptos',
		autoScroll:true,
        width:800,
        height:450,
        modal: true,
        closable:false,
        plain: false,
        items:[gridConcepto],
        buttons: [{
        			text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridConcepto.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			pasarDatosGridConcepto(registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(registro);
	                    	break;
	                    }          
                    	gridConcepto.destroy();
		      			ventanacatConcepto.destroy();                      
              		}
                   }
                   ,
                   {
                   text: 'Salir',
                   handler: function()
                   {  	
                      	gridConcepto.destroy();
		      			ventanacatConcepto.destroy();
                   }
                  }]
		});
        ventanacatConcepto.show();       
}

function buscarCargosConcepto(codconsep)
{
	var myJSONObject ={
			'oper': 'buscardetalle',
			'codconsep':codconsep				
	};
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_sep_concepto.php',
		params : parametros,
		method: 'POST',
		success: function (resultado,request)
		{
			datos = resultado.responseText;
			if (datos!='')
			{
				var objetoDetalle = eval('(' + datos + ')');
				if(objetoDetalle != '')
				{
					gridDetalles.store.loadData(objetoDetalle);
				}
				else
				{
					Ext.MessageBox.alert('Error', datos.raiz[0].mensaje+' Al cargar los detalles.');
				}
			}
		}
	});
}

function catalogoConcepto(){
	crearGridConcepto();
	var ventanaConcepto = new Ext.Window({
                    title: 'Cat&#225;logo de Conceptos',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridConcepto],
                    buttons: [{
                    			text:'Aceptar',  
                    			handler: function(){ 
                    				var registro = gridConcepto.getSelectionModel().getSelected();
                    				PasDatosGridDef(registro);
                    				buscarCargosConcepto(registro.get('codconsep'));
                    				gridConcepto.destroy();
                    				ventanaConcepto.destroy();                      
                    			}
                    		  },
                    		  {
                    			text: 'Salir',
                    			handler: function(){
                    				gridConcepto.destroy();
                    				ventanaConcepto.destroy();
                    			}
                    }]
    });
    ventanaConcepto.show();
}