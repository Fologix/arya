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
    $date_naissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'];
    $code_postal = $_POST['code_postal'];
    $ville = $_POST['ville'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $civilite = $_POST['civilite'];
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
                    $sql = "INSERT INTO client (prenom_client, nom_client, date_naiss_client, adresse_client, code_postal_client, localite_client, telephone_client, mail, civilite, password) VALUES (:prenom_client, :nom_client, :date_naiss_client, :adresse_client, :code_postal_client, :localite_client, :telephone_client, :mail, :civilite, :password)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        'prenom_client' => $prenom,
                        'nom_client' => $nom,
                        'date_naiss_client' => $date_naissance,
                        'adresse_client' => $adresse,
                        'code_postal_client' => $code_postal,
                        'localite_client' => $ville,
                        'telephone_client' => $telephone,
                        'mail' => $email,
                        'civilite' => $civilite,
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
            background-color: #4CAF50;
            width: 0;
        }
    </style>
    <script>
        function updateProgressBar() {
            var password = document.getElementById("password").value;
            var progressBar = document.getElementById("progress-bar");
            var width = 0;

            if (password.length >= 8) {
                width += 33;
            }
            if (/[A-Z]/.test(password)) {
                width += 33;
            }
            if (/[^\w]/.test(password)) {
                width += 34;
            }

            progressBar.style.width = width + '%';
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
    <input type="date" name="date_naissance" placeholder="Date de naissance"><br>
    <input type="text" name="adresse" placeholder="Adresse"><br>
    <input type="text" name="code_postal" placeholder="Code postal"><br>
    <input type="text" name="ville" placeholder="Ville"><br>
    <input type="tel" name="telephone" placeholder="Téléphone"><br>
    <input type="email" name="email" placeholder="Adresse e-mail" required><br>
    <select name="civilite">
        <option value="">Sélectionnez votre civilité</option>
        <option value="monsieur">Monsieur</option>
        <option value="madame">Madame</option>
        <option value="mademoiselle">Mademoiselle</option>
    </select><br>
    <input type="password" id="password" name="password" placeholder="Mot de passe" required onkeyup="updateProgressBar()"><br>
    <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required><br>
    <div class="progress">
        <div id="progress-bar" class="progress-bar"></div>
    </div>
    <input type="submit" name="submit" value="S'inscrire">
</form>
<a href="connexion.php">Retour à la connexion</a>
</body>
</html>

