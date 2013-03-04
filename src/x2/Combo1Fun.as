
	import flash.events.Event;
	import mx.collections.ArrayList;
	import spark.layouts.VerticalLayout;
	import x2.*;

	
	//private var i_value:Object;
	//private var ib_dirty:Boolean = false;		//field is changed.
	//private var ib_dsDirty:Boolean = false;		//dataProvider property is changed
	//private var i_ds:Object;
	
	private function init(p_event:Event=null):void{
		/*
		if (this.layout == null)
			return;
		
		var t_layout:VerticalLayout = VerticalLayout(this.layout);
		t_layout.gap = 0;
		t_layout.requestedMaxRowCount = 10;
		t_layout.horizontalAlign = "contentJustify";
		*/
	}			

	/*
	//show combobox item
	private function zz_showValue(p_value:Object):void {
		
		//keep or will get error !!
		//if (this.dataProvider == null || this.dataProvider[0] == null){
		//this.selectedIndex = -1;
		//return;
		//}
		
		
		if ((p_value != null) && (this.dataProvider != null)){
			var ta_row:Array = ArrayList(this.dataProvider).source as Array;
			for (var i:int=0; i<ta_row.length; i++){
				if (ta_row[i] != null && p_value == ta_row[i].data){
					this.selectedIndex = i; 
					return;
				}
			}
		}
		
		//case of not found !
		//if (!this.editable){
		if (!this.enabled){
			this.selectedIndex = -1;
		}
	}    
	*/
	
	//2011-5-24
	//for provide value property
	public function get value(): Object{
		return (this.selectedItem == null) ? null : this.selectedItem.data;
	}
	
	
	//for provide value property
	public function set value(p_value:Object) : void {
		Fun.comboSetValue(this, p_value);
		
		/*
		if (this.dataProvider == null)
			return;
		else if (p_value == null){
			this.selectedIndex = -1;
			return;
		}
		
		// case of normal
		// 不可直接使用 dataProvider (flex 4.5)
		var ta_row:Array = ArrayList(this.dataProvider).source as Array;
		for (var i:int=0; i<ta_row.length; i++){
			if (ta_row[i] != null && p_value == ta_row[i].data){
				this.selectedIndex = i; 
				return;
			}
		}
		*/
	}
	
	
	/* temp remark
	override public function set dataProvider(p_ds:Object):void {
	i_ds = p_ds;
	ib_dsDirty = true;
	this.invalidateProperties();	//will call commitProperties
	}
	*/
	
	/*
	//update property immediately.
	override protected function commitProperties():void {
		super.commitProperties();
		
		if (ib_dsDirty)    {
			//temp remark
			//super.dataProvider = i_ds;
			ib_dsDirty = false;
			ib_dirty = true;	//set !!
		}
		
		if (ib_dirty) {
			showValue(i_value);
			ib_dirty = false;
		}
		
		
		//if (this.editable && this.value != i_value){
		//this.value = i_value;
		//}		
	} 
	*/