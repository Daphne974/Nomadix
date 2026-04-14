<?php
session_start();

$_SESSION['page_davant'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

?>
<?php
$host = 'localhost';
$dbname = 'Voyage';
$username = 'root';
$password = '';

$url = "";
$destination = null;
$backgroundImageCss = "none";
$remoteImage = "";

function normalizeImageName($str)
{
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

    $str = preg_replace('/[^a-z0-9]+/', '', $str);
    return $str ?? '';
}

function cssUrl($url)
{
    $safe = str_replace(array("\\", "'"), array("\\\\", "\\'"), $url);
    return "url('" . $safe . "')";
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    $user = null;
}

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die('<p style="color:red;">Erreur de connexion : ' . mysqli_connect_error() . '</p>');
}

if (isset($_POST["deconnectetoi"])) {
    session_unset();
    session_destroy();
}

if (isset($_GET["ville"]) && !empty($_GET["ville"])) {
    $destination = $_GET["ville"];
}

if (!empty($destination)) {
    $destination = mysqli_real_escape_string($conn, $destination);
    $sql5 = "SELECT id FROM destinations WHERE ville = ?";
    $stmt5 = $conn->prepare($sql5);
    $stmt5->bind_param("s", $_GET["ville"]);
    $stmt5->execute();
    $result5 = $stmt5->get_result();

    if (mysqli_num_rows($result5) == 1) {
        while ($row5 = mysqli_fetch_assoc($result5)) {
            $destination = $row5['id'];
        }
    } else if (mysqli_num_rows($result5) == 0) {
        header('Location: index.php?error=ville_introuvable');
        exit();
    }
} else {
    header('Location: index.php?error=ville_introuvable');
    exit();
}

$sql2 = "SELECT image FROM destinations WHERE id = ? ";
$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $destination);
$stmt2->execute();
$result2 = $stmt2->get_result();

if (mysqli_num_rows($result2) == 1) {
    while ($row2 = mysqli_fetch_assoc($result2)) {
        $remoteImage = trim($row2['image'] ?? "");
        if ($remoteImage !== "") {
            $url = "url(\"" . $remoteImage . "\")";
        }
    }
}

$villeParam = $_GET["ville"] ?? "";
$localImagePath = "image/" . normalizeImageName($villeParam) . ".jpg";
$backgroundLayers = array(cssUrl($localImagePath));

if (!empty($url)) {
    if ($remoteImage !== "") {
        array_unshift($backgroundLayers, cssUrl($remoteImage));
    }
}

$backgroundImageCss = implode(", ", $backgroundLayers);

$verification_sql = "SELECT * FROM avis WHERE idUtilisateur = ? AND idDestination = ?";
$stmt = $conn->prepare($verification_sql);
$stmt->bind_param("ii", $user, $destination);
$stmt->execute();
$resultat_verification = $stmt->get_result();

$commentaire_existant = "";
$etoiles_existantes = "";

if ($resultat_verification->num_rows == 1) {
    $row = $resultat_verification->fetch_assoc();
    $commentaire_existant = $row['commentaire'];
    $etoiles_existantes = $row['note'];
}

