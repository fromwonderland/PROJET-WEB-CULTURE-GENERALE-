<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$loginsFile = 'logins.json';

// Charger les comptes existants
if (!file_exists($loginsFile)) {
    file_put_contents($loginsFile, json_encode([]));
}

$logins = json_decode(file_get_contents($loginsFile), true);

$input = json_decode(file_get_contents("php://input"), true);

// Gestion des requêtes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['action'])) {

    if ($input['action'] === "register") {
        $username = trim($input['username']);
        $password = trim($input['password']);

        foreach ($logins as $user) {
            if ($user['username'] === $username) {
                echo json_encode(["status" => "error", "message" => "Nom d'utilisateur déjà pris."]);
                exit;
            }
        }

        $logins[] = ["username" => $username, "password" => password_hash($password, PASSWORD_DEFAULT)];
        file_put_contents($loginsFile, json_encode($logins, JSON_PRETTY_PRINT));

        echo json_encode(["status" => "success", "message" => "Compte créé avec succès!"]);
        exit;
    }

    if ($input['action'] === "login") {
        $username = trim($input['username']);
        $password = trim($input['password']);

        foreach ($logins as $user) {
            if ($user['username'] === $username && password_verify($password, $user['password'])) {
                echo json_encode(["status" => "success", "message" => "Connexion réussie!"]);
                exit;
            }
        }

        echo json_encode(["status" => "error", "message" => "🎨🖌️🌿✨Nom d'utilisateur ou mot de passe incorrect🌟🎸⭐🔥"]);
        exit;
    }
}

echo json_encode(["status" => "error", "message" => "Requête invalide."]);
exit;
?>
