/*****************************************************************************************
* @Definición de Usuario:
* @Archivo javascript el cual contiene todos los componentes de la Definición de Usuario
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var panel    = '';
var gridPer  = '';
var gridCons = '';
var gridNom  = '';
var gridUni  = '';
var gridPre  = '';
var gridAlm  = '';
var gridCenCos  = '';
var gridCtaBan  = '';
var DataStorePer 	= '';
var DataStoreCons 	= '';
var DataStoreNom 	= '';
var DataStoreUni 	= '';
var DataStoreEstPre = '';
var DataStoreAlm = '';
var DataStoreCenCos = '';
var DataStoreCtaBan = '';
var DatosNuevo   	= '';
var DatosNuevoCons  = '';
var DatosNuevoNom   = '';
var DatosNuevoUni   = '';
var DatosNuevoEstPre   = '';
var DatosNuevoAlm   = '';
var DatosNuevoCenCos   = '';
var DatosNuevoCtaBan   = '';
var cambiar     = false;
var pantalla    = 'usuario';
var arrAdmin		= new Array();
var arrCons			= new Array();
var arrUni			= new Array();
var arrNom			= new Array();
var arrEstPre		= new Array();
var arrAlmacen		= new Array();
var arrCenCos		= new Array();
var arrCtaBan		= new Array();
var arrEliminar 	= new Array();
var arrEliminarCons = new Array();
var arrEliminarNom 	= new Array();
var arrEliminarUni 	= new Array();
var arrEliminarEst 	= new Array();
var arrEliminarAlm 	= new Array();
var arrEliminarCenCos 	= new Array();
var arrEliminarCtaBan 	= new Array();
var personalElim 	= '';
var constanteElim 	= '';
var nominaElim 		= '';
var unidadElim 		= '';
var estpreElim 		= '';
var almacenElim 	= '';
var CenCosElim 	= '';
var CtaBanElim 	= '';
var j = 0;
var rutaUsuario		= '../../controlador/sss/sigesp_ctr_sss_usuario.php';
barraherramienta    = true;
Ext.onReady
(
	function()
	{
		Ext.QuickTips.init();
		Ext.form.Field.prototype.msgTarget = 'side';					
		//Cargar los datos de los estatus para asociarlos al combo. 
		var datosEstatus={'raiz':[{'codestatus':'1','estatus':'Activo'},{'codestatus':'2','estatus':'Bloqueado'},{'codestatus':'3','estatus':'Suspendido'}]};
			record = Ext.data.Record.create([
				{name: 'codestatus'},     
				{name: 'estatus'}
		]);					
		dsestatus =  new Ext.data.Store({
				proxy: new Ext.data.MemoryProxy(datosEstatus),
				reader: new Ext.data.JsonReader(
				{
					root: 'raiz',               
					id: 'id'   
				},
				record
				),
				data: datosEstatus			
			 });
			 
		agregarPer = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregarPer,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar personal'
		});		
		quitarPer = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitarPer,
			iconCls: 'bmenuquitar',
        	tooltip: 'Eliminar personal'
		});		
		agregarCons = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregarCons,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar constantes de nómina'
		});		
		quitarCons = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitarCons,
			iconCls: 'bmenuquitar',
        	tooltip: 'Eliminar constantes de nómina'
		});			
		agregarNom = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregarNom,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar nómina'
		});		
		quitarNom = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitarNom,
			iconCls: 'bmenuquitar',
        	tooltip: 'Eliminar nómina'
		});		
		agregarUni = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregarUni,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar unidad ejecutora'
		});		
		quitarUni = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitarUni,
			iconCls: 'bmenuquitar',
        	tooltip: 'Eliminar unidad ejecutora'
		});			
		agregarEstPre = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregarEstPre,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar estructura presupuestaria'
		});
		quitarEstPre = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitarEstPre,
			iconCls: 'bmenuquitar',
        	tooltip: 'Eliminar estructura presupuestaria'
		});		 	 	 
		agregarAlmacen = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregarAlmacen,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar Almacen'
		});
		quitarAlmacen = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitarAlmacen,
			iconCls: 'bmenuquitar',
        	tooltip: 'Eliminar Almacen'
		});		 	 	 
		agregarCenCos = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregarCenCos,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar Centro Costo'
		});
		quitarCenCos = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitarCenCos,
			iconCls: 'bmenuquitar',
        	tooltip: 'Eliminar Centro Costo'
		});		 	 	 

		agregarCtaBan = new Ext.Action(
		{
			text: 'Agregar',
			handler: irAgregarCtaBan,
			iconCls: 'bmenuagregar',
        	tooltip: 'Agregar Cuenta Banco'
		});
		quitarCtaBan = new Ext.Action(
		{
			text: 'Quitar',
			handler: irQuitarCtaBan,
			iconCls: 'bmenuquitar',
        	tooltip: 'Eliminar Cuenta Banco'
		});		 	 	 

		obtenerGridPersonal();
		obtenerGridConstantes();
		obtenerGridNominas();
		obtenerGridUnidades();
		obtenerGridPresupuestos();		 
		obtenerGridAlmacen();		 
		obtenerGridCentroCosto();		 
		obtenerGridCuentaBanco();		 
		
		Xpos = ((screen.width/2)-(750/2)); 
		Ypos = ((screen.height/2)-(700/2));		
		panel = new Ext.FormPanel({
        labelWidth: 100, 
       	title: 'Definición de Usuarios',
        bodyStyle:'padding:5px 5px 0',        
		style: 'margin-top:40px;margin-left:'+Xpos+'px',
		width:750,
	   	tbar: [],
        items:[{			
			xtype:'fieldset',
				title:'Datos del Usuario',
				id:'fsformusuario',
    			autoHeight:true,
				autoWidth:true,
				cls :'fondo',	
			    items:[{
				xtype:'textfield',
				fieldLabel:'Usuario',
				name:'código del usuario',
				id:'txtcodusuario',
				width:150
			  },{
			  	layout:'column',
			  	border:false,
			  	baseCls: 'fondo',
			  	items:[{
			  		columnWidth:.3,
					layout: 'form',
					border:false,
					baseCls: 'fondo',
					items: [{
						xtype:'textfield',
						fieldLabel:'Cédula',
						name:'cédula',
						id:'txtcedula',
						width:70
					}]
				},{	
					columnWidth:.3,
					layout: 'form',
					border:false,
					baseCls: 'fondo',
					items: [{
						xtype:'datefield',
						fieldLabel:'Fecha Nacimiento',
						name:'fechanac',
						id:'txtfecnac',
					}]
				}]				
			  },{
				xtype:'textfield',
				fieldLabel:'Nombre',
				name:'nombre',
				id:'txtnombre',
				width:310
			  },{
				xtype:'textfield',
				fieldLabel:'Apellido',
				name:'apellido',
				id:'txtapellido',
				width:310,
			  },{
				xtype:'textfield',
				fieldLabel:'Contraseña',
				name:'contraseña',
				id:'txtpassword',
				inputType:'password',
				width:200
			  },{
				xtype:'textfield',
				fieldLabel:'Verificar',
				name:'verificar',
				id:'txtverpassword',
				inputType:'password',
				width:200
			},{
				xtype:'textfield',
				fieldLabel:'Teléfono',
				name:'telefono',
				id:'txttelefono',
				width:100
			},{
			  	xtype:'label',
				text: 'Formato: 5555-5555555',
				style:'position:absolute;left:250px;top:190px;font-size:12px;font-family:Verdana, Arial, Helvetica, sans-serif'			  
			},{
				xtype:'checkbox',
				fieldLabel:'No bloquear Contraseña',
				name:'estblocon',
				id:'chkestblocon',
            },{
				xtype:'textfield',
				fieldLabel:'E-mail',
				name:'email',
				id:'txtemail',
				width:360,
				vtype:'email'
			  },{
			  	layout:'column',
			  	border:false,
			  	baseCls: 'fondo',
			  	items:[{
			  		columnWidth:.3,
					layout: 'form',
					border:false,
					baseCls: 'fondo',
					items: [{
						xtype:'combo',
					  	fieldLabel:'Estatus',
						name:'estatus',
						id:'cmbestatus',
						emptyText:'Seleccione',
						displayField:'estatus',
						valueField:'codestatus',
						typeAhead: true,
						mode: 'local',
						triggerAction: 'all',
						store: dsestatus,
						width:100	
					}]  
			  	},{
			  		columnWidth:.3,
					layout: 'form',
					border:false,
					baseCls: 'fondo',
					items: [{
					  	xtype:'checkbox',
						fieldLabel:'Administrador',
						name:'administrador',
			       		id:'chbadmin',
        			}]
        		},{
        			columnWidth:.3,
					layout: 'form',
					border:false,
					baseCls: 'fondo',
					items: [{
						xtype:'textfield', 
			       		fieldLabel:'Último ingreso',
						name:'fecha ingreso',
			       		id:'txtultingreso',
						readOnly: true,
						disabled:true,
						width:100
					}]	
        		}]
			  },{
				xtype:'textfield',
				fieldLabel:'Nota',
				name:'nota',
				id:'txtnota',
				width:530
			  },{
			  	xtype:'panel',
				id:'foto',
				name:'foto',
				contentEl:'divfoto',
				width:100,
				height:120,
				style:'position:absolute;left:520px;top:30px',
			  }]  
			},{
				xtype:'tabpanel',
				border:false,
            	activeTab:0,
            	height:150,
            	width:600,
           	 	autoWidth:true,
            	autoScroll:true,
            	region:'south',
				enableTabScroll:true,
            	items:
				[{
                     contentEl:'pest1',
                     title: 'Asignar constantes de Nómina',
                     autoScroll:true
                 	},{
                     contentEl:'pest2',
                     title: 'Asignar Nóminas',
                     autoScroll:true
                 	},{
                     contentEl:'pest3',
                     title: 'Asignar Unidades Ejecutoras',
                     autoScroll:true
                	},{
                     contentEl:'pest4',
                     title: 'Asignar Presupuestos',
                     autoScroll:true                                
                 	},{
                     contentEl:'pest5',
                     title: 'Asignar Tipo de Personal',
                     autoScroll:true   
                 	},{
                     contentEl:'pest6',
                     title: 'Asignar Almacen',
                     autoScroll:true   
                 	},{
                     contentEl:'pest7',
                     title: 'Asignar Centro de Costos',
                     autoScroll:true   
                    },{
                     contentEl:'pest8',
                     title: 'Asignar Cuentas de Banco',
                     autoScroll:true   
                    }]                    		  
			 }]
		});
		panel.render(document.body);	
						
		Ext.getCmp('txtverpassword').on('blur',verificar);	
	
});	//fin del archivo
	
		
/************************************************************************************
* @ Función para verificar que la contraseña se haya escrito correctamente dos veces.
* @parametros 
* @retorno
* @fecha creación: 22/07/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
*************************************************************************************/			
	function verificar()
	{			
		var pasusuario    = Ext.getCmp('txtpassword').getValue(); 
		var verpasusuario = Ext.getCmp('txtverpassword').getValue();
		if ((pasusuario)!=(verpasusuario))
		{
			Ext.MessageBox.alert('Mensaje','Las contraseñas no coinciden');			
			Ext.getCmp('txtpassword').focus(true, true);
			Ext.getCmp('txtpassword').setValue('');
			Ext.getCmp('txtverpassword').setValue('');
		}	
	}


