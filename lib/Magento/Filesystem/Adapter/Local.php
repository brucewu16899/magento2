<?php
/**
 * Adapter for local filesystem
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Filesystem_Adapter_Local implements
    Magento_Filesystem_AdapterInterface,
    Magento_Filesystem_Stream_FactoryInterface
{
    /**
     * Checks the file existence.
     *
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        return file_exists($key);
    }

    /**
     * Reads content of the file.
     *
     * @param string $key
     * @return string
     * @throws Magento_Filesystem_Exception
     */
    public function read($key)
    {
        $result = @file_get_contents($key);
        if (false === $result) {
            $message = sprintf('Failed to read contents of %s', $key);
            throw new Magento_Filesystem_Exception($message);
        }
        return $result;
    }

    /**
     * Writes content into the file.
     *
     * @param string $key
     * @param string $content
     * @return bool true if write was success
     * @throws Magento_Filesystem_Exception
     */
    public function write($key, $content)
    {
        $result = @file_put_contents($key, $content);
        if (false === $result) {
            $message = sprintf('Failed to write contents to %s', $key);
            throw new Magento_Filesystem_Exception($message);
        }
        return true;
    }

    /**
     * Renames the file.
     *
     * @param string $source
     * @param string $target
     * @return bool
     * @throws Magento_Filesystem_Exception
     */
    public function rename($source, $target)
    {
        $result = @rename($source, $target);
        if (!$result) {
            $message = sprintf('Failed to rename %s to %s', $source, $target);
            throw new Magento_Filesystem_Exception($message);
        }
        return true;
    }

    /**
     * Copy the file.
     *
     * @param string $source
     * @param string $target
     * @return bool
     * @throws Magento_Filesystem_Exception
     */
    public function copy($source, $target)
    {
        $result = @copy($source, $target);
        if (!$result) {
            $message = sprintf('Failed to copy %s to %s', $source, $target);
            throw new Magento_Filesystem_Exception($message);
        }
        return true;
    }

    /**
     * Calculates the MD5 hash of the file specified
     *
     * @param $key
     * @return string
     * @throws Magento_Filesystem_Exception
     */
    public function getFileMd5($key)
    {
        $hash = @md5_file($key);
        if (false === $hash) {
            $message = sprintf('Failed to get hash of %s', $key);
            throw new Magento_Filesystem_Exception($message);
        }
        return $hash;
    }

    /**
     * Deletes the file or directory recursively.
     *
     * @param string $key
     * @throws Magento_Filesystem_Exception
     */
    public function delete($key)
    {
        if (!file_exists($key) && !is_link($key)) {
            return;
        }

        if (is_file($key) || is_link($key)) {
            if (true !== @unlink($key)) {
                throw new Magento_Filesystem_Exception(sprintf('Failed to remove file %s', $key));
            }
            return;
        }

        $this->_deleteNestedKeys($key);

        if (true !== @rmdir($key)) {
            throw new Magento_Filesystem_Exception(sprintf('Failed to remove directory %s', $key));
        }
    }

    /**
     * Deletes all nested keys
     *
     * @param string $key
     * @throws Magento_Filesystem_Exception
     */
    protected function _deleteNestedKeys($key)
    {
        foreach ($this->getNestedKeys($key) as $nestedKey) {
            if (is_dir($nestedKey) && !is_link($nestedKey)) {
                if (true !== @rmdir($nestedKey)) {
                    throw new Magento_Filesystem_Exception(sprintf('Failed to remove directory %s', $nestedKey));
                }
            } else {
                // https://bugs.php.net/bug.php?id=52176
                if (defined('PHP_WINDOWS_VERSION_MAJOR') && is_dir($nestedKey)) {
                    if (true !== @rmdir($nestedKey)) {
                        throw new Magento_Filesystem_Exception(sprintf('Failed to remove file %s', $nestedKey));
                    }
                } else {
                    if (true !== @unlink($nestedKey)) {
                        throw new Magento_Filesystem_Exception(sprintf('Failed to remove file %s', $nestedKey));
                    }
                }
            }
        }
    }

    /**
     * Changes permissions of filesystem key
     *
     * @param string $key
     * @param int $permissions
     * @param bool $recursively
     * @throws Magento_Filesystem_Exception
     */
    public function changePermissions($key, $permissions, $recursively)
    {
        if (!@chmod($key, $permissions)) {
            throw new Magento_Filesystem_Exception(sprintf('Failed to change mode of %s', $key));
        }

        if (is_dir($key) && $recursively) {
            foreach ($this->getNestedKeys($key) as $nestedKey) {
                if (!@chmod($nestedKey, $permissions)) {
                    throw new Magento_Filesystem_Exception(sprintf('Failed to change mode of %s', $nestedKey));
                }
            }
        }
    }

    /**
     * Gets list of all nested keys
     *
     * @param string $key
     * @return array
     * @throws Magento_Filesystem_Exception
     */
    public function getNestedKeys($key)
    {
        $result = array();

        if (!is_dir($key)) {
            throw new Magento_Filesystem_Exception(sprintf('The directory "%s" does not exist.', $key));
        }

        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($key, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
        } catch (Exception $e) {
            $iterator = new EmptyIterator;
        }


        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            $result[] = $file->getPathname();
        }

        return $result;
    }

    /**
     * Gets list of all matched keys
     *
     * @param string $pattern
     * @return array
     * @throws Magento_Filesystem_Exception
     */
    public function searchKeys($pattern)
    {
        $result = @glob($pattern);
        if (false === $result) {
            $message = sprintf('Failed to resolve the file pattern %s', $pattern);
            throw new Magento_Filesystem_Exception($message);
        }
        return $result;
    }

    /**
     * Check if key is a directory.
     *
     * @param string $key
     * @return bool
     */
    public function isDirectory($key)
    {
        return is_dir($key);
    }

    /**
     * Check if key is a file.
     *
     * @param string $key
     * @return bool
     */
    public function isFile($key)
    {
        return is_file($key);
    }

    /**
     * Check if key exists and is writable
     *
     * @param string $key
     * @return bool
     */
    public function isWritable($key)
    {
        return is_writable($key);
    }

    /**
     * Check if key exists and is readable
     *
     * @param string $key
     * @return bool
     */
    public function isReadable($key)
    {
        return is_readable($key);
    }

    /**
     * Creates new directory
     *
     * @param string $key
     * @param int $mode
     * @throws Magento_Filesystem_Exception
     */
    public function createDirectory($key, $mode)
    {
        if (!@mkdir($key, $mode, true)) {
            throw new Magento_Filesystem_Exception(sprintf('Failed to create %s', $key));
        }
    }

    /**
     * Touches a file
     *
     * @param string $key
     * @param int|null $fileModificationTime
     * @throws Magento_Filesystem_Exception
     */
    public function touch($key, $fileModificationTime = null)
    {
        if ($fileModificationTime === null) {
            $success = @touch($key);
        } else {
            $success = @touch($key, $fileModificationTime);
        }
        if (!$success) {
            throw new Magento_Filesystem_Exception(sprintf('Failed to touch %s', $key));
        }
    }

    /**
     * Get file modification time.
     *
     * @param string $key
     * @return int
     * @throws Magento_Filesystem_Exception
     */
    public function getMTime($key)
    {
        $mtime = @filemtime($key);
        if (false === $mtime) {
            throw new Magento_Filesystem_Exception(sprintf('Failed to get modification time of %s', $key));
        }
        return $mtime;
    }

    /**
     * Get file size.
     *
     * @param string $key
     * @return int
     * @throws Magento_Filesystem_Exception
     */
    public function getFileSize($key)
    {
        $size = @filesize($key);
        if (false === $size) {
            throw new Magento_Filesystem_Exception(sprintf('Failed to get file size of %s', $key));
        }
        return $size;
    }

    /**
     * Create stream object
     *
     * @param string $path
     * @return Magento_Filesystem_Stream_Local
     */
    public function createStream($path)
    {
        return new Magento_Filesystem_Stream_Local($path);
    }
}
