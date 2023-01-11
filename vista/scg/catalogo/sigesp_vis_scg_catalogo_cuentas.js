/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var dataStoreScgCuentas=null;
var gridScgCuentas=null;
var formBusquedaScgCuentas=null
var registroScgCuenta= null;
var ventanaCatalogoScgCuentas = null;

function crearDataStoreScgCuenta(operacion,cuentas)
{
	registroScgCuenta = Ext.data.Record.create([
						{name: 'sc_cuenta'},    
						{name: 'denominacion'}
					]);
	
	var objetoScgCuenta={"raiz":[{"sc_cuenta":'',"denominacion":''}]};
	dataStoreScgCuentas =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoScgCuenta),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "sc_cuenta"   
			}
			,
		    registroScgCuenta  
			),
			data: objetoScgCuenta
	  	});	
                if (cuentas == "")
                {
                    var myJSONObject ={
                    "operacion": operacion
                    }                    
                }
                else
                {
                    var myJSONObject ={
                    "operacion": operacion,
                    "cuentas": cuentas
                    }                                        
                }
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/scg/sigesp_ctr_scg_cuentacontable.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			var respuesta = null;
			respuesta = eval('(' + resultado.responseText + ')');
			if(respuesta!='')
			{
				dataStoreScgCuentas.loadData(respuesta);
			}
		}	
	})
}


function crearFormBusquedaScgCuentas() {
	
	formBusquedaScgCuentas = new Ext.FormPanel({
        labelWidth: 80,
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 650,
		height:100,
        defaults: {width: 230, labelSeparator:''},
        defaultType: 'textfield',
		items: [{
			fieldLabel: 'C&#243;digo',
            name: 'cuenta',
			id:'cuenta',
			autoCreate: {tag: 'input', type: 'text', maxlength: 25},
			changeCheck: function(){
				var v = this.getValue();
				dataStoreScgCuentas.filter('sc_cuenta',v);
			},							 
			initEvents : function() {
				AgregarKeyPress(this);
			}               
      	},{
      		fieldLabel: 'Denominaci&#243;n',
			name: 'den',
			id:'den',
			width:500,
			autoCreate: {tag: 'input', type: 'text', maxlength: 254},
			changeCheck: function() {
				var v = this.getValue();
				dataStoreScgCuentas.filter('denominacion',v,true,false);
			},							 
			initEvents : function() {
				AgregarKeyPress(this);
			}
		}]
	});				  			  

}


function CrearGridScgCuentas(operacion,cuentas)
{
	 crearFormBusquedaScgCuentas();
	 crearDataStoreScgCuenta(operacion,cuentas);
	 //var mensajeEspera = new Ext.LoadMask(Ext.getBody(), {msg:"Cargando Cuentas...",store: dataStoreScgCuentas});
	 gridScgCuentas = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaScgCuentas,
	 autoScroll:true,
     border:true,
     //loadMask: mensajeEspera,
     ds: dataStoreScgCuentas,
     cm: new Ext.grid.ColumnModel([
          {header: "Cuenta", width: 30, sortable: true,   dataIndex: 'sc_cuenta'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denominacion'}
       ]),
       stripeRows: true,
      viewConfig: {
      	forceFit:true
      }
      });            
}

function pasarDatosCuenta(Registro,codcuenta,descuenta)
{
	for(i=0;i<Campos.length;i++)
	{
		if(Registro.get('sc_cuenta')!='' && Registro.get('sc_cuenta'))
		{
			valor = Registro.get('sc_cuenta');
			valor = valor.replace('|@@@|','+');
			cadena='';
			for(j=0;j<valor.length;j++)
			{
				letra = valor.substr(j,1);
				if(letra=='|')
				{
					letra = unescape('%0A');
				}
				cadena=cadena+letra;	
			}
			codcuenta.setValue(cadena);
		}
		
		if(descuenta != null)
		{
			if(Registro.get('denominacion')!='' && Registro.get('denominacion'))
			{
				valor = Registro.get('denominacion');
				valor = valor.replace('|@@@|','+');
				cadena='';
				for(j=0;j<valor.length;j++)
				{
					letra = valor.substr(j,1);
					if(letra=='|')
					{
						letra = unescape('%0A');
					}
					cadena=cadena+letra;	
				}
				if(descuenta.isXType('label'))
				{
					descuenta.setText(cadena);
				}
				else
				{
					descuenta.setValue(cadena);
				}
				
			}
		}
	}
}


