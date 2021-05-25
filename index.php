    <?php
/*
Todo mocht ik nog verder willen gaan hiermee:
Meer slagen in 1 beurt gaat voor
Koningin
*/
session_start();
require "functies.php";
//Zet notices uit
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//Zet variabelen
if (!isset($_SESSION['beurt'])) {
    $_SESSION['beurt'] = true;
}
if (isset($_GET['reset'])) {
    unset($_SESSION['stenen']);
    $_SESSION['beurt'] = true;
    header("Location: ../Opdracht 2");
}

//Verwijder session na half uur
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
}
$_SESSION['LAST_ACTIVITY'] = time();
function invert($input, $min, $max)
{
    $distance = $input - $min;
    return $max - $distance;
}

//Geef mogelijke errors weer
echo $_SESSION['error'];
unset($_SESSION['error']);

//Echo op welke schijf is geklikt
if (isset($_GET['x']) && isset($_GET['y']) && isset($_GET['kleur'])) {
    $result = "Er is geklikt op een ";
    if ($_GET['kleur'] == "w") {
        $result .= "witte ";
    } else if ($_GET['kleur']) {
        $result .= "zwarte ";
    } else {
        return;
    }
    $result .= "schijf op positie x:" . $_GET['x'] . " y:" . $_GET['y'];
    //echo $result;
}

    //Bepaal standaardpositie
    $schijfGeplaatst = false;
    if (!isset($_SESSION['stenen'])) {
        $_SESSION['stenen'] = array();

//Vul de array dynamisch met damschijven
        //Wit
        for ($j = 1; $j <= 4; $j++) {
            if ($j % 2 == 0) {
                for ($i = 2; $i <= 10; $i = $i + 2) {
                    $_SESSION['stenen'][] = array($i, $j, "w");
                }
            } else {
                for ($i = 1; $i <= 10; $i = $i + 2) {
                    $_SESSION['stenen'][] = array($i, $j, "w");
                }
            }
        }
        //Zwart
        for ($j = 10; $j >= 7; $j--) {
            if ($j % 2 == 0) {
                for ($i = 2; $i <= 10; $i = $i + 2) {
                    $_SESSION['stenen'][] = array($i, $j, "z");
                }
            } else {
                for ($i = 1; $i <= 10; $i = $i + 2) {
                    $_SESSION['stenen'][] = array($i, $j, "z");
                }
            }
        }

        //Setup voor het testen van telSlaan()
//        $_SESSION['stenen'][] = array(2,3,"w");
//        $_SESSION['stenen'][] = array(3,4,"z");
//        $_SESSION['stenen'][] = array(3,6,"z");
//        $_SESSION['stenen'][] = array(3,8,"z");
//        $_SESSION['stenen'][] = array(5,4,"z");
//        $_SESSION['stenen'][] = array(7,4,"z");
//        //$_SESSION['stenen'][] = array(5,2,"z");
//        $_SESSION['stenen'][] = array(9,4,"z");
//        $_SESSION['stenen'][] = array(7,6,"z");
//        $_SESSION['stenen'][] = array(7,8,"z");
    }

//Maak een array met alle vakjes
$alleVakjes = array();

for ($i = 0; $i < 10; $i++) {
    for ($j = 0; $j < 10; $j++) {
        $alleVakjes[] = array($i, $j);
    }
}

//Maak een array met alle lege vakjes
$legeVakjes = array();

for ($i = 0; $i < sizeof($alleVakjes); $i++) {
    for ($j = 0; $j < sizeof($_SESSION['stenen']); $j++) {
        if ($alleVakjes[$i][0] == $_SESSION['stenen'][$j][0]) {
            $legeVakjes[] = $alleVakjes[$i];
        }
    }
}

