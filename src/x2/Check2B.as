package x2
{
	
	import flash.events.Event;
    import mx.events.FlexEvent;    
	

	public class Check2 extends Check1
	{
		
		public var i_value:Object;	//必須宣告成 public, 在 DataGrid 類別會用到這個屬性, 否則會發生錯誤 !!				
		private var i_info:Object;

		
		public function Check2()
		{
			super();
			
			this.addEventListener(FlexEvent.INITIALIZE, init);
			this.addEventListener(Event.CHANGE, change);
			this.addEventListener(FlexEvent.DATA_CHANGE, dataChange);
			
		}
	

		private function init(p_event:Event=null):void{
			//initialize datagridcolumn			
			i_info = Fun.initDW2Field(this);
			i_info.col.editorDataField = "i_value"; 
			if (i_info.dw2 != null){
				this.setStyle("paddingLeft", 3);
				this.addEventListener(Event.RENDER, setStatus);
			}			
		}
		

		//set status
		private function setStatus(p_event:Event=null):void{
			(i_info.dw2 as DW2).setSubStatus(this);
		}

        
		//user change value.
		private function change(p_event:Event=null):void{
			i_value = (this.selected) ? gYes : gNo;
			//this.data[i_info.fid] = i_value;
			if (i_info.box){
				(this.parent as Object).data[i_info.fid] = i_value;
			}else{
				this.data[i_info.fid] = i_value;				
			}				
			
			//if (gbEdit){
			if (i_info.dw2 != null){
				(i_info.dw2 as DW2).checkField(i_info.col);
			} 
		}	   
	   	
   		 
		//一筆資料的欄位值改變時會觸發這個事件
		//Dispatched when the data property changes.		
		private function dataChange(p_event:Event=null):void{
			var t_data:Object = (i_info.box) ? (this.parent as Object).data : this.data;			
			//if (this.data != null){
			if (t_data != null){
				i_value = t_data[i_info.fid];
                this.selected = (i_value == gYes) ? true : false;
			}
		}      	 
				
	}
}