function mostrarCatalogoCuentaContable(operacion,codcuenta,dencuenta)
{
				   CrearGridScgCuentas(operacion,"");
                   ventanaCatalogoScgCuentas = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de cuentas contables',
		    		autoScroll:true,
                    width:825,
                    height:485,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridScgCuentas],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function(){ 
					                    Registro = gridScgCuentas.getSelectionModel().getSelected();
					                    pasarDatosCuenta(Registro,codcuenta,dencuenta);   		
					                    gridScgCuentas.destroy();
					                    ventanaCatalogoScgCuentas.destroy();
                    				   }
                    
                    },
                   {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridScgCuentas.destroy();
		      			ventanaCatalogoScgCuentas.destroy();
                     }
                   }]
                    
                   });
                  ventanaCatalogoScgCuentas.show();       
 }

function pasarDatosGridCasamientoContable(registro)
{
	registro.set('sc_cuenta',gridScgCuentas.getSelectionModel().getSelected().get('sc_cuenta'));
}

function pasarDatosGridCasamientoContableClasificador(registro)
{
	registro.set('cueclaeco',gridScgCuentas.getSelectionModel().getSelected().get('sc_cuenta'));
}

function pasarDatosGridCasamientoContableOncop(registro)
{
	registro.set('cueoncop',gridScgCuentas.getSelectionModel().getSelected().get('sc_cuenta'));
}

function mostrarCatalogoCuentaContableCasamiento(operacion,registro)
{
	
	
    CrearGridScgCuentas(operacion,"");
    ventanaCatalogoScgCuentas = new Ext.Window(
    {
     title: 'Cat&#225;logo de cuentas contables',
	 autoScroll:true,
	 width:810,
     height:475,
     modal: true,
     closable:false,
     plain: false,
     items:[gridScgCuentas],
     buttons: [{
     text:'Aceptar',  
     handler: function(){ 
				    		if(Ext.type(registro) == 'array')
				    		{
				    			for(var i = 0; i < registro.length; i++) {
				    				pasarDatosGridCasamientoContable(registro[i]);
								}
				    		}
				    		else
				    		{
    	                     pasarDatosGridCasamientoContable(registro);
				    		}
				    		gridScgCuentas.destroy();
		                    ventanaCatalogoScgCuentas.destroy();
     				    }
     
     },
    {
      text: 'Salir',
      handler: function()
      {
      	
       	gridScgCuentas.destroy();
			ventanaCatalogoScgCuentas.destroy();
      }
    }]
     
    });
   ventanaCatalogoScgCuentas.show(); 
 	
}

function mostrarCatalogoCuentaContableClasificador(operacion,registro)
{
	
	
    CrearGridScgCuentas(operacion,"");
    ventanaCatalogoScgCuentas = new Ext.Window(
    {
     title: 'Cat&#225;logo de cuentas Clasificador Economico',
	 autoScroll:true,
	 width:810,
     height:475,
     modal: true,
     closable:false,
     plain: false,
     items:[gridScgCuentas],
     buttons: [{
     text:'Aceptar',  
     handler: function(){ 
				    		if(Ext.type(registro) == 'array')
				    		{
				    			for(var i = 0; i < registro.length; i++) {
				    				pasarDatosGridCasamientoContableClasificador(registro[i]);
								}
				    		}
				    		else
				    		{
    	                     pasarDatosGridCasamientoContableClasificador(registro);
				    		}
				    		gridScgCuentas.destroy();
		                    ventanaCatalogoScgCuentas.destroy();
     				    }
     
     },
    {
      text: 'Salir',
      handler: function()
      {
      	
       	gridScgCuentas.destroy();
			ventanaCatalogoScgCuentas.destroy();
      }
    }]
     
    });
   ventanaCatalogoScgCuentas.show(); 
 	
}

