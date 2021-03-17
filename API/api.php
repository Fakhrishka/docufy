<?php
class ApiFunctions
{
	// static function apiAddCars()

	static function getGroups($db)
	{
		$query = mysqli_query($db, "SELECT GROUP_NAME FROM groups");
		$arResult = Array();

		while (!empty($arData = mysqli_fetch_assoc($query)))
			$arResult[] = $arData;

		file_put_contents('test12.txt', print_r($arResult, 1));
		echo json_encode($arResult);
	}

	static function deleteGroup($sGroupName, $db)
	{

		$query = mysqli_query($db, "SET FOREIGN_KEY_CHECKS=0");
		$queryGroup = mysqli_query($db, "DELETE FROM groups WHERE GROUP_NAME = '".$sGroupName."'");


		file_put_contents('test543s5.txt', print_r($db->error, 1));
		$queryUserUpdate = mysqli_query($db, "UPDATE user SET GROUP_NAME='' WHERE GROUP_NAME='".$sGroupName."'");
		echo json_encode(Array('status' => '200'));


	}

	static function readGroup(int $nUserId, $db)
	{
		$query = mysqli_query($db, "SELECT GROUP_NAME FROM user WHERE USER_ID = '".intval($nUserId)."' LIMIT 1");
		$arUserData = mysqli_fetch_assoc($query);
		$arResult = Array();

		if($arUserData['GROUP_NAME'] === 'ADMIN' || $arUserData['GROUP_NAME'] === 'PROJECT_MANAGER')
		{
			$groupQuery = mysqli_query($db, "SELECT * FROM groups");
			while(!empty($arGroupDate = mysqli_fetch_assoc($groupQuery)))
				$arResult[] = $arGroupDate;

			$arResult['newgroup'] = true;
			file_put_contents('test3.txt', print_r($arResult,1));
		}
		else
		{
			$groupQuery = mysqli_query($db, "SELECT * FROM groups WHERE GROUP_NAME='".$arUserData['GROUP_NAME']."'");
			while(!empty($arGroupDate = mysqli_fetch_assoc($groupQuery)))
				$arResult[] = $arGroupDate;
		}

		
		if(!empty($arResult))
			echo json_encode($arResult);

	}

