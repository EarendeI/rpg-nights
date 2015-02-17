<?php
session_start();

//ini_set("display_errors", 1);

require_once("config.php");
require_once("functions.php");

/******************* CONTROLLI, INIZIALIZZAZIONE DB E PARAMETRI  ************************/ 

//connessione a MySQL con l'estensione MySQLi
$mysqli = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE);

// verifica dell'avvenuta connessione
if (mysqli_connect_errno()) {
           // notifica in caso di errore
        echo "Errore in connessione al DBMS: ".mysqli_connect_error();
           // interruzione delle esecuzioni i caso di errore
        exit(); 
}

// carica la sessione dai cookie
if (isset($_COOKIE['player'])) $_SESSION['player'] = $_COOKIE['player'];
if (isset($_COOKIE['short'])) $_SESSION['short'] = $_COOKIE['short'];

// in caso di richiesta di logout
if ($_POST['form_type'] == 'logout') logout($mysqli);

// in caso di richiesta di aggiornamento Disponibilita 
if ($_POST['form_type'] == "update") {
 // AGGIORNA DISPONIBILITA
 	foreach($_POST['date'] as $data => $value) {
        $mysqli -> query("UPDATE Giorni SET ".$_POST['name']." = '$value' WHERE Data = '$data'");
 	}
 	log_entry($mysqli, $_SESSION['player'], 2);
	mail (DMEMAIL, "Aggiornamento Disponibilita", $_POST['name'] . " ha aggiornato la sua disponibilità.\nhttp://dungeon.altervista.org/disp");
	
}
 
// legge la data dell'ultimo aggionrmaneto calendario
if ($result = $mysqli->query("SELECT * FROM Aggiornamento")) {
	$update = resultToArray($result);
    $result->close();
}

//Se il calendario non è stato già aggiornato
if (isset($update[0]["Oggi"]))
    if ($update[0]["Oggi"]!= date("Y-m-d", time())) {
 	
	    // elimina automaticamente tutti i giorni precedenti a quello corrente
	    $mysqli -> query("DELETE FROM Giorni WHERE Data < '".date("Y-m-d", time())."'");
	    // genera automaticamente i nuovi giorni (da t a t+15)
	    for ($i ==0 ; $i< 15; $i++) {
 		    $mysqli -> query("INSERT INTO Giorni (Data) VALUES ('".date("Y-m-d", time()+3600*24*$i)."')");
 		    //se il giorno è già esistente, poichè la data è chiave primaria, l'insert fallisce
 		    //non c'è quindi problema di sovrascrittura
	    }
	$mysqli -> query("UPDATE Aggiornamento SET Oggi = '".date("Y-m-d", time())."' WHERE IDOggi = '0'");
    }


//uso variabile di sessione per ridurre query ad ogni apertura pagina
if (count($_SESSION['players'])==0) {
	// genera array $players con tutti i giocatori
	if ($result = $mysqli->query("SELECT * FROM Giocatori ORDER BY Nome ASC")) {
		$players = resultToArray($result);
    	$result->close();
		$_SESSION['players'] = $players;
	}
}
else 
	$players = $_SESSION['players'];

// in caso di login
if ($_POST['form_type'] == 'login') {
	foreach ($players as $player) 
    	if (strtoupper($player['Nome'])==strtoupper($_POST['name'])) {
    		setcookie("player", $player['Nome'], time()+3600*24*15);
    		setcookie("short", $player['Short'], time()+3600*24*15);
        	$_SESSION['player'] = $player['Nome'];
        	$_SESSION['short'] = $player['Short'];
        	log_entry($mysqli, $_SESSION['player'], 1);
			}
}

// genero array $logs contenente tutti i log
if ($result = $mysqli->query("SELECT * FROM Log")) {
	$logs = resultToArray($result);	
    $result->close();
}

// genero array $days contenente tutte le disponibilita
if ($result = $mysqli->query("SELECT * FROM Giorni WHERE 1 ORDER BY Data ASC")) {
	$days = resultToArray($result);	
    $result->close();
}

// in caso di accettazione data
if ($_POST['datacommit'] != "") {
	foreach ($days as $d_e)
		if ($d_e['Data'] == $_POST['datacommit']) 
			foreach ($d_e as $field_e => $value_e) {		
				$fields_e[]= $field_e;
				if ($field_e == "Data") $values_e[]= $value_e . " ". $_POST['time'];
				else 
					$values_e[]= $value_e;
			}
			$val_e = implode("', '", $values_e);
			$fie_e = implode(", ", $fields_e);
	$mysqli -> query("INSERT INTO Date (".$fie_e.") VALUES ('".$val_e."')");
}

// in caso di offerta location
if ($_POST['dame'] != "") {
    $posti = get_posti($mysqli);
    if (in_array($_SESSION['short'], $posti[$_POST['dame']]['Posti'])) 
    	$mysqli -> query("DELETE FROM Posti WHERE Data = '".$_POST['dame']."' AND Posto = '".$_SESSION['short']."'");
    else 
    	$mysqli -> query("INSERT INTO Posti (Data, Posto) VALUES ('".$_POST['dame']."', '".$_SESSION['short']."')");

}

/************************************ HTML *********************************************/

echo "<!DOCTYPE html>\n<html>\n<head>\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
<link rel=\"stylesheet\" href=\"js/jquery.mobile-1.4.5.min.css\">
<script src=\"js/jquery-1.11.2.min.js\"></script>
<script src=\"js/jquery.mobile-1.4.5.min.js\"></script>
<style>.ele {position: relative; float: left;}</style>\n</head>\n<body>\n<div data-role=\"page\">
<div data-role=\"main\" class=\"ui-content\">";

if (isset($_SESSION['player'])) 
	echo "<form method=\"post\" action=\"\" id='logout'> <input type='hidden' name='form_type' value='logout'>\n";

echo "<input type='image' src='images/intesta-terzaera.jpg' width='100%' title='Logout' alt='Logout'>\n";
if (isset($_SESSION['player'])) 
	echo "</form>\n <!-- dynamic code -->\n";   

if (isset($_SESSION['player']) && $_GET['home']!=1 && $_GET['list']!=1 ) 
	generate_form($mysqli, $days);
if (isset($_SESSION['player']) && $_GET['home']==1 ) 
	generate_days($mysqli, $days, $players);
if (isset($_SESSION['player']) && $_GET['list']==1) 
	show_days($mysqli, $days);
 if (!isset($_SESSION['player']) && !isset($_COOKIE['player'])) 
 	login();

echo "<!-- end of dynamic code -->\n</div>\n</div>\n</body>\n</html>\n"; 

// chiusura della connessione
$mysqli->close();
?>

