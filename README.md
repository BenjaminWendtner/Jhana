Jhana
=====

> "Jhāna (झान) (Pāli) means meditation in Hinduism, Buddhism and Jainism. In Buddhism, it is a series of cultivated states of mind, which lead to "state of perfect equanimity and awareness (upekkhii-sati-piirisuddhl)."
-- Wikipedia

Jhana is a PHP-Framework which is very strongly oriented towards Ruby on Rails. It is currently under development but will get released soon. We also want to supply you with a small and well documented Example-Application and a better documentation soon. 


Here are some Core-Features of Jhana:

+ Lightweight
+ MVC-Structure
+ Easy to install (Drag & Drop the folder)
+ Completely self-contained
+ Includes Bootstrap by default
+ Uses Pjax by default
+ LESS Support (Serverside compiled and cached)
+ PHP - Routing
+ Localization
+ Database abstraction (Supports MySQL, SQLite, PostgresSQL, Oracle and so on)


### Installation
Installation is simple. Just extract the framework folder into your webdirectory. Then you can open */config/config.php* and setup your database connection. 

### Database
If you want to use a database there are a few important things. Each table in your database should have an "id" autoincrement column. Jhana uses this for Object Realational Mapping.
If a field references to another table, the field should be named with "[Referenced Modelname]_id". For example if a user can have multiple tasks then the "tasks" table would have a column named "user_id". This is only a convention but enables Jhana to handle the relations for you.
Optionally you can define the columns *created_at* and *updated_at*. These fields are then used automatically to set the dates of the entries correct.

### Creating Models
Each Model represents a table in your database. All columns are mapped automatically. A simple Model could look like this. 
```php
class User extends Model {
		
	// Table name
	protected static $table_name = 'users';
}
```

An object of this class would already has all the fields, the database table has. All models provide a base-set of functionalities: *all, count, find, find_by, sql, create, update, save, delete, validate.*
Here is an example on how you could use this:
```php
// Use constructor to pass attributes
$user = new User(['name' => 'Homer Simpson']);
$user->email = 'homer@springfield.com';
$user->save();

// Use the find method to get a single object.
// The parameter is always the id of the object.
$user = User::find(23);
$user->name = 'Homer Simpson';
$user->save();

// Use the find_by method to get an array of objects.
// Also ORDER, LIMIT and so on are available.
$user = User::find_by(['name' => 'Homer Simpson', 'ORDER' => 'id DESC'])[0];
$user->update(['name' => 'Bart Simpson']);

// Use the create method
$user = User::create(['name' => 'Homer Simpson', 'email' => 'homer@springfield.com']);
```
For more details (ORDER, LIMIT ...), please have look at http://medoo.in/api/where until we can provide you a better documentation.

Jhana also provides more advanced features like Relations, Validations and Callbacks.
Here an example for **Relations**:
```php
// Belongs To relation
public function user() {
	return $this->belongs_to('User');
}

// Has Many relation
public function users() {
	return $this->has_many('User');
}

// Has Many through n:m table relation.
// The second parameter is the n:m table name.
public function users() {
	return $this->has_many_through('User', 'user_task');
}

```
For *belongs_to* and *has_many* there is also an optional parameter where a reference column name can be defined.
Those relations could then be used as follows:
```php
$user_tasks = $user->tasks();
```

**Validations** are executed automatically if a model gets saved. The Validation functionname has to begin with "validate_". The return value has to be boolean.
Jhana offers a bunch of useful validation methods: *validate_email, validate_length, validate_presence, validate_is_integer, validate_is_boolean, validate_exists_in*.
Here are some examples on how to use them:

```php
public function validate_email() {
	return Jhana::validate_email($this->email);
}

public function validate_name() {
	return Jhana::validate_presence($this->name);
}

public function validate_password() {
	return Jhana::validate_length($this->password, '1..30');
}

public function validate_type() {
	return Jhana::validate_exists_in($this->type, ['admin', 'user']);
}
```


