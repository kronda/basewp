<?php
/*
 * Copyright 2011-2013 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */


/**
 * Provides an interface for accessing Amazon S3 using PHP's native file management functions.
 *
 * Amazon S3 file patterns take the following form: <code>s3://bucket/object</code>.
 */
class S3StreamWrapper
{
	/**
	 * @var array An array of AmazonS3 clients registered as stream wrappers.
	 */
	protected static $_clients = array();

	/**
	 * Registers the S3StreamWrapper class as a stream wrapper.
	 *
	 * @param AmazonS3 $s3 (Optional) An instance of the AmazonS3 client.
	 * @param string $protocol (Optional) The name of the protocol to register.
	 * @return boolean Whether or not the registration succeeded.
	 */
	public static function register(AmazonS3 $s3 = null, $protocol = 's3')
	{
		S3StreamWrapper::$_clients[$protocol] = $s3 ? $s3 : new AmazonS3();

		return stream_wrapper_register($protocol, 'S3StreamWrapper');
	}

	/**
	 * Makes the given token PCRE-compatible.
	 *
	 * @param string $token (Required) The token
	 * @return string The PCRE-compatible version of the token
	 */
	public static function regex_token($token)
	{
		$token = str_replace('/', '\/', $token);
		$token = quotemeta($token);
		return str_replace('\\\\', '\\', $token);
	}

	public $position = 0;
	public $path = null;
	public $file_list = null;
	public $open_file = null;
	public $seek_position = 0;
	public $eof = false;
	public $buffer = null;
	public $object_size = 0;

	/**
	 * Fetches the client for the protocol being used.
	 *
	 * @param string $protocol (Optional) The protocol associated with this stream wrapper.
	 * @return AmazonS3 The S3 client associated with this stream wrapper.
	 */
	public function client($protocol = null)
	{
		if ($protocol == null)
		{
			if ($parsed = parse_url($this->path))
			{
				$protocol = $parsed['scheme'];
			}
			else
			{
				trigger_error(__CLASS__ . ' could not determine the protocol of the stream wrapper in use.');
			}
		}

		return self::$_clients[$protocol];
	}

	/**
	 * Parses an S3 URL into the parts needed by the stream wrapper.
	 *
	 * @param string $path The path to parse.
	 * @return array An array of 3 items: protocol, bucket, and object name ready for <code>list()</code>.
	 */
	public function parse_path($path)
	{
		$url = parse_url($path);

		return array(
			$url['scheme'],                                       // Protocol
			$url['host'],                                         // Bucket
			(isset($url['path']) ? substr($url['path'], 1) : ''), // Object
		);
	}

	/**
	 * Close directory handle. This method is called in response to <php:closedir()>.
	 *
	 * Since Amazon S3 doesn't have real directories, always return <code>true</code>.
	 *
	 * @return boolean
	 */
	public function dir_closedir()
	{
		$this->position = 0;
		$this->path = null;
		$this->file_list = null;
		$this->open_file = null;
		$this->seek_position = 0;
		$this->eof = false;
		$this->buffer = null;
		$this->object_size = 0;

		return true;
	}

	/**
	 * Open directory handle. This method is called in response to <php:opendir()>.
	 *
	 * @param string $path (Required) Specifies the URL that was passed to <php:opendir()>.
	 * @param integer $options (Required) Not used. Passed in by <php:opendir()>.
	 * @return boolean Returns <code>true</code> on success or <code>false</code> on failure.
	 */
	public function dir_opendir($path, $options)
	{
		$this->path = $path;
		list($protocol, $bucket, $object_name) = $this->parse_path($path);

		$pattern = '/^' . self::regex_token($object_name) . '(.*)[^\/$]/';

		$this->file_list = $this->client($protocol)->get_object_list($bucket, array(
			'pcre' => $pattern
		));

		return (count($this->file_list)) ? true : false;
	}

	/**
	 * This method is called in response to <php:readdir()>.
	 *
	 * @return string Should return a string representing the next filename, or <code>false</code> if there is no next file.
	 */
	public function dir_readdir()
	{
		if (isset($this->file_list[$this->position]))
		{
			$out = $this->file_list[$this->position];
			$this->position++;
		}
		else
		{
			$out = false;
		}

		return $out;
	}

