<?php

namespace DdB\StuartApiBundle;

use Stuart\Client;
use Stuart\Infrastructure\Authenticator;
use Stuart\Infrastructure\Environment;
use Stuart\Infrastructure\HttpClient;
use Stuart\Job;
use http\Exception\InvalidArgumentException;
use Stuart\SchedulingSlots;
use Symfony\Component\HttpFoundation\Request;

class StuartApi
{

    private $privateKey;

    private $publicKey;

    private $client;

    public function __construct(string $privateKey, string $publicKey, string $environment)
    {
        if(!$privateKey || !$publicKey){
            throw new InvalidArgumentException("Please provide a public and a private key to use this bundle");
        }
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;

        if($environment === "PRODUCTION"){
            $environment = Environment::PRODUCTION;
        } else {
            $environment = Environment::SANDBOX;
        }

        $authenticator = new Authenticator($environment, $publicKey, $privateKey);

        $this->client = new Client(new HttpClient($authenticator));
    }

    public function addSimpleJob($pickupAddress, $dropOffAddress, $pickupAt,  $packageType = 'small'){
        $job = new Job();

        $job->addPickup($pickupAddress)
            ->setPickupAt($pickupAt);

        $job->addDropOff($dropOffAddress)
            ->setPackageType($packageType);

        $jobOrder = $this->client->createJob($job);

        return $jobOrder;
    }

    /**
     * @param string $city
     * @return |null
     * @throws \Exception
     */
    public function getNextPickupSlot($city = "Bordeaux"){
        $startTime = new \DateTime();
        $startTime->add(new \DateInterval("PT2H"));
        $slots = $this->client->getSchedulingSlotsAtPickup($city, $startTime);
        foreach ($slots->getSlots() as $slot){
            if($slot['start'] > $startTime){
                return $slot["start"];
            }
        }
        return null;
    }
}