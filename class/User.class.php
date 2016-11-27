<?php class User {
	
	//klassi sees saab kasutada 
	private $connection;
	
	//2 alakriipsu j�rjest __construct
	//$User = new User(see); j�uab siia sulgude vahele
	//$mysqli - v�tan �henduse vastu functions.php failist
	function __construct($mysqli) {
		//klassi sees muutuja kasutamiseks $this-> ...seda private $connectioni, ilma this kasutamata klassi enda uus muutuja $connection
		//$this viitab sellele klassile
		$this->connection = $mysqli;
	}
	
	/*TEISED FUNKTSIOONID*/
	
	function signup ($firstName, $lastName, $email, $password, $gender, $phoneNumber){
		//selle sees muutujad pole v�ljapoole n�htavad
		
		//$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $GLOBALS["database"]);
		
		$stmt = $this->connection->prepare("INSERT INTO user_sample(firstname, lastname, email, password, gender, phonenumber) VALUES(?,?,?,?,?,?)");
		echo $this->connection->error;
		
		$stmt->bind_param("ssssss", $firstName, $lastName, $email, $password, $gender, $phoneNumber); //$signupEmail emailiks lihtsalt
		
		if($stmt->execute()) {
			echo "Salvestamine �nnestus.";
		} else {
			echo "ERROR".$stmt->error;
		}
	}
	
	function login ($email, $password){
		
		$error = "";
		
		$stmt = $this->connection->prepare("
			SELECT id, firstname, email, password, created
			FROM user_sample
			WHERE email = ?
		");
		echo $this->connection->error;
		
		//asendan k�sim�rgi
		$stmt->bind_param("s", $email); //s-string
		
		//m��ran tulpadele muutujad
		$stmt->bind_result($id, $firstNameFromDb, $emailFromDb, $passwordFromDb, $created); //Db-database
		$stmt->execute(); //p�ring l�heb l�bi executiga, isegi kui �htegi vastust ei tule
		
		if($stmt->fetch()) { //fetch k�sin rea andmeid
			//oli rida
			//v�rdlen paroole 
			$hash = hash("sha512", $password);
			if($hash == $passwordFromDb){
				echo "kasutaja ".$id." logis sisse";
				
				$_SESSION["userId"] = $id;
				$_SESSION["email"] = $emailFromDb;
				$_SESSION["firstName"] = $firstNameFromDb;
				
				//suunaks uuele lehele
				header("Location: hw3_data.php");
				exit();
				
			} else {
				$error = "Parool on vale!";
			}
		} else {
			//ei olnud
			$error = "Sellise emailiga ".$email." kasutajat ei ole.";
		}
		
		return $error;
	}
}

?>