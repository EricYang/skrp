/** 操作模式 :
 *    主畫面與功能畫面切換  
 */

	import mx.collections.ArrayCollection;
	import mx.controls.SWFLoader;
	
	import x2.*;
	
	import x2.LabelImage;
	
	//[Bindable]
	//private var iR:Object;
	
	[Bindable]
	private var ia_list:ArrayCollection = new ArrayCollection();
	
	private var ia_menu:Array;
	//private var ia_child:Array = [];
	
	//current selected group Id
	private var is_groupId:String;
	
	/*
	private function whenOpen():Boolean{
		in_maxApp = 1;
		
		//setLang();
		//setMenu();	//必须在 whenOpen() 执行 !!
		return true;
	}
	*/
	
	/*
	private function init():void{
	//=== 必要的初始化设定 ===
	Fun2.wMain = this;
	Fun.aKeyByte0 = Fun.getKeyByte();		//set first !!
	Fun.init();
	//======
	
	showMain();
	
	iR = Fun.getLang("Main");
	setMenu();
	
	//ia_menu.filterFunction = filterItems;
	//tile1.dataProvider = ia_menu;
	showGroup("staff0");
	}
	
	private function fnT1():void{
	Fun.sync("t1", "t2.php");
	}
	*/
	
	//显示主画面
	public function showMain():void{
		/*
		itemArea.visible = false;
		mainArea.visible = true;
		mainArea.x = 0;
		mainArea.y = 0;
		mainArea.percentHeight = 100;
		mainArea.percentWidth = 100;
		*/
		mainArea.x = 0;
		mainArea.y = 0;
		contractItem.play();
		expandMain.play();				
	}
	
	
	//open item function
	private function openApp(ps_item:String):void{
		/*
		mainArea.visible = false;
		
		itemArea.visible = true;				
		itemArea.x = 0;
		itemArea.y = 0;
		itemArea.percentHeight = 100;
		itemArea.percentWidth = 100;
		*/
		if (!itemArea.visible){
			itemArea.visible = true;				
		}				
		itemArea.x = 0;
		itemArea.y = 0;
		contractMain.play();
		expandItem.play();
		
		//load swf
		var t_swf:SWFLoader = new SWFLoader();
		t_swf.x = 0;
		t_swf.y = 0;
		t_swf.percentHeight = 100; 
		t_swf.percentWidth = 100;	    
		var ts_time:String = new Date().getTime().toString();	//for no cache !!	    
		t_swf.load(ps_item+".swf?tt="+ts_time);
		
		if (itemArea.numChildren > 0){
			itemArea.removeChildAt(0);
		}
		itemArea.addChild(t_swf);
		
	}
	
	
	private function showGroup(ps_groupId:String):void{
		//remove all old items
		ia_list.removeAll();
		
		for (var i:int=0; i<ia_menu.length; i++){
			if (ia_menu[i].groupId == ps_groupId){
				ia_list.addItem(ia_menu[i]);
			}					
		}
		
		/*
		var tn_len:int = (tile1.dataProvider == null) ? 0 : tile1.dataProvider.length; 
		for (var i:int=tn_len-1; i>=0; i--){
		tile1.getChildAt(i).visible = false;
		}
		*/
		//tile1.getChildAt(0).visible = false;
		
		//is_groupId = ps_groupId;
		//ia_menu.filterFunction = filterItems;
		//ia_menu.refresh();
		
		//trace(ObjectUtil.toString(tile1));
		
		//tile1.getChildAt(0).visible = true;
		
		/*
		//remove
		var tn_len:int = (tile1.dataProvider == null) ? 0 : tile1.dataProvider.length; 
		for (var i:int=tn_len; i>=0; i--){
		tile1.removeChildAt(i);
		}
		
		//add
		for (i=0; i<ia_menu.length; i++){
		tile1.addChild(ia_menu[i]);
		}
		*/
	}
	
	/*
	//filter items
	private function filterItems(p_row:Object):Boolean{
	return (p_row.groupId == is_groupId);
	//Fun.msg("I", String(pn_menu));
	}			
	*/