function mostrarCatalogoCuentaContableOncop(operacion,registro)
{
	
	
    CrearGridScgCuentas(operacion,"");
    ventanaCatalogoScgCuentas = new Ext.Window(
    {
     title: 'Cat&#225;logo de Cuentas Oncop',
	 autoScroll:true,
	 width:810,
     height:475,
     modal: true,
     closable:false,
     plain: false,
     items:[gridScgCuentas],
     buttons: [{
     text:'Aceptar',  
     handler: function(){ 
				    		if(Ext.type(registro) == 'array')
				    		{
				    			for(var i = 0; i < registro.length; i++) {
				    				pasarDatosGridCasamientoContableOncop(registro[i]);
								}
				    		}
				    		else
				    		{
    	                     pasarDatosGridCasamientoContableOncop(registro);
				    		}
				    		gridScgCuentas.destroy();
		                    ventanaCatalogoScgCuentas.destroy();
     				    }
     
     },
    {
      text: 'Salir',
      handler: function()
      {
      	
       	gridScgCuentas.destroy();
			ventanaCatalogoScgCuentas.destroy();
      }
    }]
     
    });
   ventanaCatalogoScgCuentas.show(); 
 	
}

function mostrarCatalogoCuentaContableCasamientoSPG(operacion,registro)
{
    var cuentas="";
    for(var i = 0; i < registro.length; i++)
    {
        if (i == 0)
        {
            cuentas = cuentas+registro[i].get('sig_cuenta');
        }
        else
        {
            cuentas = cuentas+","+registro[i].get('sig_cuenta');
        } 
    }	
	
    CrearGridScgCuentas(operacion,cuentas);
    ventanaCatalogoScgCuentas = new Ext.Window(
    {
     title: 'Cat&#225;logo de cuentas contables',
	 autoScroll:true,
	 width:810,
     height:475,
     modal: true,
     closable:false,
     plain: false,
     items:[gridScgCuentas],
     buttons: [{
     text:'Aceptar',  
     handler: function(){ 
				    		if(Ext.type(registro) == 'array')
				    		{
				    			for(var i = 0; i < registro.length; i++) {
				    				pasarDatosGridCasamientoContable(registro[i]);
								}
				    		}
				    		else
				    		{
    	                     pasarDatosGridCasamientoContable(registro);
				    		}
				    		gridScgCuentas.destroy();
		                    ventanaCatalogoScgCuentas.destroy();
     				    }
     
     },
    {
      text: 'Salir',
      handler: function()
      {
      	
       	gridScgCuentas.destroy();
			ventanaCatalogoScgCuentas.destroy();
      }
    }]
     
    });
   ventanaCatalogoScgCuentas.show(); 
 	
}

