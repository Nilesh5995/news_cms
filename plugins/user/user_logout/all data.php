<?php
defined('_JEXEC') or die;
jimport( 'joomla.plugin.plugin' );
class PlgUserSomething extends JPlugin
{
	public function onUserAfterLogin($option)
	{
	   //$id=$options['user']->id;
	   //$groups  = $options['user']->groups;
	   echo "<script>alert('hiii')</script>";
	   echo "good this function is executes";
	  
	}
	 //echo "bad  this function is not executes but class is executes.";
} 


// no direct access
// defined('_JEXEC') or die;


// class PlgUserPopuplogin extends JPlugin
// {
//     public function onUserAfterLogin($options)
//         {
//               return 'I think I need to place Javascript/HTML here';
//  }
// }
jimport('joomla.plugin.plugin');

echo "hello"; //this works as when I login I can see the message on top of the message which makes me believe that the user plugin is installed correctly

// Whatever I try to print in the class, it seems to ignore.
class PlgUser extends JPlugin
{
    public function onUserAfterLogin($options)
    {
          $user = JFactory::getUser();
          $user_id = $user->get('username');
          $usertype = $user->get('usertype');
          $session = JFactory::getSession();
          $sessionid = $session->getId();
          echo "very very good";
    }
}
?>