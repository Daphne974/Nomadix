<?php
session_start();

$_SESSION['page_davant'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voyage";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

function normalizeString($str)
{
    // Keep UTF-8 sane when legacy-encoded data slips in.
    if (function_exists('mb_check_encoding') && function_exists('mb_convert_encoding') && !mb_check_encoding($str, 'UTF-8')) {
        $str = mb_convert_encoding($str, 'UTF-8', 'ISO-8859-1, Windows-1252');
    }

    if (function_exists('mb_strtolower')) {
        $str = mb_strtolower($str, 'UTF-8');
    } else {
        $str = strtolower($str);
    }

    if (function_exists('transliterator_transliterate')) {
        $str = transliterator_transliterate('Any-Latin; Latin-ASCII;', $str);
    } else {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        if ($converted !== false) {
            $str = $converted;
        }
    }

    // Force a filename-safe slug: "Pékin" => "pekin".
    $str = preg_replace('/[^a-z0-9]+/', '', $str);
    return $str ?? '';
}

$sql = "SELECT ville, pays, nom FROM destinations";
$result = mysqli_query($conn, $sql);

$destinations = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $destinations[] = $row;
    }
}

mysqli_close($conn);

if (isset($_POST["deconnectetoi"])) {
    session_unset();
    session_destroy();
}

$supprime = $_GET['supprime'] ?? null;

if ($supprime === 'ok') {
    $message = 'Votre compte a été supprimé avec succès';
    $messageClass = "success";
} 
else if ($supprime === 'non') {
    $message = 'Votre compte n\'a pas été supprimé. Veuillez recommencer.';
    $messageClass = "error";
}

if (!empty($message)) {
    if (!isset($_SESSION['messageShown'])) {
        $_SESSION['messageShown'] = true;
    } else {
        $message = '';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Accueil - Nomadix</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #dda0dd, #add8e6);
            margin: 0;
            padding: 0;
        }

        h1,
        p {
            text-align: center;
            margin: 0;
        }

        h1 {
            margin-top: 40px;
            color: rgb(97, 0, 132);
            font-size: 40px;
        }

        p {
            margin-top: 10px;
            color: rgb(152, 0, 207);
            font-size: 1.2em;
        }

        .nav-buttons {
            display: flex;
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .button1,
        .button2,
        .button3,
        .button4 {
            background-color: rgb(94, 203, 250);
            color: rgb(0, 29, 132);
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 10px;
            border-width: 0px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-left: 10px;
            font-size: 90%;
        }

        .button1:hover,
        .button2:hover,
        .button3:hover,
        .button4:hover {
            background-color: rgb(0, 191, 255);
            transform: scale(1.05);
        }

        .destinations {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .destinations a {
            text-decoration: none;
            color: inherit;
        }

        .destination {
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .destination:hover {
            transform: scale(1.02);
        }

        .destination img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .destinations a {
            text-decoration: none;
        }

        .city-name {
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            color: rgb(67, 0, 110);
            text-align: center;
        }

        footer {
            text-align: center;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        @media screen and (max-width: 600px) {
            h1 {
                font-size: 28px;
            }

            p {
                font-size: 1em;
            }

            .destination img {
                height: 140px;
            }
        }

        .message {
            max-width: 400px;
            margin: 20px auto;
            padding: 15px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
            position: relative;
            overflow: hidden;
            transition: opacity 0.5s ease;
        }

        .message.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .close-btn {
            position: absolute;
            top: 5px;
            right: 10px;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
            color: inherit;
        }

        .progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            background-color: currentColor;
            animation: shrinkBar 15s linear forwards;
            width: 100%;
        }

        @keyframes shrinkBar {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        .success {
            background-color: #d4edda;
            color: rgb(0, 246, 57);
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: rgb(242, 6, 30);
            border: 1px solid #f5c6cb;
        }
    </style>

    <script>

        function closeFlashMessage() {
            const flashMessage = document.getElementById('flashMessage');
            if (flashMessage) {
                flashMessage.classList.add('hidden');
                setTimeout(() => flashMessage.style.display = 'none', 500); // attendre que le fade-out se termine
            }
        }

        window.onload = function () {
            const flashMessage = document.getElementById('flashMessage');
            if (flashMessage) {
                setTimeout(() => {
                    flashMessage.classList.add('hidden');
                    setTimeout(() => flashMessage.style.display = 'none', 500);
                }, 15000);
            }
        };

    </script>
</head>

<body>
    <header>
        <?php
        if (isset($_SESSION["user"])) {
            echo "<div class=\"nav-buttons\">
                <form action=\"\" method=\"post\" class=\"deconnection\">
                    <a href=\"profil.php\" class=\"button4\">Profil</a>
                    <button type=\"submit\" name=\"deconnectetoi\" class=\"button3\" onclick=\"return confirm('Es-tu sûr de vouloir te déconnecter ?')\">Se déconnecter</button>
                </form>
            </div>";
        } else {
            echo "<div class=\"nav-buttons\">
                <a href=\"connexion.php\" class=\"button2\">Se connecter</a>
                <a href=\"inscription.php\" class=\"button1\">S'inscrire</a>
            </div>";
        }
        ?>

    </header>
    <main>
        <?php if (!empty($message)): ?>
            <div id="flashMessage" class="message <?= $messageClass ?>">
                <span class="close-btn" onclick="closeFlashMessage()">&times;</span>
                <?= $message ?>
                <div class="progress-bar"></div>
            </div>
        <?php endif; ?>

        <div class="nav-main">
            <h1>Bienvenue sur Nomadix</h1>
            <p>Découvrez nos destinations et <a href="inscription.php" style="color : rgb(152, 0, 207);">créez votre
                    compte</a> dès maintenant !</p>
        </div>
        <div class="destinations">
            <?php foreach ($destinations as $dest): ?>
                <?php
                $ville = $dest['ville'];
                $villeURL = normalizeString($ville);
                $imagePath = "image/" . $villeURL . ".jpg";
                ?>
                <a href="destination.php?ville=<?= urlencode($ville) ?>">
                    <div class="destination">
                        <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($ville) ?>">
                        <div class="city-name">
                            <?= htmlspecialchars($ville) ?> - <?= htmlspecialchars($dest['pays']) ?> </br>
                            <?= htmlspecialchars($dest['nom']) ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>&copy; 2025 Nomadix - Tous droits réservés</footer>
</body>

</html>
