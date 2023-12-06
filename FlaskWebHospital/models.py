from dataclasses import dataclass
from datetime import date, datetime

from flask_sqlalchemy import SQLAlchemy

db = SQLAlchemy()

@dataclass
class Users(db.Model):
    id: int
    fullname: str
    gender: str
    birthdate: date
    phone: str
    password: str
    policy: str

    id = db.Column(db.Integer, primary_key=True)
    fullname = db.Column(db.String(50), nullable=False)
    gender = db.Column(db.String(10), nullable=False)
    birthdate = db.Column(db.Date, nullable=False)
    phone = db.Column(db.String(15), unique=True, nullable=False)
    password = db.Column(db.String(120), nullable=False)
    policy = db.Column(db.String(20), unique=True, nullable=False)

@dataclass
class Doctors(db.Model):
    id: int
    full_name: str
    field_of_specialization: str
    experience: int
    phone_number: str
    address: str
    cabinet_number: str
    username: str
    password_hash: str

    id = db.Column(db.Integer, primary_key=True)
    full_name = db.Column(db.String(50), nullable=False)
    field_of_specialization = db.Column(db.String(50), nullable=False)
    experience = db.Column(db.Integer, nullable=False)
    phone_number = db.Column(db.String(15), unique=True, nullable=False)
    address = db.Column(db.String(120), nullable=False)
    cabinet_number = db.Column(db.String(10), nullable=False)
    username = db.Column(db.String(50), unique=True, nullable=False)
    password_hash = db.Column(db.String(120), nullable=False)

@dataclass
class Appointments(db.Model):
    id: int
    users_id: int
    doctors_id: int
    date_and_time: datetime
    treatment: str
    diagnosis: str

    id = db.Column(db.Integer, primary_key=True)
    users_id = db.Column(db.Integer, db.ForeignKey('users.id'), nullable=False)
    doctors_id = db.Column(db.Integer, db.ForeignKey('doctors.id'), nullable=False)
    date_and_time = db.Column(db.DateTime, nullable=False)
    treatment = db.Column(db.Text, nullable=True)
    diagnosis = db.Column(db.Text, nullable=True)