if (isset($_POST["ok"])) {
    if (isset($_SESSION['user']) && isset($_POST["note"])) {
        $comm = $_POST["commentaire"];
        $vote = (int) $_POST["note"];

        if (mysqli_num_rows($resultat_verification) > 0) {
            $update_sql = "UPDATE avis SET note = ?, commentaire = ?, dateAvis = current_timestamp() WHERE idUtilisateur = ? AND idDestination = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("isii", $vote, $comm, $user, $destination);
            if ($update_stmt->execute()) {
                $message = "Avis modifié avec succès.";
                $messageClass = "success";
                header("Location: destination.php?ville=" . urlencode($_GET["ville"]));
                exit();
            } else {
                echo "<h1>Erreur lors de la mise à jour de l'avis : " . htmlspecialchars($update_stmt->error) . "</h1>";
            }
            $update_stmt->close();
        } else {
            // Aucun avis trouvé, on en insère un nouveau
            $insert_stmt = $conn->prepare("INSERT INTO avis (idUtilisateur, idDestination, note, commentaire, dateAvis) VALUES (?, ?, ?, ?, current_timestamp())");
            if ($insert_stmt === false) {
                die('Erreur de préparation de la requête :' . htmlspecialchars($conn->error));
            }
            $insert_stmt->bind_param("iiis", $user, $destination, $vote, $comm);
            if ($insert_stmt->execute()) {
                header("Location: destination.php?ville=" . urlencode($_GET["ville"]));
                $message = "Avis ajouté avec succès.";
                $messageClass = "success";
                exit();
            } else {
                echo "<h1>Erreur lors de l'ajout de l'avis : " . htmlspecialchars($insert_stmt->error) . "</h1>";
            }
            $insert_stmt->close();
        }
    } else {
        echo "<h1>Veuillez mettre des étoiles pour laisser un avis.</h1>";
    }

    $stmt->close();
} else if (isset($_POST['supprimer_avis'])) {
    if (mysqli_num_rows($resultat_verification) > 0) {
        $delete_sql = "DELETE FROM avis WHERE idUtilisateur = ? AND idDestination = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ii", $user, $destination);
        if ($delete_stmt->execute()) {
            $message = "Avis supprimé avec succès.";
            $messageClass = "success";
            header("Location: destination.php?ville=" . urlencode($_GET["ville"]));
            exit();
        } else {
            echo "<h1>Erreur lors de la suppression de l'avis : " . htmlspecialchars($delete_stmt->error) . "</h1>";
        }
        $delete_stmt->close();
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Destinations - Nomadix</title>
    <style>
        .destination-panels {
            max-width: 860px;
            margin: 40px auto;
            padding: 20px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            border-radius: 14px;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #dda0dd, #add8e6);
            margin: 0;
            padding: 0;
            margin-bottom: 10px;
        }

        .destination-container {
            position: relative;
            max-width: 800px;
            margin: 0 auto 30px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
            height: 300px;
            color: rgb(97, 0, 132);
            overflow-y: auto;
        }


        .destination-container::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: -1;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.5);
        }

        .destination-image {
            width: 100%;
            border-radius: 10px;
        }

        .destination-title {
            font-size: 2.2em;
            margin-top: 20px;
            color: rgb(97, 0, 132);
        }

        .destination-description {
            font-size: 1.1em;
            color: rgb(97, 0, 132);
            line-height: 1.6;
            text-align: justify;
            margin-bottom: auto;
        }

        .avis-wrapper {
            position: relative;
            width: 840px;
            margin: 40px auto;
        }

        .avis-container {
            display: flex;
            overflow-x: auto;
            gap: 30px;
            padding: 30px;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            height: 300px;
        }

        .avis-card {
            min-width: 250px;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s;
            background-color: #b1e5d9;
            color: rgb(97, 0, 132);
            width: 300px;
            overflow-y: auto;
        }

        .avis-card:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }

        .avis-card h3 {
            font-size: 1.4rem;
            margin-bottom: 5px;
            color: #4b0082;
        }

        .avis-card p {
            margin: 10px 0;
            color: #7000a8;
            font-size: 0.95rem;
        }

        .nav-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 30px;
            text-align: center;
            text-decoration: none;
            z-index: 10;
            background: none;
        }

        .nav-button.left {
            left: 0;
        }

        .nav-button.right {
            right: 0;
        }

        .nav-button:hover {
            background-color: rgba(215, 215, 215, 0.60);
        }

        .donner-avis {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
            height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .donner-avis::before {
            content: "";
            position: absolute;
            inset: 0;
            background-color: rgba(255, 255, 255, 0.5);
            z-index: -1;
            border-radius: 10px;
        }

        .donner-avis h1 {
            font-size: 30px;
            color: rgb(97, 0, 132);
            text-align: center;
            margin: 0px;
        }

        .donner-avis h2 {
            font-size: 23px;
            color: rgb(97, 0, 132);
            text-align: center;
            margin-top: 40px;
        }

        .boutons {
            display: flex;
            justify-content: center;
            gap: 10px;
            align-items: center;
        }

        .boutons button:hover {
            background-color: rgb(97, 0, 132);
            transform: scale(1.05);
        }

        .boutons button {
            background-color: rgb(152, 0, 207);
            color: white;
            border: none;
            font-size: 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            margin: 20px;
            margin-top: 40px;
            width: 180px;
            height: 55px;
        }

        .avis-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            align-items: center;
        }

        .avis-form label {
            font-weight: bold;
            color: rgb(97, 0, 132);
            align-self: flex-start;
        }

        .avis-form textarea {
            width: 150%;
            padding: 10px;
            font-size: 18px;
            border: 1px solid #bbb;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            height: 100px;
            resize: none;
            overflow-y: auto;
        }

        .evaluation {
            direction: rtl;
            unicode-bidi: bidi-override;
            display: inline-flex;
            font-size: 25px;
        }

        .evaluation input[type="radio"] {
            display: none;
        }

        .evaluation label {
            color: rgb(97, 0, 132);
            cursor: pointer;
            transition: color 0.2s;
            font-size: 2em;
            transition: transform 0.2s;
        }

        .evaluation label:hover {
            transform: scale(1.2);
        }

        .evaluation input[type="radio"]:checked~label,
        .evaluation label:hover,
        .evaluation label:hover~label {
            color: #FFD43B;
        }

        .envoyer,
        .supp_avis {
            background-color: rgb(152, 0, 207);
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            width: 147px;
            height: 48px;
            margin-left: 15px;
        }

        .envoyer:hover,
        .supp_avis:hover {
            background-color: rgb(97, 0, 132);
            transform: scale(1.05);
        }

        .nav-main h1 {
            text-align: center;
            margin: 0;
            margin-top: 40px;
            color: rgb(97, 0, 132);
            font-size: 40px;
        }

        .nav-main h1 a {
            color: rgb(97, 0, 132);
        }

        .nav-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .button1,
        .button2 {
            background-color: rgb(94, 203, 250);
            color: rgb(0, 29, 132);
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-left: 10px;
            border-style: none;
        }

        .button1:hover,
        .button2:hover {
            background-color:rgb(0, 191, 255);
            transform: scale(1.05);
        }

        .message {
            max-width: 400px;
            margin: 20px auto;
            padding: 15px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
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

        .home-button {
            color: rgb(0, 29, 132);
            float: right;
            margin: 10px;
            padding: 10px 20px;
            background-color: rgb(94, 203, 250);
            border: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
            z-index: 100;
            position: fixed;
            bottom: 5px;
            right: 5px;
        }

        .home-button:hover {
            background-color:rgb(0, 191, 255);
            transform: scale(1.05);
        }
    </style>
    <script src="https://kit.fontawesome.com/82664567ab.js" crossorigin="anonymous"></script>
</head>

<script>

    function scrollAvis(direction) {
        const container = document.getElementById("avis-container");
        const cardWidth = 320;
        container.scrollBy({
            left: direction * cardWidth,
            behavior: "smooth"
        });
    }

</script>

<body>
    <?php if (!empty($message)): ?>
        <div class="message <?= $messageClass ?>"><?= $message ?></div>
    <?php endif; ?>

    <header>
        <?php if (isset($_SESSION["user"])): ?>
            <div class="nav-buttons">
                <form method="post">
                    <button type="submit" name="deconnectetoi" class="button1" onclick="return confirm('Es-tu sûr de vouloir te déconnecter ?')">Se déconnecter</button>
                </form>
                
            </div>
        <?php else: ?>
            <div class="nav-buttons">
                <a href="connexion.php" class="button2">Se connecter</a>
                <a href="inscription.php" class="button1">S'inscrire</a>
            </div>
        <?php endif; ?>
    </header>

    <div class="nav-main">
        <h1><a href="index.php" style="text-decoration: none;">Nomadix</a></h1>
    </div>

    <div class="destination-panels" style="background-image: <?php echo htmlspecialchars($backgroundImageCss, ENT_QUOTES, 'UTF-8'); ?>;">

    <?php
    $sql_destination_details = "SELECT * FROM destinations WHERE id = ? ";
    $stmt = $conn->prepare($sql_destination_details);
    $stmt->bind_param("i", $destination);
    $stmt->execute();
    $result_destination_details = $stmt->get_result();

    if ($result_destination_details && mysqli_num_rows($result_destination_details) > 0) {
        while ($row_destination_details = mysqli_fetch_assoc($result_destination_details)) {
            ?>
            <div class="destination-container">
                <h1 class="destination-title">
                    <?php echo $row_destination_details['nom'] . ", " . $row_destination_details['pays']; ?>
                </h1>
                <h2 class="destination-note">
                    <?php
                    $sql_note_moy = 'SELECT ROUND(AVG(note), 1) AS moyenne_notes FROM avis WHERE idDestination = "' . $destination . '"';
                    $result_note_moy = mysqli_query($conn, $sql_note_moy);
                    if ($result_note_moy && mysqli_num_rows($result_note_moy) > 0) {

                        $row_note_moy = mysqli_fetch_assoc($result_note_moy);
                        $note_moy = $row_note_moy['moyenne_notes'];

                        if ($note_moy === null) {
                            echo "Aucun avis.";
                        } else {
                            $moyenne_arrondi = round($note_moy * 2) / 2;

                            for ($i = 1; $i <= 5; $i++) {
                                if ($moyenne_arrondi >= $i) {
                                    echo "<i class='fa-solid fa-star' style='color: #FFD43B;'></i>";
                                } elseif ($moyenne_arrondi >= $i - 0.5) {
                                    echo "<i class='fa-regular fa-star-half-stroke' style='color: #FFD43B;'></i>";
                                } else {
                                    echo "<i class='fa-solid fa-star' style='color: #FFFFFF;'></i>";
                                }
                            }
                            echo " (" . htmlspecialchars($note_moy) . "/5)";
                        }
                    } else {
                        echo "Aucun avis.";
                    }
                    ?>

                </h2>
                <p class="destination-description">
                    <?php echo htmlspecialchars($row_destination_details['description']); ?>
                </p>
            </div>
            <?php
        }
    } else {
        echo "<h1>Erreur d'affichage de la destination</h1>";
    }
    ?>

    <div class="donner-avis">
        <h1>Donnez votre avis !</h1>

        <?php if (!isset($_SESSION["user"])): ?>

            <h2>Veuillez vous connecter pour mettre un avis</h2>
            <div class="boutons">
                <button name="connecter" onclick="window.location.href='connexion.php'">Se connecter</button>
                <button name="incription" onclick="window.location.href='inscription.php'">S'inscrire</button>
            </div>

        <?php else: ?>
            <form action="" method="post" class="avis-form">

                <div class="evaluation">
                    <input type="radio" name="note" id="star5" value="5" <?php if ($etoiles_existantes == 5) echo "checked"; ?>><label for="star5"
                        style="margin-left: 20px;">&#9733;</label>
                    <input type="radio" name="note" id="star4" value="4" <?php if ($etoiles_existantes == 4) echo "checked"; ?>><label for="star4"
                        style="margin-left: 20px;">&#9733;</label>
                    <input type="radio" name="note" id="star3" value="3" <?php if ($etoiles_existantes == 3) echo "checked"; ?>><label for="star3"
                        style="margin-left: 20px;">&#9733;</label>
                    <input type="radio" name="note" id="star2" value="2" <?php if ($etoiles_existantes == 2) echo "checked"; ?>><label for="star2"
                        style="margin-left: 20px;">&#9733;</label>
                    <input type="radio" name="note" id="star1" value="1" <?php if ($etoiles_existantes == 1) echo "checked"; ?>><label for="star1"
                        style="margin-left: 0px;">&#9733;</label>
                </div>

                <textarea name="commentaire" placeholder="Partagez votre expérience..." maxlength="1000"><?php echo htmlspecialchars($commentaire_existant); ?></textarea>

                <?php if ($etoiles_existantes == null) : ?>
                    <button name="ok" type="submit" class="envoyer">Envoyer</button>
                <?php else: ?>
                    <div class="boutons_modifetsupp">
                        <button name="ok" type="submit" class="envoyer" onclick="return confirm('Es-tu sûr de vouloir modifier ton commentaire ?')">Modifier</button>
                        <button name="supprimer_avis" type="submit" class="supp_avis" onclick="return confirm('Es-tu sûr de vouloir supprimer ton commentaire ?')">Supprimer</button>
                    </div>
                <?php endif; ?>

            </form>

        <?php endif; ?>

    </div>
    </div>

    <div class="avis-wrapper">

        <button class="nav-button left" onclick="scrollAvis(-1)">&#139;</button>

        <div class="avis-container" id="avis-container">

            <?php
            $sql_avis = 'SELECT avis.*, utilisateurs.login FROM avis INNER JOIN utilisateurs ON avis.idUtilisateur = utilisateurs.id WHERE idDestination = "' . $destination . '"';
            $result_avis = mysqli_query($conn, $sql_avis);

            if ($result_avis && mysqli_num_rows($result_avis) > 0) {
                while ($row_avis = mysqli_fetch_assoc($result_avis)) {
                    echo "<div class=\"avis-card\" id=\"avis" . htmlspecialchars($row_avis['id']) . "\">";
                    echo "<h3>" . htmlspecialchars($row_avis['login']) . "</h3><p>" . htmlspecialchars($row_avis['dateAvis']) . "</p>";
                    $note_avis = (int) $row_avis['note'];
                    for ($i = 1; $i <= 5; $i++) {
                        if ($note_avis >= $i) {
                            echo "<i class=\"fa-solid fa-star\" style=\"color: #FFD43B;\"></i>";
                        } else {
                            echo "<i class=\"fa-solid fa-star\" style=\"color: #FFF;\"></i>";
                        }
                    }
                    echo "<p style=\"font-size: 20px;\">" . nl2br(htmlspecialchars($row_avis['commentaire'])) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<div class=\"avis-card\" id=\"avis1\"> 
                <h1>Aucun avis pour le moment. Soyez le premier a en faire ! 😉</h1>
                </div>";
            }
            ?>

        </div>

        <button class="nav-button right" onclick="scrollAvis(1)">&#155;</button>

    </div>

    <?php

    mysqli_close($conn);

    ?>

    <form action="index.php" method="get">
        <button type="submit" class="home-button"> Retour à l'accueil</button>
    </form>

    <footer>
        <p style="text-align: center; font-size: 14px; color: rgba(255, 255, 255, 0.7);">&copy; 2025 Nomadix - Tous droits réservés</p>
    </footer>
</body>

</html>