function mostrarCatalogoCuentaContableCasamientoSPI(operacion,registro)
{
    var cuentas="";
    for(var i = 0; i < registro.length; i++)
    {
        if (i == 0)
        {
            cuentas = cuentas+registro[i].get('spi_cuenta');
        }
        else
        {
            cuentas = cuentas+","+registro[i].get('spi_cuenta');
        } 
    }	
	
    CrearGridScgCuentas(operacion,cuentas);
    ventanaCatalogoScgCuentas = new Ext.Window(
    {
     title: 'Cat&#225;logo de cuentas contables',
	 autoScroll:true,
	 width:810,
     height:475,
     modal: true,
     closable:false,
     plain: false,
     items:[gridScgCuentas],
     buttons: [{
     text:'Aceptar',  
     handler: function(){ 
				    		if(Ext.type(registro) == 'array')
				    		{
				    			for(var i = 0; i < registro.length; i++) {
				    				pasarDatosGridCasamientoContable(registro[i]);
								}
				    		}
				    		else
				    		{
    	                     pasarDatosGridCasamientoContable(registro);
				    		}
				    		gridScgCuentas.destroy();
		                    ventanaCatalogoScgCuentas.destroy();
     				    }
     
     },
    {
      text: 'Salir',
      handler: function()
      {
      	
       	gridScgCuentas.destroy();
			ventanaCatalogoScgCuentas.destroy();
      }
    }]
     
    });
   ventanaCatalogoScgCuentas.show(); 
 	
}

function mostrarCatalogoCuentaContableClasificadorSPG(operacion,registro)
{
    var cuentas="";
    for(var i = 0; i < registro.length; i++)
    {
        if (i == 0)
        {
            cuentas = cuentas+registro[i].get('sig_cuenta');
        }
        else
        {
            cuentas = cuentas+","+registro[i].get('sig_cuenta');
        } 
    }	
	
    CrearGridScgCuentas(operacion,cuentas);
    ventanaCatalogoScgCuentas = new Ext.Window(
    {
     title: 'Cat&#225;logo de cuentas Clasificador Economico',
	 autoScroll:true,
	 width:810,
     height:475,
     modal: true,
     closable:false,
     plain: false,
     items:[gridScgCuentas],
     buttons: [{
     text:'Aceptar',  
     handler: function(){ 
				    		if(Ext.type(registro) == 'array')
				    		{
				    			for(var i = 0; i < registro.length; i++) {
				    				pasarDatosGridCasamientoContableClasificador(registro[i]);
								}
				    		}
				    		else
				    		{
    	                     pasarDatosGridCasamientoContableClasificador(registro);
				    		}
				    		gridScgCuentas.destroy();
		                    ventanaCatalogoScgCuentas.destroy();
     				    }
     
     },
    {
      text: 'Salir',
      handler: function()
      {
      	
       	gridScgCuentas.destroy();
			ventanaCatalogoScgCuentas.destroy();
      }
    }]
     
    });
   ventanaCatalogoScgCuentas.show(); 
 	
}

function mostrarCatalogoCuentaContableClasificadorSPI(operacion,registro)
{
    var cuentas="";
    for(var i = 0; i < registro.length; i++)
    {
        if (i == 0)
        {
            cuentas = cuentas+registro[i].get('spi_cuenta');
        }
        else
        {
            cuentas = cuentas+","+registro[i].get('spi_cuenta');
        } 
    }	
	
    CrearGridScgCuentas(operacion,cuentas);
    ventanaCatalogoScgCuentas = new Ext.Window(
    {
     title: 'Cat&#225;logo de cuentas Clasificador Economico',
	 autoScroll:true,
	 width:810,
     height:475,
     modal: true,
     closable:false,
     plain: false,
     items:[gridScgCuentas],
     buttons: [{
     text:'Aceptar',  
     handler: function(){ 
				    		if(Ext.type(registro) == 'array')
				    		{
				    			for(var i = 0; i < registro.length; i++) {
				    				pasarDatosGridCasamientoContableClasificador(registro[i]);
								}
				    		}
				    		else
				    		{
    	                     pasarDatosGridCasamientoContableClasificador(registro);
				    		}
				    		gridScgCuentas.destroy();
		                    ventanaCatalogoScgCuentas.destroy();
     				    }
     
     },
    {
      text: 'Salir',
      handler: function()
      {
      	
       	gridScgCuentas.destroy();
			ventanaCatalogoScgCuentas.destroy();
      }
    }]
     
    });
   ventanaCatalogoScgCuentas.show(); 
 	
}