/***********************************************************************************
* @Limpiar campos del formulario
* @parametros 
* @retorno
* @fecha creación: 21/07/2008
* @autor: Ing. Gusmary Balza
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
************************************************************************************/				
	function limpiarCampos()
	{
		Ext.getCmp('txtcodusuario').setValue('');
		Ext.getCmp('txtcedula').setValue('');
		Ext.getCmp('txtnombre').setValue('');
		Ext.getCmp('txtapellido').setValue('');
		Ext.getCmp('txtpassword').setValue('');
		Ext.getCmp('txtverpassword').setValue('');
		Ext.getCmp('txttelefono').setValue('');
		Ext.getCmp('txtemail').setValue('');
		Ext.getCmp('txtultingreso').setValue('');
		Ext.getCmp('cmbestatus').setValue('Seleccione');
		Ext.getCmp('chbadmin').setValue(0);
		Ext.getCmp('chkestblocon').setValue(0);
		Ext.getCmp('txtnota').setValue('');	
		Ext.getCmp('txtfecnac').setValue('');
		Ext.getCmp('txtultingreso').setValue('');
		
		DatosNuevo     = {'raiz':[{'codemp':'','codtippersss':'','dentippersss':''}]};
		DatosNuevoCons = {'raiz':[{'codemp':'','codnom':'','codcons':'','nomcon':''}]};	
		DatosNuevoNom  = {'raiz':[{'codemp':'','codnom':'','desnom':''}]};	
		DatosNuevoUni  = {'raiz':[{'codsis':'','coduniadm':'','denuniadm':''}]};	
		DatosNuevoPre  = {'raiz':[{'codest':'','codcompleto':'','nombre':''}]};	
		DatosNuevoAlm  = {'raiz':[{'codalm':'','nomfisalm':''}]};	
		DatosNuevoCenCos  = {'raiz':[{'codcencos':'','denominacion':''}]};	
		DatosNuevoCtaBan  = {'raiz':[{'codban':'','ctaban':''}]};	
				
		gridPer.store.removeAll();		
		DataStorePer.loadData(DatosNuevo);
				
		gridCons.store.removeAll();
		DataStoreCons.loadData(DatosNuevoCons);		
		
		gridNom.store.removeAll();
		DataStoreNom.loadData(DatosNuevoNom);
				
		gridUni.store.removeAll();
		DataStoreUni.loadData(DatosNuevoUni);		
		
		gridPre.store.removeAll();
		DataStorePre.loadData(DatosNuevoPre);		

		gridAlm.store.removeAll();
		DataStoreAlm.loadData(DatosNuevoAlm);		

		gridCenCos.store.removeAll();
		DataStoreCenCos.loadData(DatosNuevoCenCos);		

		gridCtaBan.store.removeAll();
		DataStoreCtaBan.loadData(DatosNuevoCtaBan);		

		gridPer.store.commitChanges();
		gridCons.store.commitChanges();
		gridNom.store.commitChanges();
		gridUni.store.commitChanges();
		gridPre.store.commitChanges();
		gridAlm.store.commitChanges();
		gridCenCos.store.commitChanges();
		gridCtaBan.store.commitChanges();
		
		for (i=0; i<=arrAdmin.length; i++)
		{
			arrAdmin.pop();			
		}
		for (i=0; i<=arrCons.length; i++)
		{
			arrCons.pop();			
		}
		for (i=0; i<=arrNom.length; i++)
		{
			arrNom.pop();			
		}
		for (i=0; i<=arrUni.length; i++)
		{
			arrUni.pop();			
		}
		for (i=0; i<=arrEstPre.length; i++)
		{
			arrEstPre.pop();			
		}
		for (i=0; i<=arrAlmacen.length; i++)
		{
			arrAlmacen.pop();			
		}
		for (i=0; i<=arrCenCos.length; i++)
		{
			arrCenCos.pop();			
		}
		for (i=0; i<=arrCtaBan.length; i++)
		{
			arrCtaBan.pop();			
		}
		for (i=0; i<=arrEliminar.length; i++)
		{
			arrEliminar.pop();			
		}
		for (i=0; i<=arrEliminarCons.length; i++)
		{
			arrEliminarCons.pop();			
		}
		for (i=0; i<=arrEliminarNom.length; i++)
		{
			arrEliminarNom.pop();			
		}
		for (i=0; i<=arrEliminarUni.length; i++)
		{
			arrEliminarUni.pop();			
		}
		for (i=0; i<=arrEliminarEst.length; i++)
		{
			arrEliminarEst.pop();			
		}		
		for (i=0; i<=arrEliminarAlm.length; i++)
		{
			arrEliminarAlm.pop();			
		}		
		for (i=0; i<=arrEliminarCenCos.length; i++)
		{
			arrEliminarCenCos.pop();			
		}		
		for (i=0; i<=arrEliminarCtaBan.length; i++)
		{
			arrEliminarCtaBan.pop();			
		}		
	}
	
	
/*********************************************************************************
* @Función que limpia los campos y asigna un nuevo código
* @parametros
* @retorno
* @fecha de creación: 21/07/2008
* @autor: Ing. Gusmary Balza.
***********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion: 
**********************************************************************************/		
	function irNuevo(item)
	{
		limpiarCampos();
		Ext.getCmp('txtcodusuario').enable(),
		Ext.getCmp('txtpassword').enable();
		Ext.getCmp('txtverpassword').enable();
		cambiar = false;
	}
	
	
