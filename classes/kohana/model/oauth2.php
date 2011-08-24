<?php defined('SYSPATH') or die('No direct script access.');

/**
 *
 *
 * @package    OAuth2
 * @category   Model
 * @author     Managed I.T.
 * @copyright  (c) 2011 Managed I.T.
 * @license    https://github.com/managedit/kohana-oauth2/blob/master/LICENSE.md
 */
abstract class Kohana_Model_OAuth2
	extends Model
	implements Interface_Model_OAuth2
{
	/**
	 * @var  string  Table name
	 */
	protected $_table_name = NULL;

	/**
	 * @var  array  Array of data
	 */
	protected $_data = array();

	/**
	 * @var  array Array of field names
	 */
	protected $_fields = array();

	/**
	 * @var  boolean  Has this model been loaded from the DB?
	 */
	protected $_loaded = FALSE;

	/**
	 * @var  boolean  Is the latest data saved?
	 */
	protected $_saved = FALSE;

	/**
	 * @var  Config   Configuration
	 */
	protected $_config = NULL;

	public function __construct($params = array())
	{
		$this->_config = Kohana::$config->load('oauth2');

		foreach ($params as $param => $value)
		{
			$this->{'_'.$param} = $value;
		}
	}

	/**
	 * Set a field
	 *
	 * @param  string  $field
	 * @param  string  $value
	 * @return void
	 */
	public function __set($field, $value)
	{
		if (in_array($field, $this->_fields))
		{
			$this->_data[$field] = $value;
			$this->_saved = FALSE;

			return;
		}

		throw new Kohana_Exception('Invalid field :field', array(
			':field' => $field,
		));
	}

	/**
	 * Get a field
	 *
	 * @param  string $field
	 * @return mixed
	 */
	public function __get($field)
	{
		if (in_array($field, $this->_fields))
			return isset($this->_data[$field]) ? $this->_data[$field] : NULL;

		return NULL;
	}

	/**
	 * Get the primary key
	 *
	 * @return string
	 */
	public function pk()
	{
		return $this->id;
	}

	/**
	 * Is the object saved?
	 *
	 * @return boolean
	 */
	public function saved()
	{
		return $this->_saved;
	}

	/**
	 * Is the object in the DB?
	 *
	 * @return boolean
	 */
	public function loaded()
	{
		return $this->_loaded;
	}

	/**
	 * Inserts/Updates this model
	 *
	 * @return $this
	 */
	public function save()
	{
		if ($this->_saved)
			return $this;

		if ($this->_loaded)
		{
			// Update
			DB::update($this->_table_name)
				->set($this->_data)
				->where('id', '=', $this->pk())
				->execute();

			$this->_saved = TRUE;
		}
		else
		{
			// Insert
			list($insert_id, $affected_rows) = DB::insert($this->_table_name, array_keys($this->_data))
				->values(array_values($this->_data))
				->execute();

			$this->id = $insert_id;

			$this->_loaded = TRUE;
			$this->_saved = TRUE;
		}

		return $this;
	}

	/**
	 * Deletes this model
	 *
	 * @return $this
	 */
	public function delete()
	{
		if ( ! $this->loaded())
			return TRUE;

		DB::delete($this->_table_name)
			->where('id', '=', $this->pk())
			->execute();

		return TRUE;
	}
}