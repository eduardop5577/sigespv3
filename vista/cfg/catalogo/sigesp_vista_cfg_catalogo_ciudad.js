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

var dataStoreCiudad=""; //datastore del catalogo
var formBusquedaCiudad=""; //formulario de busqueda del catalogo
var gridCiudad=""; //grid del catalogo
var ventanaCiudad=""; //ventana en donde se ubica el catalogo


function crearDataStoreCiudad()
{

	registroCiudad = Ext.data.Record.create([
							{name: 'codciu'},
							{name: 'desciu'}
						]);
	
	var objetoCiudad={"raiz":[{"codciu":'',"desciu":''}]};
		
		dataStoreCiudad =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoCiudad),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroCiudad  
			),
			data: objetoCiudad
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo',
			"codpai": codpai,
			"codest": codest
			
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_ciudad.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoCiudad = eval('(' + datos + ')');
			if(objetoCiudad.raiz == null)
			{
				obtenerMensaje('informacion',ventanaCiudad.destroy(),'Mensaje','No se encontro informacion, con los parametros de busqueda seleccionados')	
			}
			else
			{
			 if(objetoCiudad!='')
			 {
				dataStoreCiudad.loadData(objetoCiudad);
			 }
			}
		}	
	})
}

function actDataStoreCiudad(criterio,cadena)
{
	dataStoreCiudad.filter(criterio,cadena,true,false);
}


function crearFormBusqueda()
{
		formBusquedaCiudad = new Ext.FormPanel({
        labelWidth: 80,
        frame:true,
        title: 'B&#250;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'cod',
				id:'cod',
				width: 100,
				changeCheck: function(){
							var v = this.getValue();
							actDataStoreCiudad('codciu',v);
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
			    fieldLabel: 'Ciudad',
			    name: 'den',
			    id:'den',
				changeCheck: function()
							{
							var v = this.getValue();
							actDataStoreCiudad('desciu',v);
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

function crearGridCiudad()
{
	crearFormBusqueda();
	crearDataStoreCiudad();
		 
	 gridCiudad = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaCiudad,
	 autoScroll:true,
     border:true,
     ds: dataStoreCiudad,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codciu'},
          {header: "Ciudades", width: 30, sortable: true,   dataIndex: 'desciu'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
}

function pasarDatosGridCiudad(ciudad)
{
	Ext.getCmp('codciu').setValue(ciudad.get('codciu'));
	Ext.getCmp('desciu').setValue(ciudad.get('desciu'));
	Ext.getCmp('codpai').disable();
	Ext.getCmp('codest').disable();
	Ext.getCmp('codciu').disable();
	Actualizar=true;
}

function irBuscar()
{
	if((codpai==null)||(codpai=='')||(codest==null)||(codest==''))
	{
		Ext.Msg.show({
		title:'Mensaje',
		msg: 'Debe seleccionar un pais y un estado, verifique por favor!!',
		buttons: Ext.Msg.OK,
		animEl: 'elId',
		icon: Ext.MessageBox.INFO
		});
	}
	else
	{
	   crearGridCiudad();
	   ObjetoFuente='definicion';
	   ventanaCiudad = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo de ciudades',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridCiudad],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	ciudad = gridCiudad.getSelectionModel().getSelected();
                    	pasarDatosGridCiudad(ciudad);       
                    	gridCiudad.destroy();
		      			ventanaCiudad.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridCiudad.destroy();
		      			ventanaCiudad.destroy();
                     }
                    }]
                    
                   });
                  ventanaCiudad.show();
	}
}