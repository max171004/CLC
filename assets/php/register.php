<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Validation de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }

    // Vérification des mots de passe
    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Hash du mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Vérification de l'email déjà enregistré
    $db = new PDO('mysql:host=localhost;dbname=clashofmax', 'root', '');
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $errors[] = "L'adresse email est déjà utilisée.";
    }

    if (empty($errors)) {
        // Insérer l'utilisateur dans la base de données
        $stmt = $db->prepare("INSERT INTO users (prenom, nom, email, password) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$prenom, $nom, $email, $hashed_password])) {
            echo "<p>Inscription réussie ! Vous pouvez maintenant vous <a href='signin.html'>connecter</a>.</p>";
        } else {
            echo "<p>Une erreur est survenue lors de l'inscription. Veuillez réessayer plus tard.</p>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>