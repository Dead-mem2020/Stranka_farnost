<?php
session_start();

// rezervace
$rezervaceMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rezervace') {
    $rezervaceData = json_decode(file_get_contents('rezervace.json'), true) ?? [];
    
    $novaRezervace = [
        'id' => time(),
        'jmeno' => $_POST['jmeno'] ?? '',
        'email' => $_POST['email'] ?? '',
        'datum' => $_POST['datum'] ?? '',
        'sluzba' => $_POST['sluzba'] ?? '',
        'zprava' => $_POST['zprava'] ?? ''
    ];
    
    $rezervaceData[] = $novaRezervace;
    file_put_contents('rezervace.json', json_encode($rezervaceData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    $rezervaceMessage = 'Vaše rezervace byla úspěšně odeslána!';
}

// Simple admin authentication check
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$adminName = $_SESSION['admin_name'] ?? 'Administrátor';

// Load data from JSON files
$sluzby = json_decode(file_get_contents('sluzby.json'), true) ?? [];
$aktuality = json_decode(file_get_contents('aktuality.json'), true) ?? [];
$galerie = json_decode(file_get_contents('galerie.json'), true) ?? [];
$pageContent = json_decode(file_get_contents('page_content.json'), true) ?? [
    'about' => ['content' => '', 'content2' => ''],
    'services' => ['content' => '', 'content2' => ''],
    'contact' => ['address' => '', 'phone' => '', 'email' => '', 'farar' => '', 'hours' => '']
];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="icon" type="image/x-icon" href="images/Farnost.png">
    <title>Farnost 👍</title>
</head>
<body>
    <header id="home">
        <nav>
            <img src="images/Farnost.png" alt="Farnost_Logo" id="logo">
            
            <ul class="nav">
                <li><a href="#home">Domů</a></li>
                <li><a href="#about">O nás</a></li>
                <li><a href="#services">Bohoslužby</a></li>
                <li><a href="#gallery">Galerie</a></li>
                <li><a href="#contact">Kontakt</a></li>
            </ul>
            
            <div class="btn">
                <?php if ($isLoggedIn): ?>
                    <a href="admin.php" class="login_btn" style="text-decoration: none;">Admin Panel</a>
                    <a href="logout.php" class="login_btn" style="text-decoration: none; margin-left: 0.5rem;">Odhlásit</a>
                <?php else: ?>
                    <a href="login.php" class="login_btn">Přihlásit</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="hero">
            <h1>Římskokatolická farnost Přeštice</h1>
            <h2>Vítejte na našich oficiálních stránkách.<br>
                Jsme živé společenství věřících v srdci Přeštic.<br>
                Najdete zde informace o bohoslužbách, svátostech a dění ve farnosti.
            </h2>
        </div>
    </header>
    
    
    <main class="container">

        <section class="panel-grid">
            <section class="aktuality">
                <h2>Aktuality</h2>
                <div class="news-grid">
                    <?php foreach ($aktuality as $akt): ?>
                    <article class="news-card">
                        <div class="news-date"><?php echo htmlspecialchars($akt['datum'] ?? ''); ?></div>
                        <h3><?php echo htmlspecialchars($akt['nazev'] ?? ''); ?></h3>
                        <p><?php echo htmlspecialchars($akt['text'] ?? ''); ?></p>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="kalendar">
                <h2>Kalendar</h2>
                <div class="calendar-card">
                    <div class="calendar-header">
                        <button id="prevMonth" type="button">&lsaquo;</button>
                        <div class="calendar-title"><span id="calendarMonth"></span></div>
                        <button id="nextMonth" type="button">&rsaquo;</button>
                    </div>
                    <div class="calendar-info" id="calendarInfo">Klikněte na tlačítko pro zobrazení aktivních bohoslužeb.</div>
                    <div class="calendar-weekdays">
                        <span>Po</span><span>Út</span><span>St</span><span>Čt</span><span>Pá</span><span>So</span><span>Ne</span>
                    </div>
                    <div class="calendar-grid" id="calendarGrid"></div>
                </div>
            </section>
        </section>
    </main>

    <section id="about" class="section">
        <div class="container">
            <h2>O nás</h2>
            <p><?php echo nl2br(htmlspecialchars($pageContent['about']['content'] ?? '')); ?></p>
            <p><?php echo nl2br(htmlspecialchars($pageContent['about']['content2'] ?? '')); ?></p>
        </div>
    </section>

    <section id="services" class="section">
        <div class="container">
            <h2>Bohoslužby</h2>
            <p><?php echo nl2br(htmlspecialchars($pageContent['services']['content'] ?? '')); ?></p>
            <p><?php echo nl2br(htmlspecialchars($pageContent['services']['content2'] ?? '')); ?></p>
            <button class="cta_btn" id="showActiveEvents">Rezervovat bohoslužbu</button>

            <div id="rezervaceModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn">&times;</span>
                    <h3>Rezervace služby</h3>
                    
                    <?php if (!empty($rezervaceMessage)): ?>
                        <div class="success-msg"><?php echo $rezervaceMessage; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="index.php#services" class="rezervace-form">
                        <input type="hidden" name="action" value="rezervace">
                        
                        <div class="form-group">
                            <label for="jmeno">Jméno a příjmení <span style="color: red;">*</span></label>
                            <input type="text" id="jmeno" name="jmeno" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">E-mail <span style="color: red;">*</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="datum">Datum a čas bohoslužby <span style="color: red;">*</span></label>
                            <input type="datetime-local" id="datum" name="datum" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sluzba">Výběr služby <span style="color: red;">*</span></label>
                            <select id="sluzba" name="sluzba" required>
                                <option value="">-- Vyberte, co požadujete --</option>
                                <?php foreach ($sluzby as $sluzba): ?>
                                    <option value="<?php echo htmlspecialchars($sluzba['nazev']); ?>">
                                        <?php echo htmlspecialchars($sluzba['nazev']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="zprava">Zpráva / Poznámka</label>
                            <textarea id="zprava" name="zprava" rows="4"></textarea>
                            <p style="font-size: 0.85rem; color: #666; margin-top: 0.5rem; text-align: left;"><span style="color: red;">*</span> vyžadováno vyplnit</p>
                        </div>
                        
                        <button type="submit" class="cta_btn" style="width: 100%; margin-top: 10px;">Odeslat rezervaci</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section id="gallery" class="section">
        <div class="container">
            <h2>Galerie</h2>
            <p>Zde najdete fotografie z našich bohoslužeb, farních akcí a života farnosti. Galerie se pravidelně aktualizuje.</p>
            <div class="gallery-grid">
                <?php foreach ($galerie as $img): ?>
                <img src="<?php echo htmlspecialchars($img['src'] ?? ''); ?>" alt="<?php echo htmlspecialchars($img['alt'] ?? ''); ?>">
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="contact" class="section">
        <div class="container">
            <h2>Kontakt</h2>
            <p><?php echo nl2br(htmlspecialchars($pageContent['contact']['address'] ?? '')); ?><br>
            Telefon: <?php echo htmlspecialchars($pageContent['contact']['phone'] ?? ''); ?><br>
            Email: <?php echo htmlspecialchars($pageContent['contact']['email'] ?? ''); ?></p>
            <p>Farář: <?php echo htmlspecialchars($pageContent['contact']['farar'] ?? ''); ?><br>
            Úřední hodiny: <?php echo htmlspecialchars($pageContent['contact']['hours'] ?? ''); ?></p>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 Římskokatolická farnost Přeštice. Všechna práva vyhrazena.</p>
    </footer>

    <script src="../Javascript/backend/kalendar.js"></script>
    <div class="footer-spacer"></div>

    <!--Skript pro rezervac-->
    <script>
        const modal = document.getElementById("rezervaceModal");
        const btn = document.getElementById("showActiveEvents");
        const span = document.getElementsByClassName("close-btn")[0];

        // Otevření modalu
        if (btn) {
            btn.onclick = function() {
                modal.style.display = "block";
            }
        }

        // Zavření křížkem
        if (span) {
            span.onclick = function() {
                modal.style.display = "none";
            }
        }

        // Zavření kliknutím mimo okno
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Automatické zobrazení modalu, pokud byla rezervace úspěšně odeslána
        <?php if (!empty($rezervaceMessage)): ?>
            modal.style.display = "block";
        <?php endif; ?>
    </script>
</body>
</html>