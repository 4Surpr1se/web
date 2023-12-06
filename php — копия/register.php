<?php
$servername = "localhost"; 
$username = "root"; 
$password = "99830056Abcd"; 
$dbname = "hospital";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
  die("Ошибка соединения: " . $conn->connect_error);
}

$success_message = '';
$empty_fields_message = '';
$existing_patient_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Получаем данные из формы регистрации
  $fullname = $_POST['fullname'];
  $gender = $_POST['gender'];
  $birthdate = $_POST['birthdate'];
  $phone = $_POST['phone'];
  $password = $_POST['password'];
  $policy = $_POST['policy'];

  // Проверяем, что все поля заполнены
  if (empty($fullname) || empty($gender) || empty($birthdate) || empty($phone) || empty($password) || empty($policy)) {
      $empty_fields_message = '<p style="color:red";>Заполните все поля</p>';
  } else {

    // Проверяем, есть ли пациент с таким же номером полиса
    $sql = "SELECT * FROM patients WHERE medical_policy = '$policy'";
    $result = $conn->query($sql);
	
    if ($result->num_rows > 0) {
      // Пациент с таким номером полиса уже существует
      $existing_patient_message = '<p style="color:red";>Пациент с таким номером полиса уже существует</p>';
    } else {
      // Регистрируем нового пациента
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
	  $sql = "INSERT INTO patients (full_name, gender, date_of_birth, phone_number, password_hash, medical_policy)
        VALUES ('$fullname', '$gender', '$birthdate', '$phone', '$hashed_password', '$policy')";

      if ($conn->query($sql) === TRUE) {
        $success_message = '<p style="color:blue";>Вы успешно зарегистрировались!</p>';
      } else {
        $empty_fields_message = '<p style="color:red";>Ошибка регистрации: ' . $conn->error . '</p>';
      }
    }
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" type="text/css" href="stylereg.css">
</head>
<body>
    <div class="container">
		<h1>Регистрация пациента</h1>
		<form method="POST">
			<div class="form-group">
				<label>ФИО:</label>
				<input type="text" name="fullname" required placeholder="Иванов Иван Иванович">
			</div>

			<div class="form-group">
				<label>Пол:</label>
				<select name="gender" required>
					<option value="" disabled selected>Выберите пол</option>
					<option value="Мужской">Мужской</option>
					<option value="Женский">Женский</option>
				</select>
			</div>

			<div class="form-group">
				<label>Дата рождения:</label>
				<input type="date" name="birthdate" required>
			</div>

			<div class="form-group">
				<label>Телефонный номер:</label>
                <input type="tel" name="phone" pattern="\+7[0-9]{10}" value="+7" required>
			</div>

			<div class="form-group">
				<label>Пароль:</label>
				<input type="password" name="password" required>
			</div>

			<div class="form-group">
				<label>Медицинский полис:</label>
				<input type="text" name="policy" required placeholder="XXX-XXX-XXX XX">
			</div>
            
			<button type="submit">Зарегистрироваться</button>
			<a href="login.php">На страницу входа</a>
			<?php echo $success_message; ?>
			<?php echo $existing_patient_message; ?>
			<?php echo $empty_fields_message; ?>
		</form>
	</div>
</body>
</html>