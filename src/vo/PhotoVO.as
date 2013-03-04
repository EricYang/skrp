package vo
{
	import flash.net.FileReference;
	
	[Bindable]
	public class PhotoVO
	{
		public var uid:String;
		public var progress:String="0%";
		public var name:String;
		public var size:Number;
		public var file:FileReference;
		public var des:String="單擊修改";
	}
}