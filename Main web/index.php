<?php
session_start();

// Simple admin authentication check
$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$adminName = $_SESSION['admin_name'] ?? 'Administrátor';

// Load data from JSON files
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
            <button class="cta_btn" id="showActiveEvents">Aktivní bohoslužby</button>
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
</body>
</html>