/**********************************************************************************
* @Función que guarda o actualiza los datos de un usuario.
* @parametros
* @retorno
* @fecha de creación: 21/07/2008
* @autor: Ing. Gusmary Balza.
***********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*************************************************************************************/
	function irGuardar(item)
	{		
		valido = true;
		continuar = false;
		if ((!tbnuevo)&&(!cambiar))
		{
			valido=false;
			Ext.MessageBox.alert('Error','No tiene permiso para Incluir.');
		}
		if ((!tbactualizar)&&(cambiar))
		{
			valido = false;
			Ext.MessageBox.alert('Error','No tiene permiso para Modificar.');
		}	
		if (!cambiar)
		{
			if ((validarObjetos('txtcodusuario','30','novacio|alfanumerico')!='0' && validarObjetos('txtcedula','8','novacio|numero')!='0' && validarObjetos('txtnombre','100','novacio|nombre')!='0' && validarObjetos('txtapellido','50','novacio|nombre')!='0' && validarObjetos('txtpassword','50','novacio')!='0' && validarObjetos('txttelefono','20','telefono')!='0' && validarObjetos('txtemail','100','vacioemail')!='0' && validarObjetos('cmbestatus','15','novacio')!='0'&& validarObjetos('txtnota','2000','alfanumerico')!='0') && validarObjetos('txtfecnac','','novacio')!='0' && (valido))
			{   
				var pasusuario = Ext.getCmp('txtpassword').getValue();
				if(validarClave(pasusuario))
				{
					obtenerMensaje('procesar','','Guardando Datos');
					pasusuario = 'sigesp'+pasusuario;
					Ext.getCmp('txtpassword').setValue(b64_sha1(pasusuario));		
					continuar = true;
					evento ='incluir';
					admusu = 0;								
					if (Ext.getCmp('chbadmin').getValue()=='1')
					{
						admusu = 1;
					}
					estblocon = 0;
					if (Ext.getCmp('chkestblocon').getValue()=='1')
					{
						estblocon = 1;
					}
					var objdata ="{'oper': evento,'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
					"','cedusu':   '"+Ext.getCmp('txtcedula').getValue()+
					"','nomusu':   '"+Ext.getCmp('txtnombre').getValue()+
					"','apeusu':   '"+Ext.getCmp('txtapellido').getValue()+
					"','pwdusu':   '"+Ext.getCmp('txtpassword').getValue()+
					"','telusu':   '"+Ext.getCmp('txttelefono').getValue()+
					"','email':    '"+Ext.getCmp('txtemail').getValue()+
					"','estatus':	'"+Ext.getCmp('cmbestatus').getValue()+
					"','admusu': 	'"+admusu+
					"','estblocon': '"+estblocon+
					"','nota': '"+Ext.getCmp('txtnota').getValue()+
					"','fecnacusu': '"+Ext.get('txtfecnac').dom.value+
					"','sistema': '"+sistema+"','vista': '"+vista+"'"
				
					arrAdmin = gridPer.store.getModifiedRecords();
					objdata = objdata+ ",datosAdmin:[";
					total = arrAdmin.length;
					if (total>0)
					{				
						for (i=0; i < total; i++)
						{
							if (i==0)
							{
								objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SNO','codintper':'"+ arrAdmin[i].get('codtippersss')+ "','enabled':1}";
							}
							else
							{
								objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SNO','codintper':'"+ arrAdmin[i].get('codtippersss')+ "','enabled':1}";
							}
						}				
					}
					objdata = objdata + "]";
					objdata = objdata+ ",datosEliminar:[";
					total = arrEliminar.length;
					if (total>0)
					{
						for (i=0; i < total; i++)
						{
							if (i==0)
							{
								objdata = objdata +"{'codusu':'"+codusu+
								"','codsis':'SNO','codintper':'"+ arrEliminar[i]+ "'}";
							}
							else
							{
								objdata = objdata +",{'codusu':'"+codusu+
								"','codsis':'SNO','codintper':'"+ arrEliminar[i]+ "'}";
							}
						}				
					}		
					objdata = objdata + "]";			
						
					arrCons = gridCons.store.getModifiedRecords();
					objdata = objdata+ ",datosCons:[";
					total = arrCons.length;
					if (total>0)
					{				
						for (i=0; i < total; i++)
						{						
							if (i==0)
							{
								objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SNO','codintper':'"+ arrCons[i].get('codnom')+ "-"+arrCons[i].get('codcons')+"','enabled':1}";
							}
							else
							{
								objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SNO','codintper':'"+ arrCons[i].get('codnom')+ "-"+arrCons[i].get('codcons')+"','enabled':1}";
							}
						}				
					}
					objdata = objdata + "]";
					objdata = objdata+ ",datosEliminarCons:[";
					total = arrEliminarCons.length;
					for (i=0; i<total; i++)
					{
						if (i == 0)
						{
							objdata = objdata +"{'codusu':'"+codusu+
						"','codsis':'SNO','codintper':'"+ arrEliminarCons[i]+ "'}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+codusu+
						"','codsis':'SNO','codintper':'"+ arrEliminarCons[i]+ "'}";
						}
					}
					objdata = objdata+"]";			
					
					arrNom = gridNom.store.getModifiedRecords();
					objdata = objdata+ ",datosNom:[";
					total = arrNom.length;
					if (total>0)
					{	
						
						for (i=0; i < total; i++)
						{
							if (i==0)
							{
								objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SNO','codintper':'"+ arrNom[i].get('codnom')+ "','enabled':1}";
							}
							else
							{
								objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SNO','codintper':'"+ arrNom[i].get('codnom')+ "','enabled':1}";
							}
						}				
					}
					objdata = objdata + "]";			
					total = arrEliminarNom.length;
					objdata = objdata+ ",datosEliminarNom:[";
					for (i=0; i<total; i++)
					{
						if (i == 0)
						{
							objdata = objdata +"{'codusu':'"+codusu+
						"','codsis':'SNO','codintper':'"+ arrEliminarNom[i]+ "'}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+codusu+
						"','codsis':'SNO','codintper':'"+ arrEliminarNom[i]+ "'}";
						}
					}
					objdata = objdata + "]";
					
					arrUni = gridUni.store.getModifiedRecords();									
					if (arrUni!='' && arrUni[i].get('codsis')=='')
					{
						Ext.Msg.alert('Mensaje','Debe seleccionar el Sistema para la Unidad Ejecutora');
					}
					else
					{	
						objdata = objdata+ ",datosUni:[";
						total = arrUni.length;
						if (total>0)
						{				
							for (i=0; i < total; i++)
							{
								if (i==0)
								{
									objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
									"','codsis':'"+ arrUni[i].get('codsis')+ "','codintper':'"+ arrUni[i].get('coduniadm')+ "','enabled':1}";
								}
								else
								{
									objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
									"','codsis':'"+ arrUni[i].get('codsis')+ "','codintper':'"+ arrUni[i].get('coduniadm')+ "','enabled':1}";
								}
							}				
						}
					}	
					objdata = objdata + "]";			
					total = arrEliminarUni.length;
					objdata = objdata+ ",datosEliminarUni:[";
					for (i=0; i<total; i++)
					{
						if (i == 0)
						{
							objdata = objdata +"{'codusu':'"+codusu+
						"','codsis':'"+ arrEliminarUni[i].get('codsis')+ "','codintper':'"+ arrEliminarUni[i].get('coduniadm')+ "'}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+codusu+
						"','codsis':'"+ arrEliminarUni[i].get('codsis')+ "','codintper':'"+ arrEliminarUni[i].get('coduniadm')+ "'}";
						}
					}
					objdata = objdata + "]";
					
					arrEstPre = gridPre.store.getModifiedRecords();
					objdata = objdata+ ",datosEstPre:[";
					total = arrEstPre.length;
					if (total>0)
					{				
						for (i=0; i < total; i++)
						{
							if (i==0)
							{	
								objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SPG','codintper':'"+ arrEstPre[i].get('codcompleto')+ "','enabled':1}";
							}
							else
							{
								objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SPG','codintper':'"+ arrEstPre[i].get('codcompleto')+ "','enabled':1}";
							}
						}			
					}			
					objdata = objdata + "]";			
					total = arrEliminarEst.length;
					objdata = objdata+ ",datosEliminarPre:[";
					for (i=0; i<total; i++)
					{
						if (i == 0)
						{
							objdata = objdata +"{'codusu':'"+codusu+
						"','codsis':'SPG','codintper':'"+ arrEliminarEst[i]+ "'}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+codusu+
						"','codsis':'SPG','codintper':'"+ arrEliminarEst[i]+ "'}";
						}
					}
					objdata = objdata + "]";
					
					arrAlmacen = gridAlm.store.getModifiedRecords();
					objdata = objdata+ ",datosAlmacen:[";
					total = arrAlmacen.length;
					if (total>0)
					{				
						for (i=0; i < total; i++)
						{
							if (i==0)
							{	
								objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SIV','codintper':'"+ arrAlmacen[i].get('codalm')+ "','enabled':1}";
							}
							else
							{
								objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SIV','codintper':'"+ arrAlmacen[i].get('codalm')+ "','enabled':1}";
							}
						}			
					}			
					objdata = objdata + "]";			
					total = arrEliminarAlm.length;
					objdata = objdata+ ",datosEliminarAlmacen:[";
					for (i=0; i<total; i++)
					{
						if (i == 0)
						{
							objdata = objdata +"{'codusu':'"+codusu+
						"','codsis':'SIV','codintper':'"+ arrEliminarAlm[i].get('codalm')+ "'}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+codusu+
						"','codsis':'SIV','codintper':'"+ arrEliminarAlm[i].get('codalm')+ "'}";
						}
					}
					objdata = objdata + "]";

					arrCenCos = gridCenCos.store.getModifiedRecords();
					objdata = objdata+ ",datosCenCos:[";
					total = arrCenCos.length;
					if (total>0)
					{				
						for (i=0; i < total; i++)
						{
							if (i==0)
							{	
								objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'CFG','codintper':'"+ arrCenCos[i].get('codcencos')+ "','enabled':1}";
							}
							else
							{
								objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'CFG','codintper':'"+ arrCenCos[i].get('codcencos')+ "','enabled':1}";
							}
						}			
					}			
					objdata = objdata + "]";			
					total = arrEliminarCenCos.length;
					objdata = objdata+ ",datosEliminarCenCos:[";
					for (i=0; i<total; i++)
					{
						if (i == 0)
						{
							objdata = objdata +"{'codusu':'"+codusu+
						"','codsis':'CFG','codintper':'"+ arrEliminarCenCos[i].get('codcencos')+ "'}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+codusu+
						"','codsis':'CFG','codintper':'"+ arrEliminarCenCos[i].get('codcencos')+ "'}";
						}
					}
					objdata = objdata + "]";


					arrCtaBan = gridCtaBan.store.getModifiedRecords();
					objdata = objdata+ ",datosCtaBan:[";
					total = arrCtaBan.length;
					if (total>0)
					{				
						for (i=0; i < total; i++)
						{
							banco=arrCtaBan[i].get('codban')+"-"+arrCtaBan[i].get('ctaban');
							if (i==0)
							{
								objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SCB','codintper':'"+ banco + "','enabled':1}";
							}
							else
							{
								objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'SCB','codintper':'"+ banco+ "','enabled':1}";
							}
						}			
					}			
					objdata = objdata + "]";			
					total = arrEliminarCtaBan.length;
					objdata = objdata+ ",datosEliminarCtaBan:[";
					for (i=0; i<total; i++)
					{
						banco=arrEliminarCtaBan[i].get('codban')+"-"+arrEliminarCtaBan[i].get('ctaban');
						if (i == 0)
						{
							objdata = objdata +"{'codusu':'"+codusu+
						"','codsis':'SCB','codintper':'"+ banco + "'}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+codusu+
						"','codsis':'SCB','codintper':'"+ banco + "'}";
						}
					}
					objdata = objdata + "]}";

				}
			}
		}
		else
		{			
			if ((validarObjetos('txtcodusuario','50','novacio|alfanumerico')!='0' && validarObjetos('txtcedula','8','novacio|numero')!='0' && validarObjetos('txtnombre','50','novacio|nombre')!='0' && validarObjetos('txtapellido','50','novacio|nombre')!='0' && validarObjetos('txttelefono','50','telefono')!='0' && validarObjetos('txtemail','100','vacioemail')!='0' && validarObjetos('cmbestatus','15','novacio')!='0' && validarObjetos('txtnota','2000','alfanumerico')!='0') && validarObjetos('txtfecnac','','novacio')!='0' && (valido))
			{   
				var pasusuario = Ext.getCmp('txtpassword').getValue();
				pasusuario = 'sigesp'+pasusuario;
				Ext.getCmp('txtpassword').setValue(b64_sha1(pasusuario));	
				
				continuar = true;
				evento ='actualizar';			
				admusu = 0;								
				if (Ext.getCmp('chbadmin').getValue()=='1')
				{
					admusu = 1;
				}
				estblocon = 0;
				if (Ext.getCmp('chkestblocon').getValue()=='1')
				{
					estblocon = 1;
				}
				var objdata ="{'oper': evento,'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
				"','cedusu':  	'"+Ext.getCmp('txtcedula').getValue()+
				"','nomusu':  	'"+Ext.getCmp('txtnombre').getValue()+
				"','apeusu':  	'"+Ext.getCmp('txtapellido').getValue()+
				"','pwdusu':   	'"+Ext.getCmp('txtpassword').getValue()+
				"','telusu':   	'"+Ext.getCmp('txttelefono').getValue()+
				"','email':    	'"+Ext.getCmp('txtemail').getValue()+
				"','estatus':  	'"+Ext.getCmp('cmbestatus').getValue()+
				"','admusu': 	'"+admusu+
				"','estblocon': '"+estblocon+
				"','nota': 		'"+Ext.getCmp('txtnota').getValue()+
				"','fecnacusu': '"+Ext.get('txtfecnac').dom.value+
				"','sistema': 	'"+sistema+"','vista': '"+vista+"'"
				
				arrAdmin = gridPer.store.getModifiedRecords();
				objdata = objdata+ ",datosAdmin:[";
				total = arrAdmin.length;
				if (total>0)
				{				
					for (i=0; i < total; i++)
					{
						if (i==0)
						{
							objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SNO','codintper':'"+ arrAdmin[i].get('codtippersss')+ "','enabled':1}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SNO','codintper':'"+ arrAdmin[i].get('codtippersss')+ "','enabled':1}";
						}
					}				
				}
				objdata = objdata + "]";
				objdata = objdata+ ",datosEliminar:[";
				total = arrEliminar.length;
				if (total>0)
				{
					for (i=0; i < total; i++)
					{
						if (i==0)
						{
							objdata = objdata +"{'codusu':'"+codusu+
							"','codsis':'SNO','codintper':'"+ arrEliminar[i]+ "'}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+codusu+
							"','codsis':'SNO','codintper':'"+ arrEliminar[i]+ "'}";
						}
					}				
				}		
				objdata = objdata + "]";			
					
				arrCons = gridCons.store.getModifiedRecords();
				objdata = objdata+ ",datosCons:[";
				total = arrCons.length;
				if (total>0)
				{				
					for (i=0; i < total; i++)
					{
						if (i==0)
						{
							objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SNO','codintper':'"+ arrCons[i].get('codnom')+ "-"+arrCons[i].get('codcons')+"','enabled':1}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SNO','codintper':'"+ arrCons[i].get('codnom')+ "-"+arrCons[i].get('codcons')+"','enabled':1}";
						}
					}				
				}
				objdata = objdata + "]";
				objdata = objdata+ ",datosEliminarCons:[";
				total = arrEliminarCons.length;
				for (i=0; i<total; i++)
				{
					if (i == 0)
					{
						objdata = objdata +"{'codusu':'"+codusu+
					"','codsis':'SNO','codintper':'"+ arrEliminarCons[i]+ "'}";
					}
					else
					{
						objdata = objdata +",{'codusu':'"+codusu+
					"','codsis':'SNO','codintper':'"+ arrEliminarCons[i]+ "'}";
					}
				}
				objdata = objdata+"]";			
				
				arrNom = gridNom.store.getModifiedRecords();
				objdata = objdata+ ",datosNom:[";
				total = arrNom.length;
				if (total>0)
				{			
					for (i=0; i < total; i++)
					{
						if (i==0)
						{
							objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SNO','codintper':'"+ arrNom[i].get('codnom')+ "','enabled':1}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SNO','codintper':'"+ arrNom[i].get('codnom')+ "','enabled':1}";
						}
					}				
				}
				objdata = objdata + "]";			
				total = arrEliminarNom.length;
				objdata = objdata+ ",datosEliminarNom:[";
				for (i=0; i<total; i++)
				{
					if (i == 0)
					{
						objdata = objdata +"{'codusu':'"+codusu+
					"','codsis':'SNO','codintper':'"+ arrEliminarNom[i]+ "'}";
					}
					else
					{
						objdata = objdata +",{'codusu':'"+codusu+
					"','codsis':'SNO','codintper':'"+ arrEliminarNom[i]+ "'}";
					}
				}
				objdata = objdata + "]";
				
				arrUni = gridUni.store.getModifiedRecords();				
				if (arrUni!='' && arrUni[i].get('codsis')=='')
				{
					Ext.Msg.alert('Mensaje','Debe seleccionar el Sistema para la Unidad Ejecutora');
				}
				else
				{				
					objdata = objdata+ ",datosUni:[";
					total = arrUni.length;
					if (total>0)
					{				
						for (i=0; i < total; i++)
						{
							if (i==0)
							{
								objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'"+ arrUni[i].get('codsis')+ "','codintper':'"+ arrUni[i].get('coduniadm')+ "','enabled':1}";
							}
							else
							{
								objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
								"','codsis':'"+ arrUni[i].get('codsis')+ "','codintper':'"+ arrUni[i].get('coduniadm')+ "','enabled':1}";
							}
						}				
					}
				}	
				objdata = objdata + "]";			
				total = arrEliminarUni.length;
				objdata = objdata+ ",datosEliminarUni:[";				
				for (i=0; i<total; i++)
				{
					if (i == 0)
					{
						objdata = objdata +"{'codusu':'"+codusu+
					"','codsis':'"+ arrEliminarUni[i].get('codsis')+ "','codintper':'"+ arrEliminarUni[i].get('coduniadm')+ "'}";
					}
					else
					{
						objdata = objdata +",{'codusu':'"+codusu+
					"','codsis':'"+ arrEliminarUni[i].get('codsis')+ "','codintper':'"+ arrEliminarUni[i].get('coduniadm')+ "'}";
					}
				}
				objdata = objdata + "]";
				arrEstPre = gridPre.store.getModifiedRecords();
				objdata = objdata+ ",datosEstPre:[";
				total = arrEstPre.length;
				if (total>0)
				{				
					for (i=0; i < total; i++)
					{
						if (i==0)
						{	
							objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SPG','codintper':'"+ arrEstPre[i].get('codcompleto')+ "','enabled':1}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SPG','codintper':'"+ arrEstPre[i].get('codcompleto')+ "','enabled':1}";
						}
					}			
				}			
				objdata = objdata + "]";			
				total = arrEliminarEst.length;
				objdata = objdata+ ",datosEliminarPre:[";
				for (i=0; i<total; i++)
				{
					if (i == 0)
					{
						objdata = objdata +"{'codusu':'"+codusu+
					"','codsis':'SPG','codintper':'"+ arrEliminarEst[i].get('codcompleto')+ "'}";
					}
					else
					{
						objdata = objdata +",{'codusu':'"+codusu+
					"','codsis':'SPG','codintper':'"+ arrEliminarEst[i].get('codcompleto')+ "'}";
					}
				}
				objdata = objdata + "]";	

				arrAlmacen = gridAlm.store.getModifiedRecords();
				objdata = objdata+ ",datosAlmacen:[";
				total = arrAlmacen.length;
				if (total>0)
				{				
					for (i=0; i < total; i++)
					{
						if (i==0)
						{	
							objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SIV','codintper':'"+ arrAlmacen[i].get('codalm')+ "','enabled':1}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SIV','codintper':'"+ arrAlmacen[i].get('codalm')+ "','enabled':1}";
						}
					}			
				}			
				objdata = objdata + "]";			
				total = arrEliminarAlm.length;
				objdata = objdata+ ",datosEliminarAlmacen:[";
				for (i=0; i<total; i++)
				{
					if (i == 0)
					{
						objdata = objdata +"{'codusu':'"+codusu+
					"','codsis':'SIV','codintper':'"+ arrEliminarAlm[i].get('codalm')+ "'}";
					}
					else
					{
						objdata = objdata +",{'codusu':'"+codusu+
					"','codsis':'SIV','codintper':'"+ arrEliminarAlm[i].get('codalm')+ "'}";
					}
				}
				objdata = objdata + "]";

				arrCenCos = gridCenCos.store.getModifiedRecords();
				objdata = objdata+ ",datosCenCos:[";
				total = arrCenCos.length;
				if (total>0)
				{				
					for (i=0; i < total; i++)
					{
						if (i==0)
						{	
							objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'CFG','codintper':'"+ arrCenCos[i].get('codcencos')+ "','enabled':1}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'CFG','codintper':'"+ arrCenCos[i].get('codcencos')+ "','enabled':1}";
						}
					}			
				}			
				objdata = objdata + "]";			
				total = arrEliminarCenCos.length;
				objdata = objdata+ ",datosEliminarCenCos:[";
				for (i=0; i<total; i++)
				{
					if (i == 0)
					{
						objdata = objdata +"{'codusu':'"+codusu+
					"','codsis':'CFG','codintper':'"+ arrEliminarCenCos[i].get('codcencos')+ "'}";
					}
					else
					{
						objdata = objdata +",{'codusu':'"+codusu+
					"','codsis':'CFG','codintper':'"+ arrEliminarCenCos[i].get('codcencos')+ "'}";
					}
				}
				objdata = objdata + "]";

				arrCtaBan = gridCtaBan.store.getModifiedRecords();
				objdata = objdata+ ",datosCtaBan:[";
				total = arrCtaBan.length;
				if (total>0)
				{				
					for (i=0; i < total; i++)
					{
						banco=arrCtaBan[i].get('codban')+"-"+arrCtaBan[i].get('ctaban');
						if (i==0)
						{	
							objdata = objdata +"{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SCB','codintper':'"+ banco + "','enabled':1}";
						}
						else
						{
							objdata = objdata +",{'codusu':'"+Ext.getCmp('txtcodusuario').getValue()+
							"','codsis':'SCB','codintper':'"+ banco + "','enabled':1}";
						}
					}			
				}			
				objdata = objdata + "]";			
				total = arrEliminarCtaBan.length;
				objdata = objdata+ ",datosEliminarCtaBan:[";
				for (i=0; i<total; i++)
				{
					banco=arrEliminarCtaBan[i].get('codban')+"-"+arrEliminarCtaBan[i].get('ctaban');
					if (i == 0)
					{
						objdata = objdata +"{'codusu':'"+codusu+"','codsis':'SCB','codintper':'"+ banco + "'}";
					}
					else
					{
						objdata = objdata +",{'codusu':'"+codusu+"','codsis':'SCB','codintper':'"+ banco + "'}";
					}
				}
				objdata = objdata + "]}";

			}
		}
		if (continuar)
		{
			objdata= eval('(' + objdata + ')');				
			objdata=JSON.stringify(objdata);
			parametros = 'objdata='+objdata; 
			Ext.Ajax.request({
			url : rutaUsuario,
			params : parametros,
			method: 'POST',
			success: function (resultad, request)
			{ 
				datos = resultad.responseText;
				
				Ext.Msg.hide();
				var datajson = eval('(' + datos + ')');
				if (datajson.raiz.valido==true)
				{
					Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
					irNuevo();  
				}
				else
				{
					Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
					irNuevo();  
				}
			},
			failure: function (result,request) 
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
			}					
			});
		}
	}


