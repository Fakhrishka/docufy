<?php
class ApiFunctions
{
	// static function apiAddCars()

	static function apiReadCars()
	{
		if(!($db = mysqli_connect("localhost", "root", "", "autospot_test")))
			echo 'pizdes';

		$query = mysqli_query($db, "SELECT * FROM users");
		echo json_encode(mysqli_fetch_all($query));

	}

	static function readGroup(int $nUserId, $db)
	{
		$query = mysqli_query($db, "SELECT GROUP_NAME FROM user WHERE USER_ID = '".intval($nUserId)."' LIMIT 1");
		$arUserData = mysqli_fetch_assoc($query);
		$arResult = Array();

		if($arUserData['GROUP_NAME'] === 'ADMIN')
		{
			$groupQuery = mysqli_query($db, "SELECT * FROM groups");
			while(!empty($arGroupDate = mysqli_fetch_assoc($groupQuery)))
				$arResult[] = $arGroupDate;

			file_put_contents('test3.txt', print_r($arResult,1));
		}

		
		if(!empty($arResult))
			echo json_encode($arResult);



	}

	// static function logCheck($nUserId, string $sHash, $db)
	// {
	// 	$query = mysqli_query($db, "SELECT HASH FROM user WHERE USER_ID='".intval($nUserId)."'");
	// 	if(empty($arData = mysqli_fetch_assoc($query)))
	// 	{
	// 		echo json_encode(Array('logged' => 'false'));
	// 		return false;
	// 	}

	// 	$bLog = ($arData['HASH'] === $sHash) ? 'true' : 'false';
	// 	echo json_encode(Array('logged' => $bLog));
	// }

	static function LoginCheckUp(int $nUserId, string $sCookieHash, $db)
	{
		$query = mysqli_query($db, "SELECT LOGIN,USER_ID,HASH FROM user WHERE USER_ID = '".intval($nUserId)."' LIMIT 1");
		$arUserData = mysqli_fetch_assoc($query);


		if(($arUserData['HASH'] !== $sCookieHash))
		{
			// setcookie("id", "", time() - 3600*24*30*12, "/");
			// setcookie("hash", "", time() - 3600*24*30*12, "/", null, null, true); // httponly !!!
			return false;
			// echo "Cookie error"; // CATCH THIS MF
		}
		else
			echo json_encode(
				Array(
					'logging_status' => 'true',
					'user_id' => $nUserId,
					'hash' => $sCookieHash,
				)
			);
	}

	static function Login(string $sLogin, string $sPass, $db)
	{
		$query = mysqli_query($db, "SELECT USER_ID,PASSWORD FROM user WHERE LOGIN='".mysqli_real_escape_string($db, $sLogin)."'");
		$arData = mysqli_fetch_assoc($query);

		if($arData['PASSWORD'] == md5(md5($sPass)))
		{
			$sNewHash = md5(md5(self::generateCode(10)));
			if(!mysqli_query($db,"UPDATE user SET HASH='".$sNewHash."' WHERE USER_ID='".$arData['USER_ID']."'"))
				return false;


			// setcookie("id", $arData['USER_ID'], time()+60*60*24*30, "/");
			// setcookie("hash", $sNewHash, time()+60*60*24*30, "/", null, null, true);

			// echo 'Login finished...<br>';
			self::LoginCheckUp($arData['USER_ID'], $sNewHash, $db);
		}
		else
		{
			// echo 'Password is wrong...';
			return false;
		}

	}

	static function Register(string $sLogin, string $sPass, string $sUserType, $db)
	{
		$query = mysqli_query($db, "SELECT USER_ID FROM users WHERE LOGIN='".mysqli_real_escape_string($db, $sLogin)."'");
		if(mysqli_num_rows($query) > 0)
		{
			echo 'user exists...';
			return false;
		}

		if(!mysqli_query($db,"INSERT INTO users SET LOGIN='".$sLogin."', PASS='".md5(md5(trim($sPass)))."', USER_TYPE='".$sUserType."'"))
			echo 'user not added';
		echo 'Register finished...<br>';
		self::Login($sLogin, $sPass, $db);

	}

