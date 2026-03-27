<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jmeno = htmlspecialchars($_POST['jmeno']);
    $email = htmlspecialchars($_POST['email']);
    $zprava = htmlspecialchars($_POST['zprava']);

    $novy_dotaz = [
        "id" => time(),
        "datum_odeslani" => date("Y-m-d H:i:s"),
        "jmeno" => $jmeno,
        "email" => $email,
        "zprava" => $zprava
    ];

    $soubor = 'dotazy.json';
    $aktualni_data = file_get_contents($soubor);
    $pole_dotazu = json_decode($aktualni_data, true);

    if (!is_array($pole_dotazu)) {
        $pole_dotazu = [];
    }

    array_push($pole_dotazu, $novy_dotaz);
    file_put_contents($soubor, json_encode($pole_dotazu, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    echo "<h1>Zpráva byla odeslána.</h1>";
    echo "<p>Děkujeme za váš dotaz, brzy se vám ozveme na email $email.</p>";
    echo "<a href='Kontakt.html'>Návrat zpět na web</a>";
} else {
    header("Location: Kontakt.html");
}
?>