<?php

	/** 
	 * This is the model baseclass. 
	 * It enables all models to find, count, save, update and delete data. 
	 * Furthermore it provides methods for building relations.
	 */
	class Model {
		
		protected static $database;
		
		
		public static function set_database() {
	       self::$database = new medoo();
	   	}
		
		/**
		 * Contructor enables to contruct with attributes
		 */
		public function __construct($params=[]) {
			$this->set_attributes($params);
		}
		
				
		/**
		 * Returns all rows of the defined table.
		 * @return Array of objects.
		 */
		public static function all() {
			$records = self::$database->select(static::$table_name, "*");
			return self::mapObjects($records);
		} 
		
		/**
		 * Counts the number of rows where the params match.
		 * @param $params: Represents the where statement.
		 * @return Integer.
		 */
		public static function count($params=[]) {
			$count = self::$database->count(static::$table_name, $params);
			return $count;
		} 
		
		/**
		 * Uses the id of a row to retrieve it.
		 * @param $id: The id of the row.
		 * @return Object.
		 */
		public static function find($id) {
			$records = self::$database->select(static::$table_name, '*', ['id' => $id]);
			return self::mapObjects($records)[0];
		} 
		
		/**
		 * Uses advanced parameters to find rows.
		 * @param $params Represents the WHERE, ORDER, LIMIT ... statement.
		 * @return Array of objects.
		 */
		public static function find_by($params) {
			$records = self::$database->select(static::$table_name, '*', $params);
			return self::mapObjects($records);
		} 
		
		/**
		 * Applies SQL for this model. Supports prepared statements.
		 * @param $query: The SQL statement.
		 * @param $params: Mixed array, The params for the prepared statement.
		 * @return Array of objects.
		 */
		public static function sql($query, $params=[]) {
			$records = self::$database->query($query, $params);			
			return self::mapObjects($records);
		} 
		
		/**
		 * The create method.
		 * @param $params: The params for creation.
		 * @return Object.
		 */
		public static function create($params) {
			
			// Ignore the id parameter
			unset($params['id']);
			$obj = new static($params);

			return $obj->save();
		} 
		
		/**
		 * The update method.
		 * @param $params: The params for updating.
		 * @return Object.
		 */
		public function update($params) {
			
			// Ignore the id parameter
			unset($params['id']);
			$this->set_attributes($params);
			
			return $this->save();
		} 


		/**
		 * Saves a model into the database. 
		 * This method is also used by update and create.
		 * All callbacks are fired in this method.
		 * @return Object.
		 */
		public function save() {
			
			$this->callback('callback_before_validate');
			if (!$this->validate()) return FALSE;
			
			$this->callback('callback_before_save');
			$this->set_timestamps();
			
			if (empty($this->id)) {
				$this->callback('callback_before_create');
				$this->id = self::$database->insert(static::$table_name, (array)$this);
				$this->callback('callback_after_create');
			} else {
				$this->callback('callback_before_update');
				self::$database->update(static::$table_name, (array)$this, ['id' => $this->id]);
				$this->callback('callback_after_update');
			}
			$this->callback('callback_after_save');
			
			return $this;
		}

		/**
		 * Deletes an object.
		 * @return Boolean: Returns FALSE if the deletion failed.
		 */
		public function delete() {
			$this->callback('before_delete');
			return self::$database->delete(static::$table_name, ['id' => $this->id]);
		}
		
		/**
		 * Validates an object by using the internal defined validate functions.
		 * @return Boolean: Returns FALSE if the validation failed.
		 */		
		public function validate() {
			$validated = TRUE;
			foreach(get_class_methods(get_called_class()) as $method)
				if (preg_match('/^validate_/', $method))
					$validated = $this->$method();
			
			return $validated;
		}
		
		/**
		 * Retrieves the related object.
		 * @param $model: The related model name.
		 * @param $foreign: The related foreign key.
		 * @return Array of objects.
		 */		
		public function belongs_to($model, $foreign='') {
			$id = $foreign == '' ? lcfirst($model).'_id' : $foreign;
			return $model::find($this->$id);
		}
		
		/**
		 * Retrieves the related objects.
		 * @param $model: The related model name.
		 * @param $foreign: The related foreign key.
		 * @return Array of objects.
		 */		
		public function has_many($model, $foreign='') {
			$id = $foreign == '' ? lcfirst(get_class($this)).'_id' : $foreign;
			return $model::find_by([$id => $this->id]); 
		}
		
		/**
		 * Retrieves the related objects
		 * @param $model: The related model name.
		 * @param $table: The n:m table name.
		 * @return Array of objects.
		 */		
		public function has_many_through($model, $table) {
			$id_self = lcfirst(get_class($this)).'_id';
			$id_other = lcfirst($model).'_id';
			
			$records = self::$database->select($model::$table_name, ['[>]'.$table => ['id' => $id_other]], '*', [$table.'.'.$id_self => $this->id]);
			return $model::mapObjects($records);
		}
		
		/**
		 * Sets this object attributes to an array of parameters.
		 * @param $params: The attribute parameters.
		 */
		private function set_attributes($params) {
			foreach ($params as $key => $value)
				$this->$key = $value;
		}
		
		/**
		 * Sets this object timestamps.
		 */
		private function set_timestamps() {
			$now = date("Y-m-d H:i:s");
			$this->updated_at = $now;
			
			if (empty($this->id))
				$this->created_at = $now;
		}
		
		/**
		 * This method looks if a callback exists and executes it.
		 * @param $name: The name of the callback.
		 */	
		private function callback($name) {
			if (method_exists($this, $name))
				$this->$name();	
		}
		
		/**
		 * This method is private and maps database records to objects.
		 * The object fields are gernerated automatically.
		 */	
		private static function mapObjects($records) {
			$result = array();
			foreach($records as $record) {
				$obj = new static();
				foreach($record as $key => $value)
					$obj->$key = $value;

				$result[] = $obj;
			}
			
			return $result;
		}


	}
	
?>