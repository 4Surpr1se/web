from flask import Flask, jsonify, request, render_template, make_response
from flask_sqlalchemy import SQLAlchemy
from werkzeug.security import generate_password_hash, check_password_hash
from flask_restful import Api, Resource, reqparse
from flasgger import Swagger
from models import db, Doctors
from models import Users

app = Flask(__name__)
# app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://postgres:99830056@127.0.0.1/hospital'
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://postgres:postgres@127.0.0.1/hospital'

db.init_app(app)
with app.app_context():
    db.create_all()

api = Api(app)
swagger = Swagger(app)


@app.route('/')
def home():
    return render_template('index.html')


@app.route('/registration')
def registration_page():
    return render_template('registration.html')


@app.route('/login')
def login_page():
    return render_template('login.html')


@app.route('/resetpassword')
def reset_page():
    return render_template('resetpass.html')


class DoctorsResource(Resource):
    def get(self):
        """
        Users Resource
        ---
        get:
          description: Get all users' information
          responses:
            200:
              description: Returns a list of users with their details
        """
        doctors = Doctors.query.all()
        doctor_list = []
        for doctor in doctors:
            doctor_data = {
                "full_name": doctor.full_name,
                "field_of_specialization": doctor.field_of_specialization,
                "experience": doctor.experience,
                "phone_number": doctor.phone_number,
                "address": doctor.address,
                "cabinet_number": doctor.cabinet_number,
                "username": doctor.username,
                "password_hash": doctor.password_hash,
            }
            doctor_list.append(doctor_data)
        return make_response(jsonify(doctor_list))

    def post(self):
        """
        Users Resource
        ---
        post:
          description: Create a new user
          parameters:
            - name: body
              in: body
              required: true
              schema:
                properties:
                  fullname:
                    type: string
                    description: Full name of the user
                  gender:
                    type: string
                    description: Gender of the user
                  birthdate:
                    type: string
                    description: User's birthdate
                  phone:
                    type: string
                    description: User's phone number
                  password:
                    type: string
                    description: User's password
                  policy:
                    type: string
                    description: Policy information
          responses:
            200:
              description: Returns the details of the created user
        """
        data = request.get_json()
        if not data:
            return make_response(jsonify({"message": "Invalid JSON"}), 400)

        full_name = data.get('full_name')
        field_of_specialization = data.get('field_of_specialization')
        experience = data.get('experience')
        phone_number = data.get('phone_number')
        address = data.get('address')
        cabinet_number = data.get('cabinet_number')
        username = data.get('username')
        password = data.get('password')

        existing_doctor_phone = Users.query.filter_by(phone=phone_number).first()
        existing_doctor_username = Users.query.filter_by(policy=username).first()

        if existing_doctor_phone or existing_doctor_username:
            error_message = "Такой доктор уже существует."
            return make_response(jsonify(message=error_message), 400)
        if password:
            hashed_password = generate_password_hash(password, method='pbkdf2:sha256')
        else:
            return make_response(jsonify(message='Введите пароль!'), 200)
        new_user = Doctors(full_name=full_name, field_of_specialization=field_of_specialization,
                           experience=experience,
                           phone_number=phone_number,
                           address=address,
                           cabinet_number=cabinet_number,
                           password_hash=hashed_password,
                           username=username)
        db.session.add(new_user)
        db.session.commit()

        response_data = {
            "message": "Доктор успешно создан!",
            "user": {
                "full_name": full_name,
                "field_of_specialization": field_of_specialization,
                "experience": experience,
                "phone_number": phone_number,
                "address": address,
                "cabinet_number": cabinet_number,
                "username": username,
            }
        }
        return make_response(jsonify(response_data), 200)


api.add_resource(DoctorsResource, '/doctors')