/*************************************************************************************
* @Función que elimina un usuario seleccionado.
* @parametros
* @retorno
* @fecha de creación: 21/07/2008
* @autor: Ing.Gusmary Balza.
**************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*************************************************************************************/	
	function irEliminar(item)
	{
		var Result;
		Ext.MessageBox.confirm('Confirmar', '¿Desea eliminar este registro?', Result);
		function Result(btn)
		{
			if(btn=='yes')
			{ 
				if(validarObjetos('txtcodusuario','30','novacio')=='0')
				{					
					Ext.MessageBox.alert('Mensaje','No existen datos para eliminar');
				}
				else
				{		
					obtenerMensaje('procesar','','Eliminando Datos');
					var objdata ={
						'oper': 'eliminar', 
						'codusu':	Ext.getCmp('txtcodusuario').getValue(), 
						'sistema': sistema,
						'vista': vista
					 };					 
					objdata=JSON.stringify(objdata);
					parametros = 'objdata='+objdata;				     
					Ext.Ajax.request({
					url : rutaUsuario,
					params : parametros,
					method: 'POST',
					success: function ( resultad, request )
					{ 
						datos = resultad.responseText;						
						Ext.Msg.hide();
						var datajson = eval('(' + datos + ')');
						if (datajson.raiz.valido==true)						
						{
							Ext.MessageBox.alert('Mensaje',datajson.raiz.mensaje);
							irNuevo();		  
						}
						else
						{
							Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
							irNuevo();
						}
					},
					failure: function ( result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', 'Error al procesar la información'); 
					} 
					});
				}
			}
		};		
	}


