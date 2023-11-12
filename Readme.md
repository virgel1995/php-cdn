# simple php cdn

## installition

### make sure you have php installed in your machine

open any cmd you have run command

```bach
php -v
```

### use sqlite

go to path to php dirctory

open `php.ini` with any editor
you will Found

#### for sqlite database

```php
;extension=sqlite3
```

To :

```php
extension=sqlite3
```

#### for postgres database

```php
;extension=pgsql
```

To :

```php
extension=pgsql
```

##### now you are reay to use this cdn

## Avilable Databases

|     Name    |  Type  |
| ----------- | ------ |
|    MySql    |   [x]  |
| PostgresSql |   [x]  |
|    SQLite   |   [x]  |
|    MongoDb  |   [ ]  |

## Main Route

|     Name    |       Route   |
| ----------- | ------------- |
|    MySql    |  /cdn/mysql   |
| PostgresSql |    /cdn/pg    |
|    SQLite   |  /cdn/sqlite  |

## Avilable Routes

|   Name   | Method |         Route           |                     QueryStrings               |
| -------- | ------ | ----------------------- | ---------------------------------------------- |
|  upload  |  POST  |      /upload/index.php  |                     none                       |
| download |   GET  |          /download      | file:`number` , base: `bool` , download:`bool` |
|   view   |   GET  |          /view          |                 file : `number`                |
