<?php

# For PHP7
// declare(strict_types=1);

// namespace PHPCore\Tests\SFTP;

use PHPUnit\Framework\TestCase;

use PHPCore\SFTP\SFTP as Sftp;

/**
 *
 */
final class SftpTest extends TestCase
{

    /* ************************************************* */
    /* ******************* SFTP::test ****************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanTest()
    {
        $test = SFTP::test($server, $user, $password, $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** SFTP::is_file **************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanIsFile()
    {
        $test = SFTP::is_file($server, $user, $password, $remote_file, $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** SFTP::delete ***************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanDelete()
    {
        $test = SFTP::delete($server, $user, $password, $remote_file, $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** SFTP::rmdir ****************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanRmdir()
    {
        $test = SFTP::rmdir($server, $user, $password, $remote_path, $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* **************** SFTP::upload_dir *************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanUploadDir()
    {
        $test = SFTP::upload_dir($server, $user, $password, $local_path, $remote_path, $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ***************** SFTP::download **************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanDownload()
    {
        $test = SFTP::download($server, $user, $password, $remote_file, $local_file, $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* *************** SFTP::download_dir ************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanDownloadDir()
    {
        $test = SFTP::download_dir($server, $user, $password, $remote_dir, $local_dir, $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** SFTP::rename ***************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanRename()
    {
        $test = SFTP::rename($server, $user, $password, $old_file, $new_file, $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ******************* SFTP::mkdir ***************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanMkdir()
    {
        $test = SFTP::mkdir($server, $user, $password, $directory, $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ******************* SFTP::touch ***************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanTouch()
    {
        $test = SFTP::touch($server, $user, $password, $remote_file, $content, $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** SFTP::upload ***************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanUpload()
    {
        $test = SFTP::upload($server, $user, $password, $local_file, $remote_file = '', $port);
        $this->assertTrue($test);
    }

    /* ************************************************* */
    /* ****************** SFTP::scandir **************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanScandir()
    {
        $test = SFTP::scandir($server, $user, $password, $path, $port);
        $this->assertTrue($test);
    }


    /* ************************************************* */
    /* ******************** SFTP::pwd ****************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanPwd()
    {
        $test = SFTP::pwd($server, $user, $password, $port);
        $this->assertTrue($test);
    }

}