	/**
	 * This method is called in response to <php:rewinddir()>.
	 *
	 * Should reset the output generated by <php:streamWrapper::dir_readdir()>. i.e.: The next call to
	 * <php:streamWrapper::dir_readdir()> should return the first entry in the location returned by
	 * <php:streamWrapper::dir_opendir()>.
	 *
	 * @return boolean Returns <code>true</code> on success or <code>false</code> on failure.
	 */
	public function dir_rewinddir()
	{
		$this->position = 0;

		return true;
	}

	/**
	 * Create a new bucket. This method is called in response to <php:mkdir()>.
	 *
	 * @param string $path (Required) The bucket name to create.
	 * @param integer $mode (Optional) Permissions. 700-range permissions map to ACL_PUBLIC. 600-range permissions map to ACL_AUTH_READ. All other permissions map to ACL_PRIVATE. Expects octal form.
	 * @param integer $options (Optional) Ignored.
	 * @return boolean Whether the bucket was created successfully or not.
	 */
	public function mkdir($path, $mode, $options)
	{
		// Get the value that was *actually* passed in as mode, and default to 0
		$trace_slice = array_slice(debug_backtrace(), -1);
		$mode = isset($trace_slice[0]['args'][1]) ? decoct($trace_slice[0]['args'][1]) : 0;

		$this->path = $path;
		list($protocol, $bucket, $object_name) = $this->parse_path($path);

		if (in_array($mode, range(700, 799)))
		{
			$acl = AmazonS3::ACL_PUBLIC;
		}
		elseif (in_array($mode, range(600, 699)))
		{
			$acl = AmazonS3::ACL_AUTH_READ;
		}
		else
		{
			$acl = AmazonS3::ACL_PRIVATE;
		}

		$client = $this->client($protocol);
		$region = $client->hostname;
		$response = $client->create_bucket($bucket, $region, $acl);

		return $response->isOK();
	}

