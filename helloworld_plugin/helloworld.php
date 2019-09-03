<?php
   defined('_JEXEC') or die;


//    class plgContentHelloworld extends JPlugin
// {
//     public function onContentAfterTitle($context, &$article, &$params, $limitstart)
//     {
//         return "Hello World!";
//     }
// }

class plgContentHelloworld extends JPlugin

{
	public function onContentAfterTitle($context,&$article,$params,$limitstart)
	{
		return "helloworld !!!!!!"
	}
}
?>