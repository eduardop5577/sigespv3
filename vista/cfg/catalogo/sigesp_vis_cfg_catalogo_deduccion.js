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

var dataStoreDeduccion="";
var formularioBusquedaDeduccion="";
var gridDeduccion="";
var ventanaDeduccion="";


function creardataStoreDeduccion()
{

		var registroDeduccion = Ext.data.Record.create([			  
								{name:'codded'},
								{name:'dended'},
								{name:'sc_cuenta'},
								{name:'porded'}, 
								{name:'monded'},
								{name:'islr'},
								{name:'iva'},
								{name:'estretmun'},
								{name:'formula'},
								{name:'otras'},
								{name:'tipopers'},
								{name:'retaposol'},
								{name:'codconret'},
								{name:'denconret'},
								{name:'estretmil'},
								{name:'denominacion'},
								{name:'desact'}
						]);							
	
		
		
		dataStoreDeduccion =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "codded"},registroDeduccion)
	  	})	
		
		var myJSONObject ={
			"operacion": 'catalogo'
		}
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_cxp_deduccion.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoDeduccion = eval('(' + datos + ')');
			if(objetoDeduccion!='')
			{
				dataStoreDeduccion.loadData(objetoDeduccion);
			}
		}	
	})
}

function actdataStoreDeduccion(criterio,cadena)
{
	dataStoreDeduccion.filter(criterio,cadena,true,false);
}


function crearFormularioBusqueda()
{
		formularioBusquedaDeduccion = new Ext.FormPanel({
        labelWidth: 75,
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 500,
		height:100,
        defaults: {width: 230},
        defaultType:'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'codigo',
				id:'codigo',
				changeCheck: function(){
							var v = this.getValue();
							actdataStoreDeduccion('codded',v);
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
			                name: 'denominacion',
			                id:'denominacion',
							changeCheck: function()
							{
										var v = this.getValue();
										actdataStoreDeduccion('dended',v);
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


function creargridDeduccion()
{
	crearFormularioBusqueda();
	creardataStoreDeduccion();
		 
	 gridDeduccion = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formularioBusquedaDeduccion,
	 autoScroll:true,
     border:true,
     ds: dataStoreDeduccion,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codded', align:'center'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'dended'},
          {header: "Cuenta Contable", width: 50, sortable: true, dataIndex: 'sc_cuenta', align:'center'},
          {header: "Concepto de Retenci&#243;n", width: 50, sortable: true, dataIndex: 'codconret', align:'center'},
          {header: "Deducible", width: 50, sortable: true, dataIndex: 'monded', align:'right'},
		  {header: "F&#243;rmula", width: 50, sortable: true, dataIndex: 'formula'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
}

function bloquearCamposPrimarios()
{
	var myJSONObject ={
		"operacion":"claveprimaria"
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_cxp_deduccion.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var pk = eval('(' + datos + ')');
		if(pk.length>0)
		{
			for(i=0; i < pk.length; i++)
			{
				Ext.getCmp(pk[i].toString()).setDisabled(true);
			}
		}
	}	
	})
}

function validarModificacionDeduccion(coddeduccion)
{
	var myJSONObject ={
		"operacion":"valmoddeduccion",
		"codded":coddeduccion
	};
	var ok = false;
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_cxp_deduccion.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		var respuesta = eval('(' + result.responseText + ')');
		if(respuesta.length>0)
		{
			ok = respuesta.valido;
		}
	}
	})
	return ok;
}


function pasarDatosGridDeduccion(registro)
{
	Ext.getCmp('codded').setValue(registro.get('codded'));
	Ext.getCmp('dended').setValue(registro.get('dended'));
	Ext.getCmp('sc_cuenta').setValue(registro.get('sc_cuenta'));
	Ext.getCmp('monded').setValue(registro.get('monded'));
	Ext.getCmp('formula').setValue(registro.get('formula'));
	Ext.getCmp('porded').setValue(registro.get('porded'));
	Ext.getCmp('dencuenta').setText(registro.get('denominacion'));
	Ext.getCmp('codconret').setValue(registro.get('codconret'));
	Ext.getCmp('desact').setText(registro.get('desact'));
	
	for( var j=0; j < Ext.getCmp('tipodeduccion').items.length; j++ ) 
	{
		switch(Ext.getCmp('tipodeduccion').items.items[j].inputValue)
		{
			case 'S': 
			         ok=(registro.get('islr')==1)?true:false;
					 Ext.getCmp('tipodeduccion').items.items[j].setValue(ok);
					 break;
			case 'I': 
			         ok=(registro.get('iva')==1)?true:false;
					 Ext.getCmp('tipodeduccion').items.items[j].setValue(ok);
			         break;
			case 'M':
			         ok=(registro.get('estretmun')==1)?true:false;
					 Ext.getCmp('tipodeduccion').items.items[j].setValue(ok); 
			         break;
			case 'A':
			         ok=(registro.get('retaposol')==1)?true:false;
					 Ext.getCmp('tipodeduccion').items.items[j].setValue(ok); 
			         break;
			case 'O':
			         ok=(registro.get('otras')==1)?true:false;
					 Ext.getCmp('tipodeduccion').items.items[j].setValue(ok);
			         break;
			case '1':
			         ok=(registro.get('estretmil')==1)?true:false;
					 Ext.getCmp('tipodeduccion').items.items[j].setValue(ok);
			         break;
			
	    }
		
	}
	
	for( var j=0; j < Ext.getCmp('tipopers').items.length; j++ ) 
	{
		switch(Ext.getCmp('tipopers').items.items[j].inputValue)
		{
			case 'J': 
			         ok=(registro.get('tipopers')=='J')?true:false;
					 Ext.getCmp('tipopers').items.items[j].setValue(ok);
					 break;
			case 'N': 
			         ok=(registro.get('tipopers')=='N')?true:false;
					 Ext.getCmp('tipopers').items.items[j].setValue(ok);
			         break;
	    }
		
	}
	
	
	Actualizar=true;			
}

function mostrarCatalogoDeduccion(tipo)
{
				   creargridDeduccion();
				   objetoDeduccion=tipo;
                   ventanaDeduccion = new Ext.Window(
                   {
                    title: 'Cat&#225;logo de Deducciones',
		    		autoScroll:true,
                    width:800,
                    height:470,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridDeduccion],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	var registro = gridDeduccion.getSelectionModel().getSelected();
                    	switch(objetoDeduccion)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
                    			pasarDatosGridDeduccion(registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(registro);
	                    	break;
 
                    		
                    	}          
                    	gridDeduccion.destroy();
		      			ventanaDeduccion.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridDeduccion.destroy();
		      			ventanaDeduccion.destroy();
                     }
                    }]
                    
                   });
                  ventanaDeduccion.show();       
 

 }