<?php

namespace DdB\StuartApiBundle;

use Stuart\Client;
use Stuart\Infrastructure\Authenticator;
use Stuart\Infrastructure\Environment;
use Stuart\Infrastructure\HttpClient;
use Stuart\Job;
use Stuart\SchedulingSlots;

class StuartApi
{

    private $privateKey;

    private $publicKey;

    private $client;

    /**
     * StuartApi constructor.
     * @param string $privateKey
     * @param string $publicKey
     * @param string $environment
     * @throws \Exception
     */
    public function __construct(string $privateKey, string $publicKey, string $environment)
    {
        if(!$privateKey || !$publicKey){
            throw new \Exception("Please provide a public and a private key to use this bundle");
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

    /**
     * @param $pickupAddress
     * @param $dropOffAddress
     * @param $pickupAt
     * @param string $packageType
     * @return mixed|Job
     * @throws \Exception
     */
    public function addSimpleJob($pickupAddress, $dropOffAddress, $pickupAt,  $packageType = 'small'){
        $job = new Job();

        $job->addPickup($pickupAddress)
            ->setPickupAt($pickupAt);

        $job->addDropOff($dropOffAddress)
            ->setPackageType($packageType);

        $jobOrder = $this->client->createJob($job);

        if(!$jobOrder instanceof Job){
            throw new \Exception($jobOrder->error);
        }

        return $jobOrder;
    }

    /**
     * @param string $city
     * @return \DateTime
     * @throws \Exception
     */
    public function getNextPickupSlot($city){
        $startTime = new \DateTime();
        $startTime->add(new \DateInterval("PT2H"));
        $slots = $this->client->getSchedulingSlotsAtPickup($city, $startTime);
        if(!$slots instanceof SchedulingSlots){
            throw new \Exception($slots->error);
        }
        foreach ($slots->getSlots() as $slot){
            if($slot['start'] > $startTime){
                return $slot["start"];
            }
        }
        throw new \Exception('NOT_FOUND');
    }
}