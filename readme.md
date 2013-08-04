# Form Generator
This is a auto form generator for Laravel 4. This package allows you to auto generate a form from a model.

- [Form Generator on Packagist](https://packagist.org/packages/robin-malfait/formgenerator)
- [Form Generator on GitHub](https://github.com/RobinMalfait/Laravel-auto-form-generator)

## Installation
To add this form generator to your Laravel application follow this steps:

Add the following to your `composer.json` file:

	"robin-malfait/formgenerator": "dev-master"

Then run `composer update` or `composer install` if you have not already installed packages.

Add below to the `providers`array in `app/config/app.php` configuration file (add at the end):

	'RobinMalfait\Formgenerator\FormgeneratorServiceProvider'

## How to use it
Make the $gen variable:
```php
$gen = new RobinMalfait\Formgenerator\Formgenerator;
```
Let's make a form now

```php
{{ Form::model($user) }}
	{{ $gen->generate($user) }}
{{ Form::close() }}
```
As a secodn param you can pass an options array for example:

```php
{{ Form::model($user) }}
	{{ $gen->generate($user, array(

		// If you want a specific type, put it in here, default is type from the database
		'types' => array(
			// Field Name 	=> Type
			'all_day' 		=> 'checkbox',

			// If you want a select field with options
			'first_name'	=> array(
				'type'		=> 'select',
				'options'	=> array(
					'Taftse' 	=> 'Taftse',
					'Robin'		=> 'Robin',
					'Jeffrey'	=> 'Jeffrey'
				),
			),
		),

		// Add a class to a field
		'extras' => array(
			// Field Name   => array('key' => 'value')
			'first_name' 	=> array(
				'class' 	=> 'span5'
			),
			'last_name'		=> array(
				'class' 	=> 'span5'
			),
			'activated' 	=> array(
				'class' => ''
			),

			// Wildcards, those will be added to every field except for the fields that are listed above
			'*'				=> array(
				'class' 	=> 'span5'
			)
		),

		// Submit? Yes or no? Set the text and set a class if you want
		'submit' => array(
			'show' 	=> true,
			'text'  => 'Save',
			'class' => 'btn btn-success btn-large'
		),

		// Fields to not display!
		'exclude' => array(
			'event_type', 'id', 'created_at', 'updated_at', 'for_user_id'
		),

		// Show labels
		'showLabels' => true,

	)) }}
{{ Form::close() }}
```