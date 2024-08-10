<?php

namespace PHPCore\SFTP;

use Exception;
use PHPCore\Exceptions\SFTPException;
use phpseclib3\Net\SFTP as SecFtp;


/**
 *
 */
class SFTP
{
	const port = 22;

	const timeout = 10;

	/**
	 * Login to SFTP server
	 * @throws SFTPException
	 */
	private static function login(
		string $server,
		string $user,
		string $password,
		int    $port = self::port,
		int    $timeout = self::timeout
	): SecFtp
	{
		$sftp = new SecFtp($server, $port, $timeout);
		if (!$sftp->login($user, $password)) {
			throw new SFTPException('Login failed');
		}
		return $sftp;
	}

	/**
	 * Test SFTP connection
	 * @throws SFTPException
	 */
	public static function test(
		string $server,
		string $user,
		string $password,
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		return !empty(SFTP::login($server, $user, $password, $port, $timeout));
	}

	/**
	 * Check if a file exists on SFTP Server
	 * @throws SFTPException
	 */
	public static function isFile(
		string $server,
		string $user,
		string $password,
		string $remoteFile,
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		return !empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))
			&& $sftp->is_file($remoteFile);
	}

	/**
	 * Delete a file on remote SFTP server
	 * @throws SFTPException
	 */
	public static function delete(
		string $server,
		string $user,
		string $password,
		string $remoteFile,
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		return !empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))
			&& $sftp->is_file($remoteFile)
			&& $sftp->delete($remoteFile);
	}

	/**
	 * Recursively deletes files and folder in given directory
	 *
	 * If remotePath ends with a slash delete folder content
	 * otherwise delete folder itself
	 *
	 * @throws SFTPException
	 */
	public static function rmdir(
		string $server,
		string $user,
		string $password,
		string $remotePath,
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		return !empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))
			&& SFTP::cleanDir($remotePath, $sftp)
			&& !str_ends_with($remotePath, '/')
			&& $sftp->rmdir($remotePath);
	}

	/**
	 * Recursively deletes files and folder
	 */
	private static function cleanDir(
		string $remotePath,
		SecFtp $sftp
	): bool
	{
		$clean = false;

		$to_delete = 0;
		$deleted = 0;

		$list = $sftp->nlist($remotePath);
		foreach ($list as $element) {
			if ($element !== '.' && $element !== '..') {
				$to_delete++;

				if ($sftp->is_dir($remotePath . DIRECTORY_SEPARATOR . $element)) {
					# Empty directory
					SFTP::cleanDir($remotePath . DIRECTORY_SEPARATOR . $element, $sftp);

					# Delete empty directory
					if ($sftp->rmdir($remotePath . DIRECTORY_SEPARATOR . $element)) {
						$deleted++;
					}
				} else {
					# Delete file
					if ($sftp->delete($remotePath . DIRECTORY_SEPARATOR . $element)) {
						$deleted++;
					}
				}
			}
		}

		if ($deleted === $to_delete) {
			$clean = true;
		}

		return $clean;
	}

	/**
	 * Recursively copy files and folders on remote SFTP server
	 *
	 * If localPath ends with a slash upload folder content
	 * otherwise upload folder itself
	 */
	public static function uploadDir(
		string $server,
		string $user,
		string $password,
		string $localPath,
		string $remotePath,
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		$uploaded = false;

		try {
			# Remove trailing slash
			$remotePath = rtrim($remotePath, DIRECTORY_SEPARATOR);

			if (!empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))) {
				# If localPath do not ends with /
				if (!str_ends_with($localPath, '/')) {
					# Create fisrt level directory on remote filesystem
					$remotePath = $remotePath . DIRECTORY_SEPARATOR . basename($localPath);
					$sftp->mkdir($remotePath);
				}

				if ($sftp->is_dir($remotePath)) {
					$uploaded = SFTP::uploadAll($sftp, $localPath, $remotePath);
				}
			}
		} catch (Exception $e) {
			throw new SFTPException("SFTP::uploadDir : " . $e->getMessage());
		}

		return $uploaded;
	}

	/**
	 * Recursively copy files and folders on remote SFTP server
	 * @throws SFTPException
	 */
	private static function uploadAll(
		SecFtp $sftp,
		string $localDir,
		string $remoteDir
	): bool
	{
		$uploadedAll = false;
		try {
			# Create remote directory
			if (!$sftp->is_dir($remoteDir)) {
				if (!$sftp->mkdir($remoteDir)) {
					throw new SFTPException('Cannot create remote directory.', 1);
				}
			}

			$toUpload = 0;
			$uploaded = 0;

			$d = dir($localDir);
			while ($file = $d->read()) {
				if ($file != '.' && $file != '..') {
					$toUpload++;

					if (is_dir($localDir . DIRECTORY_SEPARATOR . $file)) {
						# Upload directory
						# Recursive part
						if (SFTP::uploadAll(
							$sftp,
							$localDir . DIRECTORY_SEPARATOR . $file,
							$remoteDir . DIRECTORY_SEPARATOR . $file)) {
							$uploaded++;
						}
					} else {
						# Upload file
						if ($sftp->put(
							$remoteDir . DIRECTORY_SEPARATOR . $file,
							$localDir . DIRECTORY_SEPARATOR . $file,
							SecFtp::SOURCE_LOCAL_FILE)) {
							$uploaded++;
						}
					}
				}
			}
			$d->close();

			if ($toUpload === $uploaded) {
				$uploadedAll = true;
			}
		} catch (Exception $e) {
			throw new SFTPException($e->getMessage(), $e->getCode(), $e);
		}

		return $uploadedAll;
	}

	/**
	 * Download a file from remote SFTP server
	 * @throws SFTPException
	 */
	public static function download(
		string $server,
		string $user,
		string $password,
		string $remoteFile,
		string $localFile,
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		return !empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))
			&& $sftp->get($remoteFile, $localFile);
	}

	/**
	 * Download a directory from remote SFTP server
	 *
	 * If remoteDir ends with a slash download folder content
	 * otherwise download folder itself
	 * @throws SFTPException
	 */
	public static function downloadDir(
		string $server,
		string $user,
		string $password,
		string $remoteDir,
		string $localDir,
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		$downloaded = false;

		try {
			if (is_dir($localDir) && is_writable($localDir)) {
				if (!empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))) {
					# If remoteDir do not ends with /
					if (!str_ends_with($remoteDir, '/')) {
						# Create fisrt level directory on local filesystem
						$localDir = rtrim($localDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . basename($remoteDir);
						mkdir($localDir);
					}

					# Remove trailing slash
					$localDir = rtrim($localDir, DIRECTORY_SEPARATOR);

					# Recursive part
					$downloaded = SFTP::downloadAll($sftp, $remoteDir, $localDir);
				}
			} else {
				throw new Exception("Local directory does not exist or is not writable", 1);
			}
		} catch (Exception $e) {
			throw new SFTPException('SFTP::downloadDir : ' . $e->getMessage());
		}

		return $downloaded;
	}

	/**
	 * Recursive function to download remote files
	 * @throws SFTPException
	 */
	private static function downloadAll(
		SecFtp $sftp,
		string $remoteDir,
		string $localDir
	): bool
	{
		$downloadAll = false;

		try {
			if ($sftp->is_dir($remoteDir)) {
				$files = $sftp->nlist($remoteDir);
				if ($files !== false) {
					$toDownload = 0;
					$downloaded = 0;
					# do this for each file in the remote directory
					foreach ($files as $file) {
						// throw new SFTPException('file : ' . $file);
						# To prevent an infinite loop
						if ($file != "." && $file != "..") {
							$toDownload++;
							# do the following if it is a directory
							if ($sftp->is_dir($remoteDir . DIRECTORY_SEPARATOR . $file)) {
								# Create directory on local filesystem
								mkdir($localDir . DIRECTORY_SEPARATOR . basename($file));

								# Recursive part
								if (SFTP::downloadAll($sftp, $remoteDir . DIRECTORY_SEPARATOR . $file, $localDir . DIRECTORY_SEPARATOR . basename($file))) {
									$downloaded++;
								}
							} else {
								# Download files
								if ($sftp->get($remoteDir . DIRECTORY_SEPARATOR . $file, $localDir . DIRECTORY_SEPARATOR . basename($file))) {
									$downloaded++;
								}
							}
						}
					}

					# Check all files and folders have been downloaded
					if ($toDownload === $downloaded) {
						$downloadAll = true;
					}
				} else {
					# Nothing to download
					$downloadAll = true;
				}
			}
		} catch (Exception $e) {
			throw new SFTPException('SFTP::downloadAll : ' . $e->getMessage());
		}

		return $downloadAll;
	}

	/**
	 * Rename a file on remote SFTP server
	 * @throws SFTPException
	 */
	public static function rename(
		string $server,
		string $user,
		string $password,
		string $currentFilename,
		string $newFilename,
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		return !empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))
			&& $sftp->rename($currentFilename, $newFilename);
	}

	/**
	 * Create a directory on remote SFTP server
	 * @throws SFTPException
	 */
	public static function mkdir(
		string $server,
		string $user,
		string $password,
		string $directory,
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		return !empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))
			&& $sftp->mkdir($directory, true);
	}

	/**
	 * Create and fill in a file on remote SFTP server
	 * @throws SFTPException
	 */
	public static function touch(
		string $server,
		string $user,
		string $password,
		string $remoteFile,
		string $content = '',
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		$created = false;

		try {
			if (!empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))) {
				# Create temp file
				$localFile = tmpfile();
				fwrite($localFile, $content);
				fseek($localFile, 0);
				if ($sftp->put($remoteFile, $localFile, SecFtp::SOURCE_LOCAL_FILE)) {
					$created = true;
				}
				fclose($localFile);
			}
		} catch (Exception $e) {
			throw new SFTPException("SFTP::touch : " . $e->getMessage());
		}

		return $created;
	}

	/**
	 * Upload a file on SFTP server
	 * @throws SFTPException
	 */
	public static function upload(
		string $server,
		string $user,
		string $password,
		string $localFile,
		string $remoteFile,
		int    $port = self::port,
		int    $timeout = self::timeout
	): bool
	{
		return !empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))
			&& $sftp->put($remoteFile, $localFile, SecFtp::SOURCE_LOCAL_FILE);
	}

	/**
	 * List files in given directory on SFTP server
	 * @throws SFTPException
	 */
	public static function scandir(
		string $server,
		string $user,
		string $password,
		string $path,
		int    $port = self::port,
		int    $timeout = self::timeout
	): array|false
	{
		$files = false;

		if (!empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))) {
			$files = $sftp->nlist($path);
		}
		if (is_array($files)) {
			# Removes . and ..
			$files = array_diff($files, ['.', '..']);
		}

		return $files;
	}

	/**
	 * Get default login SFTP directory aka pwd
	 * @throws SFTPException
	 */
	public static function pwd(
		string $server,
		string $user,
		string $password,
		int    $port = self::port,
		int    $timeout = self::timeout
	): string
	{
		return empty($sftp = SFTP::login($server, $user, $password, $port, $timeout))
			? false
			: $sftp->pwd();
	}
}
