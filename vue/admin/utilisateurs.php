<?php
$pageTitle = "Gestion des utilisateurs - " . APP_NAME;
require ROOT_PATH . '/assets/inc/header.php';
require ROOT_PATH . '/assets/inc/navbar.php';
?>

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

    <?php foreach ($users as $user) { ?>

    <tr>

        <td><?php echo $user['id']; ?></td>
        <td><?php echo $user['prenom']; ?></td>
        <td><?php echo $user['nom']; ?></td>
        <td><?php echo $user['email']; ?></td>
        <td><?php echo $user['role']; ?></td>

        <td>
            <a href="?route=admin/utilisateurs&modifier=<?php echo $user['id']; ?>">Modifier</a>
            <a href="?route=admin/utilisateurs&supprimer=<?php echo $user['id']; ?>"
            onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">
            Supprimer
            </a>
        </td>

    </tr>

    <?php } ?>

</table>

<hr>

<?php if ($idModifier != "") { ?>

<h2>Modifier un utilisateur</h2>

<?php } ?>

<form method="POST" action="<?php echo BASE_URL; ?>/index.php?route=admin/utilisateurs">

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

        <option value="admin" <?php if ($role == "admin") echo "selected"; ?>>
            Admin
        </option>

        <option value="client" <?php if ($role == "client") echo "selected"; ?>>
            Client
        </option>

        <option value="cuisinier" <?php if ($role == "cuisinier") echo "selected"; ?>>
            Cuisinier
        </option>

        <option value="livreur" <?php if ($role == "livreur") echo "selected"; ?>>
            Livreur
        </option>

    </select>

    <br><br>

    <button type="submit" name="modifier">
        Modifier
    </button>

</form>

<?php require ROOT_PATH . '/assets/inc/footer.php'; ?>