	/**
	 * Renames a file or directory. This method is called in response to <php:rename()>.
	 *
	 * @param string $path_from (Required) The URL to the current file.
	 * @param string $path_to (Required) The URL which the <code>$path_from</code> should be renamed to.
	 * @return boolean Returns <code>true</code> on success or <code>false</code> on failure.
	 */
	public function rename($path_from, $path_to)
	{
		list($protocol, $from_bucket_name, $from_object_name) = $this->parse_path($path_from);
		list($protocol, $to_bucket_name, $to_object_name) = $this->parse_path($path_to);

		$copy_response = $this->client($protocol)->copy_object(
			array('bucket' => $from_bucket_name, 'filename' => $from_object_name),
			array('bucket' => $to_bucket_name,   'filename' => $to_object_name  )
		);

		if ($copy_response->isOK())
		{
			$delete_response = $this->client($protocol)->delete_object($from_bucket_name, $from_object_name);

			if ($delete_response->isOK())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * This method is called in response to <php:rmdir()>.
	 *
	 * @param string $path (Required) The bucket name to create.
	 * @param boolean $context (Optional) Ignored.
	 * @return boolean Whether the bucket was deleted successfully or not.
	 */
	public function rmdir($path, $context)
	{
		$this->path = $path;
		list($protocol, $bucket, $object_name) = $this->parse_path($path);

		$response = $this->client($protocol)->delete_bucket($bucket);

		return $response->isOK();
	}

	/**
	 * NOT IMPLEMENTED!
	 *
	 * @param integer $cast_as
	 * @return resource
	 */
	// public function stream_cast($cast_as) {}

	/**
	 * Close a resource. This method is called in response to <php:fclose()>.
	 *
	 * All resources that were locked, or allocated, by the wrapper should be released.
	 *
	 * @return void
	 */
	public function stream_close()
	{
		$this->position = 0;
		$this->path = null;
		$this->file_list = null;
		$this->open_file = null;
		$this->seek_position = 0;
		$this->eof = false;
		$this->buffer = null;
		$this->object_size = 0;
	}

	/**
	 * Tests for end-of-file on a file pointer. This method is called in response to <php:feof()>.
	 *
	 * @return boolean
	 */
	public function stream_eof()
	{
		return $this->eof;
	}

	/**
	 * Flushes the output. This method is called in response to <php:fflush()>. If you have cached data in
	 * your stream but not yet stored it into the underlying storage, you should do so now.
	 *
	 * Since this implementation doesn't buffer streams, simply return <code>true</code>.
	 *
	 * @return boolean Whether or not flushing succeeded
	 */
	public function stream_flush()
	{
		if ($this->buffer === null)
		{
			return false;
		}

		list($protocol, $bucket, $object_name) = $this->parse_path($this->path);

		$response = $this->client($protocol)->create_object($bucket, $object_name, array(
			'body' => $this->buffer,
		));

		$this->seek_position = 0;
		$this->buffer = null;
		$this->eof = true;

		return $response->isOK();
	}

	/**
	 * This method is called in response to <php:flock()>, when <php:file_put_contents()> (when flags contains
	 * <code>LOCK_EX</code>), <php:stream_set_blocking()> and when closing the stream (<code>LOCK_UN</code>).
	 *
	 * Not implemented in S3, so it's not implemented here.
	 *
	 * @param mode $operation
	 * @return boolean
	 */
	// public function stream_lock($operation) {}

	/**
	 * Opens file or URL. This method is called immediately after the wrapper is initialized
	 * (e.g., by <php:fopen()> and <php:file_get_contents()>).
	 *
	 * @param string $path (Required) Specifies the URL that was passed to the original function.
	 * @param string $mode (Required) Ignored.
	 * @param integer $options (Required) Ignored.
	 * @param string &$opened_path (Required) Returns the same value as was passed into <code>$path</code>.
	 * @return boolean Returns <code>true</code> on success or <code>false</code> on failure.
	 */
	public function stream_open($path, $mode, $options, &$opened_path)
	{
		$opened_path = $path;
		$this->open_file = $path;
		$this->path = $path;
		$this->seek_position = 0;
		$this->object_size = 0;

		return true;
	}

	/**
	 * Read from stream. This method is called in response to <php:fread()> and <php:fgets()>.
	 *
	 *
	 *
	 * It is important to avoid reading files that are near to or larger than the amount of memory
	 * allocated to PHP, otherwise "out of memory" errors will occur.
	 *
	 * @param integer $count (Required) Always equal to 8192. PHP is fun, isn't it?
	 * @return string The contents of the Amazon S3 object.
	 */
	public function stream_read($count)
	{
		if ($this->eof)
		{
			return false;
		}

		list($protocol, $bucket, $object_name) = $this->parse_path($this->path);

		if ($this->seek_position > 0 && $this->object_size)
		{
			if ($count + $this->seek_position > $this->object_size)
			{
				$count = $this->object_size - $this->seek_position;
			}

			$start = $this->seek_position;
			$end = $this->seek_position + $count;

			$response = $this->client($protocol)->get_object($bucket, $object_name, array(
				'range' => $start . '-' . $end
			));
		}
		else
		{
			$response = $this->client($protocol)->get_object($bucket, $object_name);
			$this->object_size = isset($response->header['content-length']) ? $response->header['content-length'] : 0;
		}

		if (!$response->isOK())
		{
			return false;
		}

		$data = substr($response->body, 0, min($count, $this->object_size));
		$this->seek_position += strlen($data);


		if ($this->seek_position >= $this->object_size)
		{
			$this->eof = true;
			$this->seek_position = 0;
			$this->object_size = 0;
		}

		return $data;
	}

	/**
	 * Seeks to specific location in a stream. This method is called in response to <php:fseek()>. The read/write
	 * position of the stream should be updated according to the <code>$offset</code> and <code>$whence</code>
	 * parameters.
	 *
	 * @param integer $offset (Required) The number of bytes to offset from the start of the file.
	 * @param integer $whence (Optional) Ignored. Always uses <code>SEEK_SET</code>.
	 * @return boolean Whether or not the seek was successful.
	 */
	public function stream_seek($offset, $whence)
	{
		$this->seek_position = $offset;

		return true;
	}

	/**
	 * @param integer $option
	 * @param integer $arg1
	 * @param integer $arg2
	 * @return boolean
	 */
	// public function stream_set_option($option, $arg1, $arg2) {}

	/**
	 * Retrieve information about a file resource.
	 *
	 * @return array Returns the same data as a call to <php:stat()>.
	 */
	public function stream_stat()
	{
		return $this->url_stat($this->path, null);
	}

	/**
	 * Retrieve the current position of a stream. This method is called in response to <php:ftell()>.
	 *
	 * @return integer Returns the current position of the stream.
	 */
	public function stream_tell()
	{
		return $this->seek_position;
	}

	/**
	 * Write to stream. This method is called in response to <php:fwrite()>.
	 *
	 * It is important to avoid reading files that are larger than the amount of memory allocated to PHP,
	 * otherwise "out of memory" errors will occur.
	 *
	 * @param string $data (Required) The data to write to the stream.
	 * @return integer The number of bytes that were written to the stream.
	 */
	public function stream_write($data)
	{
		$size = strlen($data);

		$this->seek_position = $size;
		$this->buffer .= $data;

		return $this->seek_position;
	}

	/**
	 * Delete a file. This method is called in response to <php:unlink()>.
	 *
	 * @param string $path (Required) The file URL which should be deleted.
	 * @return boolean Returns <code>true</code> on success or <code>false</code> on failure.
	 */
	public function unlink($path)
	{
		$this->path = $path;
		list($protocol, $bucket, $object_name) = $this->parse_path($path);

		$response = $this->client($protocol)->delete_object($bucket, $object_name);

		return $response->isOK();
	}

	/**
	 * This method is called in response to all <php:stat()> related functions.
	 *
	 * @param string $path (Required) The file path or URL to stat. Note that in the case of a URL, it must be a <code>://</code> delimited URL. Other URL forms are not supported.
	 * @param integer $flags (Required) Holds additional flags set by the streams API. This implementation ignores all defined flags.
	 * @return array Should return as many elements as <php:stat()> does. Unknown or unavailable values should be set to a rational value (usually <code>0</code>).
	 */
	public function url_stat($path, $flags)
	{
		// Defaults
		$out = array();
		$out[0] = $out['dev'] = 0;
		$out[1] = $out['ino'] = 0;
		$out[2] = $out['mode'] = 0;
		$out[3] = $out['nlink'] = 0;
		$out[4] = $out['uid'] = 0;
		$out[5] = $out['gid'] = 0;
		$out[6] = $out['rdev'] = 0;
		$out[7] = $out['size'] = 0;
		$out[8] = $out['atime'] = 0;
		$out[9] = $out['mtime'] = 0;
		$out[10] = $out['ctime'] = 0;
		$out[11] = $out['blksize'] = 0;
		$out[12] = $out['blocks'] = 0;

		$this->path = $path;
		list($protocol, $bucket, $object_name) = $this->parse_path($this->path);

		$file = null;
		$mode = 0;

		if ($object_name)
		{
			$response = $this->client($protocol)->list_objects($bucket, array(
				'prefix' => $object_name
			));

			if (!$response->isOK())
			{
				return $out;
			}

			// Ummm... yeah...
			if (is_object($response->body))
			{
				$file = $response->body->Contents[0];
			}
			else
			{
				$body = simplexml_load_string($response->body);
				$file = $body->Contents[0];
			}
		}
		else
		{
			$response = $this->client($protocol)->list_objects($bucket);

			if (!$response->isOK())
			{
				return $out;
			}
		}

		/*
		Type & Permission bitwise values (only those that pertain to S3).
		Simulate the concept of a "directory". Nothing has an executable bit because there's no executing on S3.
		Reference: http://docstore.mik.ua/orelly/webprog/pcook/ch19_13.htm

		0100000 => type:   regular file
		0040000 => type:   directory
		0000400 => owner:  read permission
		0000200 => owner:  write permission
		0000040 => group:  read permission
		0000020 => group:  write permission
		0000004 => others: read permission
		0000002 => others: write permission
		*/

		// File or directory?
		// @todo: Add more detailed support for permissions. Currently only takes OWNER into account.
		if (!$object_name) // Root of the bucket
		{
			$mode = octdec('0040777');
		}
		elseif ($file)
		{
			$mode = (str_replace('//', '/', $object_name . '/') === (string) $file->Key) ? octdec('0040777') : octdec('0100777'); // Directory, Owner R/W : Regular File, Owner R/W
		}
		else
		{
			$mode = octdec('0100777');
		}

		// Update stat output
		$out[2] = $out['mode'] = $mode;
		$out[4] = $out['uid'] = (isset($file) ? (string) $file->Owner->ID : 0);
		$out[7] = $out['size'] = (isset($file) ? (string) $file->Size : 0);
		$out[8] = $out['atime'] = (isset($file) ? date('U', strtotime((string) $file->LastModified)) : 0);
		$out[9] = $out['mtime'] = (isset($file) ? date('U', strtotime((string) $file->LastModified)) : 0);
		$out[10] = $out['ctime'] = (isset($file) ? date('U', strtotime((string) $file->LastModified)) : 0);

		return $out;
	}
}