	static function readPayment(int $nUserId, $db)
	{
		$query = mysqli_query($db, "SELECT GROUP_NAME FROM user WHERE USER_ID = '".intval($nUserId)."' LIMIT 1");
		$arUserData = mysqli_fetch_assoc($query);

		$groupQuery = mysqli_query($db, "SELECT ACCESS FROM groups WHERE GROUP_NAME = '".$arUserData['GROUP_NAME']."' LIMIT 1");
		$arGroupDate = mysqli_fetch_assoc($groupQuery);
		$arResult = Array();

		if($arGroupDate['ACCESS'] === 'ALL')
		{
			$query = mysqli_query($db, "SELECT PROJECT_NAME, PROJECT_CREATER, CREATE_DATE, EXPECTED_FINDATE, PRICE, LATE_DAY_COUNT FROM project_payment");

			while(!empty($arProject = mysqli_fetch_assoc($query)))
			{
				// if now > expected -> change late_day_count // price 
				$queryWorkers = mysqli_query($db, "SELECT PROJECT_WORKERS FROM project WHERE NAME='".$arProject['PROJECT_NAME']."' LIMIT 1");
				$arWorkers = mysqli_fetch_assoc($queryWorkers);


				$sFinishDate = new DateTime();
				$sFinishDate->setTimestamp($arProject['EXPECTED_FINDATE']);

				$sNowDate = new DateTime('NOW');
				$arDiffDays = $sNowDate->diff($sFinishDate);

				$sCreateDate = new DateTime();
				$sCreateDate->setTimestamp($arProject['CREATE_DATE']);

				if($arDiffDays->invert == 1)
				{
					// change late_day_count // price 
					$nPrice = $arProject['PRICE'];
					for($i=0; $i < $arDiffDays->days; $i++) { 
						$nPrice -= $nPrice*0.1;
					}
					mysqli_query($db, "UPDATE project_payment SET LATE_DAY_COUNT = '".$arDiffDays->days."' WHERE PROJECT_NAME='".$arProject['PROJECT_NAME']."'");

					$arProject['PRICE'] = $nPrice;
					$arProject['LATE_DAY_COUNT'] = $arDiffDays->days;

					file_put_contents('TESTPROJ.txt', print_r($arProject, 1), FILE_APPEND);
				}

				$arProject['EXPECTED_FINDATE'] = $sFinishDate->format('d-m-Y');
				$arProject['CREATE_DATE'] = $sCreateDate->format('d-m-Y');
				if(isset($arWorkers['PROJECT_WORKERS']) && !empty($arWorkers['PROJECT_WORKERS']))
					$arProject['WORKERS'] = $arWorkers['PROJECT_WORKERS'];

				$arResult[] = $arProject;
			}
		}
		else
		{
			$query = mysqli_query($db, "SELECT PROJECTS FROM user WHERE USER_ID='".$nUserId."' LIMIT 1");
			$arUserData = mysqli_fetch_assoc($query);
			$arProjects = explode(',', $arUserData['PROJECTS']);

			foreach ($arProjects as $key => $sProjName)
			{
				$projQuery = mysqli_query($db, "SELECT PROJECT_NAME, PROJECT_CREATER, CREATE_DATE, EXPECTED_FINDATE, PRICE, LATE_DAY_COUNT FROM project_payment WHERE PROJECT_NAME='".$sProjName."'");
				
				while(!empty($arProject = mysqli_fetch_assoc($projQuery)))
				{
					$sFinishDate = new DateTime();
					$sFinishDate->setTimestamp($arProject['EXPECTED_FINDATE']);

					$sNowDate = new DateTime('NOW');
					$arDiffDays = $sNowDate->diff($sFinishDate);

					$sCreateDate = new DateTime();
					$sCreateDate->setTimestamp($arProject['CREATE_DATE']);
					
					if($arDiffDays->invert == 1)
					{
						// change late_day_count // price 
						$nPrice = $arProject['PRICE'];
						for($i=0; $i < $arDiffDays->days; $i++) { 
							$nPrice -= $nPrice*0.1;
						}
						mysqli_query($db, "UPDATE project_payment SET LATE_DAY_COUNT = '".$arDiffDays->days."' WHERE PROJECT_NAME='".$arProject['PROJECT_NAME']."'");
						$arProject['PRICE'] = $nPrice;
						$arProject['LATE_DAY_COUNT'] = $arDiffDays->days;
						$arProject['EXPECTED_FINDATE'] = $sFinishDate->format('d-m-Y');
						$arProject['CREATE_DATE'] = $sCreateDate->format('d-m-Y');

						file_put_contents('TESTPROJ.txt', print_r($arProject, 1), FILE_APPEND);
						$arResult[] = $arProject;
					}

					$arProject['EXPECTED_FINDATE'] = $sFinishDate->format('d-m-Y');
					$arProject['CREATE_DATE'] = $sCreateDate->format('d-m-Y');
					
					$arResult[] = $arProject;
				}
			}
		}
			echo json_encode($arResult);
	}

	static function updateProject(string $sOldName, string $sNewname, $db)
	{

		mysqli_query($db, "SET FOREIGN_KEY_CHECKS=0");

		mysqli_query($db, "UPDATE project SET NAME='".$sNewname."' WHERE NAME='".$sOldName."'");

		echo json_encode(Array("status" => "200"));

	}