kleurStenen();
//Beweeg stuk als daarvoor is gevraagd
if (isset($_GET['geklikteX']) && isset($_GET['geklikteY'])) {
    if ($_GET['kleur'] == "w") {
        if ($_SESSION['beurt']) {
            beweegRegels();
        }
    } else {
        if (!$_SESSION['beurt']) {
            beweegRegels();
        }
    }
}

//Echo wie kan slaan
//echo "witSlaan:";
//if (kleurSlaan("w")) {
//    echo "true";
//} else {
//    echo "false";
//}
//echo "<br>zwartSlaan:";
//if (kleurSlaan("z")) {
//    echo "true";
//} else {
//    echo "false";
//}

if (isset($_GET['submit'])) {
    $inhoud = inhoudVakje($_GET['invoegX'],$_GET['invoegY']);
    if ($inhoud == "w" || $inhoud == "z") {
        echo array_keys($_SESSION['stenen'], array($_GET['invoegX'], $_GET['invoegY'], $inhoud))[0];
        unset($_SESSION['stenen'][array_keys($_SESSION['stenen'], array($_GET['invoegX'], $_GET['invoegY'], $inhoud))[0]]);
        $_SESSION['stenen'] = array_values($_SESSION['stenen']);
    }
    if ($_GET['invoegKleur'] != "leeg") {
        $_SESSION['stenen'][] = array($_GET['invoegX'],$_GET['invoegY'],$_GET['invoegKleur']);
    }
    header("Location: ../Opdracht 2");
}
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dambord</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div id="bord">
    <?php
    //Bord
    for ($i = 0; $i < 10; $i++) {
        for ($j = 0; $j < 10; $j++) {
            if ($i % 2 == 0) {
                if ($j % 2 == 0) {
                    ?>
                    <div class="vakje w"></div><?php
                } else {
                    ?>
                    <div class="vakje z"></div><?php
                }
            } else {
                if ($j % 2 == 0) {
                    ?>
                    <div class="vakje z"></div><?php
                } else {
                    ?>
                    <div class="vakje w"></div><?php
                }
            }
        }
        ?><br><?php
    }
    ?>
</div>
<div class="stenen">
    <?php
    for ($y = 0; $y < 10; $y++) {
        for ($x = 0; $x < 10; $x++) {
            for ($i = 0; $i < sizeof($_SESSION['stenen']); $i++) {
                if ($x == $_SESSION['stenen'][$i][0] - 1 && $y == invert($_SESSION['stenen'][$i][1] + 1, 1, 10)) {
                    ?><a class="schijf <?= $_SESSION['stenen'][$i][2] ?>
                    <?php if ($_GET['x'] == $_SESSION['stenen'][$i][0] && $_GET['y'] == $_SESSION['stenen'][$i][1]) {
                        if ($_SESSION['stenen'][$i][2] == "w") {
                            if ($_SESSION['beurt']) {
                                echo "select";
                            }
                        } else if ($_SESSION['stenen'][$i][2] == "z") {
                            if (!$_SESSION['beurt']) {
                                echo "select";
                            }
                        }

                    } ?>
                        "
                         id="<?= ($_GET['kleur'] == "w") ? "wit$i\"" : "zwart$i";?>"
                         href="?x=<?= $_SESSION['stenen'][$i][0] ?>&y=<?= $_SESSION['stenen'][$i][1] ?>&kleur=<?= $_SESSION['stenen'][$i][2] ?>"></a><?php
                    $schijfGeplaatst = true;
                }
            }
            if ($schijfGeplaatst) {
                $schijfGeplaatst = false;
            } else {
                ?><a class="leeg" href="?geklikteX=<?= $x + 1 ?>&geklikteY=<?= invert($y + 1, 1, 10);
                if (isset($_GET['x']) && isset($_GET['y']) && isset($_GET['kleur'])) {
                    echo "&x=" . $_GET['x'] . "&y=" . $_GET['y'] . "&kleur=" . $_GET['kleur']."\"";
                } ?>"></a><?php
            }
            ?><br><?php
        }
    }
    ?>
