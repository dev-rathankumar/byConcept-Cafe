<?php

class ESIGDS_Factory
{
	private static
		$objectCache = array(),
		$aliases = array(
			'dropbox' => 'DropboxFacade',
		);

	private static function getClassName($name)
	{
		if (isset(self::$aliases[$name])) {
			$name = self::$aliases[$name];
		}

		$class = '';
		foreach (explode('-', $name) as $bit) {
			$class .= '_' . ucfirst($bit);
		}

		return 'ESIGDS' . $class;
	}

	public static function db()
	{
		if (!isset(self::$objectCache['WPDB'])) {
			global $wpdb;

			if ($wpdb) {
				$wpdb->hide_errors();
			}

			if (defined('WPESIGDS_TEST_MODE')) {
				$wpdb->show_errors();
			}

			self::$objectCache['WPDB'] = $wpdb;
		}

	   return self::$objectCache['WPDB'];
	}

	public static function get($name)
	{
		$className = self::getClassName($name);

		if (!class_exists($className)) {
			return null;
		}

		if (!isset(self::$objectCache[$className])) {
			self::$objectCache[$className] = new $className();
		}

		return self::$objectCache[$className];
	}

	public static function set($name, $object)
	{
		if ($name == 'db') {
			self::$objectCache['WPDB'] = $object;
		} else {
			self::$objectCache[self::getClassName($name)] = $object;
		}
	}

	public static function reset()
	{
		self::$objectCache = array();
	}

	public static function secret($data)
	{
		return hash_hmac('sha1', $data, uniqid(mt_rand(), true)) . '-eddds-secret';
	}
}