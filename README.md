# Тестовое задание для летней пратики (backend)

Есть список директорий неограниченной вложенности. В каждой директории может присутствовать файл count. Необходимо написать консольное приложение с использованием фреймворка Symfony, которое будет проходиться по всем директориям и возвращать сумму всех чисел из файлов count.

## Запуск
### Установка
```bash
git clone https://github.com/travka47/vl-test-task.git
cd vl-test-task
composer install
```

### Использование команды
```bash
php console.php count [options] <directory>

directory (опциональный аргумент) - название директории для подсчёта суммы файлов (default: корневая папка проекта)
-t, --table - вывод результата рекурсивного обхода в виде таблицы
```

### Тестирование
```bash
php bin/phpunit
```

## Темы, изученные в рамках курса backend-разработки от [feip](https://feip.co/)

1. Основные архитектурные концепции Laravel
2. Проектирование БД: ORM, миграции, фикстуры, запросы, пагинация
3. Обработка запроса: маршрутизация, параметры, контроллер, ответ от сервера, HTTP-исключения
4. Отправка форм, валидация и санитизация данных
5. Middleware
6. Сессия: хранение пользовательских данных между запросами
7. REST API: принципы построения, методы HTTP, контроллер, ответ от сервера
8. Аутентификация: cookies, токен, JWT, OAuth
9. Авторизация, RBAC
10. Отладка и тестирование

Также умею верстать, использую Git и Docker

## Книги

* “Laravel 9: Быстрая разработка веб-сайтов на PHP” (Владимир Дронов)
* “Чистый код” (Роберт Мартин) в процессе