<?php
function read_file($file)
{
	return @file_get_contents($file);
}

function write_file($path, $data, $mode = 'wb')
{
	if ( ! $fp = @fopen($path, $mode))
	{
		return false;
	}

	flock($fp, LOCK_EX);

	for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
	{
		if (($result = fwrite($fp, substr($data, $written))) === false)
		{
			break;
		}
	}

	flock($fp, LOCK_UN);
	fclose($fp);

	return is_int($result);
}


function delete_files($path, $del_dir = false, $htdocs = false, $_level = 0)
{
	// Trim the trailing slash
	$path = rtrim($path, '/\\');

	if ( ! $current_dir = @opendir($path))
	{
		return false;
	}

	while (false !== ($filename = @readdir($current_dir)))
	{
		if ($filename !== '.' && $filename !== '..')
		{
			$filepath = $path.DIRECTORY_SEPARATOR.$filename;

			if (is_dir($filepath) && $filename[0] !== '.' && ! is_link($filepath))
			{
				delete_files($filepath, $del_dir, $htdocs, $_level + 1);
			}
			elseif ($htdocs !== true OR ! preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename))
			{
				@unlink($filepath);
			}
		}
	}

	closedir($current_dir);

	return ($del_dir === true && $_level > 0)
		? @rmdir($path)
		: true;
}

function get_filenames($source_dir, $include_path = false, $_recursion = false)
{
	static $_filedata = array();

	if ($fp = @opendir($source_dir))
	{
		// reset the array and make sure $source_dir has a trailing slash on the initial call
		if ($_recursion === false)
		{
			$_filedata = array();
			$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		}

		while (false !== ($file = readdir($fp)))
		{
			if (is_dir($source_dir.$file) && $file[0] !== '.')
			{
				get_filenames($source_dir.$file.DIRECTORY_SEPARATOR, $include_path, true);
			}
			elseif ($file[0] !== '.')
			{
				$_filedata[] = ($include_path === true) ? $source_dir.$file : $file;
			}
		}

		closedir($fp);
		return $_filedata;
	}

	return false;
}

function get_dir_file_info($source_dir, $top_level_only = true, $_recursion = false)
{
	static $_filedata = array();
	$relative_path = $source_dir;

	if ($fp = @opendir($source_dir))
	{
		// reset the array and make sure $source_dir has a trailing slash on the initial call
		if ($_recursion === false)
		{
			$_filedata = array();
			$source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		}

		// Used to be foreach (scandir($source_dir, 1) as $file), but scandir() is simply not as fast
		while (false !== ($file = readdir($fp)))
		{
			if (is_dir($source_dir.$file) && $file[0] !== '.' && $top_level_only === false)
			{
				get_dir_file_info($source_dir.$file.DIRECTORY_SEPARATOR, $top_level_only, true);
			}
			elseif ($file[0] !== '.')
			{
				$_filedata[$file] = get_file_info($source_dir.$file);
				$_filedata[$file]['relative_path'] = $relative_path;
			}
		}

		closedir($fp);
		return $_filedata;
	}

	return false;
}

function get_file_info($file, $returned_values = array('name', 'server_path', 'size', 'date'))
{
	if ( ! file_exists($file))
	{
		return false;
	}

	if (is_string($returned_values))
	{
		$returned_values = explode(',', $returned_values);
	}

	foreach ($returned_values as $key)
	{
		switch ($key)
		{
			case 'name':
				$fileinfo['name'] = basename($file);
				break;
			case 'server_path':
				$fileinfo['server_path'] = $file;
				break;
			case 'size':
				$fileinfo['size'] = filesize($file);
				break;
			case 'date':
				$fileinfo['date'] = filemtime($file);
				break;
			case 'readable':
				$fileinfo['readable'] = is_readable($file);
				break;
			case 'writable':
				$fileinfo['writable'] = is_really_writable($file);
				break;
			case 'executable':
				$fileinfo['executable'] = is_executable($file);
				break;
			case 'fileperms':
				$fileinfo['fileperms'] = fileperms($file);
				break;
		}
	}

	return $fileinfo;
}

function get_mime_by_extension($filename)
{
	static $mimes;

	if ( ! is_array($mimes))
	{
		$mimes = get_mimes();

		if (empty($mimes))
		{
			return false;
		}
	}

	$extension = strtolower(substr(strrchr($filename, '.'), 1));

	if (isset($mimes[$extension]))
	{
		return is_array($mimes[$extension])
			? current($mimes[$extension]) // Multiple mime types, just give the first one
			: $mimes[$extension];
	}

	return false;
}

function symbolic_permissions($perms)
{
	if (($perms & 0xC000) === 0xC000)
	{
		$symbolic = 's'; // Socket
	}
	elseif (($perms & 0xA000) === 0xA000)
	{
		$symbolic = 'l'; // Symbolic Link
	}
	elseif (($perms & 0x8000) === 0x8000)
	{
		$symbolic = '-'; // Regular
	}
	elseif (($perms & 0x6000) === 0x6000)
	{
		$symbolic = 'b'; // Block special
	}
	elseif (($perms & 0x4000) === 0x4000)
	{
		$symbolic = 'd'; // Directory
	}
	elseif (($perms & 0x2000) === 0x2000)
	{
		$symbolic = 'c'; // Character special
	}
	elseif (($perms & 0x1000) === 0x1000)
	{
		$symbolic = 'p'; // FIFO pipe
	}
	else
	{
		$symbolic = 'u'; // Unknown
	}

	// Owner
	$symbolic .= (($perms & 0x0100) ? 'r' : '-')
		.(($perms & 0x0080) ? 'w' : '-')
		.(($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

	// Group
	$symbolic .= (($perms & 0x0020) ? 'r' : '-')
		.(($perms & 0x0010) ? 'w' : '-')
		.(($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

	// World
	$symbolic .= (($perms & 0x0004) ? 'r' : '-')
		.(($perms & 0x0002) ? 'w' : '-')
		.(($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

	return $symbolic;
}


function octal_permissions($perms)
{
	return substr(sprintf('%o', $perms), -3);
}

function get_dir_list ($path) {
	$list = array();
	foreach(glob($path.'/*', GLOB_ONLYDIR) as $dir) {
	    $list[] = str_replace($path.'/', '', $dir);
	}
	return $list;
}