class UsersResource(Resource):
    def get(self):
        """
        Users Resource
        ---
        get:
          description: Get all users' information
          responses:
            200:
              description: Returns a list of users with their details
        """
        users = Users.query.all()
        user_list = []
        for user in users:
            user_data = {
                "fullname": user.fullname,
                "gender": user.gender,
                "birthdate": str(user.birthdate),
                "phone": user.phone,
                "policy": user.policy
            }
            user_list.append(user_data)
        return jsonify(user_list)

    def post(self):
        """
        Users Resource
        ---
        post:
          description: Create a new user
          parameters:
            - name: body
              in: body
              required: true
              schema:
                properties:
                  fullname:
                    type: string
                    description: Full name of the user
                  gender:
                    type: string
                    description: Gender of the user
                  birthdate:
                    type: string
                    description: User's birthdate
                  phone:
                    type: string
                    description: User's phone number
                  password:
                    type: string
                    description: User's password
                  policy:
                    type: string
                    description: Policy information
          responses:
            200:
              description: Returns the details of the created user
        """
        data = request.get_json()
        if not data:
            return make_response(jsonify({"message": "Invalid JSON"}), 400)

        fullname = data.get('fullname')
        gender = data.get('gender')
        birthdate = data.get('birthdate')
        phone = data.get('phone')
        password = data.get('password')
        policy = data.get('policy')

        existing_user_phone = Users.query.filter_by(phone=phone).first()
        existing_user_policy = Users.query.filter_by(policy=policy).first()

        if existing_user_phone or existing_user_policy:
            error_message = "Такой пользователь уже существует."
            return make_response(jsonify(message=error_message), 400)

        hashed_password = generate_password_hash(password, method='pbkdf2:sha256')
        new_user = Users(fullname=fullname, gender=gender, birthdate=birthdate, phone=phone,
                         password=hashed_password,
                         policy=policy)
        db.session.add(new_user)
        db.session.commit()

        response_data = {
            "message": "Пользователь успешно создан!",
            "user": {
                "fullname": fullname,
                "gender": gender,
                "birthdate": birthdate,
                "phone": phone,
                "policy": policy
            }
        }
        return make_response(jsonify(response_data), 200)


api.add_resource(UsersResource, '/users')


class LoginResource(Resource):
    def post(self):
        """
        Login Resource
        ---
        post:
          description: Authenticate user
          parameters:
            - name: phone
              in: formData
              type: string
              required: true
              description: User's phone number
            - name: password
              in: formData
              type: string
              required: true
              description: User's password
          responses:
            200:
              description: Returns user details upon successful authentication
        """
        parser = reqparse.RequestParser()
        parser.add_argument('phone', type=str, required=True)
        parser.add_argument('password', type=str, required=True)
        args = parser.parse_args()

        phone = args['phone']
        password = args['password']

        user = Users.query.filter_by(phone=phone).first()

        if user and check_password_hash(user.password, password):
            success_message = "Авторизация прошла успешно!"
            response_data = {
                "message": success_message,
                "user": {
                    "fullname": user.fullname,
                    "gender": user.gender,
                    "birthdate": str(user.birthdate),
                    "phone": user.phone,
                    "policy": user.policy
                }
            }
            return jsonify(response_data), 200
        else:
            error_message = "Неверный номер телефона или пароль"
            return jsonify(message=error_message), 400


api.add_resource(LoginResource, '/login')


class ResetPasswordResource(Resource):
    def put(self):
        """
        Reset Password Resource
        ---
        put:
          description: Reset user's password
          parameters:
            - name: phone_number
              in: body
              type: string
              required: true
              description: User's phone number
            - name: medical_policy
              in: body
              type: string
              required: true
              description: User's medical policy
            - name: new_password
              in: body
              type: string
              required: true
              description: New password for the user
          responses:
            200:
              description: Password reset successful
        """
        parser = reqparse.RequestParser()
        parser.add_argument('phone_number', type=str, required=True)
        parser.add_argument('medical_policy', type=str, required=True)
        parser.add_argument('new_password', type=str, required=True)
        args = parser.parse_args()

        data = request.get_json()
        if not data:
            return jsonify({"message": "Invalid JSON"}), 400

        phone_number = data.get('phone_number')
        medical_policy = data.get('medical_policy')
        new_password = data.get('new_password')

        user = Users.query.filter_by(phone=phone_number, policy=medical_policy).first()

        if user:
            hashed_password = generate_password_hash(new_password, method='pbkdf2:sha256')
            user.password = hashed_password
            db.session.commit()
            return jsonify({"message": "Пароль успешно сброшен"}), 200
        else:
            return jsonify({"error": "Пользователь не найден"}), 400


api.add_resource(ResetPasswordResource, '/reset_password')

if __name__ == '__main__':
    app.run(debug=True)
