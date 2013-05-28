#Form Handling Kit

This Kit is consist of 4 Classes :
1. Form (v 0.1)
2. FormHandler (v 0.1)
3. FormBuilder (not done yet)
4. Validator (v 0.1)

## Step By Step
### Load This Kit
First of all you need to include this kit within your script
```php
<?php
require_once 'form.kit.php';
?>
```
Now it's ready to use, and you\'ve access to all features .

### Register a Form
Registering a Form is about setting a handler for it to excute once it's submitted, and in order to do this you gonna use a static method within FormHandler class called register as follow :
```php
<?php
FormHandler::register('formName', 'my_custom_function');
?>
```
This method takes 3 parameters, 2 main and 1 optional as:
FormHandler::register(string $formName, callback $handler [, $type = post]);
First Parameter ($formName) : is like an identifier or a custom name for this form
Seconde Parameter ($callback) : this is the Handler that will be excuted once this form submit
Third Parameter ($type) : This indicated whether this is POST or GET form, Default is POST
