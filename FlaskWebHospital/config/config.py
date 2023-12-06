class Config:
    SQLALCHEMY_DATABASE_URI = 'postgresql://postgres:postgres@127.0.0.1/hospital'
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    DEBUG = True