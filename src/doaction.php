<?php
	include 'includes.php';

    $action = filter_input (INPUT_GET, 'action');
	$username = filter_input (INPUT_GET, 'username');
	$password = filter_input (INPUT_GET, 'password');
	$online = filter_input (INPUT_GET, 'online');
	$username2 = filter_input (INPUT_GET, 'username2');
	$guildname = filter_input (INPUT_GET, 'guildname');
    $rank = filter_input (INPUT_GET, 'rank');

	$invalid_username = false;
	$invalid_password = false;
	$invalid_online = false;
	$invalid_username2 = false;
	$invalid_guildname = false;
	$invalid_rank = false;

    if (!Input::isValidInput ($username, 1, 100, ctype_alnum ($username))) {
		$invalid_username = true;
	}
    if (!Input::isValidInput ($password, 1, 100, ctype_alnum ($password))) {
		$invalid_password = true;
	}
    if (!($online == 0 || $online == 1)) {
		$invalid_online = true;
	}
    if (!Input::isValidInput ($username2, 1, 100, ctype_alnum ($username2))) {
		$invalid_username2 = true;
	}
    if (!Input::isValidInput ($guildname, 1, 100, ctype_alnum ($guildname))) {
		$invalid_guildname = true;
	}
    if (!Input::isValidInput ($rank, 1, 100, ctype_alnum ($rank))) {
		$invalid_rank = true;
	}

	$only_alnum = ", only letters and numbers allowed";

	switch($action) {
		case "register":
			if($invalid_username || $invalid_password) {
				echo "invalid username or password" . $only_alnum;
			}
			else {
				echo User::register($username, $password);
			}
			break;
		case "request":
			if($invalid_username || $invalid_password || $invalid_username2) {
				echo "invalid username, password, or username2" . $only_alnum;
			}
			else {
				echo User::request($username, $password, $username2);
			}
			break;
		case "acceptfriend":
			if($invalid_username || $invalid_password || $invalid_username2) {
				echo "invalid username, password, or username2" . $only_alnum;
			}
			else {
				echo User::acceptfriend($username, $password, $username2);
			}
			break;
		case "invite":
			if($invalid_username || $invalid_password || $invalid_guildname || $invalid_username2) {
				echo "invalid username, password, guildname, or username2" . $only_alnum;
			}
			else {
				echo User::invite($username, $password, $guildname, $username2);
			}
			break;
		case "acceptguild":
			if($invalid_username || $invalid_password || $invalid_guildname) {
				echo "invalid username, password, or guildname" . $only_alnum;
			}
			else {
				echo User::acceptguild($username, $password, $guildname);
			}
			break;
		case "listfriends":
			if($invalid_username || $invalid_password) {
				echo "invalid username or password" . $only_alnum;
			}
			else {
				echo User::listfriends($username, $password);
			}
			break;
		case "isonline":
			if($invalid_username || $invalid_password || $invalid_username2) {
				echo "invalid username, password, or username2" . $only_alnum;
			}
			else {
				echo User::isonline($username, $password, $username2);
			}
			break;
		case "setonline":
			if($invalid_username || $invalid_password || $invalid_online) {
				echo "invalid username, password, or online" . $only_alnum . " for username and password, only 0 or 1 for online";
			}
			else {
				echo User::setonline($username, $password, $online);
			}
			break;
		case "removefriend":
			if($invalid_username || $invalid_password || $invalid_username2) {
				echo "invalid username, password, or username2" . $only_alnum;
			}
			else {
				echo User::removefriend($username, $password, $username2);
			}
			break;
		case "leave":
			if($invalid_username || $invalid_password || $invalid_guildname) {
				echo "invalid username, password, or guildname";
			}
			else {
				echo User::leave($username, $password, $guildname);
			}
			break;
		case "kick":
			if($invalid_username || $invalid_password || $invalid_guildname || $invalid_username2) {
				echo "invalid username, password, guildname, or username2" . $only_alnum;
			}
			else {
				echo User::kick($username, $password, $guildname, $username2);
			}
			break;
		case "setrank":
			if($invalid_username || $invalid_password || $invalid_guildname || $invalid_username2 || $invalid_rank) {
				echo "invalid username, password, guildname, username2, or rank" . $only_alnum;
			}
			else {
				echo User::setrank($username, $password, $guildname, $username2, $rank);
			}
			break;
		case "makeguild":
			if($invalid_username || $invalid_password || $invalid_guildname) {
				echo "invalid username, password, or guildname" . $only_alnum;
			}
			else {
				echo User::makeguild($username, $password, $guildname);
			}
			break;
		case "setguildmaster":
			if($invalid_username || $invalid_password || $invalid_guildname || $invalid_username2) {
				echo "invalid username, password, guildname, or username2" . $only_alnum;
			}
			else {
				echo User::setguildmaster($username, $password, $guildname, $username2);
			}
			break;
		case "viewmembers":
			if($invalid_username || $invalid_password || $invalid_guildname) {
				echo "invalid username, password, or guildname" . $only_alnum;
			}
			else {
				echo User::viewmembers($username, $password, $guildname);
			}
			break;
		case "viewmyrequests":
			if($invalid_username || $invalid_password) {
				echo "invalid username or password" . $only_alnum;
			}
			else {
				echo User::viewmyrequests($username, $password);
			}
			break;
		case "viewrequests":
			if($invalid_username || $invalid_password) {
				echo "invalid username or password" . $only_alnum;
			}
			else {
				echo User::viewrequests($username, $password, $guildname);
			}
			break;
		case "viewmyinvites":
			if($invalid_username || $invalid_password) {
				echo "invalid username or password" . $only_alnum;
			}
			else {
				echo User::viewmyinvites($username, $password, $guildname);
			}
			break;
		case "viewguildsinvites":
			if($invalid_username || $invalid_password || $invalid_guildname) {
				echo "invalid username, password, or guildname" . $only_alnum;
			}
			else {
				echo User::viewguildsinvites($username, $password, $guildname);
			}
			break;
		case "declinefriend":
			if($invalid_username || $invalid_password || $invalid_username2) {
				echo "invalid username, password, or username2" . $only_alnum;
			}
			else {
				echo User::declinefriend($username, $password, $username2);
			}
			break;
		case "declineinvite":
			if($invalid_username || $invalid_password || $invalid_guildname) {
				echo "invalid username, password, or guildname" . $only_alnum;
			}
			else {
				echo User::declineinvite($username, $password, $guildname);
			}
			break;
		case "removerequest":
			if($invalid_username || $invalid_password || $invalid_username2) {
				echo "invalid username, password, or username2" . $only_alnum;
			}
			else {
				echo User::removerequest($username, $password, $username2);
			}
			break;
		case "removeinvite":
			if($invalid_username || $invalid_password || $invalid_guildname || $invalid_username2) {
				echo "invalid username, password, guildname, or username2" . $only_alnum;
			}
			else {
				echo User::removeinvite($username, $password, $guildname, $username2);
			}
			break;
		default:
			break;
	}
?>
