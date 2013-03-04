/** 操作模式 :
 *    主畫面與功能畫面切換  
 */

	import mx.collections.ArrayCollection;
	import mx.controls.SWFLoader;	
	import spark.components.Group;	
	import x2.*;
	import x2.irLabelImage;


	public function showMain():void{
		/*
		itemArea.visible = false;
		mainArea.visible = true;
		mainArea.x = 0;
		mainArea.y = 0;
		mainArea.percentHeight = 100;
		mainArea.percentWidth = 100;
		*/
		//mainArea.x = 0;
		//mainArea.y = 0;
		
		//itemMin.play();
		//mainMax.play();
		areaMain.visible = true;
		areaItem.visible = false;
		areaSubItem.visible = false;
	}
	
	
	/**
	 * open item function
	 * @param pb_item true(show item), false(show sub item)
	 */ 
	public function openApp(p_item:Object, pb_item:Boolean=true):void{
		/*
		mainArea.visible = false;
		
		itemArea.visible = true;				
		itemArea.x = 0;
		itemArea.y = 0;
		itemArea.percentHeight = 100;
		itemArea.percentWidth = 100;
		*/
		setItemGlobal(p_item);

		var t_area:Group = pb_item ? areaItem : areaSubItem;
		if (!t_area.visible){
			t_area.visible = true;				
		}				
		//itemArea.x = 0;
		//itemArea.y = 0;
		//mainMin.play();
		//itemMax.play();
		
		//load swf
		var t_swf:SWFLoader = new SWFLoader();
		t_swf.x = 0;
		t_swf.y = 0;
		t_swf.percentHeight = 100; 
		t_swf.percentWidth = 100;	    
		//var ts_time:String = new Date().getTime().toString();	//for no cache !!	    
		//t_swf.load(p_item.data+".swf?tt="+ts_time);
		var ts_swf:String = (p_item.swf != null) ? p_item.swf : p_item.data;  
		t_swf.load(ts_swf+".swf?ver="+Fun2.csVer);

		/*
		if (itemArea.numChildren > 0){
			itemArea.removeChildAt(0);
		}
		*/
		if (t_area.numElements > 0){
			t_area.removeElementAt(0);
		}
		t_area.addElement(t_swf);
		
		showItem(pb_item);
	}


	/**
	 * open sub item application, called by openApp
	 * @param pb_item true(show item), false(show sub item)
	 */ 			
	public function showItem(pb_item:Boolean=true):void{
		//mainMin.play();
		//itemMax.play();
		areaMain.visible = false;
		areaItem.visible = pb_item;
		areaSubItem.visible = !pb_item;
	}

	
	private function zz_showItems(pa_menu:Array, ps_groupId:String, pa_item:Array):void{
		//remove all old items
		pa_item.removeAll();		
		for (var i:int=0; i<pa_menu.length; i++){
			if (pa_menu[i].groupId == ps_groupId){
				pa_item.addItem(pa_menu[i]);
			}					
		}
	}
	
	/*
	//filter items
	private function filterItems(p_row:Object):Boolean{
	return (p_row.groupId == is_groupId);
	//Fun.msg("I", String(pn_menu));
	}			
	*/
public function openAppByName(ps_app:String):void{
	var t_item:Object = findApp(ps_app);
	if (t_item != null){
		setItemGlobal(t_item);
		openApp(t_item);
	}
}

private function findApp(ps_app:String, pa_item:Array=null):Object{
	var ta_item:Array;
	if (pa_item != null){
		ta_item = pa_item;
	}else{
		ta_item = ia_menu;
	}
	
	for (var i:int=0;i<ta_item.length;i++){
		var t_item:Object = ta_item[i];
		if (t_item.children == null){
			if (t_item.data == ps_app){
				return t_item; 
			}
		}else{
			var t_item2:Object = findApp(ps_app, t_item.children);
			if (t_item2 != null){
				return t_item2;
			} 
		}    
	}
	
	//case of not find.
	return null;
}
