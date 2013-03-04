package x2{
	
	import mx.controls.DataGrid;
	
		
	public class zz_StruDW{

		public var deleteFirst:Boolean = false;	//(DW2) true(delete all then add new), false(default)
		public var editable:Boolean = false;	//editable(default) or not.
		public var updatable:Boolean = false;	//update(default) to db or not.
		//
		public var page:int = 0;	//tab page num(), default 0.
		public var upDW:Object;		//(2nd DW), parent DW object, if null will not retrieve record.
		public var grid:DataGrid;	//(DW2), optional, dataGrid object, if null, no input function.
		//
		public var autos:Array;		//fid array of auto field id array, order is ["creator","created","reviser","revised"], will convert to field object if need.
		public var keys:Array;		//fid array of PK for update record, will conver to field object, could empty !!
		public var upQKeys:Array;	//fid array, query key field name in parent DW.
		public var qKeys:Array;		//fid array, will conver to query key field objects.
		//
		public var inputType:String = "U";	//default input type, default is "U".
		public var dataType:String = "S";	//default data type, default is "S".
		public var mapping:Object = false;	//default mapping flag, default is false.
		//
		public var items:Array;		//field item, elements :
	}
}//x2