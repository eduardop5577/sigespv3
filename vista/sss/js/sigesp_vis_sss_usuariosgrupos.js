/*******************************************************************************
* @Reporte de usuarios grupos.
* @Archivo javascript el cual contiene los componentes del reporte de usuarios grupos.
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

ruta =  '../../controlador/sss/sigesp_ctr_sss_reportes.php'; 
pantalla = 'usuariosgrupos';
var panel='';
barraherramienta    = true;
Ext.onReady
(
	function()
	{
		Ext.QuickTips.init();
		// turn on validation errors beside the field globally
		Ext.form.Field.prototype.msgTarget = 'side';
		
		Xpos = ((screen.width/2)-(550/2)); 
		Ypos = ((screen.height/2)-(550/2));
		panel = new Ext.FormPanel({
			title: 'Reporte de Usuarios/Grupo',
			bodyStyle:'padding:5px 5px 0px',
			style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
			width:550,
			tbar: [],
			items:[{
				xtype:'fieldset',
				title:'Tipo de Busqueda',
				id:'fsbusqueda',
				autoHeight:true,
				autoWidth:true,
				cls :'fondo',		
				items:[{  
					xtype:'textfield',
					fieldLabel:'Usuario',
					name:'codigo del usuario',
					readOnly:true,
					id:'txtcodusuario',
					width:150
				},{
					xtype:'button',
					id:'btnBuscarUsuario',
					handler: irBuscarUsuario,
					iconCls: 'bmenubuscar',
					tooltip: 'Buscar un usuario',
					style:'position:absolute;left:275px;top:28px',
					width:50
				},{
					xtype:'textfield',
					name:'nombre del usuario',
					id:'hidnomusuario',
					disabled:true,
					hideLabel:true,
					width:100,
					style:'position:absolute;left:290px;top:-25px;border:none',
				},{
					xtype:'textfield',
					name:'apellido del usuario',
					id:'hidapeusuario',
					disabled:true,
					hideLabel:true,
					width:100,
					style:'position:absolute;left:390px;top:-29px;border:none',
				},{
					xtype:'textfield',
					fieldLabel:'Grupo',
					name:'nombre del grupo',
					readOnly:true,
					id:'txtnombre',
					width:150
				},{
					xtype:'button',
					id:'btnBuscarGrupo',
					handler: irBuscarGrupo,
					iconCls: 'bmenubuscar',
					tooltip: 'Buscar un grupo',
					style:'position:absolute;left:275px;top:62px',
					width:50
				}]
			},{
				xtype:'fieldset',
				title:'Ordenado Por',
				id:'fsorden',
				autoHeight:true,
				autoWidth:true,
				cls:'fondo',
				bodyStyle:'padding:5px 100px 0px',
				items:[{	
					xtype:'radio',
					fieldLabel:'Usuario',
					name:'usuario',					
					id:'rdusuario'
				},{
					xtype:'radio',
					fieldLabel:'Grupo',
					name:'grupo',
					id:'rdgrupo'				
				}]
				
			}]
		});
		panel.render(document.body);
		Ext.getCmp('btnBuscarUsuario').addListener('click',deshabilitarGrupo);
		Ext.getCmp('btnBuscarGrupo').addListener('click',deshabilitarUsuario);
			
}); //fin


/*****************************************************************************
* @Función para seleccionar el usuario como parámetro para ordenar.
* @fecha de creación: 27/08/2008
* @autor: Ing. Gusmary Balza.
************************************
* @fecha modificacion
* @autor 
* @descripcion
*******************************************************************************/
	function seleccionarUsuario()
	{
		Ext.getCmp('rdgrupo').setValue(true);
		Ext.getCmp('rdusuario').setValue(false);
	}	
	
	

/******************************************************************************
* @Función para seleccionar el grupo como parámetro para ordenar.
* @fecha de creación: 27/08/2008
* @autor: Ing. Gusmary Balza.
************************************
* @fecha modificacion
* @autor 
* @descripcion
*******************************************************************************/				
	function seleccionarGrupo()
	{
		Ext.getCmp('rdgrupo').setValue(false);
		Ext.getCmp('rdusuario').setValue(true);
	}	


