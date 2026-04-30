<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Load data files
$aktuality = json_decode(file_get_contents('aktuality.json'), true) ?? [];
$galerie = json_decode(file_get_contents('galerie.json'), true) ?? [];
$dotazy = json_decode(file_get_contents('dotazy.json'), true) ?? [];
$rezervace = json_decode(file_get_contents('rezervace.json'), true) ?? [];

// Load page content
$pageContent = json_decode(file_get_contents('page_content.json'), true) ?? [
    'about' => [
        'title' => 'O nás',
        'content' => 'Římskokatolická farnost Přeštice je živé společenství věřících, které se schází k bohoslužbám, modlitbám a společným aktivitám. Naše farnost je součástí plzeňské diecéze a snaží se být otevřeným místem pro všechny, kteří hledají duchovní útěchu a společenství.',
        'content2' => 'Farnost vede P. Jan Novák, který je k dispozici pro duchovní vedení, svátosti a pastorační péči. Máme bohatou historii sahající až do 14. století, kdy byl postaven první kostel v Přešticích.'
    ],
    'services' => [
        'title' => 'Bohoslužby',
        'content' => 'Nabízíme pravidelné bohoslužby v kostele sv. Václava v Přešticích. Nedělní mše svatá se koná každou neděli v 10:00 hodin. Během týdne máme mše svaté ve středu a pátek večer.',
        'content2' => 'Kromě pravidelných bohoslužeb pořádáme také svátosti křtu, biřmování, manželství a poslední pomazání. Pro děti a mládež organizujeme katecheze a různé aktivity.'
    ],
    'contact' => [
        'title' => 'Kontakt',
        'address' => 'Kostel sv. Václava<br>Náměstí 123, 334 01 Přeštice',
        'phone' => '+420 123 456 789',
        'email' => 'farnost@prestice.cz',
        'farar' => 'P. Jan Novák',
        'hours' => 'Po-Pá 9:00-12:00'
    ]
];

