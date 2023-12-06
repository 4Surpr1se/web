from flask_restplus import fields

def add_models(api):
    user_model = api.model('User', {
        'fullname': fields.String(required=True, description='Полное имя пользователя'),
        'gender': fields.String(required=True, description='Пол пользователя'),
        'birthdate': fields.String(required=True, description='Дата рождения пользователя'),
        'phone': fields.String(required=True, description='Номер телефона пользователя'),
        'password': fields.String(required=True, description='Пароль пользователя'),
        'policy': fields.String(required=True, description='Медицинский полис пользователя')
    })

    login_model = api.model('Login', {
        'phone': fields.String(required=True, description='Номер телефона пользователя'),
        'password': fields.String(required=True, description='Пароль пользователя')
    })

    reset_password_model = api.model('ResetPassword', {
        'phone_number': fields.String(required=True, description='Номер телефона пользователя'),
        'medical_policy': fields.String(required=True, description='Медицинский полис пользователя'),
        'new_password': fields.String(required=True, description='Новый пароль пользователя')
    })

    return user_model, login_model, reset_password_model
