# pizza-reviews <br> 
Аутентификация сделана через бд. <br> 
Авторизация стандартная из двух ролей. Плюс измененные запросы к бд для пользователя admin. <br> 
Вход в админку для не аутентифицированных скрыт с помощью поведений. <br> 
В шаблоне main в navbar ссылка на регистрацию скрыта для аутентифицированных. <br> 
Регистрация- модель SignupForm, action и view в SiteController. <br> 
auth_key создается средствами фреймворка- метод beforeSave в моделях. <br> 
Для паролей создается хеш в action контроллеров. <br> 
created_at и updated_at заполняются с помощью поведений TimestampBehavior. В бд хранится значение в unix. <br> 
В лич. каб, польз. видит только свои данные- изменен запрос к бд в search моделях. <br> 
Есть администратор username:admin, password:admin для него в админ панели выводятся все пользователи, все отзывы и приложения. <br> 
Статус отзыва в админке редактировать может только админ. <br> 
Загрузка аватара сделана стандартными средствами фреймворка. Не сохраняется basename, вместо него uniqid(). <br> 
И для сохранения оригинального названия картинки аватара есть поле avatar_initial. <br> 
В модели для загрузки файлов в $_GET записываю название и передаю в $model->avatar в UserController в модуле admin. <br> 
Смена пароля метод updatePass. Удаление аватара метод deleteAvatar. <br> 
Есть проверка старого пароля перед изменением. <br> 
Добавлен AccessControl поведение во все контроллеры админки для всех действий- зайти может только аутент польз. <br> 
Админ не может редактировать и удалять никакие данные пользователей. <br> 
Даже при редактировании id в url на любое действие может зайти только владелец. <br> 
<br><br>
APIDOC <br> 
<br><br>
Создание(регистрация)пользователя                  POST http://localhost/rest/signup/create              No Auth <br> 
Аутентификация пользователя                        GET http://localhost/rest/login                       Basic Auth <br> 
Создание(регистрация) клиента(приложение)          POST http://localhost/rest/signup-client/create       No Auth <br> 
Аутентификация клиента                             GET http://localhost/rest/client                      Basic Auth <br> 
Просмотр данных клиента                            GET http://localhost/rest/client                      Basic Auth <br> 
Обновление данных клиента                          PUT PATCH http://localhost/rest/client/update         Basic Auth <br> 
Удаление клиента                                   DEL http://localhost/rest/client/delete               Basic Auth <br> 
Создание кода авторизации                          POST http://localhost/rest/auth/create-auth-code      Basic Auth <br> 
Создание access token и refresh token              POST http://localhost/rest/auth/create-tokens         Basic Auth <br> 
Обновление access token и refresh token            POST http://localhost/rest/auth/update-tokens         Basic Auth <br> 
Просмотр данных пользователя                       GET http://localhost/rest/user                        Auth 2.0 <br> 
Просмотр данных пользователя и его отзывов         GET http://localhost/rest/user?expand=reviews         Auth 2.0 <br> 
Обновление данных пользователя                     PUT PATCH http://localhost/rest/user/update           Auth 2.0 <br> 
Удаление пользователя                              DEL http://localhost/rest/user/delete                 Auth 2.0 <br> 
Загрузка аватара                                   POST http://localhost/rest/user/avatar                Auth 2.0 <br> 
Просмотр отзывов пользователя                      GET http://localhost/rest/review                      Auth 2.0 <br> 
Создание отзыва                                    POST http://localhost/rest/review/create              Auth 2.0 <br> 
Обновление отзыва                                  PUT PATCH http://localhost/rest/review/update?id=17   Auth 2.0 <br> 
Удаление отзыва                                    DEL http://localhost/rest/review/delete?id=17         Auth 2.0 <br> 
<br><br> 
Из Postman регистрируем пользователя. <br> 
Или нужны данные доступа существущего пользователя (по примеру OpenId) <br> 
Регистрируем клиента(приложение). <br> 
Доступ к методам регистрации пользователя и клиента не закрыт аутентификацией. <br> 
Crud методы клиента(приложения), получение кода авторизации, получение и обновление access token и refresh token закрыты Basic Auth. <br> 
Для аутентификации клиента вместо username указываем client_id, вместо password указываем client_secret. <br> 
Перед получением токенов нужно получить код авторизации. <br> 
Пользователь должен быть аутентифицирован. Для получения кода авторизации, access token и refresh token (по примеру OpenId) <br> 
После получения access token клиент имеет доступ к crud методам пользователя и к crud методам отзывов пользователя. <br> 
Время действия access token одна неделя, refresh token один месяц. <br> 
Реализована загрузка аватара по апи. <br> 
Данные для аутентификации пользователя или клиента передаются в Authorization. <br> 
Для апи закрыто обновление пароля пользователя. <br> 
В корне проекта есть коллекция запросов в Postman. <br> 
<br><br> 
Проект находиться в разработке. <br> 
Требуется доработка визуальной части админ панели(личный кабинет) <br> 
