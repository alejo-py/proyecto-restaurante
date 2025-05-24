<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}
?>

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        padding-top: 60px;
    }

    .header {
        background-color: #1a1a1a;
        color: white;
        padding: 15px 20px;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        z-index: 1000;
    }

    .usuario {
        font-weight: bold;
    }

    .logout-form {
        margin: 0;
    }

    .logout-btn {
        background-color: #e60000;
        color: white;
        border: none;
        padding: 8px 14px;
        border-radius: 4px;
        cursor: pointer;
    }

    .logout-btn:hover {
        background-color: #cc0000;
    }
</style>

<div class="header">
    <div class="usuario">
        Bienvenido, <?php echo htmlspecialchars($_SESSION["usuario"]); ?>
    </div>
    <form class="logout-form" action="logout.php" method="post">
        <button type="submit" class="logout-btn">Cerrar sesión</button>
    </form>
</div>
