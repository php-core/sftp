# SFTP library for PHP >=8.1

PHP SFTP Utilities (PHP >= 8.1)

Based on: [php-sftp](https://github.com/hugsbrugs/php-sftp)

## Dependencies :

phpseclib : [Github](https://github.com/phpseclib/phpseclib) - [Documentation](https://api.phpseclib.org/master/) - [Examples](http://phpseclib.sourceforge.net/sftp/examples.html)

## Install

Install package with composer
```
composer require php-core/sftp
```

In your PHP code, load library

```php
require_once __DIR__ . '/vendor/autoload.php';
use PHPCore\SFTP\SFTP;
```

## Usage

Test SFTP connection
```php
SFTP::test($server, $user, $password, $port = 22, $timeout = 10);
```

Check if a file exists on SFTP Server
```php
SFTP::isFile($server, $user, $password, $remoteFile, $port = 22, $timeout = 10);
```

Delete a file on remote FTP server
```php
SFTP::delete($server, $user, $password, $remoteFile, $port = 22, $timeout = 10);
```

Recursively deletes files and folder in given directory (If remotePath ends with a slash delete folder content otherwise delete folder itself)
```php
SFTP::rmdir($server, $user, $password, $remotePath, $port = 22, $timeout = 10);
```

Recursively copy files and folders on remote SFTP server (If localPath ends with a slash upload folder content otherwise upload folder itself)
```php
SFTP::uploadDir($server, $user, $password, $localPath, $remotePath, $port = 22, $timeout = 10);
```

Download a file from remote SFTP server
```php
SFTP::download($server, $user, $password, $remoteFile, $localFile, $port = 22, $timeout = 10);
```

Download a directory from remote FTP server (If remoteDir ends with a slash download folder content otherwise download folder itself)
```php
SFTP::downloadDir($server, $user, $password, $remoteDir, $localDir, 
$port = 22, $timeout = 10);
```

Rename a file on remote SFTP server
```php
SFTP::rename($server, $user, $password, $oldFile, $newFile, $port = 22, $timeout = 10);
```

Create a directory on remote SFTP server
```php
SFTP::mkdir($server, $user, $password, $directory, $port = 22, $timeout = 10);
```

Create a file on remote SFTP server
```php
SFTP::touch($server, $user, $password, $remoteFile, $content, $port = 22, $timeout = 10);
```

Upload a file on SFTP server
```php
SFTP::upload($server, $user, $password, $localFile, $remoteFile = '', $port = 22, $timeout = 10);
```

List files on SFTP server
```php
SFTP::scandir($server, $user, $password, $path, $port = 22, $timeout = 10);
```

Get default login SFTP directory aka pwd
```php
SFTP::pwd($server, $user, $password, $port = 22, $timeout = 10);
```

## Tests

Edit example/test.php with your FTP parameters then run 
```php
php example/test.php
```

## To Do

PHPUnit Tests

## License
MIT

## Author

[PHPCore](https://github.com/php-core)
