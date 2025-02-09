# JEHAM Laurie
# MARTHELY Davy
# BORELLO Benjamin

---

1. Fill your database credentials into `credentials.json`
2. Run sql :
```sql
create table news
(
    id         varchar(36)  not null primary key,
    content    varchar(256) not null,
    created_at datetime     not null
);

create table users
(
    id         varchar(36)  not null primary key,
    login      varchar(64)  not null,
    email      varchar(256) not null,
    password   varchar(256) not null,
    created_at datetime     not null
);
```
3. Run script with :
   - `php src/app.php add [filepath].json` 
   - `php src/app.php update [filepath].json` 
   - `php src/app.php delete [id]`
