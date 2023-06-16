<?php
include_once 'connexion_BDD.php';

function check_password_strength($password) {
    $lowercase = preg_match('@[a-z]@', $password);
    $uppercase = preg_match('@[A-Z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $special_chars = preg_match('@[^\w]@', $password);
    return ($lowercase && $uppercase && $number && $special_chars);
}

$pdo = connexion_bdd();

if (isset($_POST['submit'])) {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($prenom) && !empty($nom) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            if (check_password_strength($password)) {
                // Vérification de l'adresse e-mail dans la base de données
                $sql_check_email = "SELECT * FROM client WHERE mail = :mail";
                $stmt_check_email = $pdo->prepare($sql_check_email);
                $stmt_check_email->execute(['mail' => $email]);
                $existing_user = $stmt_check_email->fetch();

                if (!$existing_user) {
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    echo "Password input: " . $_POST['password'] . ". Password hash: " . $password . "<br>";
                    $sql = "INSERT INTO client (prenom_client, nom_client, mail,password) VALUES (:prenom_client, :nom_client,:mail, :password)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'prenom_client' => $prenom,
                        'nom_client' => $nom,
                        'mail' => $email,
                        'password' => $password,
                    ]);
                    $message = "Inscription réussie !";
                } else {
                    $error = "Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.";
                }
            } else {
                $error = "Le mot de passe doit contenir au moins une majuscule et un caractère spécial.";
            }
        } else {
            $error = "Les mots de passe ne correspondent pas.";
        }
    } else {
        $error = "Veuillez remplir les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <style>
        .progress {
            width: 100%;
            height: 5px;
            background-color: #f3f3f3;
            position: relative;
        }
        .progress-bar {
            height: 100%;
            position: absolute;
            background-color: #ff3f34; /* Rouge */
            width: 0;
        }
    </style>
    <script>
        function updateProgressBar() {
            var password = document.getElementById("password").value;
            var progressBar = document.getElementById("progress-bar");
            var width = 0;

            if (password.length >= 8) {
                width += 16;
                document.getElementById("eight-characters").style.backgroundColor = "green";
            } else {
                document.getElementById("eight-characters").style.backgroundColor = "";
            }
            if (/[A-Z]/.test(password)) {
                width += 16;
                document.getElementById("majuscule").style.backgroundColor = "green";
            } else {
                document.getElementById("majuscule").style.backgroundColor = "";
            }
            if (/[^\w]/.test(password)) {
                width += 16;
                document.getElementById("special").style.backgroundColor = "green";
            } else {
                document.getElementById("special").style.backgroundColor = "";
            }
            if (/\d/.test(password)) {
                width += 16;
                document.getElementById("chiffre").style.backgroundColor = "green";
            } else {
                document.getElementById("chiffre").style.backgroundColor = "";
            }
            if (/[a-z]/.test(password)) {
                width += 16;
                document.getElementById("minuscule").style.backgroundColor = "green";
            } else {
                document.getElementById("minuscule").style.backgroundColor = "";
            }

            progressBar.style.width = width + '%';
            progressBar.style.backgroundColor = getProgressBarColor(width);
        }

        function getProgressBarColor(progress) {
            if (progress < 28) {
                return '#ff3f34'; // Rouge
            } else if (progress < 34) {
                return '#ffa500'; // Orange
            } else if (progress < 67) {
                return '#ffd700'; // Jaune
            } else {
                return '#4CAF50'; // Vert
            }
        }
    </script>
</head>
<body>
<h1>Inscription</h1>
<?php if (isset($error)) { echo "<p>$error</p>"; } ?>
<?php if (isset($message)) { echo "<p>$message</p>"; } ?>
<form method="POST" action="">
    <input type="text" name="prenom" placeholder="Prénom" required><br>
    <input type="text" name="nom" placeholder="Nom" required><br>
    <input type="email" name="email" placeholder="Adresse e-mail" required><br>
    <input type="password" id="password" name="password" placeholder="Mot de passe" required onkeyup="updateProgressBar()"><br>
    <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required><br>
    <div class="progress">
        <div id="progress-bar" class="progress-bar"></div>
    </div>
    <input type="submit" name="submit" value="S'inscrire">
    <br>
    <button id="eight-characters">8 caractères minimum</button>
    <button id="majuscule">Majuscule</button>
    <button id="special">Caractère spécial</button>
    <button id="chiffre">Chiffre</button>
    <button id="minuscule">Minuscule</button>
</form>
<a href="connexion.php">Retour à la connexion</a>
</body>
</html>
