<?php
    session_start();
?>


<html>
    <body>
        <h2> Welcome to your personal Profile</h2>
        <?php
            If (isset($_SESSION['email'])) {
                echo "<a>Ihre Email Adresse ist " . $_SESSION['email'] . "</a> <br>";
            }
            if (isset($_SESSION['firstname'])){
                echo "<a>Ihr Vorname ist " . $_SESSION['firstname'] . "</a> <br>";
            }
            if(isset($_SESSION['surname'])){
                echo "<a>Ihr Nachname ist " . $_SESSION['surname'] . "</a> <br>";
            }
        ?>
        <form action="../Backend/ChangeEmail.php" method="get">
            <input type="submit" value="Ändere deine Email!">
        </form> <br>

        <form action="../Backend/ChangeFirstname.php" method="post">
            <input type="text" name="firstnameChange">
            <input type="submit" value="Ändere deinen Vornamen!">
        </form> <br>

        <form action="../Backend/ChangeSurname.php" method="post">
            <input type="text" name="surnameChange">
            <input type="submit" value="Ändere deinen Nachnamen!">
        </form> <br>
        <form action="index.php" method="get">
            <input type="submit" value="Back to index!">
        </form>
    </body>

</html>
