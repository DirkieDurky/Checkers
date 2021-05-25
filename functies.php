<?php

function toggleKleur($kleur)
{
    if ($kleur == "w") {
        return "z";
    } else {
        return "w";
    }
}

//Zet richting om naar de juiste variabelen
function richtingNaarVerandering($richting)
{
    global $veranderingX;
    global $veranderingY;
    switch ($richting) {
        case "lb":
            $veranderingX = -1;
            $veranderingY = +1;
            break;
        case "rb":
            $veranderingX = +1;
            $veranderingY = +1;
            break;
        case "lo":
            $veranderingX = -1;
            $veranderingY = -1;
            break;
        case "ro":
            $veranderingX = +1;
            $veranderingY = -1;
            break;
        default:
            throw new InvalidArgumentException("Voer een juiste richting in");
    }
}

//Verplaats het geselecteerde stuk naar het geselecteerde vakje
function beweegStuk()
{
    //Plaats schijf op vakje waar is geklikt
    $_SESSION['stenen'][] = array($_GET['geklikteX'], $_GET['geklikteY'], $_GET['kleur']);

    //Haal oude schijf weg
    unset($_SESSION['stenen'][array_keys($_SESSION['stenen'], array($_GET['x'], $_GET['y'], $_GET['kleur']))[0]]);
    $_SESSION['stenen'] = array_values($_SESSION['stenen']);

    if (kanSlaan(array($_GET['x'],$_GET['y'],$_GET['kleur']))) {
        $_SESSION['i'] = "true";
    } else {
        $_SESSION['i'] = "false";
    }
}

//Krijg de inhoud van een vakje
function inhoudVakje($x, $y)
{
    if (isset($_SESSION['stenen'][array_keys($_SESSION['stenen'], array($x, $y, "w"))[0]])) {
        return "w";
    } else if (isset($_SESSION['stenen'][array_keys($_SESSION['stenen'], array($x, $y, "z"))[0]])) {
        return "z";
    } else if ($x >= 1 && $x <= 10 && $y >= 1 && $y <= 10) {
        return "leeg";
    } else {
        return "niet bestaand";
    }
}

function telSlaan($schijf){
    $slaRichtingen = kanSlaan($schijf);
    for ($i=0;$i<sizeof($slaRichtingen);$i++) {
        switch($slaRichtingen[$i]){
                case "lb":
                    $veranderingX = -2;
                    $veranderingY = +2;
                    break;
                case "rb":
                    $veranderingX = +2;
                    $veranderingY = +2;
                    break;
                case "lo":
                    $veranderingX = -2;
                    $veranderingY = -2;
                    break;
                case "ro":
                    $veranderingX = +2;
                    $veranderingY = -2;
                    break;
            default:
                throw new InvalidArgumentException("Geen juiste richting aangegeven");
            }

            $slaRichtingen2 = kanSlaan(array($schijf[0]+$veranderingX,$schijf[1]+$veranderingY,$schijf[2]));

            //Maak nieuwe array voor elke mogelijkheid
            if (sizeof($slaRichtingen2) == 0){
                $mogelijkePaden[][] = $slaRichtingen[$i];
            } else {
                for ($j = 0; $j < sizeof($slaRichtingen2); $j++) {
                    $mogelijkePaden[][] = $slaRichtingen[$i];
                }

                //Voeg kant toe aan array
                if (!isset($k)) {
                    $k=0;
                }
                for ($l=$k;$l<sizeof($slaRichtingen2)+$k;$l++) {
                    $mogelijkePaden[$l][] = $slaRichtingen2[$l-$k];
                }
                $k=$j;
            }
    }
    if (!isset($mogelijkePaden)) {
        $mogelijkePaden = array();
    }
    return $mogelijkePaden;
}

//Controleer of een kleur kan slaan
function kanSlaan($schijf){
    $result = array();
    if (controleerRichting($schijf, "lb")) {
        //echo "Schijf op (".$schijf[0].",".$schijf[1].") kan slaan naar linksboven.<br>";
        $result[] = "lb";
    }
    if (controleerRichting($schijf, "rb")) {
        //echo "Schijf op (".$schijf[0].",".$schijf[1].") kan slaan naar rechtsboven.<br>";
        $result[] = "rb";
    }
    if (controleerRichting($schijf, "lo")) {
        //echo "Schijf op (".$schijf[0].",".$schijf[1].") kan slaan naar linksonder.<br>";
        $result[] = "lo";
    }
    if (controleerRichting($schijf, "ro")) {
        //echo "Schijf op (".$schijf[0].",".$schijf[1].") kan slaan naar rechtsonder.<br>";
        $result[] = "ro";
    }
        return $result;
}

