<html><head><title>Setting up database</title></head><body>
<h3>Setting up...</h3>
    
<?php //setup.php
include_once './includes/functionsTemp.php';

createTable('members',
            'id(11) INT NOT NULL AUTO INCREMENT,
            user VARCHAR(16),
            fname VARCHAR(16),
            lname VARCHAR(16),
            email VARCHAR(255),
            pass VARCHAR(255),
            gender ENUM("m","f") NOT NULL,
            signup DATETIME NOT NULL,
            lastlogin DATETIME NOT NULL,
            activated ENUM("0","1") NOT NULL DEFAULT "0",
            PRIMARY KEY (id),
            UNIQUE KEY ("user","email"),
            INDEX(user(6))');

createTable('messages',
            'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            auth VARCHAR(16),
            recip VARCHAR(16),
            pm CHAR(1),
            tim INT UNSIGNED,
            message VARCHAR(4096),
            INDEX(auth(6)),
            INDEX(recip(6))');

createTable('friends',
            'user VARCHAR(16),
            friend VARCHAR(16),
            INDEX(user(6)),
            INDEX(friend(6))');

createTable('profiles',
            'user VARCHAR(16),
            text VARCHAR(4096),
            INDEX(user(6))');
?>

<br/>...done.
</body></html>