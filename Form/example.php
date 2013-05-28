<?php

//We need to include Form Kit First
require_once 'form.kit.php';

//Lets Register our Form, and lets Name it 'subscribe', we can choose any nice name
//my_form_handler is a function will be executed to process this form 
FormHandler::register('subscribe', 'my_form_handler');

//It's the time to create our Handler
//$form is an object that will be automatically passed to our handler, with useful info and helpers about this form  
function my_form_handler($form)
{
	//We can verify this form easily
	echo ($form->verify()) ? 'Form Verified <br >' : 'Form Not Verified <br />';
	//We can get any data passed with form easily
	//$form->data is a stdClass object which contains all inputs passed by form (not filtered)
	echo $form->data->user_email;
	echo '<br />';
	//Also we can Know about the Referrer
	echo $form->referrer;
	echo '<br />';
	//What about knowing about how this form Submitted (GET|POST)
	echo $form->method;
	echo '<br />';
	//This is  not all, we can also validate form inputs and verify the whole form and much more
}

?>

<!-- Lets Create our Form -->
<form action="" method="post">
	<input type="email" name="user_email" placeholder="email address" />
	<input type="submit" name="submit" value="Subscribe" />
	<!-- Note: that we need a hidden input named "action" with value of the form name as we registered it above -->
	<!-- Why we don't let our FormHandler do this for us -->
	<!-- This will also add another hidden field that will help us to verify this form later -->
	<!-- If you don't want the verifier field then set the second Parameter to false -->
	<?=FormHandler::createHiddenFields('subscribe')?>
</form>
