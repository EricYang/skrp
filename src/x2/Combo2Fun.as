	/* old prop
	implements="mx.controls.listClasses.IDropInListItemRenderer"
	initialize="init(event)"
	rowCount="10"
	dataChange="dataChange()" 
	render="setStatus()"
	*/
	
	//change="change()"
	
	
	/** 
	 * 說明 :
	 *  1.處理 dataChange 事件和改寫 data getter/setter 的效果相同.
	 *  2.必須設定 editorDataField = "value", 才能確定資料正確顯示在畫面上 !!	 
	 */
	
	//	focusIn="focusIn()"
	//	focusOut="focusOut()"
	
	//import mx.controls.listClasses.BaseListData;
	import flash.events.Event;
	
	import mx.events.FlexEvent;
	
	import spark.layouts.VerticalLayout;
	
	import x2.*;
	
	
	//private var _listData:BaseListData;
	
	//[Bindable]	
	//private var i_row:Object;		//用來對應 datagrid.data
	//[Bindable("dataChange")]
	// a getter method.

	[Bindable]
	private var i_info:Object;	//

	//[Bindable]
	//private var is_fid:String;	//欄位代碼
/*
	public function get listData():BaseListData
	{
		return _listData;
	}
	
	public function set listData(value:BaseListData):void
	{
		_listData = value;
	}
*/

	//private var i_dw2:DW2;		//access DW2 directly for more effective !!
	//private var is_fid:String;
	
	//private var ib_change:Boolean = true;
	//private var ib_init:Boolean = false;
	
	
	//editorDataField of dataGridColumn
	//public var i_value:Object;	
	
	private function init(p_event:Event):void{
		//this.init(p_event);
		
		this.percentWidth = 98;
		this.percentHeight = 98;
		
		/*
		var t_layout:VerticalLayout = VerticalLayout(this.layout);
		t_layout.gap = 0;
		t_layout.requestedMaxRowCount = 10;
		t_layout.horizontalAlign = "contentJustify";
		*/
		
		
		//this.addEventListener(FlexEvent.DATA_CHANGE, dataChange);
		
		i_info = Fun.initDW2Field(this);
		//is_fid = i_info.fid;
		//i_row = i_info.row.data;
		
		//Fun.setItem(this, i_info.row.data[i_info.fid]);
		//Fun.setItem(this, i_info.row.data[i_info.fid]);
		//var t_bind:ChangeWatcher = BindingUtils.bindSetter(bindValue, this, "value");			
		//BindingUtils.bindProperty(this, "value", i_info.row.data, ["i_info.fid"]);			
		//BindingUtils.bindProperty(i_info.row.data, "i_info.fid", this, "value");			
			
		//i_dw2 = t_info.dw2;
		//is_fid = t_info.fid;
		
		//var t_col:Object = this.styleName;
		
		//spark gridColumn 沒有這個屬性 !!
		//i_info.col.editorDataField = "value"; 
		
		//使用者選取不同值的時候, 會觸發 change event
		//temp remark
		//if (!this.editable){
		//this.addEventListener(Event.CHANGE, change);
		//}
		
	}

	/*
	private function bindValue(p_value:String):void {
		//Object(this).value = i_info.row.data[i_info.fid];
	}
	*/

	//欄位為 editable時, 必須使用 focusIn/focusOut 的方式來控制 change event
	private function focusIn(p_event:Event):void{
		DW2(i_info.dw2).fieldFocusIn(p_event);            
	}
	
	
	//will triggerred twice, filter out one !		
	private function focusOut(p_event:Event):void{
		//update field value to dataProvider, important !!
		//temp remark
		/*
		if (i_info.box){
		//temp change
		//Object(this.parent).data[i_info.fid] = this.value;
		//Object(this.parent).data[i_info.fid] = this.selectedItem.data;
		//var t1:String = "t1";
		}else{
		//temp change
		//this.data[i_info.fid] = this.value;				
		//Object(this).data[i_info.fid] = this.selectedItem.data;				
		//var t2:String = "t1";
		}
		*/
		DW2(i_info.dw2).fieldFocusOut(p_event, false);		
	}
	
	
	//set status
	private function setStatus():void{
		DW2(i_info.dw2).setSubStatus(this);
		/*
		if (this.enabled != (i_info.dw2 as DW2).gbStatus){
		this.enabled = (i_info.dw2 as DW2).gbStatus;
		}
		*/
	}
	
	/*
	private var i_value2:Object;
	private function focusIn():void{
	ib_change = false;
	
	if (this.selectedItem != null){
	i_value2 = this.selectedItem.data;
	}
	
	}		
	
	
	//restore cell value	   
	private function focusOut():void{
	//this.data[i_info.fid] = i_value;
	if (this.value == null){
	//this.value = i_value2;
	data[i_info.fid] = i_value2;
	}
	}
	*/	   
	
	
	//user change value.
	private function change(p_event:Event):void{
		if (this.selectedItem == null){
			return;
		}
		
		//ib_change = true;
		
		//update comboBox data
		//this.data[i_info.fid] = this.value;
		/* temp remark
		if (i_info.box){
		(this.parent as Object).data[i_info.fid] = this.value;
		}else{
		this.data[i_info.fid] = this.value;				
		}
		*/

		//here
		//i_info.row.data[i_info.fid] = this.selectedItem.data;
		i_info.row.data[i_info.fid] = this.selectedItem.data;
		//this.data[i_info.fid] = i_value;			
		//i_value = this.value;
		
		//call parent DataGrid function
		//(i_info.dw2 as DW2).checkField(this.styleName); 
		DW2(i_info.dw2).checkField(i_info.col); 
	}	   
	

	//spark ComboBox/DropDownList 元件沒有 value 屬性, 必須實作 for Fun.getItem()/setItem()
	//setter
/*
	public function set value(p_value:Object):void{
		this.selectedValue = p_value;
	}
	
	//getter
	public function get value():Object{
		return (this.selectedItem == null) ? null : this.selectedItem.data;
	}
*/
	
	//called when combobox data property is changed. (when load data into combobox)		
	//private function dataChange(p_event:Event):void{
	private function dataChange():void{
		//var t_data:Object;
		
		//here
		//var t_value:Object = i_info.row.data[i_info.fid];
		var t_value:Object = i_info.row.data[i_info.fid];
		if (t_value == null){
			this.selectedIndex = -1;
			return;
		}
		
		
		//set selectedIndex
		try {
			for (var i:int=0; i<this.dataProvider.length; i++){
				if (t_value == this.dataProvider[i].data){
					this.selectedIndex = i;
					return;
				}
			}
		}catch (e:Error){
			//do nothing
		}
		
		//ib_change = false;
		
		//case of not found. 
		this.selectedIndex = -1;
	}     
