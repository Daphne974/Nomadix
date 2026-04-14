<?php
session_start();

$host = 'localhost';
$dbname = 'voyage';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die('<p style="color:red;">Erreur de connexion : ' . mysqli_connect_error() . '</p>');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $inputPassword = $_POST['password'];

    $query = "SELECT * FROM utilisateurs WHERE login = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($inputPassword, $user['motDePasse'])) {
        $_SESSION['user'] = $user['id'];

        if (isset($_SESSION['page_davant'])){
            header('Location: ' . $_SESSION['page_davant']);
            exit();
        }else{
            header('Location: index.php');
            exit();
        }
    } else {
        echo '<p style="color:red;">Nom d\'utilisateur ou mot de passe incorrect.</p>';
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Connexion - Nomadix</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #dda0dd, #add8e6);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            position: relative;
        }

        .login-container {
            background-color: #fdfdfd;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        h2 {
            margin-top: 0;
            text-align: center;
            color: rgb(97, 0, 132);
            font-size: 1.5rem;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color:rgb(255, 255, 255);
            box-sizing: border-box;
        }

        .connect {
            background-color: rgb(10, 49, 248);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
        }

        .connect:hover {
            background-color: rgb(1, 19, 113);
        }

        .insc-button {
            background: none;
            border: none;
            color: blue;
            text-decoration: underline;
            cursor: pointer;
            font-size: 16px;
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 10px;
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

        footer {
            position: fixed;
            bottom: 2px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            z-index: 5;
        }

        @media (max-width: 600px) {
            .login-container {
                padding: 15px;
                margin: 10px;
            }

            h2 {
                font-size: 1.3rem;
            }

            .connect,
            .insc-button {
                font-size: 15px;
            }

            .home-button {
                font-size: 14px;
                padding: 8px 16px;
                bottom: 15px;
                right: 15px;
            }

            footer {
                font-size: 12px;
            }
        }

        @media (max-height: 500px) {
            body {
                justify-content: flex-start;
            }

            .home-button,
            footer {
                position: static;
                margin-top: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Formulaire de connexion</h2>
        <form action="" method="post">
            <label for="login">Nom d'utilisateur :</label>
            <input type="text" id="login" name="login" placeholder="Nom d'utilisateur" required></br></br>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" placeholder="Mot de passe" required>

            <button type="submit" class="connect">Se connecter</button>


        </form>
        <form action="inscription.php" method="get" style="">
            <button type="submit" class="insc-button"> Aller s'inscrire</button>
        </form>
    </div>

    <form action="index.php" method="get">
        <button type="submit" class="home-button">Retour à l'accueil</button>
    </form>

    <footer>&copy; 2025 Nomadix - Tous droits réservés</footer>
</body>

</html>