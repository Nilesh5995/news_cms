
<?php
defined('_JEXEC') or die;
class PlgContentLanguageflag extends JPlugin
{	

   	public function onContentPrepare($context, &$row, &$params, $page = 0)
		{
		// Don't run this plugin when the content is being indexed
		//echo $row->text;

		   //$userinfo = "Name: <b>John Poul</b> <br> Title: <b>PHP Guru</b>";
		   // preg_match_all ("/<b>(.*)<\/b>/U", $row->text;, $pat_array);
		   
		   // print $pat_array[0][0]." <br> ".$pat_array[0][1]."\n";

		// $content ="<p>This is a sample text where {123456} and {7894560} ['These are samples']{145789}</p>";
		if(preg_match_all('/{(.*?)}/', $row->text, $matches))
		{
				//echo "preg match";
			//print_r(array_map('charval'$matches[1]));
			//print_r($matches[1]);
			//echo "<img src='../../media/mod_languages/images/en.gif' ></img>";
			foreach($matches[1] as $arr)
			{
			        $mn= "{".$arr."}";
			    // / echo $arr;
			     //$row->text = preg_replace("{$mn}", "<img src='../../media/mod_languages/images/en.gif' ></img>",$row->text);
			     $row->text  = str_replace($mn, "<img src='../../media/mod_languages/images/".$arr.".gif' ></img>",$row->text); 
			     //print_r( $row->text);
			    
			   
			}
		}
		else
		{
			//echo "preg not,match";
		}
   }
}



