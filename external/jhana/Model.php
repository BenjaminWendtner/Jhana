<?php

	/** 
	 * This is the model baseclass. 
	 * It enables all models to find, count, save, update and delete data. 
	 * Querybuilding is possible. For example: User::all()->update(['name' => 'joe']).
	 * Furthermore it provides methods for access relations.
	 */
	class Model {
		
		// Database connection
		private static $database;
		
		// Variable for automatically saving the id of this object when it's retrieved.
		// It prevents the user from overwriting the id accidentally.
		private $old_id;
		
		// Determines if the return values of a query is a single object or an array of objects.
		private $return_one = FALSE;
		
		// Holds the current query and the parameters which are used for binding the prepared statement.
		private $query;
		private $params = [];
		
		// Holds the current ORDER, LIMIT and OFFSET configuration.
		private $order;
		private $limit;
		private $offset;
		

		/**
		 * Static method for setting up the database.
		 */
		public static function set_database() {
			
			// MySQL, PostgresSQL
		   if (DB_TYPE == 'mysql' || DB_TYPE == 'pgsql')
	       		self::$database = new PDO(DB_TYPE.':host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);

		   // SQL Lite
		   else if (DB_TYPE == 'sqlite')
		   		self::$database = new PDO(DB_TYPE.':'.DB_PORT);

		   // Oracle
		   else if (DB_TYPE == 'oracle')
		   		self::$database = new PDO('OCI:dbname=//'.DB_HOST.':'.DB_PORT.'/'.DB_NAME, DB_USER, DB_PASSWORD);
		   

		   // Set the charset to UTF-8
		   self::$database->query("SET CHARACTER SET utf8");
	   	}
		
		/**
		 * Contructor enables to contruct with attributes
		 */
		public function __construct($params=[]) {
			foreach ($params as $key => $value)
				$this->$key = $value;
		}
		
		
		/**
		 * Executes a SELECT query.
		 * @param: $column: The column which should be selected. 
		 * Also provides additional functionality: DISTINCT, MIN, MAX and SUM.
		 */
		public function get($column='*') {
			
			// Check if user wants a certain functionality
			$function = strtok($column, ' ');
			if ($function == 'DISTINCT') {
				$column = strtok(' ');
				$select = 'DISTINCT '.$column;
			} elseif ($function == 'MIN' || $function == 'MAX' || $function == 'SUM') {
				$column = strtok(' ');
				$select = $function.'('.$column.')';
			} else 
				$select = $function;
			
			// Execute SELECT
			$records = $this->execute_query('SELECT '.$select.' FROM '.$this->table.' '.$this->current_query());
			
			// Fetch results for DISTINCT
			if ($function == 'DISTINCT')
				return  array_map(function($val) use ($column) {return $val[$column];} , $records->fetchAll(PDO::FETCH_ASSOC));
			
			// Fetch results for MIN, MAX or SUM
			elseif ($function == 'MIN' || $function == 'MAX' || $function == 'SUM')
				return $records->fetchColumn();
			
			// Fetch results for all columns
			elseif ($column == '*')
				return ($this->return_one == TRUE) ? $this->mapObjects($records)[0] : $this->mapObjects($records);
					
			// Fetch results for one particular column
			else {
				// Return one value of one column
				if ($this->return_one == TRUE)
					return $records->fetchAll(PDO::FETCH_ASSOC)[0][$column];
				
				// Return the whole column mapped into an array
				else
					return array_map(function($val) use ($column) {return $val[$column];} , $records->fetchAll(PDO::FETCH_ASSOC));
			}
		} 		
		
		/**
		 * Returns all rows of the defined table.
		 * Can be used like that: User::all()->get();
		 */
		public static function all() {
			return new static();
		} 
		
		/**
		 * Counts the number of rows.
		 * @param $column: If a column is provided, only rows where the column is not null are returned.
		 * @return Integer.
		 */
		public function count($column='*') {
			$result = $this->execute_query('SELECT COUNT('.$column.') FROM (SELECT * FROM '.$this->table.' '.$this->current_query().') A');
			return $result->fetchColumn();
		} 
		
		/**
		 * Returns the first row.
		 * Can be used like that: User::all()->first()->get();
		 */
		public function first() {
			$this->limit = 1;
			$this->return_one = TRUE;
			return $this;
		}
		
		/**
		 * Returns the last row.
		 * Can be used like that: User::all()->last()->get();
		 */
		public function last() {
			
			// To prevent overwriting: first()->last().
			if ($this->return_one == FALSE) {
				$this->order = str_replace('ASC', '', $this->order);
				
				// Reverse order of the current query
				if ($this->order == '')
					$this->order =  'id DESC';
				elseif (strpos($this->order,'DESC') !== FALSE)
					$this->order = str_replace('DESC', '', $this->order);
				else
					$this->order .= ' DESC';
			}
			
			// Set LIMIT and OFFSET 
			$this->limit = 1;
			$this->offset = '';
			$this->return_one = TRUE;
			
			return $this;
		}

		/**
		 * Uses the ids of rows to retrieve them.
		 * @param $id: The id of the row or an array of ids.
		 * @return Array of objects.
		 */
		public static function find($ids) {
			$temp = new static();

			if (is_array($ids))
				$temp->query = 'WHERE id IN ('.implode(',', $ids).')';
			else {
				$temp->query = 'WHERE id = '.$ids;
				$temp->return_one = TRUE;
			}
			
			return $temp;
		} 

		/**
		 * Sets the ORDER property.
		 */
		public function order($order) {
			$this->order = $order;
			return $this;
		}
		
		/**
		 * Sets the LIMIT property.
		 */
		public function limit($limit) {
			$this->limit = $limit;
			return $this;
		}
		
		/**
		 * Sets the OFFSET property.
		 */
		public function offset($offset) {
			$this->offset = $offset;
			return $this;
		}
		
		/**
		 * Uses advanced parameters to find rows.
		 * @param $prepared: Prepared statement for WHERE clause.
		 * @param $params: The parameters for the prepared statement.
		 */
		public function where($prepared, $params=[]) {
			
			// Compute prepared statement later
			$this->params = array_merge($this->params, $params);

			// Insert into WHERE clause
			if (strtok($this->query, ' ') == 'WHERE')
				$this->query = preg_replace('/WHERE/', 'WHERE '.$prepared.' AND ', $this->query, 1);
			else	
				$this->query = 'WHERE '.$prepared;

			return $this;
		} 
		
		/**
		 * Saves a model into the database by creating or updating it.
		 */
		public function save() {
			
			// Create
			if (empty($this->old_id))
				return static::create(get_object_vars($this));

			// Update
			else 
				return $this->update(get_object_vars($this));
		}
		
		/**
		 * Creates a new row in the database with given parameters.
		 * @param $params: The params for creation.
		 * @return object or FALSE.
		 */
		public static function create($params) {
			
			// Create a new instance
			$temp = new static($params);
		
			// Execute callbacks, Validations and Timestamps
			if ($temp->callback('callback_before_validate') && $temp->validate() && 
				$temp->callback('callback_before_save') && $temp->set_timestamps() && 
				$temp->callback('callback_before_create')) {

				// Use reflection for getting the attributes set by the user
				$reflect = new ReflectionObject($temp);
				
				// Iterate over all properties and split them into names and values
				foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
					$property_name = $property->name;
					
					if ($property_name != 'id') {
						$properties[] = $property_name;
						$temp->params[] = $temp->$property_name;
					}
				}
				
				// Create a string with as many "?" as parameters are provided
				$temp_params = rtrim(str_repeat('?,', count($properties)), ',');
	
				// The Create query
				$temp->execute_query('INSERT INTO '.$temp->table.' ('.implode(',', $properties).') VALUES ('.$temp_params.')');

				// Execute callbacks and return
				return ($temp->callback('callback_after_create') && $temp->callback('callback_after_save')) ? $temp : FALSE;
			}
			else
				return FALSE;	
		} 
		
		
		/**
		 * Updates the current chained data. For example:
		 * User::all()->update(['name' => 'Herbert']) will update all users.
		 * @param $params: The params for updating.
		 * @return Array: The ids of the objects which failed.
		 */
		public function update($params) {

			// First select all affected objects
			if ($this->query != '') {
				$records = $this->execute_query('SELECT * FROM '.$this->table.' '.$this->current_query());
				$objects = $this->mapObjects($records);
			} else 
				$objects[] = $this;
			
			// Execute callbacks
			foreach ($objects as $object)
				if ($object->callback('before_validate') && $object->validate() && 
				    $object->callback('before_save') && $this->set_timestamps() && 
				    $object->callback('before_update'))
					
					$validated[$object->old_id] = $object->old_id;
				else 
					$failed[] = $object->old_id;
					
			// Use reflection for getting the attributes set by the user
			$reflect = new ReflectionObject(new self());
			
			// Generate a filter array which contains disallowed values
			foreach ($reflect->getProperties(ReflectionProperty::IS_PRIVATE) as $property)
				$filter[] = $property->name;

			// Iterate over all properties and filter them
			foreach ($params as $key => $value)
				if (!in_array($key, $filter) && $key != 'id' && $key != 'table' && $key != 'set_created_at' && $key != 'set_updated_at') {
					$temp_params .= $key.'=?,';
					$this->params[] = $value;
				}
			
			// The Update query
			if (!empty($validated) && $temp_params != '')
				$this->execute_query('UPDATE '.$this->table.' SET '.rtrim($temp_params, ',').' WHERE id IN ('.implode(',', $validated).')');

			// Execute callbacks
			foreach ($objects as $object)
				empty($validated[$object->id]) && $object->callback('after_update') && $object->callback('after_save');
			
			// Return failed ids
			return $failed;
		} 


		/**
		 * Deletes the current chained data. For example:
		 * User::all()->delete() will delete all users.
		 * @return Array: The ids of the objects which failed.
		 */
		public function delete() {

			// First select all affected objects
			if ($this->query != '') {
				$records = $this->execute_query('SELECT * FROM '.$this->table.' '.$this->current_query());
				$objects = $this->mapObjects($records);
			} else 
				$objects[] = $this;
			
			// Execute callbacks
			foreach ($objects as $object)
				if ($object->callback('before_delete'))
					$validated[$object->old_id] = $object->old_id;
				else 
					$failed[] = $object->old_id;

			// The Delete query
			if (!empty($validated))
				$records = $this->execute_query('DELETE FROM '.$this->table.' WHERE id IN ('.implode(',', $validated).')');
			
			// Execute callbacks
			foreach ($objects as $object)
				empty($validated[$object->id]) && $object->callback('after_delete');
			
			// Return failed ids
			return $failed;
		}
		
		/**
		 * Applies SQL for this model. Supports prepared statements.
		 * @param $query: The SQL statement.
		 * @param $params: Mixed array, The params for the prepared statement.
		 */
		public static function sql($query, $params=[]) {
			
			// Prepare statement
			$stmt = self::$database->prepare($query);	
			
			// Bind all parameters to prepared statement
			foreach ($params as $key => $value)
				$stmt->bindParam($key, $value);
			
			// Execute query
			$stmt->execute();
			
			// Detect the type of the SQL and return the appropriate values
			$first_word = strtok($query, ' ');
			
			if ($first_word == 'SELECT') {
				if (strtok(' (') == 'COUNT')
					return $stmt->fetchColumn();
				else {
					$temp = new static();
					return $temp->mapObjects($stmt);
				}
			} 
			elseif ($first_word == 'INSERT' || $first_word == 'UPDATE' || $first_word == 'DELETE')	
				return TRUE;
			else
				return FALSE;
		} 
		
		
		
		/**
		 * Retrieves the related objects.
		 * @param $model: The related model name.
		 * @param $foreign: The related foreign key.
		 */		
		public function belongs_to($model, $foreign='') {
			
			// Set the default foreign key
			if ($foreign == '')
				$foreign = lcfirst($model).'_id';

			// Execute the query finding all related objects
			$records = $this->execute_query('SELECT '.$foreign.' FROM '.$this->table.' '.$this->current_query());		
			
			// Get all ids from all retrieved objects
			$ids = array_map(function($val) use ($foreign) {return $val[$foreign];} , $records->fetchAll(PDO::FETCH_ASSOC));			
			
			// Return type of a relation is another model type
			$temp = new $model();
			
			// Set the ids as WHERE parameter
			$temp->query = 'WHERE id IN ('.implode(',', $ids).')';
		
			// Relations always return arrays
			$temp->return_one = FALSE;
			
			// Returns object of new type
			return $temp;
		}
		
		/**
		 * Retrieves the related objects.
		 * @param $model: The related model name.
		 * @param $foreign: The related foreign key.
		 */		
		public function has_many($model, $foreign='') {
			
			// Set the default foreign key
			if ($foreign == '')
				$foreign = lcfirst(get_called_class()).'_id';
			
			// Execute the query finding all related objects
			$records = $this->execute_query('SELECT id FROM '.$this->table.' '.$this->current_query());
			
			// Get all ids from all retrieved objects			
			$ids = array_map(function($val) {return $val['id'];} , $records->fetchAll(PDO::FETCH_ASSOC));		
			
			// Return type of a relation is another model type
			$temp = new $model();
			
			// Set the ids as WHERE parameter
			$temp->query = 'WHERE '.$foreign.' IN ('.implode(',', $ids).')';
			
			// Relations always return arrays
			$temp->return_one = FALSE;
			
			// Returns object of new type
			return $temp;
		}
		
		/**
		 * Retrieves the related objects
		 * @param $model: The related model name.
		 * @param $table: The n:m table name.
		 * @param $foreign_self, $foreign_other: The related foreign keys.
		 */		
		public function has_many_through($model, $table, $foreign_self='', $foreign_other='') {
			
			// Set the default foreign key for the self object
			if ($foreign_self == '')
				$foreign_self = lcfirst(get_called_class()).'_id';
		
			// Set the default foreign key for the other object
			if ($foreign_other == '')
				$foreign_other = lcfirst($model).'_id';
			
			// Get all ids from all retrieved objects
			$records = $this->execute_query('SELECT id FROM '.$this->table.' '.$this->current_query());
			
			// Get all ids from all retrieved objects			
			$ids = array_map(function($val) {return $val['id'];} , $records->fetchAll(PDO::FETCH_ASSOC));		
			
			// Return type of a relation is another model type
			$temp = new $model();
			
			// JOIN tables and set the ids as WHERE parameter
			$temp->query = 'LEFT JOIN '.$table.' ON id = '.$foreign_other.' WHERE '.$table.'.'.$foreign_self.' IN ('.implode(',', $ids).')';
			
			// Relations always return arrays
			$temp->return_one = FALSE;
			
			// Returns object of new type
			return $temp;
		}
		
		/**
		 * Validates this object by using the user defined validate functions.
		 * @return Boolean: Returns FALSE if the validation failed.
		 */		
		public function validate() {
			$validated = TRUE;
			
			foreach(get_class_methods(get_called_class()) as $method)
				if (preg_match('/^validate_/', $method))
					$validated = $validated && $this->$method();
			
			return $validated;
		}

		/**
		 * Executes the query by using prepared statements.
		 * @param $query: A query string (maybe contains some "?").
		 * @return The result of the PDO query.
		 */
		private function execute_query($query) {
			
			// Prepare statement
			$stmt = self::$database->prepare($query);
			
			// Bind all parameters to prepared statement
			if (!empty($this->params))
				for ($i=0; $i < count($this->params); $i++)
					$stmt->bindParam($i + 1, $this->params[$i]);
		
			// Execute query
			$stmt->execute();
			
			// Reinitialize the $params array.
			$this->params = [];
			
			return $stmt;
		}
		
		/**
		 * Concatinates the current query.
		 * It also prepares the ORDER, LIMIT and OFFSET parameters.
		 * @return A query string.
		 */
		private function current_query() {
			
			// Prepare ORDER BY clause
			if ($this->order  != '') 
				$this->order  = ' ORDER BY '.$this->order;
			
			// Prepare LIMIT and OFFSET clauses
			if ($this->limit != '' && $this->offset != '') {
				$this->limit  = ' LIMIT ' .$this->limit;
				$this->offset = ' OFFSET '.$this->offset;
				
			} elseif ($this->limit != '') {
				$this->limit  = ' LIMIT ' .$this->limit;
				
			} elseif ($this->offset != '') {
				$this->limit  = ' LIMIT 999999999999999';
				$this->offset = ' OFFSET '.$this->offset;
			} 
			
			// Return concatinated query
			return $this->query.$this->order.$this->limit.$this->offset;
		}

		/**
		 * Sets timestamps for this object.
		 */
		private function set_timestamps() {
			$now = date("Y-m-d H:i:s");
			
			if ($this->set_updated_at != FALSE)
				$this->updated_at = $now;
			
			if (empty($this->old_id) && $this->set_created_at != FALSE)
				$this->created_at = $now;
			
			return TRUE;
		}
		
		/**
		 * This method looks if a callback exists and executes it.
		 * If a callback returns FALSE then this method returns FALSE.
		 * @param $name: The name of the callback.
		 */	
		private function callback($name) {
			if (method_exists($this, $name)) {
				$callback = $this->name();
				return $callback != FALSE || $callback === NULL;
			} else 
				return TRUE;
		}
		
		/**
		 * This method is private and maps database records to objects.
		 * The object fields are generated automatically.
		 */	
		private function mapObjects($records) {
			$result = array();
			if (empty($records)) return; 
			
			$records = $records->fetchAll(PDO::FETCH_ASSOC);
			foreach($records as $record) {
				$obj = new static();
				foreach($record as $key => $value)
					$obj->$key = $value;
				
				// Save this object id, users should not be able to change the id
				$obj->old_id = $obj->id;
				$result[] = $obj;
			}
			
			return $result;
		}
	}
?>