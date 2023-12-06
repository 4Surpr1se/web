from flask_sqlalchemy import SQLAlchemy

db = SQLAlchemy()


class Users(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    fullname = db.Column(db.String(50), nullable=False)
    gender = db.Column(db.String(10), nullable=False)
    birthdate = db.Column(db.Date, nullable=False)
    phone = db.Column(db.String(15), unique=True, nullable=False)
    password = db.Column(db.String(120), nullable=False)
    policy = db.Column(db.String(20), unique=True, nullable=False)


class Doctors(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    full_name = db.Column(db.String(50), nullable=False)
    field_of_specialization = db.Column(db.String(50), nullable=False)
    experience = db.Column(db.Integer, nullable=False)
    phone_number = db.Column(db.String(15), unique=True, nullable=False)
    address = db.Column(db.String(120), nullable=False)
    cabinet_number = db.Column(db.String(10), nullable=False)
    username = db.Column(db.String(50), unique=True, nullable=False)
    password_hash = db.Column(db.String(120), nullable=False)


class Appointments(db.Model):
    id = db.Column(db.Integer, primary_key=True)
    users_id = db.Column(db.Integer, db.ForeignKey('users.id'), nullable=False)
    doctors_id = db.Column(db.Integer, db.ForeignKey('doctors.id'), nullable=False)
    date_and_time = db.Column(db.DateTime, nullable=False)
    treatment = db.Column(db.Text, nullable=True)
    diagnosis = db.Column(db.Text, nullable=True)