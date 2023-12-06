<?php
// Параметры подключения к базе данных
$host = "localhost";
$user = "root";
$password = "99830056Abcd";
$db_name = "hospital";

// Устанавливаем соединение с базой данных
$conn = mysqli_connect($host, $user, $password, $db_name);

// Проверяем, был ли пользователь авторизован
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$errormsg = '';
$success_message = '';

// Получаем информацию о пользователе
$name = $_SESSION['username'];
$sql = "SELECT * FROM patients WHERE full_name = '$name'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Проверяем, была ли отправлена форма записи на прием
if (isset($_POST['submit'])) {
    $doctor_id = $_POST['doctor_id'];
    $date_and_time = $_POST['date_and_time'];
    $patient_id = $row['id'];
    
    if (empty($doctor_id) || empty($date_and_time)) {
        $errormsg = "Пожалуйста, выберите врача и дату/время для записи на прием.";
    } else {
        // Получаем текущую дату и время
        $current_datetime = date("Y-m-d H:i:s");
        
        // Преобразуем выбранную дату и время в секунды
        $selected_datetime = strtotime($date_and_time);
        $current_datetime_seconds = strtotime($current_datetime);
        
        // Проверяем, что выбранная дата и время больше текущей даты и времени
        if ($selected_datetime <= $current_datetime_seconds) {
            $errormsg = "Выбранная дата и время должны быть больше текущей даты и времени.";
        } else {
            $sql = "SELECT * FROM appointments WHERE doctor_id = '$doctor_id' AND date_and_time = '$date_and_time'";
            $result = mysqli_query($conn, $sql);
            $num_rows = mysqli_num_rows($result);

            if ($num_rows == 0) {
                // Добавляем новую медицинскую запись
                $sql = "INSERT INTO appointments (doctor_id, patient_id, date_and_time) 
                    VALUES ('$doctor_id', '$patient_id', '$date_and_time')";
                mysqli_query($conn, $sql);
                // Выводим сообщение об успешной записи на прием
                $success_message = "Вы успешно записаны на прием к врачу!";
            } else {
                $errormsg = "Выбранная дата и время недоступны для записи. Пожалуйста, выберите другую дату и время.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Запись на прием</title>
    <link rel="stylesheet" type="text/css" href="stylemakeappointments.css">
</head>
<body>
    <h1>Запись на прием к врачу</h1>
   
    <form method="post">
    <p><?php echo $row['full_name']; ?>, пожалуйста, заполните форму записи на прием:</p>
        <label for="doctor_id">Врач:</label>
        <select name="doctor_id" id="doctor_id">
            <?php
            // Получаем список врачей из базы данных
            $sql = "SELECT * FROM doctors";
            $result = mysqli_query($conn, $sql);
             // Выводим список врачей в виде выпадающего списка
        while ($doctor = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $doctor['id'] . "'>" . $doctor['full_name'] . " - " . $doctor['field_of_specialization'] . "</option>";
        }
        ?>
    </select><br><br>

    <label for="date_and_time">Дата и время:</label>
    <input type="datetime-local" name="date_and_time" id="date_and_time" step="900"><br><br>
    <input type="submit" name="submit" value="Записаться на прием">
    <a href="account.php">Вернуться назад</a>
    <p style="color: red;"><?php echo $errormsg; ?></p>
    <p style="color: green;"><?php echo $success_message; ?></p>
    </form>
</body>
</html>