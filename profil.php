<?php
session_start();

$_SESSION['page_davant'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voyage";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

if (!isset($_SESSION['user'])) {
    header("Location: connexion.php");
    exit;
}

if (isset($_POST["deconnectetoi"])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

$message = "";
$messageClass = "";
$emailMessage = "";
$emailMessageClass = "";

$userId = $_SESSION['user'];
$sql = "SELECT login, email, motDePasse FROM utilisateurs WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
$login = $user['login'];
$currentEmail = $user['email'];

$message = "";
$messageClass = "";
$emailMessage = "";
$emailMessageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['miseAJourEmail'])) {
    $ancienEmail = trim($_POST['ancienEmail']);
    $nouveauEmail = trim($_POST['nouveauEmail']);
    $currentPassword = $_POST['currentPassword'];

    if ($ancienEmail !== $currentEmail) {
        $emailMessage = "L'ancien email ne correspond pas à celui enregistré.";
        $emailMessageClass = "error";
    } elseif (!filter_var($nouveauEmail, FILTER_VALIDATE_EMAIL)) {
        $emailMessage = "Adresse e-mail invalide.";
        $emailMessageClass = "error";
    } elseif (!password_verify($currentPassword, $user['motDePasse'])) {
        $emailMessage = "Mot de passe incorrect.";
        $emailMessageClass = "error";
    } else {
        $updateEmailQuery = "UPDATE utilisateurs SET email = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateEmailQuery);
        mysqli_stmt_bind_param($stmt, 'si', $nouveauEmail, $userId);
        if (mysqli_stmt_execute($stmt)) {
            $emailMessage = "Adresse e-mail mise à jour avec succès !";
            $emailMessageClass = "success";
            $currentEmail = $nouveauEmail;
        } else {
            $emailMessage = "Erreur lors de la mise à jour de l'e-mail.";
            $emailMessageClass = "error";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['miseAJourMotdepasse'])) {
    $ancien = $_POST['ancienMotDePasse'];
    $nouveau = $_POST['nouveauMotDePasse'];
    $confirmer = $_POST['confirmerMotDePasse'];

    if (!password_verify($ancien, $user['motDePasse'])) {
        $message = "Ancien mot de passe incorrect.";
        $messageClass = "error";
    } elseif (
        strlen($nouveau) < 12 ||
        !preg_match('/[A-Z]/', $nouveau) ||
        !preg_match('/[a-z]/', $nouveau) ||
        !preg_match('/[0-9]/', $nouveau) ||
        !preg_match('/[\W_]/', $nouveau)
    ) {
        $message = "Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un symbole.";
        $messageClass = "error";
    } elseif ($nouveau !== $confirmer) {
        $message = "Les mots de passe ne correspondent pas.";
        $messageClass = "error";
    } else {
        $nouveauHash = password_hash($nouveau, PASSWORD_BCRYPT);
        $update = "UPDATE utilisateurs SET motDePasse = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, "si", $nouveauHash, $userId);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Mot de passe mis à jour avec succès.";
            $messageClass = "success";
        } else {
            $message = "Erreur lors de la mise à jour du mot de passe.";
            $messageClass = "error";
        }
    }
}

