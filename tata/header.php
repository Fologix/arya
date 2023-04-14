<?php
session_start();
include_once 'connexion_BDD.php';
$pdo = connexion_bdd();

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT prenom_client FROM client WHERE id_client = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    $prenom = $user['prenom_client'];
} else {
    $prenom = "Identifiez-vous";
}
?>

<div class="header">
    <div class="header_logo">
        <a href="index.php">image du logo</a>
    </div>

    <div class="header_connexion">
        <?php if (isset($_SESSION['user_id'])) : ?>
            <span class="header_greeting">Bonjour, <?php echo htmlspecialchars($prenom); ?></span>
            <div class="header_user_menu">
                <a href="espace_membre.php">votre compte</a>
                <a href="deconnexion.php">Déconnexion</a>
            </div>
        <?php else : ?>
            <a href="connexion.php">Connexion</a>
        <?php endif; ?>
    </div>
</div>