//Controleer voor een richting of er geslagen kan worden
function controleerRichting($schijf, $richting)
{
    global $veranderingX;
    global $veranderingY;

    if ($schijf[2] == "w") {
        $andereKleur = "z";
    } else {
        $andereKleur = "w";
    }

    richtingNaarVerandering($richting);
    if (inhoudVakje($schijf[0] + $veranderingX, $schijf[1] + $veranderingY) == $andereKleur &&
        inhoudVakje($schijf[0] + $veranderingX * 2, $schijf[1] + $veranderingY * 2) == "leeg") {
        return true;
    } else {
        return false;
    }
}

function beweegRegels(){
    //Lopen
    if (($_GET['geklikteY'] == $_GET['y'] + 1 && ($_GET['geklikteX'] == $_GET['x'] + 1 || $_GET['geklikteX'] == $_GET['x'] - 1)) ||
        ($_GET['geklikteY'] == $_GET['y'] - 1 && ($_GET['geklikteX'] == $_GET['x'] + 1 || $_GET['geklikteX'] == $_GET['x'] - 1))) {
        //Wit
        if ($_GET['kleur'] == "w") {
            if (!kleurSlaan("w")) {
                if ($_GET['geklikteY'] == $_GET['y'] + 1 && ($_GET['geklikteX'] == $_GET['x'] + 1 || $_GET['geklikteX'] == $_GET['x'] - 1)) {
                    beweegStuk();
                    $_SESSION['beurt'] = !$_SESSION['beurt'];
                }
            }
        //Zwart
        } else {
            if (!kleurSlaan("z")) {
                if ($_GET['geklikteY'] == $_GET['y'] - 1 && ($_GET['geklikteX'] == $_GET['x'] + 1 || $_GET['geklikteX'] == $_GET['x'] - 1)) {
                    beweegStuk();
                    $_SESSION['beurt'] = !$_SESSION['beurt'];
                }
            }
        }
    } else {
        //Slaan
        regelsSlaan("lb", $_GET['kleur']);
        regelsSlaan("rb", $_GET['kleur']);
        regelsSlaan("lo", $_GET['kleur']);
        regelsSlaan("ro", $_GET['kleur']);
    }
//Haal alle $_GET variabelen weg
    //header("Location: ../Opdracht 2?x=".$_GET['x']."&y=".$_GET['y']."&kleur=".$_GET['kleur']);
    //header('Restart');
    //header("Location: ../Opdracht 2?geklikteY=".$_GET['geklikteY']."&geklikteX".$_GET['geklikteX']."&kleur=".$_GET['kleur']);
}

function regelsSlaan($richting, $kleur){
    global $veranderingX;
    global $veranderingY;
    $andereKleur = toggleKleur($kleur);
    richtingNaarVerandering($richting);

    if ($_GET['geklikteX'] == $_GET['x'] + $veranderingX * 2 && $_GET['geklikteY'] == $_GET['y'] + $veranderingY * 2) {
        if (inhoudVakje($_GET['x'] + $veranderingX, $_GET['y'] + $veranderingY) == $andereKleur) {
            unset($_SESSION['stenen'][array_keys($_SESSION['stenen'], array($_GET['x'] + $veranderingX, $_GET['y'] + $veranderingY, $andereKleur))[0]]);
            $_SESSION['stenen'] = array_values($_SESSION['stenen']);
            beweegStuk();
                if (!kanSlaan(array($_GET['geklikteX'],$_GET['geklikteY'],$_GET['kleur']))) {
                    $_SESSION['beurt'] = !$_SESSION['beurt'];
                }
        }
    }
}

//Update de $witteStenen en $zwarteStenen array
function kleurStenen() {
    global $witteStenen;
    global $zwarteStenen;
    $witteStenen = array();
    $zwarteStenen = array();

    for ($i = 0; $i < sizeof($_SESSION['stenen']); $i++) {
        if ($_SESSION['stenen'][$i][2] == "w") {
            $witteStenen[] = $_SESSION['stenen'][$i];
        }
    }
    for ($i = 0; $i < sizeof($_SESSION['stenen']); $i++) {
        if ($_SESSION['stenen'][$i][2] == "z") {
            $zwarteStenen[] = $_SESSION['stenen'][$i];
        }
    }
}

function kleurSlaan($kleur) {
    global $witteStenen;
    global $zwarteStenen;
    $slaan = false;
    if ($kleur == "w"){
        for ($i = 0; $i < sizeof($witteStenen); $i++) {
            if (kanSlaan($witteStenen[$i])) {
                $slaan = true;
            }
        }
    } else if ($kleur == "z"){
        for ($i = 0; $i < sizeof($zwarteStenen); $i++) {
            if (kanSlaan($zwarteStenen[$i])) {
                $slaan = true;
            }
        }
    } else {
        throw new InvalidArgumentException("Geef een juiste kleur (w of z)");
    }
    return $slaan;
}