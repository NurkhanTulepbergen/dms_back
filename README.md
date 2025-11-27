Как запустить back на вашем устройстве(Предворительно до этого усановите docker):
1) git clone ttps://github.com/NurkhanTulepbergen/dms_back
2) заходим на проект и пишем в терминал
3) docker-compose up -d --build


Для проверки в Постмене:

POST : http://127.0.0.1:8000/api/register
body:
{
    "name" : "test",
    "email" : "test@example.com",
    "password" : "test12345"
}


POST : http://127.0.0.1:8000/api/login
body:
{
    "email" : "test@example.com",
    "password" : "test12345"
}


POST : http://127.0.0.1:8000/api/logout
