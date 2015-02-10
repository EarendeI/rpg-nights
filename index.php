<?php
session_start();

if ($_POST['form_type'] == 'logout') logout();


// connessione a MySQL con l'estensione MySQLi
$mysqli = new mysqli("localhost", "dungeon", "", "my_dungeon");

function show_days ($days) {

	echo "<table width='100%'><tr ><td><a href='#' class='ui-btn ui-icon-grid ui-btn-icon-left' onClick='window.location=\"/disp?home=1\"'>Elenco</a></td><td><a href=\"#\" class=\"ui-btn ui-icon-search ui-btn-icon-left\" onClick='window.location=\"/disp\"'>Modifica</a></td></tr></table>";

	$gg = array("X", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab", "Dom");
	$options = best_options($days);
    echo "<table width='100%' style='text-align: center;'><tr ><th colspan='4'>Date Suggerite<br>&nbsp;</th></tr><tr><th></th><th>Data</th><th>Si</th><th>(R)</th></tr>";
    
    $c = 0;
    
    foreach ($options as $da=> $op) {
    		
    	$c++;
        
        echo "<tr><td style='background-color: ".$op['colour'].";'>$c.</td><td style='background-color: ".$op['colour'].";'>".$gg[date("N", strtotime($da))]." " . date("d/m", strtotime($da)). "</td><td style='background-color: ".$op['colour'].";'>". $op['y'] . "</td><td style='background-color: ".$op['colour'].";'>".  $op['r']. "</td></tr>";
    
    }
	
    echo "</table>";

}


function best_options ($days) {

	foreach ($days as $day) {
    	//il master è presente
    	if ($day["Davide"] > 0) $stage1[$day["Data"]] = $day;     
    }
    
    foreach ($stage1 as $s) {
    
       foreach ($s as $n => $p) {
       
       		if ($n != "Data") {
            
            	if ($p == 1) $y++;
                if ($p == 2) $r++;
            }
                
       }
       //tra riserva e ok siamo almeno 6
       if (($y+$r)>=6) {
       
        $stage2[$s["Data"]]["y"]=$y;
        $stage2[$s["Data"]]["r"]=$r;
        $stage2[$s["Data"]]["t"]=$r+$y;
       
       }
       $y = $r = 0;	
    }



# get a list of sort columns and their data to pass to array_multisort
$sort = array();
foreach($stage2 as $k=>$v) {
    $sort['y'][$k] = $v['y'];
    $sort['t'][$k] = $v['t'];
}
# sort by event_type desc and then title asc
array_multisort($sort['t'], SORT_DESC, $sort['y'], SORT_DESC,$stage2);



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
    $rows = array();
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    return $rows;
} 
 
 
 
// verifica dell'avvenuta connessione
if (mysqli_connect_errno()) {
           // notifica in caso di errore
        echo "Errore in connessione al DBMS: ".mysqli_connect_error();
           // interruzione delle esecuzioni i caso di errore
        exit();
 
}
else {
           // notifica in caso di connessione attiva
       	// echo "Connessione avvenuta con successo";
}
 
 
 if ($_POST['form_type'] == "update") {
 // AGGIORNA DISPONIBILITA
 	foreach($_POST['date'] as $data => $value) {
    	
       print_r($_POST);
       echo "UPDATE Giorni SET ".$_POST['name']." = '$value' WHERE Data = '$data'";
       
        
    	$mysqli -> query("UPDATE Giorni SET ".$_POST['name']." = '$value' WHERE Data = '$data'");
    	
 	}
 
    
 }
 
 
 // ELIMINA GIORNI VECCHI
 
 $mysqli -> query("DELETE FROM Giorni WHERE Data < '".date("Y-m-d", time())."'");
 
 
 // GENERA PROSSIMI 15 GIORNI
 
 for ($i ==0 ; $i< 15; $i++) {
 
 	$mysqli -> query("INSERT INTO Giorni (Data) VALUES ('".date("Y-m-d", time()+3600*24*$i)."')");
 	
 }
 
 
 
 /* Select queries return a resultset */
if ($result = $mysqli->query("SELECT * FROM Giocatori ORDER BY Nome ASC")) {
    //printf("Select returned %d rows.\n", $result->num_rows);

	$players = resultToArray($result);
	
    /* free result set */
    $result->close();
}



 if ($_POST['form_type'] == 'login') {
 
 
 	foreach ($players as $player) 
    	if (strtoupper($player['Nome'])==strtoupper($_POST['name']))
        	$_SESSION['player'] = $player['Nome'];
    	
    
 
 
 }

 /* Select queries return a resultset */
if ($result = $mysqli->query("SELECT * FROM Giorni WHERE 1 ORDER BY Data ASC")) {
    //printf("Select returned %d rows.\n", $result->num_rows);

	$days = resultToArray($result);
	
    /* free result set */
    $result->close();
}


 
// chiusura della connessione
$mysqli->close();

function generate_days($days) {

$output = best_options($days);

$gg = array("X", "L", "M", "M", "G", "V", "S", "D");

$im = array(0 => "images/red.png", 1 => "images/green.png", 2 => "images/yellow.png");

echo "<table width='100%'><tr><td><a href=\"/disp/?list=1\" class=\"ui-btn ui-icon-info ui-btn-icon-left\">Date</a></td><td><a href=\"#\" class=\"ui-btn ui-icon-search ui-btn-icon-left\" onClick='window.location=\"/disp\"'>Modifica</a></td></tr></table>";
//echo "<input type=\"button\" value=\"Mia Disponibilita\" id = 'my' onClick='window.location=\"/disp\"'>";

echo "<style>td, th {padding: 0px !important; spacing: 0px; text-align: center !important;}</style>";
  
echo "<table  width='100%'><thead><tr><th colspan='10'>Elenco Disponibilita<br>&nbsp;</th></tr><tr>";
//  data-role=\"table\" data-mode=\"columntoggle\" class=\"ui-responsive ui-shadow\" id=\"myTable\"

foreach($days[0] as $n => $v) {
	
     switch ($n) {
    
    case "Data": echo "<th></th>"; break;
    case "Davide": echo "<th >D</th>"; break;
    case "Andrea": echo "<th>A</th>"; break;
    case "Marco": echo "<th>B</th>"; break;
    case "Beatrice": echo "<th>T</th>"; break;
    case "Antonello": echo "<th>N</th>"; break;
    case "Alessandro": echo "<th>P</th>"; break;
    case "Leonardo": echo "<th>L</th>"; break;
    case "Morris": echo "<th>M</th>"; break;
    case "Matteo": echo "<th>F</th>"; break;
    
    
    }
   
    
    
}

echo "</tr></thead><tbody>";

foreach ($days as $day) {
    if (isset($output[$day["Data"]])) $bgc = "style='background-color: ".$output[$day["Data"]]["colour"]." !important;'";
    else $bgc = "";
	echo "<tr $bgc>";
    
    foreach ($day as $t => $d)
    	if ($t == "Data")
            echo "<td>".$gg[date("N" ,strtotime($d))]." ". date("d" ,strtotime($d))."</td>";
        else
         {
         	if ($t == $_SESSION['player']) $bg = "style='background-color: lightblue;'";
            else $bg='';
        	echo "<td  $bg><img src='".$im[$d]."' width='12'></td>";
          }  
    echo "</tr>";

}

echo "</tbody></table>";

}


function generate_form($days) {

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
                    
                    echo "<fieldset class=\"ui-field-contain\">
   
    <select name=\"date[".$day['Data']."]\" id=\"day\">
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
}

function login() {

	echo "<form method=\"post\" action=\"\" id='login'>
      <div class=\"ui-field-contain\">       
        <input type=\"text\" name=\"name\" id=\"name\">       
      </div>
    <input type='hidden' name='form_type' value='login'>
       <a href='#' class='ui-btn ui-icon-lock ui-btn-icon-left' onClick='$(\"#login\").submit();'>Dite Amici ed Entrate</a>
    </form><br><div align='center'><img src='images/earlogo.png'></div>";
}


function logout() {
	session_destroy();
    unset($_SESSION);
}


echo "<!DOCTYPE html>\n<html>\n<head>\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
<link rel=\"stylesheet\" href=\"js/jquery.mobile-1.4.5.min.css\">
<script src=\"js/jquery-1.11.2.min.js\"></script>
<script src=\"js/jquery.mobile-1.4.5.min.js\"></script>
<style>
.ele {position: relative; float: left;}
</style>\n</head>\n<body>\n<div data-role=\"page\">
<div data-role=\"main\" class=\"ui-content\">";
if (isset($_SESSION['player'])) echo "
<form method=\"post\" action=\"\" id='logout'> <input type='hidden' name='form_type' value='logout'>\n";
echo "<input type='image' src='images/intesta-terzaera.jpg' width='100%' title='Logout' alt='Logout'>\n";
if (isset($_SESSION['player'])) echo "</form>\n <!-- dynamic code -->";   

    if (isset($_SESSION['player']) && $_GET['home']!=1 && $_GET['list']!=1 ) generate_form($days);
    if (isset($_SESSION['player']) && $_GET['home']==1 ) generate_days($days);
    if (isset($_SESSION['player']) && $_GET['list']==1) show_days($days);
 	if (!isset($_SESSION['player'])) login();

echo "<!-- end of dynamic code --></div>\n</div>\n</body>\n</html>\n"; 

?>

