package 
{
	public class AWDUtil
	{

		public static function numbersAreApproxEqual(number1:Number, number2:Number, maxDifference:Number):Boolean
		{
			if(Math.abs(number1 - number2) < maxDifference)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
}