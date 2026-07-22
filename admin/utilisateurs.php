<?php
session_start();
require_once "../config/db.php";

$prenom = "";
$nom = "";
$email = "";
$telephone = "";
$adresse = "";
$ville = "";
$role = "";
$idModifier = "";

$stmt = $pdo->prepare("
SELECT
users.id,
users.email,
profiles.prenom,
profiles.nom,
profiles.role
FROM users
INNER JOIN profiles
ON users.id = profiles.user_id
");

$stmt->execute();

$users = $stmt->fetchAll();

if (isset($_GET['modifier'])) {

    $id = $_GET['modifier'];

    $stmt = $pdo->prepare("
        SELECT
            users.id,
            users.email,
            profiles.prenom,
            profiles.nom,
            profiles.telephone,
            profiles.adresse,
            profiles.ville,
            profiles.role
        FROM users
        INNER JOIN profiles
        ON users.id = profiles.user_id
        WHERE users.id = ?
    ");

    $stmt->execute([$id]);

    $user = $stmt->fetch();

    $idModifier = $user['id'];
    $prenom = $user['prenom'];
    $nom = $user['nom'];
    $email = $user['email'];
    $telephone = $user['telephone'];
    $adresse = $user['adresse'];
    $ville = $user['ville'];
    $role = $user['role'];
}

if(isset($_GET['supprimer'])){

    $id = $_GET['supprimer'];
    $stmt = $pdo->prepare("DELETE FROM profiles WHERE user_id = ?");
    $stmt->execute([$id]);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: utilisateurs.php");
    exit();
}

if(isset($_POST['modifier'])){

    $id = $_POST['id'];
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'];
    $ville = $_POST['ville'];
    $role = $_POST['role'];

    $stmt = $pdo->prepare("
        UPDATE users
        SET email = ?
        WHERE id = ?
    ");

    $stmt->execute([$email, $id]);

    $stmt = $pdo->prepare("
        UPDATE profiles
        SET
            prenom = ?,
            nom = ?,
            telephone = ?,
            adresse = ?,
            ville = ?,
            role = ?
        WHERE user_id = ?
    ");

    $stmt->execute([
        $prenom,
        $nom,
        $telephone,
        $adresse,
        $ville,
        $role,
        $id
    ]);

    header("Location: utilisateurs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
</head>
<body>

<h1>Gestion des utilisateurs</h1>

<table border="1" cellpadding="10">

    <tr>
        <th>ID</th>
        <th>Prénom</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Actions</th>
    </tr>

    <?php foreach($users as $user){ ?>

    <tr>

        <td><?php echo $user['id']; ?></td>
        <td><?php echo $user['prenom']; ?></td>
        <td><?php echo $user['nom']; ?></td>
        <td><?php echo $user['email']; ?></td>
        <td><?php echo $user['role']; ?></td>

        <td>
            <a href="?modifier=<?php echo $user['id']; ?>">Modifier</a>
            <a href="?supprimer=<?php echo $user['id']; ?>"
            onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">
            Supprimer
            </a>
        </td>

    </tr>

    <?php } ?>

</table>

<hr>

<?php if($idModifier != ""){ ?>

<h2>Modifier un utilisateur</h2>

<?php } ?>

<form method="POST">

    <input type="hidden" name="id" value="<?php echo $idModifier; ?>">

    <label>Prénom</label><br>
    <input type="text" name="prenom" value="<?php echo $prenom; ?>">
    <br><br>

    <label>Nom</label><br>
    <input type="text" name="nom" value="<?php echo $nom; ?>">
    <br><br>

    <label>Email</label><br>
    <input type="email" name="email" value="<?php echo $email; ?>">
    <br><br>

    <label>Téléphone</label><br>
    <input type="text" name="telephone" value="<?php echo $telephone; ?>">
    <br><br>

    <label>Adresse</label><br>
    <input type="text" name="adresse" value="<?php echo $adresse; ?>">
    <br><br>

    <label>Ville</label><br>
    <input type="text" name="ville" value="<?php echo $ville; ?>">
    <br><br>

    <label>Rôle</label><br>

    <select name="role">

        <option value="admin" <?php if($role=="admin") echo "selected"; ?>>
            Admin
        </option>

        <option value="client" <?php if($role=="client") echo "selected"; ?>>
            Client
        </option>

        <option value="cuisinier" <?php if($role=="cuisinier") echo "selected"; ?>>
            Cuisinier
        </option>

        <option value="livreur" <?php if($role=="livreur") echo "selected"; ?>>
            Livreur
        </option>

    </select>

    <br><br>

    <button type="submit" name="modifier">
        Modifier
    </button>

</form>

</body>
</html>