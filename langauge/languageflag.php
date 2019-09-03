<?php
defined('_JEXEC') or die;
class PlgContentLanguageflag extends JPlugin
{	
	
    function onAfterRender()
    {
            $code = 'some code'; 

            $documentbody = JResponse::getBody();
            $documentbody = str_replace ("</body>", $code." </body>", $documentbody);
            JResponse::setBody($documentbody);

            //return true;

    }
}

