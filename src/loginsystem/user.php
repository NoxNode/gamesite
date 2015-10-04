<?php
class User {
	public static function isValidUser($username, $password) {
		return Queries::getRowWithTwoValues(Database::$table_users, "username", $username, "password", $password);
	}

	public static function isAUser($username) {
		return Queries::getRowWithValue(Database::$table_users, "username", $username);
	}

	public static function isValidGuildname($guildname) {
        return Queries::getRowWithValue(Database::$table_guilds, "guildname", $guildname);
	}

	public static function isGuildMaster($username, $guildname) {
        return Queries::getRowWithValue(Database::$table_guilds, "guildmaster", $username);
	}

	public static function register($username, $password) {
		if(!User::isAUser($username)) {
			if(Queries::insertWithTwoVals(Database::$table_users, "username", $username, "password", $password)) {
				return "success";
			}
			return "failed to access database";
		}
		return "username already exists";
	}

	public static function request($username, $password, $username2) {
		if(User::isValidUser($username, $password)) {
			if($username != $username2 && User::isAUser($username2)) {
				if(!User::areFriends($username, $username2)) {
					if(!Queries::getRowWithTwoValues(Database::$table_requests, "username1", $username, "username2", $username2)) {
						if(Queries::insertWithTwoVals(Database::$table_requests, "username1", $username, "username2", $username2)) {
							return "success";
						}
						return "failed to access database";
					}
					return "request already sent";
				}
				return "users are already friends";
			}
			return "invalid username2 or username2 is the same as username";
		}
		return "invalid user";
	}

	public static function acceptfriend($username, $password, $username2) {
		if(User::isValidUser($username, $password)) {
	        if(Queries::getRowWithTwoValues(Database::$table_requests, "username2", $username, "username1", $username2)) {
				if(Queries::deleteWithTwoVals(Database::$table_requests, "username2", $username, "username1", $username2)
					&& Queries::insertWithTwoVals(Database::$table_friends, "username1", $username, "username2", $username2)
					&& Queries::insertWithTwoVals(Database::$table_friends, "username2", $username, "username1", $username2)) {
						return "success";
				}
				return "failed to access database";
			}
			return "request not found";
		}
		return "invalid user";
	}

	public static function invite($username, $password, $guildname, $username2) {
		if($username != $username2 && User::isValidUser($username, $password)) {
			if(User::isAUser($username2)) {
				if(User::isValidGuildname($guildname)) {
					if(User::isGuildMaster($username, $guildname)) {
						if(!User::isInGuild($username2, $guildname)) {
							if(!Queries::getRowWithTwoValues(Database::$table_invites, "guildname", $guildname, "username", $username2)) {
								if(Queries::insertWithTwoVals(Database::$table_invites, "guildname", $guildname, "username", $username2)) {
									return "success";
								}
								return "failed to access database";
							}
							return "invite already sent";
						}
						return "user is already in guild";
					}
					return "only guildmaster can do this";
				}
				return "invalid guildname";
			}
			return "invalid username2";
		}
		return "invalid user or username and username2 are the same";
	}

	public static function acceptguild($username, $password, $guildname) {
		if(User::isValidUser($username, $password)) {
			if(User::isValidGuildname($guildname)) {
				$row = Queries::getRowWithValue(Database::$table_invites, "username", $username);
				if($row && $row['guildname'] == $guildname) {
					if(Queries::deleteWithTwoVals(Database::$table_invites, "username", $username, "guildname", $guildname)
					&& Queries::insertWithTwoVals(Database::$table_members, "username", $username, "guildname", $guildname)) {
						return "success";
					}
					return "failed to access database";
				}
				return "no invite to accept";
			}
			return "invalid guildname";
		}
		return "invalid user";
	}

	public static function areFriends($username, $username2) {
		$rows = Queries::getRowsWithValue(Database::$table_friends, "username1", $username);
		for($i = 0; $i < sizeof($rows); $i++) {
			if($rows[$i]['username2'] == $username2) {
				return true;
			}
		}
		return false;
	}

	public static function listfriends($username, $password) {
		if(User::isValidUser($username, $password)) {
			$retVal = "";

			$rows = Queries::getRowsWithValue(Database::$table_friends, "username1", $username);
			for($i = 0; $i < sizeof($rows); $i++) {
				$retVal = $retVal . $rows[$i]['username2'] . "<br>";
			}

			return $retVal;
		}
		return "invalid user";
	}

	public static function isonline($username, $password, $username2) {
		if(User::isValidUser($username, $password)) {
			if(User::isAUser($username2)) {
				if($username === $username2 || User::areFriends($username, $username2)) {
					$row = Queries::getRowWithValue(Database::$table_users, "username", $username2);
					if($row && $row['online'] == 1) {
						return 1;
					}
					return 0;
				}
				return "users aren't friends. access denied";
			}
			return "invalid username2";
		}
		return "invalid user";
	}

