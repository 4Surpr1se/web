<!DOCTYPE html>
<html>
<head>
  <title>Информация о пользователе</title>
  <link rel="stylesheet" type="text/css" href="styleaccount.css">
</head>
<body>
  <?php
    // Параметры подключения к базе данных
    $host = "localhost";
    $user = "root";
    $password = "9983";
    $db_name = "hospital";
          
    // Устанавливаем соединение с базой данных
    $conn = mysqli_connect($host, $user, $password, $db_name);

    // Проверяем, был ли пользователь авторизован
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }

    // Получаем информацию о пользователе
    $name = $_SESSION['username'];
    $sql = "SELECT * FROM patients WHERE full_name = '$name'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    // Получаем связанные с пользователем медицинские записи
    $patient_id = $row['id'];
    $sql = "SELECT appointments.*, doctors.full_name AS doctor_name, doctors.field_of_specialization, doctors.experience, doctors.cabinet_number FROM appointments JOIN doctors ON appointments.doctor_id = doctors.id WHERE appointments.patient_id = '$patient_id'";
    $result = mysqli_query($conn, $sql);
    ?>

    <div class="header">
    <img src="images/polytech_logo_main_RGB.png" alt="logo">
    <h1>Личный кабинет</h1>
    <div class="header-links">
        <a class="header-link" href="make_appointments.php">Записаться на прием</a>
        <a class="header-link" href="logout.php">На главную</a>
    </div>
    </div>

    <div class="user-info">
      <h2>Личная информация</h2>
      <p><b>Номер телефона:</b> <?php echo $row['phone_number']; ?></p>
      <p><b>Медицинская полис:</b> <?php echo $row['medical_policy']; ?></p>
      <p><b>ФИО:</b> <?php echo $row['full_name']; ?></p>
      <p><b>Пол:</b> <?php echo $row['gender']; ?></p>
      <p><b>Дата рождения:</b> <?php echo $row['date_of_birth']; ?></p>
    </div>

    <div class="appointments">
      <h2>Медицинские записи</h2>

      <?php if (mysqli_num_rows($result) > 0) { ?>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
          <div class="appointment">
            <p><b>Дата и время:</b> <?php echo $row['date_and_time']; ?></p>
            <p><b>Доктор:</b> <?php echo $row['doctor_name']; ?></p>
            <p><b>Специальность:</b> <?php echo $row['field_of_specialization']; ?></p>
            <p><b>Стаж:</b> <?php echo $row['experience']; ?> лет</p>
            <p><b>Кабинет:</b> <?php echo $row['cabinet_number']; ?></p>
            <p><b>Лечение:</b> <?php echo $row['treatment']; ?></p>
            <p><b>Диагноз:</b> <?php echo $row['diagnosis']; ?></p>
            <form method='post'>
              <input type='hidden' name='appointment_id' value='<?php echo $row['id']; ?>'>
              <button type='submit' name='delete_appointment'>Удалить запись</button>
            </form>
          </div>
          <hr>
        <?php } ?>
      <?php } else { ?>
        <p>Нет активных записей</p>
      <?php } ?>
    </div>

    <?php if (isset($_POST['delete_appointment'])) {
        $appointment_id = $_POST['appointment_id'];
        $sql = "DELETE FROM appointments WHERE id = '$appointment_id'";
        mysqli_query($conn, $sql);
        header("Location: account.php");
        exit;
    } ?>

    <?php mysqli_close($conn); ?>
</body>
</html>