**Callbacks** can be usefull when it comes to preprocessing or postprocessing data. 
They are executed when using one of the functions *save, create, update* or *delete*.
Currently Jhana provides types of callbacks: *callback_before_validation, callback_before_save, callback_before_create, callback_before_update, callback_after_create, callback_after_update, callback_after_save, callback_before_delete*
```php
// Example: Set a default password before validation
protected function callback_before_validate() {
  if ($this->password == '')
	  $this->password = 'default password';
}

// Example: Delete all associated models before deletion.
protected function callback_before_delete() {
  foreach ($this->users() as $user)
  	$user->delete();
}
```


### Creating Controllers
Each Request calls a Controller-Method (let's call them actions). 

```php
class UserController extends ApplicationController {
		
	// Show all users
	static function index($params) {
		$users = User::all();
		self::render(['users' => $users], 'Users overview');
	}
		
	// Show details of a spefic user. 
	static function show($params) {
		$user = User::find($params['id']);
		self::render(['user' => $user]);
	}
}
```
Simple, isn't it? Each Controller-Action calls either ```self::render(...);``` or ```self::redirect(...);``` and can pass parameters to views in the render-method (like shown above). The second parameter of the render method is optional and sets the page-title for the view. If you ommit this title-parameter, Jhana will then use your Application title as defined in your config.php. 


Controllers also provide filtering. Filters are always executed BEFORE an action is called. A convention is, that filter functionnames begin with "filter_". They can be used as follows:
```php
class UserController extends ApplicationController {

  	// The filter array maps filters to actions
	protected static $filters = [
		'filter_test' => ['show']
	];
		
	// An ordinary action
	static function show($params) {
		$user = User::find($params['id']);
		self::render(['user' => $user]);
	}

  	// This filter function is executed before show() is executed
	protected static function filter_test(&params) {
		// Could do everything here...
		if (empty($_SESSION["user_id"]))
				self::redirect('http://www.google.at');
	}
}
```
We pass the *&params* to each filter, so that we can modify all request parameters before the action gets called.


### Map routes
Since we have our Controller Actions, now is the time to route the URLs. Jhana uses the AltoRouter-PHP Plugin.
As long as you have not defined a root URL, Jhana will display the Welcome page.
In *config/routes.php* write something like this:

```php
$router->map('GET', '', 'user#index');
$router->map('GET', 'user/[i:id]', 'user#show');
```

Optinally you can name your routes with an additional parameter:
```php
$router->map('GET', 'users', 'user#index', 'user_path');
```
You can than generate URLs from that:
```php
echo $router->generate('user_path');
```
This becomes very handy for generating links for example.

### Code the views
Views contain all the HTML. Each Controller gets an extra View folder. Each Controller-Action gets an extra PHP-file which is named after the action. You can use all the Variables you passed via the controller render-method.
An important file is the *views/layout/layout.php*. Every view is rendered into this layout file. So you could use the layout for things that stay the same for each view, like Header, Footer and so on.

### Assets
The assets are devided into three parts: JavaScript, CSS and Images. Feel free to add any subfolders to the JS or the CSS folders. All these files will be automatically loaded recursively anyways. Jhana imports the Bootstrap-Frontend Framework by default. For Stylesheets you could use CSS and LESS. Also Pjax is used to provide the fastest possible site-loading. For all those things, you have to configure nothing, just enjoy the magic ;-)

### Helpers
You can use the Helpers-Folder to create your own Helpers. Helpers are loaded automatically and their methods can be executed everywhere, so it's up to you when and where to use them.

### Localization
Jhana also supports localization. Put your language files into *config/languages/* folder and name them appropriately ("en.php", "de.php" and so on).
Use the function ```Jhana::set_language('en');``` to set the session to this language and the function ```Jhana::get_language();``` to get the current language. 
Then use ```Jhana::t(...);``` to print your translations. You can set your default language in *config/config.php*