	public static function setonline($username, $password, $online) {
		if(User::isValidUser($username, $password)) {
			if(Queries::setValue(Database::$table_users, "username", $username, "online", $online)) {
				return "success";
			}
			return "failed to access database";
		}
		return "invalid user";
	}

	public static function removefriend($username, $password, $username2) {
		if(User::isValidUser($username, $password)) {
			if(User::areFriends($username, $username2)) {
				$rows = Queries::getRowsWithValue(Database::$table_friends, "username1", $username);
				for($i = 0; $i < sizeof($rows); $i++) {
					if($rows[$i]['username2'] == $username2) {
						if(Queries::deleteWithTwoVals(Database::$table_friends, "username1", $username, "username2", $username2)
						&& Queries::deleteWithTwoVals(Database::$table_friends, "username2", $username, "username1", $username2)) {
							return "success";
						}
					}
				}
				return "failed to access database";
			}
			return "users aren't friends";
		}
		return "invalid user";
	}

	public static function isInGuild($username, $guildname) {
		if(Queries::getRowWithValue(Database::$table_guilds, "guildmaster", $username)) {
			return 2;
		}
		$rows = Queries::getRowsWithValue(Database::$table_members, "guildname", $guildname);
		for($i = 0; $i < sizeof($rows); $i++) {
			if($rows[$i]['username'] == $username) {
				return 1;
			}
		}
		return false;
	}

	public static function disband($guildname) {
		return (Queries::deleteValueFromTable(Database::$table_guilds, "guildname", $guildname)
		&& Queries::deleteAllValueFromTable(Database::$table_members, "guildname", $guildname));
	}

	// NOTE: also disbands if username is guildmaster
	public static function leave($username, $password, $guildname) {
		if(User::isValidUser($username, $password)) {
			if(User::isValidGuildname($guildname)) {
				$member_status = User::isInGuild($username, $guildname);
				if($member_status == 1) {
					if(Queries::deleteWithTwoVals(Database::$table_members, "username", $username, "guildname", $guildname)) {
						return "success";
					}
					return "failed to access database";
				}
				else if($member_status == 2) {
					if(User::disband($guildname)) {
						return "success";
					}
					return "failed to access database";
				}
				else if($member_status == 0) {
					return "user isn't a member of specified guild";
				}
			}
			return "invalid guildname";
		}
		return "invalid user";
	}

	public static function kick($username, $password, $guildname, $username2) {
		if(User::isValidUser($username, $password)) {
			if(User::isValidGuildname($guildname)) {
				if(User::isGuildMaster($username, $guildname)) {
					if(User::isInGuild($username2, $guildname)) {
						if(Queries::deleteWithTwoVals(Database::$table_members, "username", $username2, "guildname", $guildname)) {
							return "success";
						}
						return "failed to access database";
					}
					return "username2 isn't in guildname";
				}
				return "only guildmaster can do this";
			}
			return "invalid guildname";
		}
		return "invalid user";
	}

	public static function setrank($username, $password, $guildname, $username2, $rank) {
		if(User::isValidUser($username, $password)) {
			if(User::isValidGuildname($guildname)) {
				if(User::isGuildMaster($username, $guildname)) {
					if(User::isInGuild($username2, $guildname)) {
						if(Queries::setValueWithTwoConditions(Database::$table_members, "username", $username2, "guildname", $guildname, "rank", $rank)) {
							return "success";
						}
						return "failed to access database";
					}
					return "username2 isn't in guildname";
				}
				return "only guildmaster can do this";
			}
			return "invalid guildname";
		}
		return "invalid user";
	}

	public static function makeguild($username, $password, $guildname) {
		if(User::isValidUser($username, $password)) {
			if(!User::isValidGuildname($guildname)) {
				if(Queries::insertWithTwoVals(Database::$table_guilds, "guildname", $guildname, "guildmaster", $username)) {
					return "success";
				}
				return "failed to access database";
			}
			return "guildname already exists";
		}
		return "invalid user";
	}

	// NOTE: doesn't remove username2 from members, if they want to do that, kick username2 from the guild, then set them as the guildmaster
	public static function setguildmaster($username, $password, $guildname, $username2) {
		if(User::isValidUser($username, $password)) {
			if(User::isValidGuildname($guildname)) {
				if(User::isGuildMaster($username, $guildname)) {
					if(User::isAUser($username2)) {
						if(Queries::setValue(Database::$table_guilds, "guildname", $guildname, "guildmaster", $username2)) {
							return "success";
						}
						return "failed to access database";
					}
					return "username2 isn't a user";
				}
				return "only guildmaster can do this";
			}
			return "invalid guildname";
		}
		return "invalid user";
	}

