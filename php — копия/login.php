<?php
// Параметры подключения к базе данных
$host = "localhost";
$user = "root";
$password = "9983";
$db_name = "hospital";

// Устанавливаем соединение с базой данных
$conn = mysqli_connect($host, $user, $password, $db_name);

// Обработка данных из формы
if(isset($_POST['login'])) {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Выполнение запроса к базе данных для проверки существования пользователя
    $sql = "SELECT * FROM patients WHERE phone_number = '$phone'";
    $result = mysqli_query($conn, $sql);

    // Проверка результата запроса
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        // Проверяем, совпадает ли введенный пароль с хешем в базе данных
        if (password_verify($password, $row['password_hash'])) {
            // Сессия для сохранения имени пользователя
            session_start();
            $_SESSION['username'] = $row['full_name'];
            header("Location: account.php");
        } else {
            
            $error = "Неверный номер телефона или пароль";
        }
    } else {
        $error = "Неверный номер телефона или пароль";
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" type="text/css" href="stylelogin.css">
</head>
<body>
    <div class="container">
        <h1>Вход в личный кабинет</h1>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Номер телефона:</label>
                <input type="tel" name="phone" pattern="\+7[0-9]{10}" value="+7" required>
            </div>
            <div class="form-group">
                <label>Пароль:</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit" name="login">Войти</button>
            </div>
        </form>
        <?php if (isset($error)) { ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php } ?>
        <form action="reset_password.php">
            <div class="form-group">
                <button type="submit">Восстановить пароль</button>
            </div>
        </form>
        <a href="register.php">Зарегистрироваться</a>
    </div>
</body>
</html>





