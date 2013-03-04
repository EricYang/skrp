/**
 * GridColumn 沒有視覺化輸出, 所以使用 as.
 */ 
package x2
{
	import flash.events.MouseEvent;	
	//import mx.controls.dataGridClasses.DataGridColumn;
	import spark.components.gridClasses.GridColumn;
	
	
	//[Event(name="click", type="flash.events.MouseEvent")]

	public class CheckHeaderColumn extends GridColumn
	{
		
		public var fClick:Function ;
		//public var bSelected:Boolean = false;
		
		public function CheckHeaderColumn(ps_colName:String=null)
		{
			super(ps_colName);
			
			//addEventListener(MouseEvent.CLICK, fnClick);
		}
		
		
		private function fnClick(p_event:MouseEvent):void{
			//if (fClick != null){
			//	fClick(bSelected);
			//}
			
			Fun.msg("I", "column click");
		}
		
	}
}