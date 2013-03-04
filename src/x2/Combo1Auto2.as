
package x2{
	import flash.events.Event;
	import flash.events.FocusEvent;
	//import flash.events.MouseEvent;
	
	//import mx.controls.ComboBox;
	
	
	
	public class Combo1Auto2 extends Combo1
	{
		//private var is_text:String = "";
		//private var _caseSensitiveSearching : Boolean = true;
		
		public function Combo1Auto()
		{
			super();   
			
			this.editable = true;
			
			//設定 iconColor 為紅色
			this.setStyle("iconColor", 0xFF0000);	
			
			if (this.rowCount < 10){
				this.rowCount = 10;
			}
		}
		
		/*
		public function set caseSensitiveSearching (bool : Boolean) : void {
			_caseSensitiveSearching = bool;      
		}
		
		public function get caseSensitiveSearching () : Boolean {
			return _caseSensitiveSearching;      
		}
		*/
		
		
		override protected function textInput_changeHandler(event:Event):void {
			//is_text = this.textInput.text;
			if (!this.dropdown.visible){
				this.open();
			}
			
			// variables used in the loop
			var ts_label:String = null;
			var tn_row:int = 0;
			var tb_find:Boolean = false;
			var ts_text:String = this.textInput.text.toLowerCase();
			
			//if ( caseSensitiveSearching == false ) 
			//ts_text = ts_text.toLowerCase();
			
			
			// using a for each loop on dataProvider does not strongly 
			// couple to it only being an ArrayCollection... simlar 
			// with weak typing on the items in the set
			for each (var t_item:Object in this.dataProvider) 
			{
				// using itemToLabel() checks a few things like
				// if the item is a String, or it there&apos;s a 
				// labelFunction being used
				ts_label = this.itemToLabel(t_item);
				
				/*
				// if searching should not be case sensitive
				// do a toLowerCase() on label
				if ( this.caseSensitiveSearching == false )
				{
					ts_label = ts_label.toLowerCase();
				}
				*/
				
				// find the first item that starts with ts_text
				// if there&apos;s a match, break out of the loop
				if (ts_label.substr(0, ts_text.length).toLowerCase() == ts_text){
					this.dropdown.selectedIndex = tn_row;
					this.dropdown.scrollToIndex(tn_row);
					tb_find = true
					break;
				}
				tn_row++;
			}
			
			
			// if there was no match found set selectedIndex to -1 
			// (unselect the list)
			if (!tb_find){
				this.dropdown.selectedIndex = -1;
			}
		}
		
		/*
		override public function close(p_event:Event=null):void{ 
			super.close(p_event);
			if (this.text == ""){
				this.selectedIndex = 0;
			}
			this.editable = false;
		}
		*/
		
		private var ib_focus:Boolean = false;	//防止 focusOut 觸發2次
		override protected function focusOutHandler(p_event:FocusEvent):void
		{
			if (!ib_focus){
				return;
			}
			
			trace("focus out");
			ib_focus = false;
			super.focusOutHandler(p_event);
			
			this.text = (this.selectedItem) ? this.selectedItem.label : (this.textInput.text + "(錯誤!)");
			/*
			if (this.text == ""){
				this.selectedIndex = 0;
			}
			*/
			
			//this.textInput.selectionEndIndex = this.textInput.width;
			//this.textInput.selectionAnchorPosition = this.textInput.width;			
			//this.editable = false;     
		}
		
		
		override protected function focusInHandler(p_event:FocusEvent):void
		{
			//trace("focus in");
			ib_focus = true;
			super.focusInHandler(p_event);
			
			//this.editable = true;
			//this.textInput.setFocus();
			Fun.focusField(null, this.textInput, false);
			//this.open();
			
		}			
	}
}