	static function generateCode($length=6)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
		$code = "";
		$clen = strlen($chars) - 1;
		while (strlen($code) < $length)
			$code .= $chars[mt_rand(0,$clen)];
		return $code;
	}

	static function Logout()
	{
		// setcookie("id", "", time() - 3600*24*30*12, "/");
		// setcookie("hash", "", time() - 3600*24*30*12, "/", null, null, true);

		header("Location: /");
	}

	static function ChangePass(string $sNewPass, string $sOldPass, $db)
	{
		$query = mysqli_query($db, "SELECT USER_ID,PASS FROM users WHERE USER_ID='".$_COOKIE['id']."'");
		$arData = mysqli_fetch_assoc($query);

		if($arData['PASS'] == md5(md5($sOldPass)))
		{
			$sNewHash = md5(md5(self::generateCode(10)));
			if(!mysqli_query($db,"UPDATE users SET HASH='".$sNewHash."',PASS='".md5(md5(trim($sNewPass))) ."' WHERE USER_ID='".$arData['USER_ID']."'"))
				echo 'user not added';

			// setcookie("id", $arData['USER_ID'], time()+60*60*24*30, "/");
			// setcookie("hash", $sNewHash, time()+60*60*24*30, "/", null, null, true);

			echo 'Pass change finished...<br>';
			self::LoginCheckUp($arData['USER_ID'], $sNewHash, $db);
		}
		else
			echo 'Old password is wrong';

	}

	static function createGroup(string $sGroupName, string $sAccess, $db)
	{
		file_put_contents('test5.txt', $sGroupName.$sAccess);
		if(!mysqli_query($db, "INSERT INTO groups (GROUP_NAME,NUM_USER,ACCESS) VALUES (".$sGroupName.",3,".$sAccess.");"))
			echo json_encode(Array('response' => 'fail'));
		else
			echo json_encode(Array('response' => 'success'));

	}

	static function readUser(int $nUserId, $db)
	{
		$query = mysqli_query($db, "SELECT GROUP_NAME FROM user WHERE USER_ID = '".intval($nUserId)."' LIMIT 1");
		$arUserData = mysqli_fetch_assoc($query);
		$arResult = Array();

		if(!empty($arUserData['GROUP_NAME']))
		{
			$query = mysqli_query($db, "SELECT NAME,EMAIL,PHONE,GROUP_NAME FROM user WHERE GROUP_NAME = '".$arUserData['GROUP_NAME']."'");
			while (!empty($arData = mysqli_fetch_assoc($query)))
				$arResult[] = $arData;
		}

		file_put_contents('test6.txt', print_r($arResult,1));
		if(!empty($arResult))
			echo json_encode($arResult);
	}

}



if(!($db = mysqli_connect("localhost", "root", "", "docufy")))
		return false;

file_put_contents('test4.txt', print_r($_GET, 1));

if(!empty($_GET))
{
	switch ($_GET['action'])
	{
		case 'read':
			ApiFunctions::apiReadCars();
			break;
		
		case 'logcheck':
			file_put_contents('test2.txt', print_r($_GET, 1));
			ApiFunctions::LoginCheckUp($_GET['id'], $_GET['hash'], $db);
			break;

		case 'login':
			ApiFunctions::Login($_GET['login'], $_GET['pass'], $db);
			break;

		case 'readGroup':
			ApiFunctions::readGroup($_GET['id'], $db);
			break;

		case 'readUser':
			ApiFunctions::readUser($_GET['id'], $db);
			break;

		case 'createGroup':
			ApiFunctions::createGroup($_GET['group_name'], $_GET['access'], $db);
			break;	

		default:
			# code...
			break;
	}
}



?>