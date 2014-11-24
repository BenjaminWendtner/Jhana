Jhana
=====

> "Jhāna (झान) (Pāli) means meditation in Hinduism, Buddhism and Jainism. In Buddhism, it is a series of cultivated states of mind, which lead to "state of perfect equanimity and awareness (upekkhii-sati-piirisuddhl)."
-- Wikipedia

Jhana is a PHP-Framework which is very strongly oriented towards Ruby on Rails. It is currently under development but will get released soon. We also want to supply you with a small and well documented Example-Application and a better documentation soon. 


Here are some Core-Features of Jhana:

+ MVC-Structure
+ Easy to install (Drag & Drop the folder)
+ Includes Bootstrap by default
+ Uses Pjax by default
+ LESS Support (Serverside compiled and cached)
+ PHP - Routing
+ Database abstraction (Supports MySQL, SQLite, PostgresSQL, Oracle, etc.)


### Installation
Installation is simple. Just extract the framework folder into your webdirectory. Then you can open */config/config.php* and setup your database connection. Each table in your database should have an "id" autoincrement column. Jhana uses this for the Object Realtional Mapping.

### Creating Models
Each Model represents a table in your database. All columns are mapped automatically. A simple Model could look like this. 
```php
class User extends Model {
		
	// Table name
	static protected $table_name = 'users';
}
```

An object of this class would already has all the fields, the database table has. All models provide a base-set of functionalities: *all, count, find, find_by, sql, create, save, delete, validate.*
Here is an example on how you could use this:
```php
$user = User::find(23);
$user->name = 'Max Mustermann';
$user->save();

$user = User::find_by('name', 'Max Mustermann');
$user->delete();
```

Jhana also provides more advanced features like Relations, Validations and Callbacks.
Here an example for **Relations**:
```php
// Belongs To relation
public function user() {
	return User::find($this->user_id);
}

// Has Many relation
public function tasks() {
	return Task::find_by("user_id", $this->id);
}
```

Those relations could then be used as follows:
```php
$user_tasks = $user->tasks();
```

**Validation** are executed automatically if a model gets saved. The Validation functionname has to begin with "validate_". The return value has to be boolean.

```php
public function validate_password() {
	return (strlen($this->password) >= 1 && strlen($this->password) <= 50);
}
```

**Callbacks** can be usefull when it comes to preprocessing or postprocessing data. Currently Jhana provides three  types of callbacks: *callback_validate, callback_save, callback_delete*
```php
protected function callback_delete() {
  if ($this->password == '')
	  $this->password = 'default';
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
	static protected $filters = [
		'filter_test' => ['index']
	];
		
	// An ordinary action
	static function index($params) {
		$users = User::all();
		self::render(['users' => $users]);
	}

  	// This filter function is executed before index() will be called 
	protected static function filter_test() {
		// Could do everything here...
		if (empty($_SESSION["user_id"]))
				self::redirect('http://www.google.at');
	}
}
```

### Map routes
Since we have our Controller Actions, now is the time to route the URLs. Jhana uses the AltoRouter-PHP Plugin.
In *config/routes.php* write something like this:

```php
$router->map('GET', 'users', 'user#index');
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
An important file is the *views/layout/layout.php*. Every view is rendered into this layout file. So you could use the layout for things that stay the same for each view, like Header, Footer etc.

### Assets
The assets are devided into three parts: JavaScript, CSS and Images. Feel free to add any subfolders to the JS or the CSS folders. All these files will be automatically loaded recursively anyways. Jhana imports the Bootstrap-Frontend Framework by default. For Stylesheets you could use CSS and LESS. Also Pjax is used to provide the fastest possible site-loading. For all those things, you have to configure nothing, just enjoy the magic ;-)

### Helpers
You can use the Helpers-Folder to create your own Helpers. Helper methods can be executed everywhere but we would recommend to use them in views.