</div>
<a id="resetKnop" href="?reset=1">Reset</a>
<div id="beurt">Beurt:
    <div id="beurtIcon"></div>
</div>
    <form id="schijfInsertForm">
        Voeg een schijf in:
        <label>
            <input type="number" name="invoegX" placeholder="X">
        </label>
        <label>
            <input type="number" name="invoegY" placeholder="Y">
        </label>
        <label>
            <input type="text" name="invoegKleur" placeholder="Kleur (w, z, of leeg)">
        </label>
        <input type="submit" name="submit" value="Versturen">
    </form>
<style>
    #beurtIcon {
        background-color: <?php if($_SESSION['beurt']){echo "#FFFFFF";} else {echo "#000000";}?>
    }

    .schijf.w:hover {
    <?php if ($_SESSION['beurt']){echo "background-color: lime";}?>
    }

    .schijf.z:hover {
    <?php if (!$_SESSION['beurt']){echo "background-color: lime";}?>
    }
</style>
<script>
    const bord = document.querySelectorAll(".leeg,.schijf");

    for (let i = 0; i < bord.length; i++) {
        bord[i].addEventListener('contextmenu', function(e) {
            location.href = "../Opdracht 2";
            e.preventDefault();
        });
    }
</script>
<?php
kleurStenen();
if ($witteStenen == array() || $zwarteStenen == array()) {
    ?>
    <div id="eindScherm">
        <?php if ($witteStenen == array()) {
            $winResult = "Zwart";
        } else {
            $winResult = "Wit";
        }
        $winResult .= " heeft gewonnen!<br>Druk linksonder op reset om opnieuw te beginnen!";
        echo $winResult;
        ?>
    </div>
<?php
}
//$telSlaanMax = 0;
//for ($i=0;$i<sizeof($witteStenen);$i++){
//    if ($telSlaan = telSlaan($witteStenen[$i])) {
//        if ($telSlaan > $telSlaanMax) {
//            $telSlaanMax = $telSlaan;
//        }
//        echo "De schijf op (".$witteStenen[$i][0].",".$witteStenen[$i][1].") kan ".$telSlaan." keer slaan<br>";
//    }
//}
//if ($telSlaanMax > 0) {
//    echo "Wit kan maximaal " . $telSlaanMax . " keer slaan<br>";
//} else {
//    echo "Wit kan niet slaan.<br>";
//}
//
//$telSlaanMax = 0;
//for ($i=0;$i<sizeof($zwarteStenen);$i++){
//    if ($telSlaan = telSlaan($zwarteStenen[$i])) {
//        if ($telSlaan > $telSlaanMax) {
//            $telSlaanMax = $telSlaan;
//        }
//        echo "De schijf op (".$zwarteStenen[$i][0].",".$zwarteStenen[$i][1].") kan ".$telSlaan." keer slaan<br>";
//    }
//}
//if ($telSlaanMax > 0) {
//    echo "Zwart kan maximaal " . $telSlaanMax . " keer slaan<br>";
//} else {
//    echo "Zwart kan niet slaan.<br>";
//}
//?>
<!--<br><br><br><br><br>-->
<?php
if (isset($_GET['x'])) {
    echo "De geselecteerde schijf kan op de volgende manieren slaan:<br>";
    $telSlaan = telSlaan(array($_GET['x'],$_GET['y'],$_GET['kleur']));
    for ($i=0;$i<sizeof($telSlaan);$i++){
        for ($j=0;$j<sizeof($telSlaan[$i]);$j++) {
            echo $telSlaan[$i][$j];
            if ($j<sizeof($telSlaan[$i])-1){
                echo " -> ";
            }
        }
        echo "<br>";
    }
//    print_r($telSlaan);
    //echo kanSlaan(array($_GET['x'],$_GET['y'],$_GET['kleur']))[0];
}
?>
</body>
</html>