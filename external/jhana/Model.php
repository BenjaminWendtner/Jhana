<?php

	/** 
	 * This is the model baseclass. 
	 * It enables all models to find, save, update and delete data. 
	 * Do NOT change this class, unless your know what you're doing!
	 */
	class Model {
		
		protected static $database;
		
		
		public static function set_database($database) {
	       self::$database = $database;
	   	}
				
		/**
		 * The all method
		 * @return array of objects
		 */
		public static function all() {
			$records = self::$database->select(static::$table_name, "*");
			return self::mapObjects($records);
		} 
		
		/**
		 * The count method.
		 * @param $params: Represents the where statement
		 * @return integer
		 */
		public static function count($params=[]) {
			$count = self::$database->count(static::$table_name, $params);
			return $count;
		} 
		
		/**
		 * The find method. Uses the "id" database field.
		 * @param $id
		 * @return Object
		 */
		public static function find($id) {
			$records = self::$database->select(static::$table_name, '*', ['id' => $id]);
			return self::mapObjects($records)[0];
		} 

		/**
		 * The find-by-column method
		 * @param $column, $value: Represents the SQL-WHERE statement
		 * @return array of objects
		 */
		public static function find_by_column($column, $value) {
			$records = self::$database->select(static::$table_name, '*', [$column => $value]);
			return self::mapObjects($records);
		} 
		
		/**
		 * The find-by method
		 * @param $params Represents the SQL-WHERE statement
		 * @return array of objects
		 */
		public static function find_by($params) {
			$records = self::$database->select(static::$table_name, '*', $params);
			return self::mapObjects($records);
		} 
		
		/**
		 * The apply_sql method
		 * @param $query: The SQL statement
		 * @param $params: Mixed arrays, The params for the prepared statement
		 * @return array of objects
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
		 * The create method.
		 * @param $params: The params for creation
		 * @return boolean
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
		 * The save method
		 * @return boolean
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
		 * The update method.
		 * @param $params: The params for updating
		 * @return boolean
		 */
		public function update($params) {
			
			if (!$this->validate() || empty($this->id)) return FALSE;
			
			self::$database->update(static::$table_name, $params, ['id' => $this->id]);
			
			if (method_exists($this, 'callback_save'))
				$this->callback_save();

			return TRUE;
		} 
		
		
		/**
		 * The delete method
		 * @return boolean
		 */
		public function delete() {
			
			if (method_exists($this, 'callback_delete'))
				$this->callback_delete();
			
			return self::$database->delete(static::$table_name, ['id' => $this->id]);
		}
		
		/**
		 * The validation method
		 * @return boolean
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
		 * This method maps database records to an object
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