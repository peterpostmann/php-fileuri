<?php

/**
 * Returns a file uri from a (relative) path
 *
 * @param string $path
 * @param string $basePath      Used if path is relative

 * @return string       Returns false if relative path is given but no base path
 */
function fileuri($path, $basePath = null)
{
    // Check protocol
    if (!preg_match('/^.+\:\/\/.*/', $path)) {
        // Fix relative path
        if (preg_match('#^(\w|\.)[^\:]#', $path)) {
            if (!is_string($basePath)) {
                return false;
            }
            $basePath = rtrim($basePath, '/');
            $basePath = rtrim($basePath, '\\');
            $path     = $basePath.'/'.$path;
        }
        
        // Fix blackslashes
        $path = str_replace('\\', '/', $path);

        // Fix Windows drive (C:/path --> /C:/path)
        $path = preg_replace("#^(\w:)#", "/$1", $path);
        
        // Fix network path (//host/path --> host/path)
        $path = preg_replace("#^(\/\/)#", "", $path);

        // Fix '//' or '/./' or '/foo/../' with '/'
        $pattern = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for ($n=1; $n>0; $path = preg_replace($pattern, '/', $path, -1, $n)) {
        }
    
        // Encode Windows charachters which are also rfc3986 gen-delims
        $path = preg_replace_callback(
            '/[ #%\{\}\^`]/u',
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $path
        );
    
        // Add protocol
        $path = 'file://'.$path;
    }
    
    return $path;
}
