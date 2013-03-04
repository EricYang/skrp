package 
{
	import flash.display.Bitmap;

	public class LargeImage
	{
		public var bitmap:Bitmap;
//		public var image:Image;
		public var title:String;
		public var id:int = -1;
		
		public function LargeImage(title:String, id:int, bitmap:Bitmap)
		{
			this.id = id;
			this.bitmap = bitmap;
			this.bitmap.smoothing = true;
			//this.bitmap.width = 200;
			//this.bitmap.height = 200;
//			this.image = image; 
			this.title = title;
		}
	}
}