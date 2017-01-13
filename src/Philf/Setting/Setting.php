<?php namespace Philf\Setting;

use Philf\Setting\interfaces\AdapterInterface;
/*
 * ---------------------------------------------
 * | Do not remove!!!!                         |
 * |                                           |
 * | @package   PhoenixCore                    |
 * | @version   2.0                            |
 * | @develper  Phil F (http://www.Weztec.com) |
 * | @author    Phoenix Development Team       |
 * | @license   Free to all                    |
 * | @copyright 2013 Phoenix Group             |
 * | @link      http://www.phoenix-core.com    |
 * ---------------------------------------------
 *
 * Example syntax:
 * use Setting (If you are using namespaces)
 *
 * Single dimension
 * set:         Setting::set('name', 'Phil'))
 * get:         Setting::get('name')
 * forget:      Setting::forget('name')
 * has:         Setting::has('name')
 *
 * Multi dimensional
 * set:         Setting::set('names' , array('firstName' => 'Phil', 'surname' => 'F'))
 * setArray:    Setting::setArray(array('firstName' => 'Phil', 'surname' => 'F'))
 * get:         Setting::get('names.firstName')
 * forget:      Setting::forget('names.surname'))
 * has:         Setting::has('names.firstName')
 *
 * Clear:
 * clear:        Setting::clear()
 *
 */

/**
 * Class Setting
 * @package Philf\Setting
 */
class Setting {

    /**
     * Adapter class
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * The class working array
     * @var array
     */
    protected $settings;

    /**
     * Create the Setting instance
     * @param AdapterInterface $adapter
     * @param null|string|int $configName
     * @param interfaces\FallbackInterface $fallback
     */
    public function __construct(AdapterInterface $adapter, $configName = null, $fallback = null)
    {
        $this->adapter  = $adapter;
        $this->configName  = $configName;
        $this->fallback = $fallback;

        // Load the file and store the contents in $this->settings
        $this->load();
    }

    /**
     * Get a value and return it
     * @param string $key String using dot notation
     * @param Mixed $default
     * @return Mixed             The value(s) found
     */
    public function get(string $key = null, $default = null)
    {
        if (empty($key))
        {
            return $this->settings;
        }

        $ts = microtime(true);

        if($ts !== array_get($this->settings, $key, $ts))
        {
            return array_get($this->settings, $key);
        }

        if ( ! is_null($this->fallback) and $this->fallback->fallbackHas($key))
        {
            return $this->fallback->fallbackGet($key, $default);
        }

        return $default;
    }

     /**
     * Store the passed value in to the json file
     * @param $key
     * @param  mixed $value The value(s) to be stored
     * @return void
     */
    public function set($key, $value)
    {
        array_set($this->settings,$key,$value);
        $this->save();
        $this->load();
    }

    /**
     * Forget the value(s) currently stored
     * @param  mixed $deleteKey The value(s) to be removed (dot notation)
     * @return void
     */
    public function forget($deleteKey)
    {
        array_forget($this->settings,$deleteKey);
        $this->save();
        $this->load();
    }

    /**
     * Check to see if the value exists
     * @param  string  $searchKey The key to search for
     * @return boolean            True: found - False not found
     */
    public function has($searchKey)
    {
        $default = microtime(true);

        if(null !== $this->fallback && $default === array_get($this->settings, $searchKey, $default))
        {
            return $this->fallback->fallbackHas($searchKey);
        }
        return $default !== array_get($this->settings, $searchKey, $default);
    }

    /**
     * Load the file in to $this->settings so values can be used immediately
     * @param null $configName
     * @return Setting
     */
    public function load($configName = null)
    {
        $this->settings = json_decode($this->adapter->load($configName ?? $this->configName), true);
        return $this;
    }

    /**
     * Save the file
     * @param null $configName
     * @return void
     */
    public function save($configName = null)
    {
        $this->adapter->save($configName ?? $this->configName, json_encode($this->settings));
    }

    /**
     * Clears the JSON Config file
     * @param null $configName
     */
    public function clear($configName = null)
    {
        $this->settings = [];
        $this->save($configName);
        $this->load();
    }

    /**
     * This will mass assign data to the Setting
     * @param array $data
     * @param bool $storeAsNew
     * @param null|string|int $configName
     */
    public function setArray(array $data, bool $storeAsNew = false, $configName = null)
    {
        foreach ($data as $key => $value)
        {
            array_set($this->settings,$key,$value);
        }

        if($storeAsNew && $configName) {
            $this->configName = $configName;
        }

        $this->save();
        $this->load();
    }
}