/******************************************************************************
* @Función para deshabilitar las opciones para seleccionar el grupo.
* @fecha de creación: 27/08/2008
* @autor: Ing. Gusmary Balza.
************************************
* @fecha modificacion
* @autor 
* @descripcion
*******************************************************************************/	
	function deshabilitarGrupo()
	{
		Ext.getCmp('txtnombre').setValue('');	
		Ext.getCmp('btnBuscarGrupo').disable();
	}
	

/******************************************************************************
* @Función para deshabilitar las opciones para seleccionar el usuario.
* @fecha de creación: 27/08/2008
* @autor: Ing. Gusmary Balza.
************************************
* @fecha modificacion
* @autor 
* @descripcion
*******************************************************************************/	

	function deshabilitarUsuario()
	{
		Ext.getCmp('txtcodusuario').setValue('');
		Ext.getCmp('hidnomusuario').setValue('');
		Ext.getCmp('hidapeusuario').setValue('');
		Ext.getCmp('btnBuscarUsuario').disable();
	}
	
/******************************************************************************
* @Función para buscar en el catalogo el usuario seleccionado.
* @fecha de creación: 27/08/2008
* @autor: Ing. Gusmary Balza.
************************************
* @fecha modificacion
* @autor 
* @descripcion
********************************************************************************/	
	function irBuscarUsuario()
	{
		var arreglotxt = new Array('txtcodusuario','hidnomusuario','hidapeusuario');		
		var arreglovalores = new Array('codusu','nomusu','apeusu');				
		objCatusuario = new catalogoUsuario();
		objCatusuario.mostrarCatalogo(panel,'fsbusqueda',arreglotxt, arreglovalores);
		seleccionarUsuario();
	}
		
/*******************************************************************************
* @Función para buscar en el catalogo el grupo seleccionado.
* @fecha de creación: 27/08/2008
* @autor: Ing. Gusmary Balza.
************************************
* @fecha modificacion
* @autor 
* @descripcion
*******************************************************************************/		
	function irBuscarGrupo()
	{
		var arreglotxt = new Array('txtnombre');		
		var arreglovalores = new Array('nomgru');	
		objCatGrupo = new catalogoGrupo();
		objCatGrupo.mostrarCatalogo(arreglotxt, arreglovalores);
		seleccionarGrupo();
	}
	
	
/********************************************************************************
* @Función para limpiar los campos
* @fecha de creación: 27/08/2008
* @autor: Ing. Gusmary Balza.
************************************
* @fecha modificacion
* @autor 
* @descripcion
********************************************************************************/			
	function irCancelar()
	{
		Ext.getCmp('txtcodusuario').setValue('');
		Ext.getCmp('txtnombre').setValue('');
		Ext.getCmp('hidnomusuario').setValue('');
		Ext.getCmp('hidapeusuario').setValue('');
		Ext.getCmp('rdusuario').enable();
		Ext.getCmp('rdgrupo').enable();
		Ext.getCmp('btnBuscarGrupo').enable();
		Ext.getCmp('btnBuscarUsuario').enable();
	}


/********************************************************************************
* @Función para mostrar el reporte de los permisos por usuario y sistema.
* @fecha de creación: 27/08/2008
* @autor: Ing. Gusmary Balza.
************************************
* @fecha modificacion
* @autor 
* @descripcion
*******************************************************************************/		
	function irImprimir()
	{
		continuar = false;
		if (Ext.getCmp('txtcodusuario').getValue()=='' && Ext.getCmp('txtnombre').getValue()=='')
		{
			Ext.Msg.alert('Mensaje','Seleccione el tipo de búsqueda');
		}
		else
		{			
			continuar = true;
			orden = 'codusu';
			if (Ext.getCmp('rdgrupo').getValue()==true)
			{
				orden = 'nomgru';
			}
			if (Ext.getCmp('txtcodusuario').getValue()=='')
			{
				codusu = ''; 
			}
			else
			{
				codusu = Ext.getCmp('txtcodusuario').getValue();
			}
			if (Ext.getCmp('txtnombre').getValue()=='')
			{
				nomgru = ''; 
			}
			else
			{
				nomgru = Ext.getCmp('txtnombre').getValue();
			}
			if (continuar)
			{
				window.open("reportes/sigesp_sss_rpp_usuariosgrupo.php?codusu="+codusu+"&nomgru="+nomgru+"&orden="+orden+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=50,top=50,location=no,resizable=yes");
			} 
		}		   
	}
