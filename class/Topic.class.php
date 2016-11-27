<?php class Topic {
	
	private $connection;
	
	function __construct($mysqli) {
		$this->connection = $mysqli;
	}
	
	function createNew($subject, $content, $user, $email, $user_id){
		
		$stmt = $this->connection->prepare("INSERT INTO topics(subject, content, user, email, user_id) VALUES(?,?,?,?,?)");
		echo $this->connection->error;
		
		$stmt->bind_param("ssssi", $subject, $content, $user, $email, $user_id); 
		
		if($stmt->execute()) {
			echo "Salvestamine �nnestus.<br>";
		} else {
			echo "ERROR".$stmt->error;
		}
	}
	
	function addToArray (){
		
		$stmt = $this->connection->prepare("
			SELECT id, subject, created, user, email
			FROM topics
			WHERE deleted IS NULL 
		");
		echo $this->connection->error;
		
		$stmt->bind_result ($id, $subject, $date, $user, $email);
		$stmt-> execute();
		
		$result = array();

		while ($stmt->fetch()){	
			$topic = new StdClass();
			$topic->id = $id;
			$topic->subject = $subject;
			$topic->created = $date;
			$topic->user = $user;
			$topic->email = $email;
			
			array_push ($result, $topic);
			$_SESSION["subject"] = $subject;
		}
		$stmt->close();
		//$mysqli->close();
		
		return $result;
	}
	
	function get($topic_id){
		
		$stmt = $this->connection-> prepare("SELECT subject, content, created, user, email FROM topics WHERE id=? AND deleted IS NULL");
		
		echo $this->connection->error;

		$stmt->bind_param("i", $topic_id);
		$stmt->bind_result($subject, $content, $created, $user, $email);
		$stmt->execute();
		
		//tekitan objekti
		$topic = new Stdclass();
		
		//saime �he rea andmeid
		if($stmt->fetch()){
			// saan siin alles kasutada bind_result muutujaid
			$topic->subject = $subject;
			$topic->content = $content;
			$topic->created = $created;
			$topic->user = $user;
			$topic->email = $email;
			
		}else{
			// ei saanud rida andmeid k�tte
			// sellist id'd ei ole olemas
			// see rida v�ib olla kustutatud
			header("Location: hw3_data.php");
			exit();
		}
		
		$stmt->close();
		//$mysqli->close();
		
		return $topic;
	}

	function checkUser($topic_id, $user_id){
		$stmt = $this->connection-> prepare("SELECT subject, content FROM topics WHERE id=? and user_id=?");
		
		echo $this->connection->error;
		
		$stmt->bind_param("ii", $topic_id, $user_id);
		$stmt->bind_result($subject, $content);
		$stmt->execute();
		
		$del_topic = "";
		
		if($stmt->fetch()){
		
			$del_topic = "<a href='hw3_topics.php?id=$topic_id&delete=true' style='text-decoration:none'>Kustuta oma teema</a>";
			//echo $del_topic;
		
		}
		
		$stmt->close();
		return $del_topic;
	}
	
	function del($topic_id){
		$stmt = $this->connection->prepare("UPDATE topics SET deleted=NOW() WHERE id=? AND deleted IS NULL");
 		$stmt->bind_param("i",$topic_id);
		
		// kas �nnestus salvestada
 		if($stmt->execute()){
 			// �nnestus
 			echo "Kustutamine �nnestus!";
 		}
 		
 		$stmt->close();
	}
}
?>