/****************************************************************************************
* @Función que llama al catalogo para mostrar los datos de los usuarios.
* @parametros
* @retorno
* @fecha de creación: 21/07/2008
* @autor: Ing. Gusmary Balza.
*****************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
******************************************************************************************/
	function irBuscar(item)
	{	
		var arreglotxt = new Array('txtcodusuario','txtcedula','txtnombre','txtapellido','txtfecnac','txttelefono','txtemail','txtultingreso','cmbestatus','chbadmin','txtnota','chkestblocon');
		var arreglovalores = new Array('codusu','cedusu','nomusu','apeusu','fecnacusu','telusu','email','ultingusu','estatus','admusu','nota','estblocon');
		objCatusuario = new catalogoUsuario();
		objCatusuario.mostrarCatalogo(panel,'fsformusuario',arreglotxt, arreglovalores);
		cambiar = true;		
		Ext.getCmp('txtcodusuario').disable();
		Ext.getCmp('txtpassword').disable();
		Ext.getCmp('txtverpassword').disable();
	}


/*****************************************************************************************************
*Función que imprime un reporte ficha de un usuario seleccionado de acuerdo a un archivo Xml generado.
*@parámetros: 
*@retorna: 
*@fecha de creación:  21/07/2008
*@Autor: Gusmary Balza.	
*****************************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
****************************************************************************************************/	
	function irImprimir(item)
	{
		var objdata ={
			'oper': 'reporteficha',
			'codusu': Ext.getCmp('txtcodusuario').getValue(),
			'sistema': sistema,
			'vista': vista			
		}
		objdata=JSON.stringify(objdata);
		parametros = 'objdata='+objdata; 
		Ext.Ajax.request({
		url : rutaUsuario,
		params : parametros,
		method: 'POST',
		success: function (resultado,request)
		{
			datos = resultado.responseText;
			if(datos!='')
			{
				abrirVentana(datos);
			}			
			else
			{
				Ext.MessageBox.alert('Mensaje', 'No existen datos para imprimir');		
			}
		},
		failure: function ( result, request) 
		{ 
			Ext.MessageBox.alert('Error', result.responseText); 
		} 
		});				
	}
