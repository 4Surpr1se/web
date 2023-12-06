import React, { useState } from 'react';
import { BrowserRouter as Router, Switch, Route, Link, useHistory } from 'react-router-dom';

const LoginPage = () => {
  const history = useHistory();
  const [phone, setPhone] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handlePhoneChange = (event) => {
    setPhone(event.target.value);
  };

  const handlePasswordChange = (event) => {
    setPassword(event.target.value);
  };

  const handleSubmit = (event) => {
    event.preventDefault();

    // Отправка данных на сервер
    // Вместо этого кода нужно использовать соответствующую функцию или хук для обращения к серверу, например, с помощью Axios или Fetch API

    // Вместо $_POST можно использовать стейт переменные phone и password
    const formData = new FormData();
    formData.append('phone', phone);
    formData.append('password', password);

    fetch('login_endpoint', {
      method: 'POST',
      body: formData
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Сохранение имени пользователя в localStorage или состоянии приложения, например, используя Redux или Context API
          localStorage.setItem('username', data.username);
          history.push('/account');
        } else {
          setError('Неверный номер телефона или пароль');
        }
      })
      .catch((error) => {
        console.error('Ошибка:', error);
        setError('Произошла ошибка. Попробуйте еще раз.');
      });
  };

  return (
    <div className="container">
      <h1>Вход в личный кабинет</h1>
      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label>Номер телефона:</label>
          <input
            type="tel"
            name="phone"
            pattern="\+7[0-9]{10}"
            value={phone}
            onChange={handlePhoneChange}
            required
          />
        </div>
        <div className="form-group">
          <label>Пароль:</label>
          <input
            type="password"
            name="password"
            value={password}
            onChange={handlePasswordChange}
            required
          />
        </div>
        <div className="form-group">
          <button type="submit">Войти</button>
        </div>
      </form>
      {error && <p style={{ color: 'red' }}>{error}</p>}
      <form action="reset_password.php">
        <div className="form-group">
          <button type="submit">Восстановить пароль</button>
        </div>
      </form>
      <Link to="/register">Зарегистрироваться</Link>
    </div>
  );
};

const AccountPage = () => {
  // Получение имени пользователя из localStorage или состояния приложения
  const username = localStorage.getItem('username');

  return (
    <div className="container">
      <h1>Добро пожаловать, {username}!</h1>
      {/* Остальное содержимое страницы аккаунта */}
    </div>
  );
};

const ResetPasswordPage = () => {
  return (
    <div className="container">
      <h1>Восстановление пароля</h1>
      {/* Форма восстановления пароля */}
    </div>
  );
};

const RegisterPage = () => {
  return (
    <div className="container">
      <h1>Регистрация</h1>
      {/* Форма регистрации */}
    </div>
  );
};

const App = () => {
  return (
    <Router>
      <Switch>
        <Route exact path="/">
          <LoginPage />
        </Route>
        <Route path="/account">
          <AccountPage />
        </Route>
        <Route path="/reset_password">
          <ResetPasswordPage />
        </Route>
        <Route path="/register">
          <RegisterPage />
        </Route>
      </Switch>
    </Router>
  );
};

export default App;