if (isset($_POST['validsuppr'])) {
    $motdepasse = $_POST['mdp_supprimer'];

    $verification_sql = "SELECT motDePasse FROM utilisateurs WHERE id = ?";
    $stmt = $conn->prepare($verification_sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $resultat_verification = $stmt->get_result();

    if ($resultat_verification->num_rows === 1) {

        $row = $resultat_verification->fetch_assoc();

        if (password_verify($motdepasse, $row['motDePasse'])) {

            $stmt1 = $conn->prepare("DELETE FROM avis WHERE idUtilisateur = ?");
            $stmt1->bind_param("i", $userId);
            $stmt1->execute();

            $stmt2 = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt2->bind_param("i", $userId);

            if ($stmt2->execute()) {
                session_unset();
                session_destroy();
                header("Location: index.php?supprime=ok");
                exit;
            } else {
                header("Location: index.php?supprime=non");
                exit;
            }
        } else {
            echo "<div class='message error'>Mot de passe incorrect.</div>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Profil - Nomadix</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #dda0dd, #add8e6);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
        }

        h2 {
            color: rgb(97, 0, 132);
            text-align: center;
            font-size: clamp(1.5rem, 4vw, 2rem);
            margin-bottom: 20px;
        }

        .form1,
        .form2,
        .supprimer-compte {
            width: 100%;
            margin: auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #000000;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: rgb(10, 49, 248);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: rgb(1, 19, 113);
        }

        .miseAJour {
            position: flex;
            background-color: rgb(10, 49, 248);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            flex-shrink: 0;
        }

        .miseAJour:hover {
            background-color: rgb(1, 19, 113);
        }

        .deconnect-button,
        .home-button {
            padding: 10px 20px;
            background-color: rgb(10, 49, 248);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .buttons {
            position: absolute;
            display: flex;
            top: 20px;
            right: 20px;
            align-items: center;
            gap: 7px;
        }

        .message {
            max-width: 450px;
            margin: 20px auto;
            padding: 15px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1rem;
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

        footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 2px;
            text-align: center;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        .update-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .update-section input[type="text"],
        .update-section input[type="email"],
        .update-section input[type="password"] {
            flex: 1 1 auto;
            min-width: 0;
        }

        @media (max-width: 600px) {
            .update-section {
                flex-direction: column;
                align-items: stretch;
            }

            .miseAJour {
                width: 100%;
            }

            .home-button,
            .deconnect-button {
                font-size: 0.9rem;
                padding: 8px 16px;
            }

            footer {
                font-size: 0.75rem;
            }
        }

        @media (max-height: 500px) {
            body {
                padding-bottom: 100px;
            }

            .home-button {
                margin-top: 20px;
            }

            footer {
                margin-top: 10px;
            }
        }

        .supprimer-compte {

            text-align: center;
        }

        .supprimer-compte a {
            text-decoration: none;
            background-color: rgb(255, 0, 0);
            color: rgb(255, 255, 255);
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 90%;
            display: inline-block;
            $ transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .supprimer-compte button {
            padding: 10px 20px;
            background-color: rgb(255, 0, 0);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php if (!empty($message)): ?>
        <div class="message <?= $messageClass ?>"><?= $message ?></div>
    <?php endif; ?>

    <?php if (!empty($emailMessage)): ?>
        <div class="message <?= $emailMessageClass ?>"><?= $emailMessage ?></div>
    <?php endif; ?>

    <div class="forms">
        <form method="POST">
            <h2>Modifier les informations</h2>

            <div style="text-align: center; margin-top: 10px; font-size: 18px;">
                Bonjour, <strong><?= htmlspecialchars($login) ?></strong> 👋
            </div></br></br>

            <div class="form1">
                <label for="ancienEmail">Ancien e-mail :</label>
                <input type="email" name="ancienEmail" placeholder="Ancien e-mail" required>

                <label for="nouveauEmail">Nouvel e-mail :</label>
                <input type="email" name="nouveauEmail" placeholder="Nouveau e-mail" required>

                <label for="currentPassword">Mot de passe actuel :</label>
                <input type="password" name="currentPassword" placeholder="Mot de passe" required>

                <button type="submit" name="miseAJourEmail" class="miseAJour"
                    onclick="return confirm('Es-tu sûr de vouloir mettre à jour ton e-mail ?')">Mettre à jour</button>
            </div>
        </form>

        </br></br>
        <form method="POST">
            <div class="form2">
                <label>Ancien mot de passe :</label>
                <input type="password" name="ancienMotDePasse" placeholder="Ancien mot de passe" required>

                <label>Nouveau mot de passe :</label>
                <input type="password" name="nouveauMotDePasse" placeholder="Nouveau mot de passe" required>

                <label>Confirmer le nouveau mot de passe :</label>
                <input type="password" name="confirmerMotDePasse" placeholder="Confirmer le nouveau mot de passe"
                    required>

                <button type="submit" name="miseAJourMotdepasse" class="miseAJour"
                    onclick="return confirm('Es-tu sûr de vouloir mettre à jour ton mot de passe ?')">Mettre à
                    jour</button>
            </div>
        </form>

        </br></br>

        <?php if (isset($_GET['suppr'])): ?>

            <form method="POST" action="">
                <div class="supprimer-compte">
                    <label for="mdp_supprimer">Confirmer le mot de passe :</label>
                    <input type="password" name="mdp_supprimer" placeholder="Mot de passe" required>
                    <button type="submit" name="validsuppr"
                        onclick="return confirm('Toutes vos informations, y compris vos avis, seront supprimées. Voulez-vous continuer ?')">
                        Supprimer le compte
                    </button>
                </div>
            </form>

        <?php else: ?>
            <form method="POST" action="">
                <div class="supprimer-compte">
                    <a href="profil.php?suppr=ok"
                        onclick="return confirm('Es-tu sûr de vouloir supprimer ton compte ?')">Supprimer mon compte</a>
                </div>
            </form>
        <?php endif; ?>

    </div>

    <div class="buttons">
        <form action="index.php" method="post">
            <button type="submit" class="home-button"> Retour à l'accueil</button>
        </form>

        <form action="" method="post">
            <button type="submit" name="deconnectetoi" class="deconnect-button"
                onclick="return confirm('Es-tu sûr de vouloir te déconnecter ?')"> Se déconnecter </button>
        </form>
    </div>

    <footer>&copy; 2025 Nomadix - Tous droits réservés</footer>
</body>

</html>