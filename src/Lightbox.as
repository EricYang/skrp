package 
{
	//
	// Lightbox class by David Salahi 
	// (dave@artistic-webdesign.com) 
	// http://flexperiential.com
	// You may use this class in your own projects but please leave this notice in place.
	//
	// For more info about this class see the blog post:
	// http://flexperiential.com/2010/06/27/flex-4-actionscript-3-lightbox-for-flash-player/
	
	import flash.display.Bitmap;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	
	import mx.core.UIComponent;
	import mx.effects.Parallel;
	import mx.effects.Sequence;
	import mx.events.EffectEvent;
	
	import spark.components.Label;
	import spark.effects.Fade;
	import spark.effects.Move;
	import spark.effects.Scale;
	
	public class Lightbox extends UIComponent
	{
		private var _images:Vector.<LargeImage>;
		private var _currentImage:LargeImage;
		
		private const TOP_PADDING:int = 10;
		private const LEFT_RIGHT_PADDING:int = 10;
		private const BOTTOM_PADDING:int = 10;
		
		private const START_WIDTH:Number = 100;		// The first time the lightbox is called this will be the lightbox's background starting size.
		private const START_HEIGHT:Number = 100;	// The background/frame will then resize dynamically to fit the image.
		// Subsequently, the starting size will be the size of the previous image.
		
		private const CAPTION_AREA_HEIGHT:Number = 50;
		
		private const MAX_SAME_SCALE_DIFFERENCE:Number = 0.01; 			// for comparing image scales for equality
		
		private var _stageWidth:int;
		private var _stageHeight:int;
		
		[Embed(source="images/closebox.png")]
		private var CloseBtnImage:Class;
		private var _closeBtnBitmap:Bitmap;
		private var _closeBtn:UIComponent = new UIComponent;
		
		[Embed(source="images/left-arrow.png")]
		private var LeftArrowBtnImage:Class;
		private var _leftArrowBtnBitmap:Bitmap;
		private var _leftArrowBtn:UIComponent = new UIComponent;
		
		[Embed(source="images/right-arrow.png")]
		private var RightArrowBtnImage:Class;
		private var _rightArrowBtnBitmap:Bitmap;
		private var _rightArrowBtn:UIComponent = new UIComponent;
		
		private var _endScaleX:Number;
		private var _endScaleY:Number;
		private var _dimmer:UIComponent = new UIComponent;				// dims the page except for the lightbox image on top; covers the entire page
		
		private var _lightboxBackground:UIComponent = new UIComponent;	// the image frame
		private var _bitmapContainer:UIComponent = new UIComponent;		// just a container for the image Bitmap, that's all  
		private var _captionArea:UIComponent = new UIComponent;			// caption area below the image
		private var _caption:spark.components.Label = new spark.components.Label;
		
		
		public function Lightbox(stageWidth:int, stageHeight:int)
		{
			super();
			
			_stageWidth = stageWidth;
			_stageHeight = stageHeight;
			
			// The following lightbox elements must be drawn in the order shown so that
			// the layering will be correct.
			
			// Draw the dark translucent background that covers the entire stage.
			drawDimmer();
			
			// Draw the image background/frame
			drawLightbox(START_WIDTH, START_HEIGHT);
			
			// The caption area needs to be behind the image so that when it slides down and 
			// fades in you don't see it fading in on top of the image.
			drawCaptionArea(START_WIDTH, CAPTION_AREA_HEIGHT);	
			
			// Contains the image
			addChild(_bitmapContainer);	
			
			drawButtons();
		}
		
		public function set images(value:Vector.<LargeImage>):void
		{
			// Image IDs are assumed to be consecutive, no gaps, but do not have to have the same 
			// indexes as the element # in the _images Vector.
			_images = value;
		}
		
		public function showImage(id:int):void
		{
			for(var i:int = 0; i < _images.length; i++)
			{
				if(_images[i].id == id)
				{
					_currentImage = _images[i];
					break;
				}
			}
			
			for(i = 0; i < _bitmapContainer.numChildren; i++) // should only ever be zero or one child but loop just in case 
				_bitmapContainer.removeChildAt(0);
			
			this.visible = true;
			_dimmer.visible = true;
			_lightboxBackground.visible = true;
			_captionArea.visible = false;
			_closeBtn.visible = false;
			_leftArrowBtn.visible = false;
			_rightArrowBtn.visible = false;
			if(this.contains(_caption)) removeChild(_caption); 
			_dimmer.alpha = _lightboxBackground.alpha = _bitmapContainer.alpha = 1;
			
			// Calculate the size of the image background/frame to match the size of the image.
			var lightboxEndWidth:int = _currentImage.bitmap.width + 2 * LEFT_RIGHT_PADDING;
			var lightboxEndHeight:int = _currentImage.bitmap.height + TOP_PADDING + BOTTOM_PADDING;
			
			// Do the resize effect.
			resizeLightbox(lightboxEndWidth, lightboxEndHeight);
		}
		
		private function resizeLightbox(width:Number, height:Number):void
		{
			// Transform the background/frame (_lightboxBackground) to fit the requested image.
			_endScaleX = width / START_WIDTH;
			_endScaleY = height / START_HEIGHT;
			
			var scaleX:Scale = new Scale();
			scaleX.scaleXTo = _endScaleX;
			
			var scaleY:Scale = new Scale();
			scaleY.scaleYTo = _endScaleY;
			
			// Play the resize effects in sequence, first the width and then the height.
			var sequence:Sequence = new Sequence(_lightboxBackground);	
			sequence.addChild(scaleX);
			sequence.addChild(scaleY);
			
			// If the same image is clicked consecutively then the _lightboxBackground scale will already be at the proper value.
			// In that case, we don't want to play the scaling effect as there will be nothing happening on screen.
			// To simplify coding we go ahead and play the effect but for only 1 ms.
			if( AWDUtil.numbersAreApproxEqual(_endScaleX, _lightboxBackground.scaleX, MAX_SAME_SCALE_DIFFERENCE) &&
				AWDUtil.numbersAreApproxEqual(_endScaleY, _lightboxBackground.scaleY, MAX_SAME_SCALE_DIFFERENCE))
				sequence.duration = 1; //  
			
			sequence.addEventListener(EffectEvent.EFFECT_END, fadeInImage);
			sequence.play();
		}
		
		private function fadeInImage(event:EffectEvent):void
		{
			// Show the image inside the frame which is now the appropriate size.
			
			// Add image to container and position it
			_bitmapContainer.visible = true;
			_bitmapContainer.alpha = 0;
			_bitmapContainer.addChild(_currentImage.bitmap);
			_bitmapContainer.x = (_stageWidth - _currentImage.bitmap.width) / 2;	// Center the image on the stage
			_bitmapContainer.y = (_stageHeight - _currentImage.bitmap.height) / 2;
			
			// Position close button at upper right corner
			_closeBtn.x = _bitmapContainer.x + _currentImage.bitmap.width - _closeBtnBitmap.width / 2 + 2;
			_closeBtn.y = _bitmapContainer.y - _closeBtnBitmap.height / 2; 
			_closeBtn.alpha = 0;
			
			// Position previous image button to the left of the image
			_leftArrowBtn.x = _bitmapContainer.x - _leftArrowBtnBitmap.width - 20;
			_leftArrowBtn.y = _bitmapContainer.y + (_currentImage.bitmap.height / 2) - (_leftArrowBtnBitmap.height / 2);
			_leftArrowBtn.alpha = 0;
			
			// Position the next image button to the right of the image
			_rightArrowBtn.x = _bitmapContainer.x + _currentImage.bitmap.width + 20;
			_rightArrowBtn.y = _bitmapContainer.y + (_currentImage.bitmap.height / 2) - (_leftArrowBtnBitmap.height / 2);
			_rightArrowBtn.alpha = 0;
			
			// Fade in the image
			var fadeIn:Fade = new Fade();
			fadeIn.alphaTo = 1;
			fadeIn.addEventListener(EffectEvent.EFFECT_END, showCaptionArea);
			fadeIn.play([_bitmapContainer, _closeBtn, _leftArrowBtn, _rightArrowBtn]);
		}
		
		private function showCaptionArea(event:EffectEvent):void
		{
			// Show the caption/title/description area below the image
			
			_captionArea.x = _bitmapContainer.x - LEFT_RIGHT_PADDING;
			var captionAreaEndY:Number = _bitmapContainer.y + _currentImage.bitmap.height + BOTTOM_PADDING;
			_captionArea.scaleX = _endScaleX;
			
			_captionArea.visible = true;
			_captionArea.alpha = 0; // prepare to fade in
			_captionArea.y = captionAreaEndY - CAPTION_AREA_HEIGHT; // prepare to slide down while fading in
			
			var fadeIn:Fade = new Fade;
			fadeIn.alphaTo = 1;
			
			var slideDown:Move = new spark.effects.Move;
			slideDown.yTo = captionAreaEndY;
			
			// Fade in and slide down simultaneously.
			var parallelEffect:Parallel = new Parallel(_captionArea);
			parallelEffect.addChild(fadeIn);
			parallelEffect.addChild(slideDown);
			parallelEffect.addEventListener(EffectEvent.EFFECT_END, showCaption);
			parallelEffect.play();
		}
		
		private function showCaption(event:EffectEvent):void
		{
			// Can't addChild _caption to _captionArea because the text would get scaled which we don't want
			// so just addChild to this.
			_caption.height = CAPTION_AREA_HEIGHT;
			_caption.text = _currentImage.title;
			_caption.width = _currentImage.bitmap.width;
			_caption.x = _captionArea.x + LEFT_RIGHT_PADDING;
			_caption.y = _captionArea.y;
			_caption.alpha = 0;
			addChild(_caption);
			
			var fadeIn:Fade = new Fade(_caption);
			fadeIn.alphaTo = 1;
			fadeIn.play();
		}
		
		private function drawDimmer():void
		{
			// Dims the entire page except for the lightbox where the image is displayed
			_dimmer.graphics.beginFill(0x000000, 0.75);
			_dimmer.graphics.drawRect(0, 0, _stageWidth, _stageHeight);
			_dimmer.graphics.endFill();
			addChild(_dimmer);
		}	
		
		private function drawLightbox(width:int, height:int):void
		{
			// Frame around (behind) the picture; does not include the caption area.
			_lightboxBackground.graphics.clear();
			_lightboxBackground.graphics.beginFill(0xffffff);
			_lightboxBackground.graphics.drawRect(-width/2, -height/2, width, height); // make the origin the center so that scaling occurs around the center of the image
			_lightboxBackground.graphics.endFill();
			_lightboxBackground.x = _stageWidth / 2.;
			_lightboxBackground.y = _stageHeight / 2.;
			addChild(_lightboxBackground);
		}
		
		private function drawCaptionArea(width:Number, height:Number):void
		{
			_captionArea.graphics.beginFill(0xffffff);
			_captionArea.graphics.drawRect(0, 0, width, height);
			_captionArea.graphics.endFill();
			addChild(_captionArea);
			_captionArea.visible = false;
		}
		
		private function drawButtons():void
		{
			drawCloseButton();
			drawPreviousButton();
			drawNextButton();
		}
		
		private function drawCloseButton():void
		{
			// Close button typically looks a bit rough, with the "jaggies" as though it needs anti-aliasing.
			// This is because it's getting scaled by the browser unless the browser size just happens to be exactly the right size
			// to fit the stage at its specified size. This is a side-effect of using SHOW_ALL stage scaling mode.
			_closeBtnBitmap = new Bitmap(new CloseBtnImage().bitmapData);
			_closeBtn.addChild(_closeBtnBitmap);
			addChild(_closeBtn);
			_closeBtn.visible = false;
			_closeBtn.buttonMode = true;
			_closeBtn.addEventListener(MouseEvent.CLICK, closeLightbox);
			_closeBtn.addEventListener(MouseEvent.MOUSE_OVER, onCloseBtnMouseOver);
			_closeBtn.addEventListener(MouseEvent.MOUSE_OUT, onCloseBtnMouseOut);
		}
		
		private function drawPreviousButton():void
		{
			_leftArrowBtnBitmap = new Bitmap(new LeftArrowBtnImage().bitmapData);
			_leftArrowBtn.addChild(_leftArrowBtnBitmap);
			addChild(_leftArrowBtn);
			_leftArrowBtn.visible = false;
			_leftArrowBtn.buttonMode = true;
			_leftArrowBtn.addEventListener(MouseEvent.CLICK, previousImage);
			_leftArrowBtn.addEventListener(MouseEvent.MOUSE_OVER, onLeftBtnMouseOver);
			_leftArrowBtn.addEventListener(MouseEvent.MOUSE_OUT, onLeftBtnMouseOut);
		}
		
		private function previousImage(event:MouseEvent):void
		{
			var previousID:int = _currentImage.id - 1;
			var maxImageID:int = 0;
			if(previousID < 0)
			{
				previousID = _images.length - 1;
			}
			showImage(previousID);
		}
		
		private function nextImage(event:MouseEvent):void
		{
			var nextID:int = _currentImage.id + 1;
			if(nextID >= _images.length)
			{
				nextID = 0;
			}
			showImage(nextID);
		}
		
		private function onLeftBtnMouseOver(event:MouseEvent):void
		{
			_leftArrowBtn.filters = [new GlowFilter(0x777777)];
		}
		
		private function onLeftBtnMouseOut(event:MouseEvent):void
		{
			_leftArrowBtn.filters = null;
		}
		
		private function drawNextButton():void
		{
			_rightArrowBtnBitmap = new Bitmap(new RightArrowBtnImage().bitmapData);
			_rightArrowBtn.addChild(_rightArrowBtnBitmap);
			addChild(_rightArrowBtn);
			_rightArrowBtn.visible = false;
			_rightArrowBtn.buttonMode = true;
			_rightArrowBtn.addEventListener(MouseEvent.CLICK, nextImage);
			_rightArrowBtn.addEventListener(MouseEvent.MOUSE_OVER, onRightBtnMouseOver);
			_rightArrowBtn.addEventListener(MouseEvent.MOUSE_OUT, onRightBtnMouseOut);			
		}
		
		private function onRightBtnMouseOver(event:MouseEvent):void
		{
			_rightArrowBtn.filters = [new GlowFilter(0x777777)];
		}
		
		private function onRightBtnMouseOut(event:MouseEvent):void
		{
			_rightArrowBtn.filters = null;
		}
		
		private function onCloseBtnMouseOver(event:MouseEvent):void
		{
			_closeBtn.filters = [new GlowFilter(0xdddddd)];
		}
		
		private function onCloseBtnMouseOut(event:MouseEvent):void
		{
			_closeBtn.filters = null;
		}
		
		protected function closeLightbox(event:MouseEvent):void
		{
			var fade:Fade = new Fade;
			fade.alphaTo = 0;
			fade.addEventListener(EffectEvent.EFFECT_END, backgroundFadeComplete);
			fade.play([_dimmer, _lightboxBackground, _bitmapContainer, _captionArea, _closeBtn, _leftArrowBtn, _rightArrowBtn]);
		}
		
		protected function backgroundFadeComplete(event:EffectEvent):void
		{
			if(_bitmapContainer.contains(_currentImage.bitmap))		// This handler gets called once for each object in its list of targets.
				_bitmapContainer.removeChild(_currentImage.bitmap);	// We'll get an error if we try to do this more than once
			
			if(this.contains(_caption))
			{
				removeChild(_caption);
			}
			
			_dimmer.visible = false;
			_lightboxBackground.visible = false;
			_bitmapContainer.visible = false;
			_captionArea.visible = false;
		}
	}
}