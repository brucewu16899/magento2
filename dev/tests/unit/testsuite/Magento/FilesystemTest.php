<?php
/**
 * Unit Test for Magento_Filesystem
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FilesystemTest extends PHPUnit_Framework_TestCase
{
    public function testSetWorkingDirectory()
    {
        $filesystem = new Magento_Filesystem($this->_getDefaultAdapterMock());
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertAttributeEquals('/tmp', '_workingDirectory', $filesystem);
    }

    /**
     * @expectedException InvalidArgumentException
     * @exceptedExceptionMessage Working directory "/tmp" does not exists
     */
    public function testSetWorkingDirectoryException()
    {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(false));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
    }

    /**
     * @dataProvider allowCreateDirectoriesDataProvider
     * @param bool $allow
     * @param int $mode
     */
    public function testSetIsAllowCreateDirectories($allow, $mode)
    {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $filesystem = new Magento_Filesystem($adapterMock);
        $this->assertSame($filesystem, $filesystem->setIsAllowCreateDirectories($allow, $mode));
        $this->assertAttributeEquals($allow, '_isAllowCreateDirs', $filesystem);
        if (!$mode) {
            $mode = 0777;
        }
        $this->assertAttributeEquals($mode, '_newDirPermissions', $filesystem);
    }

    /**
     * @return array
     */
    public function allowCreateDirectoriesDataProvider()
    {
        return array(
            array(true, 0644),
            array(false, null)
        );
    }

    /**
     * @dataProvider twoFilesOperationsValidDataProvider
     *
     * @param string $method
     * @param string $checkMethod
     * @param string $source
     * @param string $target
     * @param string|null $workingDirectory
     * @param string|null $targetDir
     */
    public function testTwoFilesOperation($method, $checkMethod, $source, $target, $workingDirectory = null,
        $targetDir = null
    ) {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->exactly(2))
            ->method('isDirectory')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method($checkMethod)
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method($method)
            ->with($source, $target);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->$method($source, $target, $workingDirectory, $targetDir);
    }

    /**
     * @return array
     */
    public function twoFilesOperationsValidDataProvider()
    {
        return array(
            'copy both tmp' => array('copy', 'isFile', '/tmp/path/file001.log', '/tmp/path/file001.bak'),
            'move both tmp' => array('rename', 'exists', '/tmp/path/file001.log', '/tmp/path/file001.bak'),
            'copy both tmp #2' => array('copy', 'isFile', '/tmp/path/file001.log', '/tmp/path/file001.bak', '/tmp'),
            'move both tmp #2' => array('rename', 'exists', '/tmp/path/file001.log', '/tmp/path/file001.bak', '/tmp'),
            'copy different'
                => array('copy', 'isFile', '/tmp/path/file001.log', '/storage/file001.bak', null, '/storage'),
            'move different'
                => array('rename', 'exists', '/tmp/path/file001.log', '/storage/file001.bak', null, '/storage'),
            'copy different #2'
                => array('copy', 'isFile', '/tmp/path/file001.log', '/storage/file001.bak', '/tmp', '/storage'),
            'move different #2'
                => array('rename', 'exists', '/tmp/path/file001.log', '/storage/file001.bak', '/tmp', '/storage'),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     * @dataProvider twoFilesOperationsInvalidDataProvider
     * @param string $method
     * @param string $source
     * @param string $destination
     * @param string|null $workingDirectory
     * @param string|null $targetDir
     */
    public function testTwoFilesOperationsIsolationException($method, $source, $destination, $workingDirectory = null,
        $targetDir = null
    ) {
        $adapterMock = $this->_getDefaultAdapterMock();
        $adapterMock->expects($this->never())
            ->method($method);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->$method($source, $destination, $workingDirectory, $targetDir);
    }

    /**
     * @return array
     */
    public function twoFilesOperationsInvalidDataProvider()
    {
        return array(
            'copy first path invalid' => array('copy', '/tmp/../etc/passwd', '/tmp/path001'),
            'copy first path invalid #2' => array('copy', '/tmp/../etc/passwd', '/tmp/path001', '/tmp'),
            'copy second path invalid' => array('copy', '/tmp/uploaded.txt', '/tmp/../etc/passwd'),
            'copy both path invalid' => array('copy', '/tmp/../etc/passwd', '/tmp/../dev/null'),
            'rename first path invalid' => array('rename', '/tmp/../etc/passwd', '/tmp/path001'),
            'rename first path invalid #2' => array('rename', '/tmp/../etc/passwd', '/tmp/path001', '/tmp'),
            'rename second path invalid' => array('rename', '/tmp/uploaded.txt', '/tmp/../etc/passwd'),
            'rename both path invalid' => array('rename', '/tmp/../etc/passwd', '/tmp/../dev/null'),
            'copy target path invalid' => array('copy', '/tmp/passwd', '/etc/../dev/null', null, '/etc'),
            'rename target path invalid' => array('rename', '/tmp/passwd', '/etc/../dev/null', null, '/etc'),
            'copy target path invalid #2' => array('copy', '/tmp/passwd', '/etc/../dev/null', '/tmp', '/etc'),
            'rename target path invalid #2' => array('rename', '/tmp/passwd', '/etc/../dev/null', '/tmp', '/etc'),
        );
    }

    public function testEnsureDirectoryExists()
    {
        $dir = '/tmp/path';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->at(0))
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->at(1))
            ->method('isDirectory')
            ->with($dir)
            ->will($this->returnValue(true));
        $adapterMock->expects($this->exactly(2))
            ->method('isDirectory');
        $adapterMock->expects($this->never())
            ->method('createDirectory');
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->ensureDirectoryExists($dir, 0644);
    }

    /**
     * @expectedException Magento_Filesystem_Exception
     * @expectedExceptionMessage Directory '/tmp/path' doesn't exist.
     */
    public function testEnsureDirectoryExistsException()
    {
        $dir = '/tmp/path';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->at(0))
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->at(1))
            ->method('isDirectory')
            ->with($dir)
            ->will($this->returnValue(false));
        $adapterMock->expects($this->exactly(2))
            ->method('isDirectory');
        $adapterMock->expects($this->never())
            ->method('createDirectory');
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->ensureDirectoryExists($dir, 0644);
    }

    public function testEnsureDirectoryExistsNoDir()
    {
        $dir = '/tmp/path1/path2';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->at(0))
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->at(1))
            ->method('isDirectory')
            ->with($dir)
            ->will($this->returnValue(false));
        $adapterMock->expects($this->at(2))
            ->method('isDirectory')
            ->with('/tmp/path1')
            ->will($this->returnValue(false));
        $adapterMock->expects($this->at(3))
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->exactly(4))
            ->method('isDirectory');
        $adapterMock->expects($this->at(4))
            ->method('createDirectory')
            ->with('/tmp/path1');
        $adapterMock->expects($this->at(5))
            ->method('createDirectory')
            ->with('/tmp/path1/path2');
        $adapterMock->expects($this->exactly(2))
            ->method('createDirectory');
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->setIsAllowCreateDirectories(true);
        $filesystem->ensureDirectoryExists($dir, 0644);
    }

    /**
     * @dataProvider allowCreateDirsDataProvider
     * @param bool $allowCreateDirs
     */
    public function testTouch($allowCreateDirs)
    {
        $validPath = '/tmp/path/file.txt';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->exactly(2))
            ->method('isDirectory')
            ->will($this->returnValue(true));

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setIsAllowCreateDirectories($allowCreateDirs);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->touch($validPath);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     */
    public function testTouchIsolation()
    {
        $filesystem = new Magento_Filesystem($this->_getDefaultAdapterMock());
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->touch('/etc/passwd');
    }

    /**
     * @return array
     */
    public function allowCreateDirsDataProvider()
    {
        return array(array(true), array(false));
    }

    public function testCreateStreamCustom()
    {
        $path = '/tmp/test.txt';
        $streamMock = $this->getMockBuilder('Magento_Filesystem_Stream_Local')
            ->disableOriginalConstructor()
            ->getMock();
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_Adapter_Local')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method('createStream')
            ->with($path)
            ->will($this->returnValue($streamMock));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertInstanceOf('Magento_Filesystem_Stream_Local', $filesystem->createStream($path));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     */
    public function testCreateStreamIsolation()
    {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_Adapter_Local')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->createStream('/tmp/../etc/test.txt');
    }

    /**
     * @expectedException Magento_Filesystem_Exception
     * @expectedExceptionMessage Filesystem doesn't support streams.
     */
    public function testCreateStreamException()
    {
        $filesystem = new Magento_Filesystem($this->_getDefaultAdapterMock());
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->createStream('/tmp/test.txt');
    }

    /**
     * @dataProvider modeDataProvider
     * @param string|Magento_Filesystem_Stream_Mode $mode
     */
    public function testCreateAndOpenStream($mode)
    {
        $path = '/tmp/test.txt';
        $streamMock = $this->getMockBuilder('Magento_Filesystem_Stream_Local')
            ->disableOriginalConstructor()
            ->getMock();
        $streamMock->expects($this->once())
            ->method('open');
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_Adapter_Local')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method('createStream')
            ->with($path)
            ->will($this->returnValue($streamMock));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertInstanceOf('Magento_Filesystem_Stream_Local', $filesystem->createAndOpenStream($path, $mode));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Wrong mode parameter
     */
    public function testCreateAndOpenStreamException()
    {
        $path = '/tmp/test.txt';
        $streamMock = $this->getMockBuilder('Magento_Filesystem_Stream_Local')
            ->disableOriginalConstructor()
            ->getMock();
        $streamMock->expects($this->never())
            ->method('open');
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_Adapter_Local')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method('createStream')
            ->with($path)
            ->will($this->returnValue($streamMock));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertInstanceOf('Magento_Filesystem_Stream_Local',
            $filesystem->createAndOpenStream($path, new stdClass()));
    }

    /**
     * @return array
     */
    public function modeDataProvider()
    {
        return array(
            array('r'),
            array(new Magento_Filesystem_Stream_Mode('w'))
        );
    }

    /**
     * @dataProvider adapterMethods
     * @param string $method
     * @param string $adapterMethod
     * @param array|null $params
     */
    public function testAdapterMethods($method, $adapterMethod, array $params = null)
    {
        $validPath = '/tmp/path/file.txt';
        $adapterMock = $this->_getDefaultAdapterMock();
        $adapterMock->expects($this->once())
            ->method($adapterMethod)
            ->with($validPath);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $params = (array)$params;
        array_unshift($params, $validPath);
        call_user_func_array(array($filesystem, $method), $params);
    }

    /**
     * @return array
     */
    public function adapterMethods()
    {
        return array(
            'exists' => array('has', 'exists'),
            'delete' => array('delete', 'delete'),
            'isFile' => array('isFile', 'isFile'),
            'isWritable' => array('isWritable', 'isWritable'),
            'isReadable' => array('isReadable', 'isReadable'),
            'getNestedKeys' => array('getNestedKeys', 'getNestedKeys'),
            'changePermissions' => array('changePermissions', 'changePermissions', array(0777, true)),
            'exists #2' => array('has', 'exists', array('/tmp')),
            'delete #2' => array('delete', 'delete', array('/tmp')),
            'isFile #2' => array('isFile', 'isFile', array('/tmp')),
            'isWritable #2' => array('isWritable', 'isWritable', array('/tmp')),
            'isReadable #2' => array('isReadable', 'isReadable', array('/tmp')),
            'getNestedKeys #2' => array('getNestedKeys', 'getNestedKeys', array('/tmp')),
            'changePermissions #2' => array('changePermissions', 'changePermissions', array(0777, true, '/tmp')),
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     * @dataProvider adapterIsolationMethods
     * @param string $method
     * @param string $adapterMethod
     * @param array|null $params
     */
    public function testIsolationException($method, $adapterMethod, array $params = null)
    {
        $invalidPath = '/tmp/../etc/passwd';
        $adapterMock = $this->_getDefaultAdapterMock();
        $adapterMock->expects($this->never())
            ->method($adapterMethod);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $params = (array)$params;
        array_unshift($params, $invalidPath);
        call_user_func_array(array($filesystem, $method), $params);
    }

    /**
     * @return array
     */
    public function adapterIsolationMethods()
    {
        return $this->adapterMethods()
            + array(
                'read' => array('read', 'read'),
                'read #2' => array('read', 'read', array('/tmp')),
                'createDirectory' => array('createDirectory', 'createDirectory', array(0777)),
                'createDirectory #2' => array('createDirectory', 'createDirectory', array(0777, '/tmp')),
            );
    }

    /**
     * @dataProvider workingDirDataProvider
     * @param string|null $workingDirectory
     */
    public function testRead($workingDirectory)
    {
        $validPath = '/tmp/path/file.txt';
        $adapterMock = $this->_getDefaultAdapterMock();
        $adapterMock->expects($this->once())
            ->method('isFile')
            ->with($validPath)
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method('read')
            ->with($validPath);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->read($validPath, $workingDirectory);
    }

    /**
     * @dataProvider workingDirDataProvider
     * @param string|null $workingDirectory
     */
    public function testCreateDirectory($workingDirectory)
    {
        $validPath = '/tmp/path';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->exactly(2))
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method('createDirectory')
            ->with($validPath);

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->createDirectory($validPath, 0777, $workingDirectory);
    }

    /**
     * @dataProvider workingDirDataProvider
     * @param string|null $workingDirectory
     */
    public function testWrite($workingDirectory)
    {
        $validPath = '/tmp/path/file.txt';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->at(0))
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->at(1))
            ->method('isDirectory')
            ->with('/tmp/path')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->exactly(2))
            ->method('isDirectory');
        $adapterMock->expects($this->once())
            ->method('write')
            ->with($validPath, 'TEST TEST');

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->write($validPath, 'TEST TEST', $workingDirectory);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     * @dataProvider workingDirDataProvider
     * @param string|null $workingDirectory
     */
    public function testWriteIsolation($workingDirectory)
    {
        $invalidPath = '/tmp/../path/file.txt';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->never())
            ->method('write');

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $filesystem->write($invalidPath, 'TEST TEST', $workingDirectory);
    }

    /**
     * @return array
     */
    public function workingDirDataProvider()
    {
        return array(
            array(null), array('/tmp')
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage "/tmp/test/file.txt" does not exists
     * @dataProvider methodsWithFileChecksDataProvider
     * @param string $method
     * @param array|null $params
     */
    public function testFileChecks($method, array $params = null)
    {
        $path = '/tmp/test/file.txt';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method('exists')
            ->with($path)
            ->will($this->returnValue(false));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $params = (array)$params;
        array_unshift($params, $path);
        call_user_func_array(array($filesystem, $method), $params);
    }

    /**
     * @return array
     */
    public function methodsWithFileChecksDataProvider()
    {
        return array(
            'delete' => array('delete'),
            'rename' => array('rename', array('/tmp/file001.txt'))
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage "/tmp/test/file.txt" does not exists
     * @dataProvider methodsWithPathChecksDataProvider
     * @param string $method
     * @param array|null $params
     */
    public function testPathChecks($method, array $params = null)
    {
        $path = '/tmp/test/file.txt';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->once())
            ->method('isFile')
            ->with($path)
            ->will($this->returnValue(false));
        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $params = (array)$params;
        array_unshift($params, $path);
        call_user_func_array(array($filesystem, $method), $params);
    }

    /**
     * @return array
     */
    public function methodsWithPathChecksDataProvider()
    {
        return array(
            'read' => array('read'),
            'copy' => array('copy', array('/tmp/file001.txt')),
        );
    }

    /**
     * Test isDirectory
     *
     * @dataProvider workingDirDataProvider
     * @param string|null $workingDirectory
     */
    public function testIsDirectory($workingDirectory)
    {
        $validPath = '/tmp/path';
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->at(0))
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->at(1))
            ->method('isDirectory')
            ->with($validPath)
            ->will($this->returnValue(true));
        $adapterMock->expects($this->exactly(2))
            ->method('isDirectory');

        $filesystem = new Magento_Filesystem($adapterMock);
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertTrue($filesystem->isDirectory($validPath, $workingDirectory));
    }

    /**
     * Test isDirectory isolation
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid path
     * @dataProvider workingDirDataProvider
     * @param string|null $workingDirectory
     */
    public function testIsDirectoryIsolation($workingDirectory)
    {
        $validPath = '/tmp/../etc/passwd';
        $filesystem = new Magento_Filesystem($this->_getDefaultAdapterMock());
        $filesystem->setWorkingDirectory('/tmp');
        $this->assertTrue($filesystem->isDirectory($validPath, $workingDirectory));
    }

    /**
     * @dataProvider absolutePathDataProvider
     * @param string $path
     * @param string $expected
     */
    public function testGetAbsolutePath($path, $expected)
    {
        $this->assertEquals($expected, Magento_Filesystem::getAbsolutePath($path));
    }

    /**
     * @return array
     */
    public function absolutePathDataProvider()
    {
        return array(
            array('/tmp/../file.txt', '/file.txt'),
            array('/tmp/../etc/mysql/file.txt', '/etc/mysql/file.txt'),
            array('/tmp/../file.txt', '/file.txt'),
            array('/tmp/./file.txt', '/tmp/file.txt'),
            array('/tmp/./../file.txt', '/file.txt'),
            array('/tmp/../../../file.txt', '/file.txt'),
            array('../file.txt', '/file.txt'),
            array('/../file.txt', '/file.txt'),
            array('/tmp/path/file.txt', '/tmp/path/file.txt'),
            array('/tmp/path', '/tmp/path'),
            array('C:\\Windows', 'C:/Windows'),
            array('C:\\Windows\\system32\\..', 'C:/Windows'),
        );
    }

    /**
     * @dataProvider pathDataProvider
     * @param array $parts
     * @param string $expected
     * @param bool $isAbsolute
     */
    public function testGetPathFromArray(array $parts, $expected, $isAbsolute)
    {
        $this->assertEquals($expected, Magento_Filesystem::getPathFromArray($parts, $isAbsolute));
    }

    /**
     * @return array
     */
    public function pathDataProvider()
    {
        return array(
            array(array('etc', 'mysql', 'my.cnf'), '/etc/mysql/my.cnf',true),
            array(array('etc', 'mysql', 'my.cnf'), 'etc/mysql/my.cnf', false),
            array(array('C:', 'Windows', 'my.cnf'), 'C:/Windows/my.cnf', false),
            array(array('C:', 'Windows', 'my.cnf'), 'C:/Windows/my.cnf', true),
        );
    }

    /**
     * @dataProvider pathDataProvider
     * @param array $expected
     * @param string $path
     */
    public function testGetPathAsArray(array $expected, $path)
    {
        $this->assertEquals($expected, Magento_Filesystem::getPathAsArray($path));
    }

    /**
     * @dataProvider isAbsolutePathDataProvider
     * @param bool $isReal
     * @param string $path
     */
    public function testIsAbsolutePath($isReal, $path)
    {
        $this->assertEquals($isReal, Magento_Filesystem::isAbsolutePath($path));
    }

    /**
     * @return array
     */
    public function isAbsolutePathDataProvider()
    {
        return array(
            array(true, '/tmp/file.txt'),
            array(false, '/tmp/../etc/mysql/my.cnf'),
            array(false, '/tmp/../tmp/file.txt')
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Path must contain at least one node
     */
    public function testGetPathFromArrayException()
    {
        Magento_Filesystem::getPathFromArray(array());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDefaultAdapterMock()
    {
        $adapterMock = $this->getMockBuilder('Magento_Filesystem_AdapterInterface')
            ->getMock();
        $adapterMock->expects($this->once())
            ->method('isDirectory')
            ->with('/tmp')
            ->will($this->returnValue(true));
        $adapterMock->expects($this->any())
            ->method('exists')
            ->will($this->returnValue(true));
        return $adapterMock;
    }
}