//fin de las operaciones


/********************************************************************************************
*Función que obtiene las constantes de nomina para mostrar en la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*********************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
**********************************************************************************************/	
	function obtenerGridConstantes()
	{
		RecordDefCons = Ext.data.Record.create
		([
			{name: 'codemp'}, 
			{name: 'codnom'}, 
			{name: 'codcons'},
			{name: 'nomcon'},
		]);
		
		var DatosNuevoCons = {'raiz':[{'codemp':'','codnom':'','codcons':'','nomcon':''}]};	
		DataStoreCons =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevoCons),
		reader: new Ext.data.JsonReader({
		    root: 'raiz',               
		    id: 'id'   
		    },
	            RecordDefCons
		    ),
			data: DatosNuevoCons
		});				
		gridCons = new Ext.grid.GridPanel({
			id:'Cons',	
			width:730,
			autoScroll:true,
		    border:true,
		    ds:DataStoreCons,
		    tbar:[agregarCons,quitarCons],
		    cm: new Ext.grid.ColumnModel([
		     new Ext.grid.CheckboxSelectionModel(),
				{header: 'Código de la Nómina', width: 150, sortable: true, dataIndex: 'codnom'},
				{header: 'Código de la Constante', width: 150, sortable: true, dataIndex: 'codcons'},
	            {header: 'Denominación de la Constante', width: 400, sortable: true, dataIndex: 'nomcon'},
			]),
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		                      viewConfig:{
		                      forceFit:true
		                      },
			autoHeight:true,
			stripeRows: true
		});				   		   
		gridCons.render('pest1');		
	}


/************************************************************************************************
*Función que obtiene las nominas para mostrar en la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
**************************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
***************************************************************************************************/	
	function obtenerGridNominas()
	{
		RecordDefNom = Ext.data.Record.create
		([
			{name: 'codemp'}, 
			{name: 'codnom'}, 
			{name: 'desnom'},
		]);		
		var DatosNuevoNom = {'raiz':[{'codemp':'','codnom':'','desnom':''}]};	
		DataStoreNom =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevoNom),
		reader: new Ext.data.JsonReader({
		    root: 'raiz',               
		    id: 'id'   
		    },
	            RecordDefNom
		    ),
			data: DatosNuevoNom
		});				
		gridNom = new Ext.grid.GridPanel({
			id:'Nom',	
			width:730,
			autoScroll:true,
		    border:true,
		    ds:DataStoreNom,
		    tbar:[agregarNom,quitarNom],
		    cm: new Ext.grid.ColumnModel([
		     new Ext.grid.CheckboxSelectionModel(),
				{header: 'Código', width: 200, sortable: true, dataIndex: 'codnom'},
				{header: 'Denominación', width: 500, sortable: true, dataIndex: 'desnom'},
			]),
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		                      viewConfig:{
		                      forceFit:true
		                      },
			autoHeight:true,
			stripeRows: true
		});				   		   
		gridNom.render('pest2');		
	}


/******************************************************************************************
*Función que obtiene las unidades ejecutoras para mostrar en la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*******************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
********************************************************************************************/	
	function obtenerGridUnidades()
	{
		RecordDefUni = Ext.data.Record.create
		([			
			{name: 'codsis'},
			{name: 'coduniadm'}, 
			{name: 'denuniadm'},
		]);		
		var DatosNuevoUni = {'raiz':[{'codsis':'','coduniadm':'','denuniadm':''}]};	
		DataStoreUni =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevoUni),
		reader: new Ext.data.JsonReader({
		    root: 'raiz',               
		    id: 'id'   
		    },
	            RecordDefUni
		    ),
			data: DatosNuevoUni
		});	
		
		var datosSistemas={'raiz':[{'codsis':'SEP','nomsis':'Solicitud de Ejecución Presupuestaria'},
								   {'codsis':'SOC','nomsis':'Ordenes de Compra'},
								   {'codsis':'CXP','nomsis':'Cuentas por Pagar'}]};
		
		record = Ext.data.Record.create([
				{name: 'codsis'},   
				{name: 'nomsis'}
		]);					
		dssistema =  new Ext.data.Store({
				proxy: new Ext.data.MemoryProxy(datosSistemas),
				reader: new Ext.data.JsonReader(
				{
					root: 'raiz',               
					id: 'id'   
				},
				record
				),
				data: datosSistemas			
			 });
			 
		gridUni = new Ext.grid.EditorGridPanel({
			id:'Uni',	
			width:730,
			autoScroll:true,
		    border:true,
		    ds:DataStoreUni,
		    tbar:[agregarUni,quitarUni],
		    cm: new Ext.grid.ColumnModel([
		    new Ext.grid.CheckboxSelectionModel(),
				{header: 'Sistema', width: 100, sortable: true, dataIndex: 'codsis',editor: new Ext.form.ComboBox({
																						name:'sistema',
																						id:'cmbsistema',
																						readOnly:true,
																						emptyText:'Seleccione',
																						displayField:'codsis',
																						valueField:'codsis',
																						typeAhead: true,
																						mode: 'local',
																						triggerAction: 'all',
																						store: dssistema
																						})},								  
				{header: 'Código', width: 200, sortable: true, dataIndex: 'coduniadm'},
				{header: 'Denominación', width: 500, sortable: true, dataIndex: 'denuniadm'},
			]),
			sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
		                      viewConfig:{
		                      forceFit:true
		                      },
			autoHeight:true,
			stripeRows: true
		});			   
		gridUni.render('pest3');
	}


