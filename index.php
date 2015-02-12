<?php
session_start();
require_once("config.php");
//ini_set("display_errors", 1);
/*************************************** FUNZIONI **************************************/

function show_days (&$mysqli, $days) {
	// Mostra la pagina "Date"
	echo "<table width='100%'><tr ><td><a href='#' class='ui-btn ui-icon-grid ui-btn-icon-left' onClick='window.location=\"/disp?home=1\"'>Elenco</a></td><td><a href=\"#\" class=\"ui-btn ui-icon-search ui-btn-icon-left\" onClick='window.location=\"/disp\"'>Modifica</a></td></tr></table>";
	$gg = array("X", "L", "M", "M", "G", "V", "S", "D");
	$options = best_options($days);
	$posti = get_posti($mysqli);
	if ($result = $mysqli->query("SELECT * FROM Date ORDER BY Data DESC LIMIT 1")) {
		$dates = resultToArray($result);
    	$result->close();
    }
    foreach ($dates as $ds) 
    		$newdates[date("Y-m-d", strtotime($ds['Data']))] = date("H:i", strtotime($ds['Data']));
    echo "<table width='100%' style='text-align: center; border-collapse: collapse;'><tr ><th colspan='4'>Date Suggerite<br>&nbsp;</th></tr><tr><th>Data</th><th>Si+R</th><th>Dove</th><th></th></tr>";
    $c = 0;
     foreach ($options as $da=> $op) {
     	if (isset($posti[$da])) $pos = implode(" ", $posti[$da]['Posti']);
     	else $pos = "";	
     	if (in_array($_SESSION['short'], $posti[$da]['Posti'])) $subm = "Non +";
     	else $subm = "Da me";
     	if (isset($newdates[$da])) $bordtr = " style='border: solid thin; border-color: red;'";
     	else $bordtr = "";
    	$c++;
        echo "<tr".$bordtr."><td style='background-color: ".$op['colour'].";'>".$gg[date("N", strtotime($da))]." " . date("d/m", strtotime($da)). " " . $newdates[$da]."</td><td style='background-color: ".$op['colour'].";'>". $op['y'] . "+". $op['r']."</td><td style='background-color: ".$op['colour'].";'>". $pos . "</td><td><form method='post' action='?list=1' id='".$da."_e'><input type='hidden' name='datacommit' value='$da'>"; if ($_SESSION['player']=="Davide") echo "<input type='text' name='time' value='21:30'><input type='submit' value='OK' >"; echo "</form><form method='post' action='?list=1'><input type='hidden' name='dame' value='".$da."'><input type='submit'value='$subm'> </form></td></tr>";
    }
    echo "</table>";
    log_entry($mysqli, $_SESSION['player'], 3);
}

function get_posti($mysqli) {
	if ($result = $mysqli->query("SELECT * FROM Posti")) {
		$places = resultToArray($result);
    	$result->close();
    }
	foreach ($places as $p) {
		$output[$p['Data']]['Posti'][] = $p['Posto']; 
	}
	if (count($output) > 0 ) return $output;
	else return false;
}


function log_entry(&$mysqli, $name, $action) {
	if ($name != "Davide")
			$mysqli -> query("INSERT INTO Log (Tempo, Nome, Azione) VALUES ('".date("Y-m-d H:i:s", time())."', '".$name."', '".$action."')");
}

function best_options ($days) {
	//Algoritmo di scelta dei giorni migliori
	foreach ($days as $day) {
    	// il master deve essere presente
    	if ($day["Davide"] > 0) $stage1[$day["Data"]] = $day;     
    }
    foreach ($stage1 as $s) {
       foreach ($s as $n => $p) {
       		if ($n != "Data") {
            	if ($p == 1) $y++;
                if ($p == 2) $r++;
            }
       }
       //il numero complessivo di disponibilità (tra effettive e con riserva) deve essere almeno di 6
       if (($y+$r)>= THRESHOLD) {
       		$stage2[$s["Data"]]["y"]=$y;
        	$stage2[$s["Data"]]["r"]=$r;
        	$stage2[$s["Data"]]["t"]=$r+$y;       
       }
       $y = $r = 0;	
    }
// Dare priorità a 1) Numero complessivo 2) A parità di numero complessivo a presenze effettive
$sort = array();
foreach($stage2 as $k=>$v) {
    $sort['y'][$k] = $v['y'];
    $sort['t'][$k] = $v['t'];
}
array_multisort($sort['t'], SORT_DESC, $sort['y'], SORT_DESC,$stage2);

// Assegna gradazione di colore all'output
$n = 0;
$colours = array("#05FF48", "#BCFF05", "#FFFF05", "#FFEA05", "#FFCD05", "#FF8A05", "#FF5D05", "#FF0000");
foreach ($stage2 as $da => $re) {
	$output[$da] = $re;
    $output[$da]['colour'] = $colours[$n];
    $n++;
	}
return $output;
}
 
function resultToArray($result) {
 	//mostra in array il risultato di una query SELECT
    $rows = array();
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
} 
 
function get_stats($logs, $players) {
	foreach ($logs as $l) {
		$output[$l['Nome']] += 1;		
	}
	$total = count($logs);
	foreach ($output as $n => $v) {
		$new_output[$n]['attivita'] = round($v/$total ,2); 
	}
	foreach ($players as $k) {
		if (!isset($new_output[$k['Nome']])) $new_output[$k['Nome']]=0;
	}
	return $new_output;
}
 