	static function readProject(int $nUserId, $db)
	{
		$query = mysqli_query($db, "SELECT GROUP_NAME, LOGIN FROM user WHERE USER_ID = '".intval($nUserId)."' LIMIT 1");
		$arUserData = mysqli_fetch_assoc($query);	
		$arResult = Array();

		$groupQuery = mysqli_query($db, "SELECT ACCESS FROM groups WHERE GROUP_NAME = '".$arUserData['GROUP_NAME']."' LIMIT 1");
		$arGroupDate = mysqli_fetch_assoc($groupQuery);
		if($arGroupDate['ACCESS'] === 'ALL')
		{
			$query = mysqli_query($db, "SELECT * FROM project");

			while(!empty($arProject = mysqli_fetch_assoc($query)))
			{
				if($arProject['CREATER'] === $arUserData['LOGIN'])
					$arProject['projectedit'] = true;
				$arResult[] = $arProject;
			}
		}
		else
		{
			$query = mysqli_query($db, "SELECT PROJECTS FROM user WHERE USER_ID = '".intval($nUserId)."' LIMIT 1");
			$arProjectData = mysqli_fetch_assoc($query);
			
			if($arProjectData['PROJECTS'] === "" || empty($arProjectData['PROJECTS']))
			{
				echo json_encode(Array('status' => 'no_project'));
				return false;
			}

			$arProjects = explode(',', $arProjectData['PROJECTS']);
			foreach ($arProjects as $key => $sProjName)
			{
				$groupQuery = mysqli_query($db, "SELECT * FROM project WHERE NAME= '".$sProjName."'");
				while(!empty($arProject = mysqli_fetch_assoc($groupQuery)))
				{
					file_put_contents('checkup.txt', $arProject['CREATER'] . "  :  " . $arUserData['LOGIN'], FILE_APPEND);
					if($arProject['CREATER'] === $arUserData['LOGIN'])
						$arProject['projectedit'] = true;
					$arResult[] = $arProject;
				}
			}
		}

		// Response
		if(!empty($arResult))
		{
			if($arGroupDate['ACCESS'] === 'ALL' || $arGroupDate['ACCESS'] === 'Write')
				$arResult['newproject'] = true;
			echo json_encode($arResult);
		}
	}