$message = '';
$activeTab = $_GET['tab'] ?? 'aktuality';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Add new aktualita
    if (isset($_POST['action']) && $_POST['action'] === 'add_aktualita') {
        $newAktualita = [
            'id' => time(),
            'datum' => $_POST['datum'] ?? date('d.m.Y'),
            'nazev' => $_POST['nazev'] ?? '',
            'text' => $_POST['text'] ?? ''
        ];
        $aktuality[] = $newAktualita;
        file_put_contents('aktuality.json', json_encode($aktuality, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $message = 'Aktualita byla přidána.';
    }
    
    // Delete aktualita
    if (isset($_POST['action']) && $_POST['action'] === 'delete_aktualita') {
        $id = intval($_POST['id']);
        $aktuality = array_filter($aktuality, function($a) use ($id) { return $a['id'] != $id; });
        file_put_contents('aktuality.json', json_encode(array_values($aktuality), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $message = 'Aktualita byla smazána.';
    }
    
    // Add gallery image
    if (isset($_POST['action']) && $_POST['action'] === 'add_gallery') {
        $newImage = [
            'id' => time(),
            'src' => $_POST['src'] ?? '',
            'alt' => $_POST['alt'] ?? ''
        ];
        $galerie[] = $newImage;
        file_put_contents('galerie.json', json_encode($galerie, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $message = 'Obrázek byl přidán do galerie.';
    }
    
    // Delete gallery image
    if (isset($_POST['action']) && $_POST['action'] === 'delete_gallery') {
        $id = intval($_POST['id']);
        $galerie = array_filter($galerie, function($g) use ($id) { return $g['id'] != $id; });
        file_put_contents('galerie.json', json_encode(array_values($galerie), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $message = 'Obrázek byl smazán z galerie.';
    }
    
    // Update page content
    if (isset($_POST['action']) && $_POST['action'] === 'update_content') {
        $section = $_POST['section'] ?? '';
        if ($section === 'about') {
            $pageContent['about']['content'] = $_POST['content'] ?? '';
            $pageContent['about']['content2'] = $_POST['content2'] ?? '';
        } elseif ($section === 'services') {
            $pageContent['services']['content'] = $_POST['content'] ?? '';
            $pageContent['services']['content2'] = $_POST['content2'] ?? '';
        } elseif ($section === 'contact') {
            $pageContent['contact']['address'] = $_POST['address'] ?? '';
            $pageContent['contact']['phone'] = $_POST['phone'] ?? '';
            $pageContent['contact']['email'] = $_POST['email'] ?? '';
            $pageContent['contact']['farar'] = $_POST['farar'] ?? '';
            $pageContent['contact']['hours'] = $_POST['hours'] ?? '';
        }
        file_put_contents('page_content.json', json_encode($pageContent, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $message = 'Obsah byl aktualizován.';
    }
    
    // Delete query
    if (isset($_POST['action']) && $_POST['action'] === 'delete_dotaz') {
        $id = intval($_POST['id']);
        $dotazy = array_filter($dotazy, function($d) use ($id) { return $d['id'] != $id; });
        file_put_contents('dotazy.json', json_encode(array_values($dotazy), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $message = 'Dotaz byl smazán.';
    }
    
    // Delete reservation
    if (isset($_POST['action']) && $_POST['action'] === 'delete_rezervace') {
        $id = intval($_POST['id']);
        $rezervace = array_filter($rezervace, function($r) use ($id) { return $r['id'] != $id; });
        file_put_contents('rezervace.json', json_encode(array_values($rezervace), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        $message = 'Rezervace byla smazána.';
    }
    
    // Reload data after changes
    $aktuality = json_decode(file_get_contents('aktuality.json'), true) ?? [];
    $galerie = json_decode(file_get_contents('galerie.json'), true) ?? [];
    $dotazy = json_decode(file_get_contents('dotazy.json'), true) ?? [];
    $rezervace = json_decode(file_get_contents('rezervace.json'), true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Admin Panel - Farnost Přeštice</title>
    <style>
        * { box-sizing: border-box; }
        .admin-container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #6F7D5C; }
        .admin-header h1 { color: #6F7D5C; text-shadow: none; }
        .logout-btn { padding: 0.6rem 1.5rem; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; }
        
        /* Tabs */
        .admin-tabs { display: flex; gap: 0.5rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .admin-tab { padding: 0.8rem 1.5rem; background: #f0f0f0; border: none; border-radius: 5px 5px 0 0; cursor: pointer; font-size: 1rem; transition: all 0.3s; }
        .admin-tab.active { background: #6F7D5C; color: white; }
        .admin-tab:hover:not(.active) { background: #e0e0e0; }
        
        /* Content */
        .admin-content { background: white; padding: 2rem; border-radius: 0 5px 5px 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        
        /* Forms */
        .form-section { margin-bottom: 2rem; padding: 1.5rem; background: #f9f9f9; border-radius: 8px; }
        .form-section h3 { color: #6F7D5C; margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #333; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .btn { padding: 0.8rem 1.5rem; background: #6F7D5C; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; }
        .btn:hover { background: #758268; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        
        /* Lists */
        .item-list { list-style: none; }
        .item-list li { padding: 1rem; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .item-list li:last-child { border-bottom: none; }
        .item-info h4 { color: #333; margin-bottom: 0.3rem; }
        .item-info p { color: #666; font-size: 0.9rem; }
        .item-actions { display: flex; gap: 0.5rem; }
        
        /* Stats */
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-box { background: white; padding: 1.5rem; border-radius: 10px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .stat-box h3 { color: #6F7D5C; font-size: 2rem; margin-bottom: 0.5rem; }
        .stat-box p { color: #666; }
        
        /* Message */
        .message { padding: 1rem; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 1rem; }
        
        /* Gallery grid */
        .gallery-admin-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
        .gallery-item { position: relative; border-radius: 8px; overflow: hidden; }
        .gallery-item img { width: 100%; height: 150px; object-fit: cover; }
        .gallery-item .overlay { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.7); color: white; padding: 0.5rem; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>🔧 Admin Panel - Farnost Přeštice</h1>
            <div>
                <span style="margin-right: 1rem;">Přihlášen: <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                <a href="logout.php" class="logout-btn">Odhlásit</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-box">
                <h3><?php echo count($aktuality); ?></h3>
                <p>Aktualit</p>
            </div>
            <div class="stat-box">
                <h3><?php echo count($galerie); ?></h3>
                <p>Obrázků v galerii</p>
            </div>
            <div class="stat-box">
                <h3><?php echo count($dotazy); ?></h3>
                <p>Dotazů</p>
            </div>
            <div class="stat-box">
                <h3><?php echo count($rezervace); ?></h3>
                <p>Rezervací</p>
            </div>
        </div>

        <div class="admin-tabs">
            <button class="admin-tab <?php echo $activeTab === 'aktuality' ? 'active' : ''; ?>" onclick="location.href='?tab=aktuality'">📰 Aktuality</button>
            <button class="admin-tab <?php echo $activeTab === 'galerie' ? 'active' : ''; ?>" onclick="location.href='?tab=galerie'">🖼️ Galerie</button>
            <button class="admin-tab <?php echo $activeTab === 'obsah' ? 'active' : ''; ?>" onclick="location.href='?tab=obsah'">📝 Obsah stránek</button>
            <button class="admin-tab <?php echo $activeTab === 'dotazy' ? 'active' : ''; ?>" onclick="location.href='?tab=dotazy'">📧 Dotazy</button>
            <button class="admin-tab <?php echo $activeTab === 'rezervace' ? 'active' : ''; ?>" onclick="location.href='?tab=rezervace'">📅 Rezervace</button>
        </div>

        <div class="admin-content">
            <!-- Aktuality Tab -->
            <div class="tab-panel <?php echo $activeTab === 'aktuality' ? 'active' : ''; ?>" id="aktuality">
                <h2>Správa aktualit</h2>
                
                <div class="form-section">
                    <h3>Přidat novou aktualitu</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_aktualita">
                        <div class="form-group">
                            <label>Datum:</label>
                            <input type="text" name="datum" placeholder="např. 30.4.2026" value="<?php echo date('d.m.Y'); ?>">
                        </div>
                        <div class="form-group">
                            <label>Název:</label>
                            <input type="text" name="nazev" placeholder="Název aktuality" required>
                        </div>
                        <div class="form-group">
                            <label>Text:</label>
                            <textarea name="text" placeholder="Popis aktuality..." required></textarea>
                        </div>
                        <button type="submit" class="btn">Přidat aktualitu</button>
                    </form>
                </div>

                <h3>Existující aktuality</h3>
                <ul class="item-list">
                    <?php foreach (array_reverse($aktuality) as $akt): ?>
                        <li>
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($akt['nazev'] ?? ''); ?></h4>
                                <p><?php echo htmlspecialchars($akt['datum'] ?? ''); ?> - <?php echo htmlspecialchars(mb_substr($akt['text'] ?? '', 0, 100)); ?>...</p>
                            </div>
                            <div class="item-actions">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_aktualita">
                                    <input type="hidden" name="id" value="<?php echo $akt['id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Opravdu smazat?')">🗑️</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Galerie Tab -->
            <div class="tab-panel <?php echo $activeTab === 'galerie' ? 'active' : ''; ?>" id="galerie">
                <h2>Správa galerie</h2>
                
                <div class="form-section">
                    <h3>Přidat nový obrázek</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_gallery">
                        <div class="form-group">
                            <label>Cesta k obrázku:</label>
                            <input type="text" name="src" placeholder="images/nazev.jpg" required>
                        </div>
                        <div class="form-group">
                            <label>Alternativní text:</label>
                            <input type="text" name="alt" placeholder="Popis obrázku" required>
                        </div>
                        <button type="submit" class="btn">Přidat obrázek</button>
                    </form>
                </div>

                <h3>Existující obrázky</h3>
                <div class="gallery-admin-grid">
                    <?php foreach (array_reverse($galerie) as $img): ?>
                        <div class="gallery-item">
                            <img src="<?php echo htmlspecialchars($img['src'] ?? ''); ?>" alt="<?php echo htmlspecialchars($img['alt'] ?? ''); ?>">
                            <div class="overlay">
                                <?php echo htmlspecialchars($img['alt'] ?? ''); ?>
                                <form method="POST" style="display:inline; float: right;">
                                    <input type="hidden" name="action" value="delete_gallery">
                                    <input type="hidden" name="id" value="<?php echo $img['id']; ?>">
                                    <button type="submit" class="btn btn-danger" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Opravdu smazat?')">🗑️</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Obsah Tab -->
            <div class="tab-panel <?php echo $activeTab === 'obsah' ? 'active' : ''; ?>" id="obsah">
                <h2>Správa obsahu stránek</h2>
                
                <div class="form-section">
                    <h3>Sekce "O nás"</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_content">
                        <input type="hidden" name="section" value="about">
                        <div class="form-group">
                            <label>Text:</label>
                            <textarea name="content"><?php echo htmlspecialchars($pageContent['about']['content'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Další text:</label>
                            <textarea name="content2"><?php echo htmlspecialchars($pageContent['about']['content2'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn">Uložit</button>
                    </form>
                </div>

                <div class="form-section">
                    <h3>Sekce "Bohoslužby"</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_content">
                        <input type="hidden" name="section" value="services">
                        <div class="form-group">
                            <label>Text:</label>
                            <textarea name="content"><?php echo htmlspecialchars($pageContent['services']['content'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Další text:</label>
                            <textarea name="content2"><?php echo htmlspecialchars($pageContent['services']['content2'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn">Uložit</button>
                    </form>
                </div>

                <div class="form-section">
                    <h3>Sekce "Kontakt"</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_content">
                        <input type="hidden" name="section" value="contact">
                        <div class="form-group">
                            <label>Adresa:</label>
                            <input type="text" name="address" value="<?php echo htmlspecialchars($pageContent['contact']['address'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Telefon:</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($pageContent['contact']['phone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="text" name="email" value="<?php echo htmlspecialchars($pageContent['contact']['email'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Farář:</label>
                            <input type="text" name="farar" value="<?php echo htmlspecialchars($pageContent['contact']['farar'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Úřední hodiny:</label>
                            <input type="text" name="hours" value="<?php echo htmlspecialchars($pageContent['contact']['hours'] ?? ''); ?>">
                        </div>
                        <button type="submit" class="btn">Uložit</button>
                    </form>
                </div>
            </div>

            <!-- Dotazy Tab -->
            <div class="tab-panel <?php echo $activeTab === 'dotazy' ? 'active' : ''; ?>" id="dotazy">
                <h2>Dotazy od návštěvníků</h2>
                <ul class="item-list">
                    <?php foreach (array_reverse($dotazy) as $dotaz): ?>
                        <li>
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($dotaz['jmeno'] ?? ''); ?> (<?php echo htmlspecialchars($dotaz['email'] ?? ''); ?>)</h4>
                                <p><?php echo htmlspecialchars($dotaz['datum_odeslani'] ?? ''); ?></p>
                                <p><?php echo htmlspecialchars($dotaz['zprava'] ?? ''); ?></p>
                            </div>
                            <div class="item-actions">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_dotaz">
                                    <input type="hidden" name="id" value="<?php echo $dotaz['id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Opravdu smazat?')">🗑️</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($dotazy)): ?>
                        <li><p class="empty-message">Žádné dotazy</p></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Rezervace Tab -->
            <div class="tab-panel <?php echo $activeTab === 'rezervace' ? 'active' : ''; ?>" id="rezervace">
                <h2>Rezervace</h2>
                <ul class="item-list">
                    <?php foreach (array_reverse($rezervace) as $rez): ?>
                        <li>
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($rez['jmeno'] ?? ''); ?> (<?php echo htmlspecialchars($rez['email'] ?? ''); ?>)</h4>
                                <p><?php echo htmlspecialchars($rez['datum'] ?? ''); ?></p>
                                <p><?php echo htmlspecialchars($rez['zprava'] ?? ''); ?></p>
                            </div>
                            <div class="item-actions">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_rezervace">
                                    <input type="hidden" name="id" value="<?php echo $rez['id']; ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Opravdu smazat?')">🗑️</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($rezervace)): ?>
                        <li><p class="empty-message">Žádné rezervace</p></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>