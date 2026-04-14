<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voyage";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $motDePasse = $_POST['motDePasse'];
    $confirmerMotDePasse = $_POST['confirmerMotDePasse'];

    if ($motDePasse !== $confirmerMotDePasse) {
        $message = "Les mots de passe ne correspondent pas.";
        $messageClass = "error";
    } elseif (
        strlen($motDePasse) < 12 ||
        !preg_match('/[A-Z]/', $motDePasse) ||
        !preg_match('/[a-z]/', $motDePasse) ||
        !preg_match('/[0-9]/', $motDePasse) ||
        !preg_match('/[\W_]/', $motDePasse)
    ) {
        $message = "Le mot de passe doit contenir au moins 12 caractères, une majuscule, une minuscule, un chiffre et un symbole.";
        $messageClass = "error";
    } else {
        $checkEmail = "SELECT id FROM utilisateurs WHERE email = ?";
        $stmt = mysqli_prepare($conn, $checkEmail);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $message = "Cet e-mail est déjà utilisé pour un autre compte.";
            $messageClass = "error";
        } else {
            $motDePasseHache = password_hash($motDePasse, PASSWORD_BCRYPT);

            $insertSql = "INSERT INTO utilisateurs (login, email, motDePasse) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insertSql);
            mysqli_stmt_bind_param($stmt, "sss", $login, $email, $motDePasseHache);

            if (mysqli_stmt_execute($stmt)) {
                session_start();

                $query = "SELECT * FROM utilisateurs WHERE login = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 's', $login);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);

                if ($user) {
                    $_SESSION['user'] = $user["id"];
                    header("Location: index.php");
                    exit;
                }

                $message = "Inscription réussie !";
                $messageClass = "success";
            } else {
                $message = "Erreur lors de l'inscription : " . mysqli_error($conn);
                $messageClass = "error";
            }
        }
    }
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
<html>

<head>
    <meta charset="UTF-8">
    <title>Inscription - Nomadix</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #dda0dd, #add8e6);
            padding: 0;
            margin: 0;
            min-height: 100vh;
            position: relative;
            padding-bottom: 80px;
            box-sizing: border-box;
        }

        h2 {
            color: rgb(97, 0, 132);
            text-align: center;
            font-size: 1.5rem;
        }

        .forms {
            width: 100%;
            max-width: 400px;
            margin: 0px auto;
            margin-top: 5%;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: rgb(10, 49, 248);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: rgb(1, 19, 113);
        }

        .connect-button {
            background: none;
            border: none;
            color: blue;
            text-decoration: underline;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            display: block;
            width: 100%;
            text-align: center;
            margin-top: -10px;
        }

        .home-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: rgb(10, 49, 248);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            cursor: pointer;
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

        footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 5px;
            text-align: center;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 600px) {
            .forms {
                padding: 20px;
                margin: 20px;
            }

            h2 {
                font-size: 1.3rem;
            }

            input[type="submit"],
            .connect-button {
                font-size: 15px;
            }

            .home-button {
                bottom: 10px;
                right: 10px;
                padding: 8px 16px;
                font-size: 14px;
            }

            footer {
                font-size: 12px;
            }
        }

        @media (max-height: 500px) {

            .home-button,
            footer {
                position: absolute;
                margin-top: 20px;
            }
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
    <?php if (!empty($message)): ?>
        <div id="flashMessage" class="message <?= $messageClass ?>">
            <span class="close-btn" onclick="closeFlashMessage()">&times;</span>
            <?= $message ?>
            <div class="progress-bar"></div>
        </div>
    <?php endif; ?>

    <div class="forms">
        <form method="POST">
            <h2>Inscription</h2>

            <label>Login :</label>
            <input type="text" name="login" required><br><br>

            <label>Email :</label>
            <input type="email" name="email" required><br><br>

            <label>Mot de passe :</label>
            <input type="password" name="motDePasse" required><br><br>

            <label>Confirmer le mot de passe :</label>
            <input type="password" name="confirmerMotDePasse" required>

            <input type="submit" value="S'inscrire"><br><br>
        </form>

        <form action="connexion.php" method="get" style="">
            <button type="submit" class="connect-button"> Aller se connecter</button>
        </form>

        <form action="index.php" method="get">
            <button type="submit" class="home-button"> Retour à l'accueil</button>
        </form>
    </div>

    <footer>&copy; 2025 Nomadix - Tous droits réservés</footer>
</body>

</html>