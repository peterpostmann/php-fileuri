<?php

use function peterpostmann\fileuri;

class fileuriTest extends \PHPUnit_Framework_TestCase
{
    public function paths()
    {
        return [
            
            // Examples from php.net (http://php.net/manual/en/wrappers.file.php)
            ['/path/to/file.ext',                               'file:///path/to/file.ext'],
            ['relative/path/to/file.ext',                       false],
            ['fileInCwd.ext',                                   false],
            ['C:/path/to/winfile.ext',                          'file:///C:/path/to/winfile.ext'],
            ['C:\\path\\to\\winfile.ext',                       'file:///C:/path/to/winfile.ext'],
            ['\\\\smbserver\\share\\path\\to\\winfile.ext',     'file://smbserver/share/path/to/winfile.ext'],
            ['file:///path/to/file.ext',                        'file:///path/to/file.ext'],
                        
            // Unix network path
            ['//10.0.0.1/smb/path/file.txt',                    'file://10.0.0.1/smb/path/file.txt'],
            
            // Directory traversal
            ['/home/user/abc/../file.txt',                      'file:///home/user/file.txt'],
            
            // File uri is unchanged
            ['file:///path/to/abc/../file.ext',                 'file:///path/to/abc/../file.ext'],
            
            // Encode special Characters (https://blogs.msdn.microsoft.com/ie/2006/12/06/file-uris-in-windows/)
            ['\\\\laptop\\My Documents\\FileSchemeURIs.doc',                    'file://laptop/My%20Documents/FileSchemeURIs.doc'],
            ['C:\\Documents and Settings\\user\\FileSchemeURIs.doc',            'file:///C:/Documents%20and%20Settings/user/FileSchemeURIs.doc'],
            ['C:\\Program Files\\Music\\Web Sys\\main.html?REQUEST=RADIO',      'file:///C:/Program%20Files/Music/Web%20Sys/main.html?REQUEST=RADIO'],
            ['\\\\applib\\products\\a-b\\abc_9/4148.920a\\media\\start.swf',    'file://applib/products/a-b/abc_9/4148.920a/media/start.swf'],
            ['\\\\applib\\products\\a-b\\abc_9/4148.920a\\media\\start.swf',    'file://applib/products/a-b/abc_9/4148.920a/media/start.swf'],
            ['C:\\exampleㄓ.txt',                                               'file:///C:/exampleㄓ.txt'],
            ['C:\ #%{}^`.txt',                                                  'file:///C:/%20%23%25%7B%7D%5E%60.txt'],
            
        ];
    }

    /**
     * @dataProvider paths
     */
    function test_uri_matches_expected_results($path, $uri)
    {
        $this->assertSame($uri, fileuri($path));
    }
    
    public function relative_paths()
    {
        return [
            ['file.txt',                        null,               false],
            ['file.txt',                        '',                 'file:///file.txt'],
            ['file.txt',                        '/',                'file:///file.txt'],
            ['relative/path/to/file.ext',       'C:\\',             'file:///C:/relative/path/to/file.ext'],
            ['relative/path/to/file.ext',       'C:',               'file:///C:/relative/path/to/file.ext'],
            ['fileInCwd.ext',                   '/home/user/',      'file:///home/user/fileInCwd.ext'],
            ['fileInCwd.ext',                   '/home/user',       'file:///home/user/fileInCwd.ext'],
            ['file.txt',                        '\\\\server',       'file://server/file.txt'],
            ['file.txt',                        '\\\\server\\',     'file://server/file.txt'],
            ['abc/../file.txt',                 '/home/user/',      'file:///home/user/file.txt'],
            ['../file.txt',                     '/home/user/abc',   'file:///home/user/file.txt'],
        ];
    }
    
    /**
     * @dataProvider relative_paths
     */
    function test_uri_matches_expected_results_with_given_basePath($path, $basePath, $uri)
    {
        $this->assertSame($uri, fileuri($path, $basePath));
    }
    
    public function file_list()
    {
        return [
            ['fixtures/file.txt'],
            ['fixtures/ #%{}^`.txt'],
            ['fixtures/exampleㄓ.txt'],
            ['fixtures/My Documents/file.txt'],
        ];
    }
    
    /**
     * @dataProvider file_list
     */
    function test_file_get_contents_with_fileuri($path)
    {
        $this->assertSame('test', file_get_contents(urldecode(fileuri($path, __DIR__))));
    }
    
    /**
     * @dataProvider file_list
     */
    function test_file_get_contents_with_relative_paths($path)
    {
        $this->assertSame('test', file_get_contents(urldecode(fileuri('tests/'.$path))));
    }
}
