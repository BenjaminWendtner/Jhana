<?php

	/** 
	 * This is the model baseclass. 
	 * It enables all models to find, count, save, update and delete data. 
	 */
	class Model {
		
		protected static $database;
		
		
		public static function set_database($database) {
	       self::$database = $database;
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
			
			// Prepare statement
			if (isset($params))
				foreach ($params as $param)
					$query = preg_replace('/\?/', $param, $query, 1);
			
			$records = self::$database->query($query);
			return self::mapObjects($records);
		} 
		
		/**
		 * Creates a new row in the database with the specified parameters.
		 * @param $params: The params for creation.
		 * @return Boolean: Returns FALSE if the validation failed.
		 */
		public static function create($params) {
			
			$obj = new static;
			foreach ($params as $key => $value)
				$obj->$key = $value;
			
			if (!$obj->validate()) return FALSE;
			
			$obj->id = self::$database->insert(static::$table_name, $params);
			
			if (method_exists($obj, 'callback_save'))
				$obj->callback_save();
			
			return $obj;
		} 
		
		/**
		 * Saves an object to the database. 
		 * Automatically detects if the object is new or has been updated.
		 * @return Boolean: Returns FALSE if the validation failed.
		 */
		public function save() {
			
			if (!$this->validate()) return FALSE;
			
			if (empty($this->id))
				$this->id = self::$database->insert(static::$table_name, (array)$this);
			else
				self::$database->update(static::$table_name, (array)$this, ['id' => $this->id]);
			
			if (method_exists($this, 'callback_save'))
				$this->callback_save();

			return TRUE;
		}

		/**
		 * Updates an object with the specified parameters.
		 * @param $params: The params for updating.
		 * @return Boolean: Returns FALSE if the validation failed.
		 */
		public function update($params) {
			
			if (!$this->validate() || empty($this->id)) return FALSE;
			
			self::$database->update(static::$table_name, $params, ['id' => $this->id]);
			
			if (method_exists($this, 'callback_save'))
				$this->callback_save();

			return TRUE;
		} 
		
		/**
		 * Deletes an object.
		 * @return Boolean: Returns FALSE if the deletion failed.
		 */
		public function delete() {
			
			if (method_exists($this, 'callback_delete'))
				$this->callback_delete();
			
			return self::$database->delete(static::$table_name, ['id' => $this->id]);
		}
		
		/**
		 * Validates an object by using the internal defined validate functions.
		 * @return Boolean: Returns FALSE if the validation failed.
		 */		
		public function validate() {
				
			if (method_exists($this, 'callback_validate'))
				$this->callback_validate();	
			
			$validated = TRUE;
			
			foreach(get_class_methods(get_called_class()) as $method)
				if (preg_match('/^validate_/', $method))
					$validated = $this->$method();

			return $validated;
		}
		
		/**
		 * Retrieves the related object.
		 * @param $class: The related class name.
		 * @return Array of objects.
		 */		
		public function belongs_to($class) {
			$id = lcfirst($class).'_id';
			return $class::find($this->$id);
		}
		
		/**
		 * Retrieves the related objects.
		 * @param $class: The related class name.
		 * @return Array of objects.
		 */		
		public function has_many($class) {
			$id = lcfirst(get_class($this)).'_id';
			return $class::find_by([$id => $this->id]); 
		}
		
		/**
		 * Retrieves the related objects
		 * @param $class: The related class name.
		 * @param $table: The n:m table name.
		 * @return Array of objects.
		 */		
		public function has_many_through($class, $table) {
			$id_self = lcfirst(get_class($this)).'_id';
			$id_other = lcfirst($class).'_id';
			
			$records = self::$database->select($class::$table_name, ['[>]'.$table => ['id' => $id_other]], '*', [$table.'.'.$id_self => $this->id]);
			return $class::mapObjects($records);
		}
		
		/**
		 * This method is private and maps database records to objects.
		 * The object fields are gernerated automatically.
		 */	
		private static function mapObjects($records) {
			$result = array();
			foreach($records as $record) {
				$obj = new static;
				foreach($record as $key => $value)
					$obj->$key = $value;

				$result[] = $obj;
			}
			
			return $result;
		}


	}
	
?>