	public static function viewmembers($username, $password, $guildname) {
		if(User::isValidUser($username, $password)) {
			if(User::isValidGuildname($guildname)) {
				$userIsInGuild = false;
				$retVal = "";

				$row = Queries::getRowWithValue(Database::$table_guilds, "guildname", $guildname);
				if($row['guildmaster'] == $username) {
					$userIsInGuild = true;
				}
				$retVal = $retVal . "guildmaster: " . $row['guildmaster'];
				$retVal = $retVal . "<br>";

				$rows = Queries::getRowsWithValue(Database::$table_members, "guildname", $guildname);
				for($i = 0; $i < sizeof($rows); $i++) {
					if($rows[$i]['username'] == $username) {
						$userIsInGuild = true;
					}
					$retVal = $retVal . "member: " . $rows[$i]['username'];
					$retVal = $retVal . " rank: " . $rows[$i]['rank'];
					$retVal = $retVal . "<br>";
				}

				if($userIsInGuild) {
					return $retVal;
				}
				else {
					return "user not in specified guild, access denied";
				}
			}
			return "invalid guildname";
		}
		return "invalid user";
	}

	public static function viewmyrequests($username, $password) {
		if(User::isValidUser($username, $password)) {
			$retVal = "";

			$rows = Queries::getRowsWithValue(Database::$table_requests, "username1", $username);
			for($i = 0; $i < sizeof($rows); $i++) {
				$retVal = $retVal . $rows[$i]['username2'] . "<br>";
			}

			return $retVal;
		}
		return "invalid user";
	}

	public static function viewrequests($username, $password) {
		if(User::isValidUser($username, $password)) {
			$retVal = "";

			$rows = Queries::getRowsWithValue(Database::$table_requests, "username2", $username);
			for($i = 0; $i < sizeof($rows); $i++) {
				$retVal = $retVal . $rows[$i]['username1'] . "<br>";
			}

			return $retVal;
		}
		return "invalid user";
	}

	public static function viewmyinvites($username, $password) {
		if(User::isValidUser($username, $password)) {
			$retVal = "";

			$rows = Queries::getRowsWithValue(Database::$table_invites, "username", $username);
			for($i = 0; $i < sizeof($rows); $i++) {
				$retVal = $retVal . $rows[$i]['guildname'] . "<br>";
			}

			return $retVal;
		}
		return "invalid user";
	}

	public static function viewguildsinvites($username, $password, $guildname) {
		if(User::isValidUser($username, $password)) {
			if(User::isInGuild($username, $guildname)) {
				$retVal = "";

				$rows = Queries::getRowsWithValue(Database::$table_invites, "guildname", $guildname);
				for($i = 0; $i < sizeof($rows); $i++) {
					$retVal = $retVal . $rows[$i]['username'] . "<br>";
				}

				return $retVal;
			}
			return "user not in specified guild, access denied";
		}
		return "invalid user";
	}

	public static function declinefriend($username, $password, $username2) {
		if(User::isValidUser($username, $password)) {
			$rows = Queries::getRowsWithValue(Database::$table_requests, "username2", $username);
			for($i = 0; $i < sizeof($rows); $i++) {
				if($rows[$i]['username1'] == $username2) {
					if(Queries::deleteWithTwoVals(Database::$table_requests, "username1", $username2, "username2", $username)) {
						return "success";
					}
					return "failed to access database";
				}
			}
			return "request not found";
		}
		return "invalid user";
	}

	public static function declineinvite($username, $password, $guildname) {
		if(User::isValidUser($username, $password)) {
			$rows = Queries::getRowsWithValue(Database::$table_invites, "username", $username);
			for($i = 0; $i < sizeof($rows); $i++) {
				if($rows[$i]['guildname'] == $guildname) {
					if(Queries::deleteWithTwoVals(Database::$table_invites, "username", $username, "guildname", $guildname)) {
						return "success";
					}
					return "failed to access database";
				}
			}
			return "invite not found";
		}
		return "invalid user";
	}

	public static function removerequest($username, $password, $username2) {
		if(User::isValidUser($username, $password)) {
			$rows = Queries::getRowsWithValue(Database::$table_requests, "username1", $username);
			for($i = 0; $i < sizeof($rows); $i++) {
				if($rows[$i]['username2'] == $username2) {
					if(Queries::deleteWithTwoVals(Database::$table_requests, "username1", $username, "username2", $username2)) {
						return "success";
					}
					return "failed to access database";
				}
			}
			return "request not found";
		}
		return "invalid user";
	}

	public static function removeinvite($username, $password, $guildname, $username2) {
		if(User::isValidUser($username, $password)) {
			if(User::isGuildMaster($username, $guildname)) {
				$rows = Queries::getRowsWithValue(Database::$table_invites, "guildname", $guildname);
				for($i = 0; $i < sizeof($rows); $i++) {
					if($rows[$i]['username'] == $username2) {
						if(Queries::deleteWithTwoVals(Database::$table_invites, "username", $username2, "guildname", $guildname)) {
							return "success";
						}
						return "failed to access database";
					}
				}
				return "request not found";
			}
			return "only guildmaster can do this";
		}
		return "invalid user";
	}
}
?>
