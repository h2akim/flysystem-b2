<?php

namespace H2akim\Flysystem\BackBlaze;

use ChrisWhite\B2\Client;
use ChrisWhite\B2\Bucket;

class B2Adapter extends AbstractAdapter
{
    /**
     * @var Client
     */
    protected $b2client;

    /**
     * @var string
     */
    protected $bucket_id;

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
        $this->bucket_id = $client->getBucketIdFromName($bucket);
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
     * Returns bucket id
     *
     * @return    string
     */
    public function getBucketId()
    {
        return $this->bucket_id;
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
     * Delete file version from bucket
     *
     * @param
     * @return    void
     * @author
     * @copyright
     */
    public function delete($path)
    {
        $command = $this->b2client->deleteFile([
            'BucketName' => getBucketName(),
            'Filename' => $this->setPathPrefix($path)
        ]);

        return ! $this->has($path);
    }

    /**
     * Delete directory from bucket
     *
     * @param
     * @return    void
     * @author
     * @copyright
     */
    public function deleteDir($path)
    {

        $this->b2client->deleteFile([
            'BucketName' => getBucketName(),
            'Filename' => $this->setPathPrefix($path) . '/'
        ]);

        return ! $this->has($path);
    }

    /**
     * Create directory in bucket (file representation)
     *
     * @param
     * @return    void
     * @author
     * @copyright
     */
    public function createDir($dirname, Config $config)
    {

        return $this->upload($dirname . '/', '', $config);

    }

    /**
     * Check file exists or not
     *
     * @param
     * @return    void
     * @author
     * @copyright
     */
    public function has($path)
    {
        if ($this->b2client->getFile($path)) {
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
            'BucketName' => getBucketName(),
            'Filename' => $path,
            'Body' => $body
        ]);

        return $command;

    }

    /**
     * Description of what this does.
     *
     * @param
     * @return    void
     * @author
     * @copyright
     */

    protected function setPathPrefix($prefix)
    {
        $prefix = ltrim($prefix, '/');

        return parent::setPathPrefix($prefix);
    }


}
