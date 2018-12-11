<?php

namespace DdB\StuartApiBundle;

use Stuart\Client;
use Stuart\Infrastructure\Authenticator;
use Stuart\Infrastructure\Environment;
use Stuart\Infrastructure\HttpClient;
use Stuart\Job;
use http\Exception\InvalidArgumentException;

class StuartApi
{

    private $privateKey;

    private $publicKey;

    private $client;

    public function __construct(string $privateKey, string $publicKey)
    {
        if(!$privateKey || !$publicKey){
            throw new InvalidArgumentException("Please provide a public and a private key to use this bundle");
        }
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;

        $environment = Environment::SANDBOX;

        $authenticator = new Authenticator($environment, $publicKey, $privateKey);

        $this->client = new Client(new HttpClient($authenticator));
    }

    public function addSimpleJob($pickupAddress, $dropOffAddress, $packageType = 'small'){
        $job = new Job();

        $job->addPickup($pickupAddress);

        $job->addDropOff($dropOffAddress)
            ->setPackageType($packageType);

        $jobOrder = $this->client->createJob($job);

        return $jobOrder;
    }
}