/*********************************************************************************
*Función que obtiene las estructuras presupuestarias para mostrar en la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
**********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*********************************************************************************/	
	function obtenerGridPresupuestos()
	{
		RecordDefPre = Ext.data.Record.create
		([
			{name: 'codest'}, 
			{name: 'codcompleto'},					
			{name: 'nombre'}
		]);		
		var DatosNuevoPre = {'raiz':[{'codest':'','codcompleto':'','nombre':''}]};	
		DataStorePre =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevoPre),
		reader: new Ext.data.JsonReader({
		    root: 'raiz',               
		    id: 'id'   
		    },
				RecordDefPre
		    ),
			data: DatosNuevoPre
	    });				
		gridPre = new Ext.grid.GridPanel({
			id:'Pre',	
			width:730,
			autoScroll:true,
	        border:true,
	        ds:DataStorePre,
	        tbar:[agregarEstPre,quitarEstPre],
	        cm: new Ext.grid.ColumnModel([
	        new Ext.grid.CheckboxSelectionModel(),
	       		{header: 'Código de la Estructura', width: 200, sortable: true, dataIndex: 'codest'},
	       		{header: 'Código de la Estructura', width: 30, sortable: true,hidden:true, dataIndex: 'codcompleto'},
	       		{header: 'Denominación', width: 500, sortable: true, dataIndex: 'nombre'}
			]),
	
				sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
	                        viewConfig:{
	                        forceFit:true
	                        },
				autoHeight:true,
				stripeRows: true
		});				   		   
		gridPre.render('pest4');	
	}


/**********************************************************************************
*Función que obtiene los tipos de personal para mostrar en la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
**********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
************************************************************************************/	
	function obtenerGridPersonal()
	{
		RecordDefPer = Ext.data.Record.create
		([
			{name: 'codemp'}, 
			{name: 'codtippersss'}, 
			{name: 'dentippersss'},
		]);		
		var DatosNuevo={'raiz':[{'codemp':'','codtippersss':'','dentippersss':''}]};	
		DataStorePer =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevo),
		reader: new Ext.data.JsonReader({
		    root: 'raiz',               
		    id: 'id'   
		    },
	            RecordDefPer
		    ),
			data: DatosNuevo
		});		
		gridPer = new Ext.grid.GridPanel({
			id:'Pers',	
			width:730,
			autoScroll:true,
	        border:true,
	        ds:DataStorePer,
	        tbar:[agregarPer,quitarPer],
	        cm: new Ext.grid.ColumnModel([
	         new Ext.grid.CheckboxSelectionModel(),
				{header: 'Código', width: 200, sortable: true, dataIndex: 'codtippersss'},
	            {header: 'Denominación', width: 500, sortable: true, dataIndex: 'dentippersss',editor: new Ext.form.TextField({allowBlank: false})},
			]),
	
			 sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
	                      viewConfig:{
	                      forceFit:true
	                      },
			autoHeight:true,
			stripeRows: true
	    });				   		   
		gridPer.render('pest5');	
	}


/*********************************************************************************
*Función que obtiene los almacenes para mostrar en la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
**********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*********************************************************************************/	
	function obtenerGridAlmacen()
	{
		RecordDefAlm = Ext.data.Record.create
		([
			{name: 'codalm'}, 
			{name: 'nomfisalm'},
		]);		
		var DatosNuevoAlm={'raiz':[{'codalm':'','nomfisalm':''}]};	
		DataStoreAlm =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevoAlm),
		reader: new Ext.data.JsonReader({
		    root: 'raiz',               
		    id: 'id'   
		    },
	            RecordDefAlm
		    ),
			data: DatosNuevoAlm
		});		
		gridAlm = new Ext.grid.GridPanel({
			id:'Almacen',	
			width:730,
			autoScroll:true,
	        border:true,
	        ds:DataStoreAlm,
	        tbar:[agregarAlmacen,quitarAlmacen],
	        cm: new Ext.grid.ColumnModel([
	         new Ext.grid.CheckboxSelectionModel(),
				{header: 'Código', width: 200, sortable: true, dataIndex: 'codalm'},
	            {header: 'Denominación', width: 500, sortable: true, dataIndex: 'nomfisalm',editor: new Ext.form.TextField({allowBlank: false})},
			]),
	
			 sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
	                      viewConfig:{
	                      forceFit:true
	                      },
			autoHeight:true,
			stripeRows: true
	    });				   		   
		gridAlm.render('pest6');	
	}



/*********************************************************************************
*Función que obtiene los almacenes para mostrar en la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
**********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*********************************************************************************/	
	function obtenerGridCentroCosto()
	{
		RecordDefCenCos = Ext.data.Record.create
		([
			{name: 'codcencos'}, 
			{name: 'denominacion'},
		]);		
		var DatosNuevoCenCos={'raiz':[{'codcencos':'','denominacion':''}]};	
		DataStoreCenCos =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevoAlm),
		reader: new Ext.data.JsonReader({
		    root: 'raiz',               
		    id: 'id'   
		    },
	            RecordDefCenCos
		    ),
			data: DatosNuevoCenCos
		});		
		gridCenCos = new Ext.grid.GridPanel({
			id:'Centro de Costos',	
			width:730,
			autoScroll:true,
	        border:true,
	        ds:DataStoreCenCos,
	        tbar:[agregarCenCos,quitarCenCos],
	        cm: new Ext.grid.ColumnModel([
	         new Ext.grid.CheckboxSelectionModel(),
				{header: 'Código', width: 200, sortable: true, dataIndex: 'codcencos'},
	            {header: 'Denominación', width: 500, sortable: true, dataIndex: 'denominacion',editor: new Ext.form.TextField({allowBlank: false})},
			]),
	
			 sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
	                      viewConfig:{
	                      forceFit:true
	                      },
			autoHeight:true,
			stripeRows: true
	    });				   		   
		gridCenCos.render('pest7');	
	}


/*********************************************************************************
*Función que obtiene los almacenes para mostrar en la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
**********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*********************************************************************************/	
	function obtenerGridCuentaBanco()
	{

		RecordDefCtaBan = Ext.data.Record.create
		([
			{name: 'codban'}, 
			{name: 'ctaban'},
		]);		
		var DatosNuevoCtaBan={'raiz':[{'codban':'','ctaban':''}]};	
		DataStoreCtaBan =  new Ext.data.Store({
		proxy: new Ext.data.MemoryProxy(DatosNuevoAlm),
		reader: new Ext.data.JsonReader({
		    root: 'raiz',               
		    id: 'id'   
		    },
	            RecordDefCtaBan
		    ),
			data: DatosNuevoCtaBan
		});		

		gridCtaBan = new Ext.grid.GridPanel({
			id:'Cuentas de Banco',	
			width:730,
			autoScroll:true,
	        border:true,
	        tbar:[agregarCtaBan,quitarCtaBan],
	        ds:DataStoreCtaBan,
	        cm: new Ext.grid.ColumnModel([
	         new Ext.grid.CheckboxSelectionModel(),
				{header: 'Banco', width: 200, sortable: true, dataIndex: 'codban',editor: new Ext.form.TextField({allowBlank: false})},
	            {header: 'Cuenta', width: 500, sortable: true, dataIndex: 'ctaban',editor: new Ext.form.TextField({allowBlank: false})},
			]),
	
			 sm:new Ext.grid.CheckboxSelectionModel({singleSelect:false}),
	                      viewConfig:{
	                      forceFit:true
	                      },
			autoHeight:true,
			stripeRows: true
	    });				   		   
		gridCtaBan.render('pest8');	
	}



/***********************************************************************************
*Función que llama al catalogo de los tipos de personal.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
************************************************************************************/	
	function irAgregarPer()
	{	
		objCatPersonal = new catalogoPersonal();
		objCatPersonal.mostrarCatalogoPersonal(panel,'','', '');	
	
	}