function generate_days(&$mysqli, $days, $players) {
	//mostra la pagina "Elenco"
	$output = best_options($days);
	$gg = array("X", "L", "M", "M", "G", "V", "S", "D");
	$im = array(0 => "images/red.png", 1 => "images/green.png", 2 => "images/yellow.png");
	if ($result = $mysqli->query("SELECT * FROM Date ORDER BY Data DESC LIMIT 2")) {
		$dates = resultToArray($result);
    	$result->close();
    }
	echo "<table width='100%'><tr><td><a href=\"/disp/?list=1\" class=\"ui-btn ui-icon-info ui-btn-icon-left\">Date</a></td><td><a href=\"#\" class=\"ui-btn ui-icon-search ui-btn-icon-left\" onClick='window.location=\"/disp\"'>Modifica</a></td></tr></table>";
	echo "<style>td, th {padding: 0px !important; spacing: 0px; text-align: center !important;}</style>";
  	echo "<table  width='100%'><thead><tr><th colspan='10'>Elenco Disponibilita<br>&nbsp;</th></tr><tr>";
	$cl = array ("#FF0000;", "#00FF00;", "#FFFF00;");
    foreach($players as $pl) {
    	$giocatori[$pl['Nome']]= $pl;
    }
	foreach($days[0] as $n => $v) {
    	if ($n == "Data")  
    		echo "<th></th>"; 
    	else 
    		echo "<th style='background-color: ".$cl[$dates[1][$n]]."'>".$giocatori[$n]['Short']."</th>"; 
	}
	echo "</tr></thead><tbody>";
	foreach ($days as $day) {
    if (isset($output[$day["Data"]])) 
    	$bgc = "style='background-color: ".$output[$day["Data"]]["colour"]." !important;'";
    else 
    	$bgc = "";
	echo "<tr $bgc>";
    foreach ($day as $t => $d)
    	if ($t == "Data")
            echo "<td>".$gg[date("N" ,strtotime($d))]." ". date("d" ,strtotime($d))."</td>";
        else {
        	if ($t == $_SESSION['player']) $bg = "style='background-color: lightblue;'";
            else $bg='';
        	echo "<td  $bg><img src='".$im[$d]."' width='12'></td>";
        }  
	    echo "</tr>";
	}
	echo "</tbody></table>";
	log_entry($mysqli, $_SESSION['player'], 4);
}

function generate_form(&$mysqli, $days) {
	// Mostra la pagina "Disponibilita"
	$gg = array("X", "L", "M", "M", "G", "V", "S", "D");
	echo "<table width='100%'><tr><td> <a href=\"/disp/?list=1\" class=\"ui-btn ui-icon-info ui-btn-icon-left\">Date</a></td><td> <a href='#' class='ui-btn ui-icon-grid ui-btn-icon-left' onClick='window.location=\"/disp?home=1\"'>Elenco</a></td></tr></table>";
	echo "<style>td, th {height: 20px; padding-top: 0px !important; padding-bottom: 0px !important; spacing: 0px !important; text-align: center;} table {border-collapse: collapse;}</style>";
	echo "<form method='post' action ='?my=1' id='disponibilita'><table width='100%' ><tr><th colspan='2'>Modifica Disponibilita<br>&nbsp;</th></tr><tr>";
	foreach($days[0] as $n => $v) {
		if ($n == $_SESSION['player'] || $n == "Data")
		echo "<th>$n</th>\n";    
	}
	echo "</tr>\n";
	$x = 0;
	foreach ($days as $day) {
		echo "<tr>";
    	foreach ($day as $n => $d) {
    		 if ($n == $_SESSION['player'] || $n == "Data") {
             	if ($n == "Data")
                	echo "<td>".$gg[date("N" ,strtotime($d))]." ". date("d" ,strtotime($d))."</td>";
        		if ($n != "Data")   {
            		echo "<td>";
                    switch($d) {
                    	case 0: $sel0 = "SELECTED"; $sel1 = $sel2 = ""; break;
                    	case 1: $sel1 = "SELECTED"; $sel0 = $sel2 = ""; break;
                    	case 2: $sel2 = "SELECTED"; $sel1 = $sel0 = ""; break;
                    }
                	echo "<fieldset class=\"ui-field-contain\"><select name=\"date[".$day['Data']."]\" id=\"day\">
      					  <option value=\"0\" $sel0>No</option>
      					  <option value=\"1\" $sel1>Si</option>
      					  <option value=\"2\" $sel2>Si (R)</option>
    					  </select>
  						  </fieldset>";
                }  
                echo "</td>";    
            }
        }
    	echo "</tr>\n";
	}
	echo "<input type='hidden' name='name' value='".$_SESSION['player']."'>";
	echo "<input type='hidden' name='form_type' value='update'>";
	echo "<tr><td colspan='2' align='center'><input type='submit' value='Aggiorna'></td></tr>";
	echo "</form>";
	log_entry($mysqli, $_SESSION['player'], 5);
}

function login() {
	echo "<form method=\"post\" action=\"\" id='login'>
      	  <div class=\"ui-field-contain\"><input type=\"text\" name=\"name\" id=\"name\"></div>
    	  <input type='hidden' name='form_type' value='login'>
          <a href='#' class='ui-btn ui-icon-lock ui-btn-icon-left' onClick='$(\"#login\").submit();'>Dite Amici ed Entrate</a>
          </form><br><div align='center'><img src='images/earlogo.png'></div>";
}

function logout(&$mysqli) {
	log_entry($mysqli, $_SESSION['player'], 0);
	session_destroy();
    unset($_SESSION);
    setcookie("player", null);
    setcookie("short", null);
    header("location: /disp");
    exit();
}

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