	static function deleteProject($sProjName, $db)
	{
		$userQuery = mysqli_query($db, "SELECT PROJECT_WORKERS FROM project WHERE NAME = '".$sProjName."'");
		$userData = mysqli_fetch_assoc($userQuery);

		if(isset($userData['PROJECT_WORKERS']) && !empty($userData['PROJECT_WORKERS']) && $userData['PROJECT_WORKERS'] != '')
		{
			$arUserData = explode(',', $userData['PROJECT_WORKERS']);

			foreach ($arUserData as $key => $sUser) // Updating user's projects as we delete one
			{
				$userQuery = mysqli_query($db, "SELECT PROJECTS from user WHERE LOGIN='".$sUser."'");
				$userData = mysqli_fetch_assoc($userQuery);

				if(!isset($userData['PROJECTS']) && empty($userData['PROJECTS']))
					continue;
				$arUserProjects = explode(',', $userData['PROJECTS']);
				foreach ($arUserProjects as $key => $sProj)
				{
					if($sProj === $sProjName)
						unset($arUserProjects[$key]);
				}

				mysqli_query($db, "UPDATE user SET PROJECTS='".implode(',', $arUserProjects)."' WHERE NAME='".$sUser."'");
			}			
		}


		$query = mysqli_query($db, "SET FOREIGN_KEY_CHECKS=0");
		mysqli_query($db, "DELETE FROM project WHERE NAME = '".$sProjName."'");
		mysqli_query($db, "DELETE FROM project_payment WHERE PROJECT_NAME = '".$sProjName."'");
		echo json_encode(Array('status' => '200'));
	}


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
		{
			$query = mysqli_query($db, "UPDATE user SET LAST_VISIT='".time()."' WHERE USER_ID = '".intval($nUserId)."'");
			echo json_encode(
				Array(
					'logging_status' => 'true',
					'user_id' => $nUserId,
					'hash' => $sCookieHash,
				)
			);

		}
	}

	static function Login(string $sLogin, string $sPass, $db)
	{
		$query = mysqli_query($db, "SELECT USER_ID,PASSWORD FROM user WHERE LOGIN='".mysqli_real_escape_string($db, $sLogin)."'");
		$arData = mysqli_fetch_assoc($query);
		file_put_contents('test9.txt', md5(md5($sPass)));

		if($arData['PASSWORD'] == md5(md5($sPass)))
		{
			$sNewHash = md5(md5(self::generateCode(10)));
			if(!mysqli_query($db,"UPDATE user SET HASH='".$sNewHash."' WHERE USER_ID='".$arData['USER_ID']."'"))
				return false;

			self::LoginCheckUp($arData['USER_ID'], $sNewHash, $db);
		}
		else
		{
			// echo 'Password is wrong...';
			return false;
		}

	}
	
	static function Register(string $sLogin, string $sPass, string $sEmail,string $sName, 
							string $sPhone, string $sGroupName, string $sAccess, $db)
	{
		$query = mysqli_query($db, "SELECT USER_ID FROM user WHERE LOGIN='".mysqli_real_escape_string($db, $sLogin)."'");
		file_put_contents('test20.txt', print_r($query, 1));
		if(mysqli_num_rows($query) > 0)
		{
			// echo 'user exists...';
			return false;
		}

		if(!mysqli_query($db,"INSERT INTO user SET LOGIN='".$sLogin."', PASSWORD='".md5(md5(trim($sPass)))."', EMAIL='".$sEmail."', NAME='".$sName."', PHONE='".$sPhone."', GROUP_NAME='".$sGroupName."', DATE_REGISTER='".time()."', ACCESS='".$sAccess."', LAST_VISIT='".time()."'"))
			echo 'user not added';
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
		if(!mysqli_query($db, "INSERT INTO groups (`GROUP_NAME`,`NUM_USERS`,`ACCESS`) VALUES ('".$sGroupName."',3,'".$sAccess."')"))
		{

			file_put_contents('test5.txt', "Error description: " . $db->error);
			echo json_encode(Array('response' => 'fail'));
		}
		else
			echo json_encode(Array('response' => 'success'));

	}

	static function getUsers($db)
	{
		$query = mysqli_query($db, "SELECT NAME,LOGIN FROM user");
		$arResult = Array();

		while (!empty($arData = mysqli_fetch_assoc($query)))
			$arResult[] = $arData;

		file_put_contents('test11.txt', print_r($arResult, 1));
		echo json_encode($arResult);
	}

	static function createProject($sProjName, $nUserId, $sDate, $sWorker, $sDesc, $sStatus, $db)
	{
		$query = mysqli_query($db, "SELECT LOGIN FROM user WHERE USER_ID = '".intval($nUserId)."' LIMIT 1");
		$arUser = mysqli_fetch_assoc($query);


		$sFinishDate = new DateTime($sDate);
		$sNowDate = new DateTime('NOW');
		$nDiffDays = $sNowDate->diff($sFinishDate)->days;

		if(!mysqli_query($db, "INSERT INTO `project` (`NAME`, `CREATER`, `PROJECT_WORKERS`, `DATE_CREATE`, `DATE_FINISH`, `LATE_DAY_COUNT`, `STATUS`, `ACCESS`) VALUES ('".$sProjName."', '".$arUser['LOGIN']."', '".$sWorker."', '".$sNowDate->getTimestamp()."', '".$sFinishDate->getTimestamp()."', '0', '".$sStatus."', 'vremenno tak...')"))
			echo json_encode(Array('response' => 'fail'));
		else
		{
			$arWorkers = explode(',', $sWorker);
			$nPrice = $nDiffDays*count($arWorkers)*10; // NOT REAL PRICE, MAY CHANGE

			if(!mysqli_query($db, "INSERT INTO `project_payment` 
					(`PROJECT_NAME`, 
					`PROJECT_CREATER`, 
					`CREATE_DATE`, 
					`EXPECTED_FINDATE`, 
					`LATE_DAY_COUNT`, 
					`PRICE`, 	
					`ACCESS`) 
				VALUES (
					'".$sProjName."', 
					'".$arUser['LOGIN']."', 
					'".$sNowDate->getTimestamp()."', 
					'".$sFinishDate->getTimestamp()."', 
					'0', 
					'".$nPrice."', 
					'vremenno tak...'
					)")
			)
				echo json_encode(Array('status' => '404'));	
			else
				echo json_encode(Array('status' => '200'));
		}


	}

	static function updateGroup(string $sOldName, string $sNewName, $db)
	{
		mysqli_query($db, "SET FOREIGN_KEY_CHECKS=0");

		file_put_contents('testgroup.txt', $sOldName . $sNewName);
		mysqli_query($db, "UPDATE groups SET GROUP_NAME='".$sNewName."' WHERE GROUP_NAME='".$sOldName."'");

		file_put_contents('testgrou2p.txt', $db->error);
		echo json_encode(Array("status" => "200"));
	}

	static function readUser(int $nUserId, $db)
	{
		$query = mysqli_query($db, "SELECT GROUP_NAME FROM user WHERE USER_ID = '".intval($nUserId)."' LIMIT 1");
		$arUserData = mysqli_fetch_assoc($query);
		$arResult = Array();

		if(!empty($arUserData['GROUP_NAME']))
		{
			$groupQuery = mysqli_query($db, "SELECT ACCESS FROM groups WHERE GROUP_NAME='".$arUserData['GROUP_NAME']."'");
			$arGroupData = mysqli_fetch_assoc($groupQuery);
			
			if($arGroupData['ACCESS'] === 'ALL')
			{
				$query = mysqli_query($db, "SELECT NAME,EMAIL,PHONE,GROUP_NAME FROM user");
				while (!empty($arData = mysqli_fetch_assoc($query)))
					$arResult[] = $arData;
			}
			else
			{
				$query = mysqli_query($db, "SELECT NAME,EMAIL,PHONE,GROUP_NAME,PROJECTS FROM user WHERE GROUP_NAME = '".$arUserData['GROUP_NAME']."'");

				while (!empty($arData = mysqli_fetch_assoc($query)))
					$arResult[] = $arData;
			}

			file_put_contents('test6.txt', print_r($arResult,1));
			if(!empty($arResult))
				echo json_encode($arResult);

		}
		else
			echo json_encode(Array('status' => 'no_group'));
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

		case 'register':
			ApiFunctions::Register(
				$_GET['login'],
				$_GET['pass'],
				$_GET['email'],
				$_GET['name'],
				$_GET['phone'],
				$_GET['group'],
				$_GET['access'],
				$db
			);
			break;
		case 'login':
			ApiFunctions::Login($_GET['login'], $_GET['pass'], $db);
			break;

		case 'readGroup':
			ApiFunctions::readGroup($_GET['id'], $db);
			break;

		case 'getGroups':
			ApiFunctions::getGroups($db);
			break;
			
		case 'readUser':
			ApiFunctions::readUser($_GET['id'], $db);
			break;

		case 'createGroup':
			ApiFunctions::createGroup($_GET['group_name'], $_GET['access'], $db);
			break;	
		case 'deleteGroup':
			ApiFunctions::deleteGroup($_GET['group_name'], $db);
			break;
		case 'updateGroup':
			ApiFunctions::updateGroup($_GET['oldname'], $_GET['newname'], $db);
			break;

		case 'updateProject':
			ApiFunctions::updateProject($_GET['oldname'], $_GET['newname'], $db);
			break;

		case 'readProjects':
			ApiFunctions::readProject($_GET['id'], $db);
			break;

		case 'createProject':
			ApiFunctions::createProject(
				$_GET['name'],
				$_GET['creater'],
				$_GET['date'],
				$_GET['workers'],
				$_GET['worktodo'],
				$_GET['status'],
				$db
			);
			break;

		case 'deleteProject':
			ApiFunctions::deleteProject($_GET['name'], $db);
			break;	

		case 'getUsers':
			ApiFunctions::getUsers($db);
			break;
		case 'readPayment':
			ApiFunctions::readPayment($_GET['id'], $db);
			break;
		default:
			# code...
			break;
	}
}



?>