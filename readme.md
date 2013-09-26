# Form Generator
This is a auto form generator for Laravel 4. This package allows you to auto generate a form from a model.

- [Form Generator on Packagist](https://packagist.org/packages/robin-malfait/formgenerator)
- [Form Generator on GitHub](https://github.com/RobinMalfait/Laravel-auto-form-generator)

## Installation
To add this form generator to your Laravel application follow this steps:

Add the following to your `composer.json` file:

	"robin-malfait/formgenerator": "dev-master"

Then run `composer update` or `composer install` if you have not already installed packages.

Add below to the `providers` array in `app/config/app.php` configuration file (add at the end):

	'RobinMalfait\Formgenerator\FormgeneratorServiceProvider'

Add `'Formgenerator' => 'RobinMalfait\Formgenerator\Facades\Formgenerator',` to the `aliases` array also in `app/config/app.php`

## What's new
* You may now use hidden fields and set values to those hidden elements. The label associated will be hidden.
```php
	'customers_id'	=> array(
		'type'		=>	'hidden',
		'value'		=>	2
	)
```

* The ability to add custom labels in the `extras` array:

	```'label' => 'Supercalifragilisticexpialidocious'```

## How to use it
Let's make a form now, you can either pass an object like `$user` OR you can pass `table_name` as a string instead of the $model variable like so:

```php
{{ Form::open() }}
	{{ Formgenerator::generate('table_name_here') }}
{{ Form::close() }}
```


With a $model object
```php
{{ Form::model($user) }}
	{{ Formgenerator::generate($user) }}
{{ Form::close() }}
```

As a second param you can pass an options array for example:
```php
{{ Form::model($user) }}
	{{ Formgenerator::generate($user, array(

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
			// Support for hidden fields (auto-hides associated label) and setting values!
			'customers_id'			=> array(
				'type'		=>	'hidden',
				'value'		=>	2
			),
			// Field Name   => array('key' => 'value'),
			'first_name' 			=> array(
				'class' 			=> 'span5',
				'content_before'	=> '<fieldset><legend>My Form</legend>'
			),
			'last_name'				=> array(
				'class' 			=> 'span5',
				'content_before'	=> '<br>'
			),
			'activated' 	=> array(
				'class' 		=> '',
				'content_after'	=> '</fieldset>'

				// Set a custom label if you want
				'label' => 'Supercalifragilisticexpialidocious'
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