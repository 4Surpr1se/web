<?php
// Параметры подключения к БД
$host = "localhost"; // адрес сервера БД
$user = "root"; // имя пользователя БД
$password = "99830056Abcd"; // пароль пользователя БД
$dbname = "hospital"; // имя базы данных

$succes = '';
$non_succes = '';
$not_find = '';
// Подключаемся к БД
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Если форма отправлена
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем номер телефона пароль и медицинский полис из формы
    $phone_number = $_POST["phone_number"];
    $medical_policy = $_POST["medical_policy"];
    $new_password = $_POST["password"];
    
    // Запрос на поиск пациента в БД по номеру телефона и медицинскому полису
    $sql = "SELECT * FROM patients WHERE phone_number = '$phone_number' AND medical_policy = '$medical_policy'";
    $result = $conn->query($sql);

    // Если пациент найден в БД, генерируем новый пароль и обновляем запись в БД
    if ($result->num_rows == 1) { 
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // хешируем новый пароль
        $sql = "UPDATE patients SET password_hash = '$hashed_password' WHERE phone_number = '$phone_number' AND medical_policy = '$medical_policy'";
        if ($conn->query($sql) === TRUE) {
            $succes = '<p style="color:green">Пароль успешно изменен. Новый пароль: '. $new_password . '</p>';
        } else {
            $non_success = '<p style="color:red">Ошибка при изменении пароля: '. $conn->error . '</p>'; 
        }
    } else {
        $not_find = '<p style="color:red">Пациент не найден</p>'; 
    }

    $conn->close(); // закрываем соединение с БД
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля</title>
    <link rel="stylesheet" type="text/css" href="styleresetpassword.css">
</head>
<body>
    <form method="POST">
        <h1>Восстановление пароля</h1>
        <label for="phone_number">Телефонный номер при регистрации:</label>
        <input type="tel" id="phone_number" name="phone_number" required placeholder="+7XXXXXXXXXX"><br><br>
        <label for="medical_policy">Медицинский полис при регистрации:</label>
        <input type="text" id="medical_policy" name="medical_policy" required placeholder="XXX-XXX-XXX XX"><br><br>
        <label for="password">Новый пароль:</label> 
        <input type="password" id="password" name="password" required placeholder="Ваш новый пароль"><br><br>
        <button type="submit" name="refresh" id="refresh">Обновить пароль</button>
        <a href="login.php">Вернуться на страницу входа</a>
        <?php echo $succes; ?>
        <?php echo $non_succes; ?>
        <?php echo $not_find; ?>
    </form>
</body>
</html>