/***********************************************************************************
*Función que selecciona un registro de la grid para eliminarlo.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
***********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
************************************************************************************/	
	function irQuitarPer()
	{
		var personal = gridPer.selModel.selections.keys;
		if(personal.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar los registros?', borrarPersonal);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}			
	}


/**********************************************************************************
*Función que elimina un registro de la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
***********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
***********************************************************************************/	
	function borrarPersonal(btn)
	{
		if (btn=='yes') 
		{
			var filas = gridPer.getSelectionModel().getSelections();
			for (j=0;j<filas.length;j++)
			{			
				personalElim = filas[j].get('codtippersss');
				arrEliminar.push(personalElim);
				gridPer.store.remove(filas[j]);
				Ext.MessageBox.alert('Mensaje', 'Registro eliminado con éxito');
				gridPer.getSelectionModel().clearSelections();
			}
		}	
	}


/*******************************************************************************************
*Función que llama al catalogo de las constantes de nomina.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
********************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
********************************************************************************************/	
	function irAgregarCons()
	{	
		objCatConstantes = new catalogoConstante();
		objCatConstantes.mostrarCatalogoConstante(panel,'','', '');	
	}
	
	
/*************************************************************************************
*Función que selecciona un registro de la grid para eliminarlo.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
***************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
****************************************************************************************/		
	function irQuitarCons()
	{
		var constante = gridCons.selModel.selections.keys;
		if(constante.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar los registros?', borrarConstante);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}			
	}


/***************************************************************************************
*Función que elimina un registro de la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*****************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
******************************************************************************************/	
	function borrarConstante(btn)
	{
		if (btn=='yes') 
		{
			var filas = gridCons.getSelectionModel().getSelections();
			for (j=0;j<filas.length;j++)		
			{			
				constanteElim = filas[j].get('codcons');
				arrEliminarCons.push(constanteElim);
				gridCons.store.remove(filas[j]);
				Ext.MessageBox.alert('Mensaje', 'Registro eliminado con éxito');
				gridCons.getSelectionModel().clearSelections();
			}
		}	
	}		


/**************************************************************************************
*Función que llama al catalogo de las nominas.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
**************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
***************************************************************************************/		
	function irAgregarNom()
	{
		objCatNomina = new catalogoNomina();
		objCatNomina.mostrarCatalogoNomina(panel,'','', '');			
	}


/**************************************************************************************
*Función que selecciona un registro de la grid para eliminarlo.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
***************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
**************************************************************************************/		
	function irQuitarNom()
	{
		var nomina = gridNom.selModel.selections.keys;
		if(nomina.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar el(los) registro(s)?', borrarNomina);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}			
	}


/************************************************************************************
*Función que elimina un registro de la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*************************************************************************************/	
	function borrarNomina(btn)
	{
		if (btn=='yes') 
		{
			var filas = gridNom.getSelectionModel().getSelections();
			for (j=0;j<filas.length;j++)		
			{		
				nominaElim   = filas[j].get('codnom');
				arrEliminarNom.push(nominaElim);
				gridNom.store.remove(filas[j]);
				Ext.MessageBox.alert('Mensaje', 'Registro eliminado con éxito');
				gridNom.getSelectionModel().clearSelections();
			}
		}	
	}		

	
/***********************************************************************************
*Función que llama al catalogo de las unidades ejecutoras.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
***********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
**********************************************************************************/		
	function irAgregarUni()
	{		
		objCatUnidad = new catalogoUnidad();
		objCatUnidad.mostrarCatalogoUnidad(panel,'','', '');		
	}
	
	
/***************************************************************************************
*Función que selecciona un registro de la grid para eliminarlo.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*****************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*******************************************************************************************/		
	function irQuitarUni()
	{
		var unidad = gridUni.selModel.selections.keys;
		if(unidad.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar el(los) registro(s)?', borrarUnidad);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}			
	}


/***************************************************************************************
*Función que elimina un registro de la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
***********************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*******************************************************************************************/	
	function borrarUnidad(btn)
	{
		if (btn=='yes') 
		{
			var filas = gridUni.getSelectionModel().getSelections();
			for (j=0;j<filas.length;j++)		
			{		
				//unidadElim   = gridUni.getSelectionModel().getSelected();
				unidadElim   = filas[j];
				//arrEliminarUni[j] = unidadElim;
				arrEliminarUni.push(unidadElim);
				gridUni.store.remove(filas[j]);
				Ext.MessageBox.alert('Mensaje', 'Registro eliminado con éxito');
				gridUni.getSelectionModel().clearSelections();
				//gridUni.store.commitChanges();				
			}			
		}	
	}		


/************************************************************************************
*Función que llama al catalogo de las estructuras presupuestarias.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
************************************************************************************/	
	function irAgregarEstPre()
	{	
		objCatEstPre = new catalogoEstPre();
		objCatEstPre.mostrarCatalogoEstPre(panel,'','','', '','');
	}
	
	
/************************************************************************************
*Función que selecciona un registro de la grid para eliminarlo.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
**************************************************************************************/		
	function irQuitarEstPre()
	{
		var estpre = gridPre.selModel.selections.keys;
		if(estpre.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar el(los) registro(s)?', borrarEstPre);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}			
	}


/************************************************************************************
*Función que elimina un registro de la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*************************************************************************************/	
	function borrarEstPre(btn)
	{
		if (btn=='yes') 
		{
			var filas = gridPre.getSelectionModel().getSelections();
			for (j=0;j<filas.length;j++)		
			{			
				estpreElim   = filas[j];
				arrEliminarEst.push(estpreElim);
				gridPre.store.remove(filas[j]);
				Ext.MessageBox.alert('Mensaje', 'Registro eliminado con éxito');
				gridPre.getSelectionModel().clearSelections();
			}
		}	
	}	


/************************************************************************************
*Función que llama al catalogo de los almacenes
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
************************************************************************************/	
	function irAgregarAlmacen()
	{	
		objCatAlmacen = new catalogoAlmacen();
		objCatAlmacen.mostrarCatalogoAlmacen(panel,'','','', '','');
	}
	
/************************************************************************************
*Función que selecciona un registro de la grid para eliminarlo.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
**************************************************************************************/		
	function irQuitarAlmacen()
	{
		var almacen = gridAlm.selModel.selections.keys;
		if(almacen.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar el(los) registro(s)?', borrarAlmacen);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}			
	}


/************************************************************************************
*Función que elimina un registro de la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*************************************************************************************/	
	function borrarAlmacen(btn)
	{
		if (btn=='yes') 
		{
			var filas = gridAlm.getSelectionModel().getSelections();
			for (j=0;j<filas.length;j++)		
			{			
				almacenElim   = filas[j];
				arrEliminarAlm.push(almacenElim);
				gridAlm.store.remove(filas[j]);
				Ext.MessageBox.alert('Mensaje', 'Registro eliminado con éxito');
				gridAlm.getSelectionModel().clearSelections();
			}
		}	
	}	

/************************************************************************************
*Función que llama al catalogo de los almacenes
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
************************************************************************************/	
	function irAgregarCenCos()
	{	
		objCatCenCos = new catalogoCentroCostos();
		objCatCenCos.mostrarCatalogoCentroCosto(panel,'','','', '','');
	}
	
/************************************************************************************
*Función que selecciona un registro de la grid para eliminarlo.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
**************************************************************************************/		
	function irQuitarCenCos()
	{
		var centro = gridCenCos.selModel.selections.keys;
		if(centro.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar el(los) registro(s)?', borrarCentroCosto);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}			
	}

/************************************************************************************
*Función que elimina un registro de la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*************************************************************************************/	
	function borrarCentroCosto(btn)
	{
		if (btn=='yes') 
		{
			var filas = gridCenCos.getSelectionModel().getSelections();
			for (j=0;j<filas.length;j++)		
			{			
				CenCosElim   = filas[j];
				arrEliminarCenCos.push(CenCosElim);
				gridCenCos.store.remove(filas[j]);
				Ext.MessageBox.alert('Mensaje', 'Registro eliminado con éxito');
				gridCenCos.getSelectionModel().clearSelections();
			}
		}	
	}	

/************************************************************************************
*Función que llama al catalogo de los almacenes
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
************************************************************************************/	
	function irAgregarCtaBan()
	{	
		objCatCtaBan = new catalogoCuentaBanco();
		objCatCtaBan.mostrarCatalogoCuentaBanco(panel,'','','', '','');
	}
	
/************************************************************************************
*Función que selecciona un registro de la grid para eliminarlo.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
**************************************************************************************/		
	function irQuitarCtaBan()
	{
		var centro = gridCtaBan.selModel.selections.keys;
		if(centro.length > 0)
		{
			Ext.Msg.confirm('Alerta!','Realmente desea eliminar el(los) registro(s)?', borrarCuentaBanco);
		} 
		else 
		{
			Ext.Msg.alert('Alerta!','Seleccione un registro para eliminar');
		}			
	}

/************************************************************************************
*Función que elimina un registro de la grid.
*@parámetros: 
*@retorna: 
*@fecha de creación:  09/10/2008
*@Autor: Gusmary Balza.	
*************************************************************************************
* @fecha modificacion
* @autor 
* @descripcion
*************************************************************************************/	
	function borrarCuentaBanco(btn)
	{
		if (btn=='yes') 
		{
			var filas = gridCtaBan.getSelectionModel().getSelections();
			for (j=0;j<filas.length;j++)		
			{			
				CtaBanElim   = filas[j];
				arrEliminarCtaBan.push(CtaBanElim);
				gridCtaBan.store.remove(filas[j]);
				Ext.MessageBox.alert('Mensaje', 'Registro eliminado con éxito');
				gridCtaBan.getSelectionModel().clearSelections();
			}
		}	
	}	
