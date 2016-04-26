<?php

namespace H2akim\Flysystem;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Util;
use League\Flysystem\Config;
use ChrisWhite\B2\Client;
use ChrisWhite\B2\Bucket;

class B2Adapter extends AbstractAdapter
{

    /**
     * @var array
     */
    protected static $resultMap = [
        'size' => 'size',
        'path' => 'name',
        'type' => 'type',
        'id' => 'id'
    ];

    /**
     * @var Client
     */
    protected $b2client;

    /**
     * @var string
     */
    protected $bucket_name;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Description of what this does.
     *
     * @param     Client $client
     * @param     string $bucket
     * @param     string $prefix
     * @param     array $options
     */
    public function __construct(Client $client, $bucket, $prefix = '', array $options = [])
    {
        $this->b2client = $client;
        $this->bucket_name = $bucket;
        $this->setPathPrefix($prefix);
        $this->options = $options;
    }

    /**
     * Returns bucket name
     *
     * @return    string
     */
    public function getBucketName()
    {
        return $this->bucket_name;
    }

    /**
     * Write new file
     *
     * @param     string $path
     * @param     string $contents
     * @param     Config $config Config object
     *
     * @return    false|array false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {
        return $this->upload($path, $contents, $config);
    }

    /**
     * Read a file
     *
     * @param     string $path
     * @return    false|array false on failure file meta data on success
     */
    public function read($path)
    {
        $response = $this->b2client->getFile([
            'FileName' => $path,
            'BucketName' => $this->getBucketName()
        ]);

        return $response;
    }

    /**
     * Delete file version from bucket
     *
     * @param     string $path
     * @return    bool
     */
    public function delete($path)
    {
        $command = $this->b2client->deleteFile([
            'BucketName' => $this->getBucketName(),
            'FileName' => $path
        ]);

        return ! $this->has($path);
    }

    /**
     * list contents
     *
     * @param     string $directory
     * @param     bool $recursive
     * @return    array
     */
    public function listContents($directory = '', $recursive = false)
    {
        $listing = [];

        $responses = $this->b2client->listFiles([
            'BucketName' => $this->getBucketName()
        ]);

        foreach ($responses as $response) {

            $protected_property = [
                'name' => $response->getName(),
                'id' => $response->getId(),
                'size' => $response->getSize(),
                'type' => $response->getType()
            ];

            $listing[] = $this->normalizeResponse($protected_property);

            if ($recursive) {
                $listing = array_merge($listing, $this->listContents($path, true));
            }
        }

        return $listing;
    }

    /**
     * Normalize object result array
     *
     * @param     array $response
     * @param     string $path
     * @return    array
     */
    protected function normalizeResponse(array $response, $path = null)
    {
        $result = ['path' => $response['name']];
        $result = array_merge($result, Util::map($response, static::$resultMap));
        return $result;
    }



    /**
     * Delete directory from bucket
     *
     * @param     string $path
     * @return    bool
     */
    public function deleteDir($path)
    {
        if ($this->has($path)) {

            $this->b2client->deleteFile([
                'BucketName' => $this->getBucketName(),
                'FileName' => $this->setPathPrefix($path) . '/'
            ]);

        }

        return ! $this->has($path);
    }

    /**
     * Create directory in bucket (file representation)
     *
     * @param     string $dirname
     * @param     Config $config
     * @return    json
     */
    public function createDir($dirname, Config $config)
    {
        return $this->upload($dirname . '/', '', $config);
    }

    /**
     * Write new file (stream)
     *
     * @param     string $path
     * @param     string $contents
     * @param     Config $config Config object
     *
     * @return    false|array false on failure file meta data on success
     */
    public function writeStream($path, $contents, Config $config)
    {
        return $this->write($path, $contents, $config);
    }

    /**
     * Check file exists or not
     *
     * @param     string $path
     * @return    bool
     */
    public function has($path)
    {
        $settings = [
            'BucketName' => $this->getBucketName(),
            'FileName' => $path
        ];

        try {
            $file = $this->b2client->getFile($settings);
        } catch (BadJsonException $e) {
            $file = false;
        }

        if ($file) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Upload object
     *
     * @param     string $path
     * @param     string $body
     * @param     Config $config Config object
     */
    protected function upload($path, $body, Config $config)
    {
        $command = $this->b2client->upload([
            'BucketName' => $this->getBucketName(),
            'FileName' => $path,
            'Body' => $body
        ]);

        return $command;
    }

    /**
     * Description of what this does.
     *
     * @param     string $prefix
     * @return    string
     */

    public function setPathPrefix($prefix)
    {
        $prefix = ltrim($prefix, '/');

        return parent::setPathPrefix($prefix);
    }

    /* These methods not yet implemented */

    /**
     * update Stream
     *
     */
    public function updateStream($path, $contents, Config $config)
    {
        // TO BE IMPLEMENT
    }
        /**
     * get meta data
     *
     */
    public function getMetadata($path)
    {
        // TO BE IMPLEMENT
    }

    /**
     * get Mimetype
     *
     */
    public function getMimetype($path)
    {
        // TO BE IMPLEMENT
    }

    /**
     * get Timestamps
     *
     */
    public function getTimestamp($path)
    {
        // TO BE IMPLEMENT
    }

    /**
     * get visibility
     *
     */
    public function getVisibility($path)
    {
        // TO BE IMPLEMENT
    }

    /**
     * getSize
     *
     */
    public function getSize($path)
    {
        // TO BE IMPLEMENT
    }

    /**
     * Copy a file
     *
     */
    public function copy($path, $newpath)
    {
        // TO BE IMPLEMENT
    }

    /**
     * update Stream
     *
     */
    public function readStream($path)
    {
        // TO BE IMPLEMENT
    }

    /**
     * update Stream
     *
     */
    public function setVisibility($path, $visibility)
    {
        // TO BE IMPLEMENT
    }

    /**
     * Update file
     *
     */
    public function update($path, $contents, Config $config)
    {
        // TO BE IMPLEMENT
    }

    /**
     * Rename file
     *
     */
    public function rename($path, $newpath)
    {
        // TO BE IMPLEMENT
    }
}
