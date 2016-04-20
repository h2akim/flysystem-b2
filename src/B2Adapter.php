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
    protected $bucket;

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
        $this->bucket = $bucket;
        $this->prefix = $prefix;
        $this->options = $options;
    }

    /**
     * Returns bucket name
     *
     * @return    string
     */
    public function getBucket()
    {
        return $this->bucket;
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
        // to be implement
    }

    /**
     * Update file
     *
     * @param     string $path
     * @param     string $contents
     * @param     Config $config Config object
     *
     * @return    false|array false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {
        // to be implement
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
        $this->b2client->upload([
            'BucketName' => $this->bucket,
            'Filename' => $path,
            'Body' => $body
        ]);
    }
}
