<?php
class Database {

	public static $host = "host";

	public static $username = "username";

	public static $password = "password";

	public static $db_name = "db_name";

	public static $table_users = "users";
	public static $table_requests = "requests";
	public static $table_friends = "friends";
	public static $table_invites = "invites";
	public static $table_members = "members";
	public static $table_guilds = "guilds";

	public static $isConnected = false;

	public static function connect() {
		if (! self::$isConnected) {
			mysql_connect (self::$host, self::$username, self::$password) or die ("Cannot connect to server");

			mysql_select_db (self::$db_name) or die ("Cannot select DB");

			self::$isConnected = true;
		}
	}
}
?>
