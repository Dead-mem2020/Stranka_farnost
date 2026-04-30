<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jmeno = htmlspecialchars($_POST['jmeno_prijmeni']);
    $email = htmlspecialchars($_POST['kontakt_email']);
    $datum = htmlspecialchars($_POST['termin_akce']);
    $obsah = htmlspecialchars($_POST['obsah_zadosti']);

    $nova_rezervace = [
        "id" => time(),
        "timestamp" => date("Y-m-d H:i:s"),
        "jmeno_prijmeni" => $jmeno,
        "kontakt_email" => $email,
        "termin_akce" => $datum,
        "kategorie" => "Úmysl mše",
        "obsah_zadosti" => $obsah,
        "stav_vyrizeni" => "pending"
    ];

    $soubor = 'rezervace.json';
    $aktualni_data = file_get_contents($soubor);
    $pole_rezervaci = json_decode($aktualni_data, true);

    if (!is_array($pole_rezervaci)) {
        $pole_rezervaci = [];
    }

    array_push($pole_rezervaci, $nova_rezervace);
    file_put_contents($soubor, json_encode($pole_rezervaci, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    echo "<h1>Děkujeme! Vaše rezervace byla úspěšně odeslána.</h1>";
    echo "<p>Váš úmysl: <strong>$obsah</strong> na datum <strong>$datum</strong> evidujeme.</p>";
    echo "<a href='Kontakt.html'>Návrat zpět na web</a>";

} else {
    header("Location: Kontakt.html");
}
?>