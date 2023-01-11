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

var dataStoreEstado="";
var formBusquedaEstado="";
var gridEstado="";
var ventanaEstado="";

function crearDataStoreEstado()
{

	registroEstado = Ext.data.Record.create([
							{name: 'codpai'},
							{name: 'codest'},
							{name: 'desest'}
						]);
	
	var objetoEstado={"raiz":[{"codpai":'',"codest":'',"destest":''}]};
		
		dataStoreEstado =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoEstado),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroEstado  
			),
			data: objetoEstado
	  	})
		//var valor = ComboTipo.getValue();
		var JSONObject ={
			"oper": 'catalogo',
			"codpai": codpai
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_estado.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoEstado = eval('(' + datos + ')');
			if(objetoEstado.raiz == null)
			{
				obtenerMensaje('informacion',ventanaEstado.destroy(),'Mensaje','No se encontro informacion, con los parametros de busqueda seleccionados')	
			}
			else
			{
			 if(objetoEstado!='')
			 {
				dataStoreEstado.loadData(objetoEstado);
			 }
			}
		}	
	})
}

function actDataStoreEstado(criterio,cadena)
{
	dataStoreEstado.filter(criterio,cadena,true,false);
}


function crearFormBusqueda()
{
		formBusquedaEstado = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'codigo',
				id:'cod',
				width: 100,
				changeCheck: function(){
							var v = this.getValue();
							actDataStoreEstado('codest',v);
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
			                fieldLabel: 'Estado',
			                name: 'den',
			                id:'den',
							changeCheck: function()
							{
										var v = this.getValue();
										actDataStoreEstado('desest',v);
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

function crearGridCatalogoEstado()
{
	crearFormBusqueda();
	crearDataStoreEstado();
		 
	 gridEstado = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaEstado,
	 autoScroll:true,
     border:true,
     ds: dataStoreEstado,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codest'},
          {header: "Estados", width: 30, sortable: true,   dataIndex: 'desest'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
}

function pasarDatosEstado(estado)
{
	Ext.getCmp('codest').setValue(estado.get('codest'));
	Ext.getCmp('codpai').disable();
	Ext.getCmp('codest').disable();
	Ext.getCmp('desest').setValue(estado.get('desest'));
	Actualizar=true;
}

function irBuscar()
{
              
			   if((codpai==null)||(codpai==''))
			   {
			    Ext.Msg.show({
			    title:'Mensaje',
			    msg: 'Debe seleccionar un pais, verifique por favor!!',
			    buttons: Ext.Msg.OK,
			    animEl: 'elId',
			    icon: Ext.MessageBox.INFO
				});
			   }
			   else
			   {
				   crearGridCatalogoEstado();
				   ObjetoFuente='definicion';
                   ventanaEstado = new Ext.Window(
                   {
                    resizable:false,
                    title: 'Cat&#225;logo de estados',
		    		autoScroll:true,
                    width:800,
                    height:475,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridEstado],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridEstado.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			pasarDatosEstado(registro);
								Actualizar=1;
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(registro);
	                    	break;
 
                    		
                    	}          
                    	gridEstado.destroy();
		      			ventanaEstado.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridEstado.destroy();
		      			ventanaEstado.destroy();
                     }
                    }]
                    
                   });
                  ventanaEstado.show();   
				   }    
 

 }