<?php

namespace DdB\StuartApiBundle;

use Stuart\Client;
use Stuart\Infrastructure\Authenticator;
use Stuart\Infrastructure\Environment;
use Stuart\Infrastructure\HttpClient;
use Stuart\Job;
use Stuart\SchedulingSlots;
use Symfony\Component\HttpFoundation\Request;

class StuartApi
{

    private $privateKey;

    private $publicKey;

    private $client;

    private $environment;

    private $environment_url;

    private $vatRate;

    private $authorizedWebhookIps;

    /**
     * StuartApi constructor.
     * @param string $privateKey
     * @param string $publicKey
     * @param string $environment
     * @param float $vatRate
     * @param array $authorizedWebhookIps
     * @throws \Exception
     */
    public function __construct(string $privateKey, string $publicKey, string $environment, float $vatRate, array $authorizedWebhookIps)
    {
        if(!$privateKey || !$publicKey){
            throw new \Exception("Please provide a public and a private key to use this bundle");
        }
        $this->privateKey = $privateKey;
        $this->publicKey = $publicKey;

        $this->vatRate = $vatRate;

        if($environment === "PRODUCTION"){
            $this->environment_url = Environment::PRODUCTION;
            $this->environment = "PRODUCTION";
        } else {
            $this->environment_url = Environment::SANDBOX;
            $this->environment = "SANDBOX";
        }

        $authenticator = new Authenticator($this->environment_url, $publicKey, $privateKey);

        $this->client = new Client(new HttpClient($authenticator));

        $this->authorizedWebhookIps = $authorizedWebhookIps;
    }

    /**
     * @param $pickupAddress
     * @param $dropOffAddress
     * @param $pickupAt
     * @param string $packageType
     * @return mixed|Job
     * @throws \Exception
     */
    public function addJob(Job $job){
        /** @var Job $jobOrder */
        $jobOrder = $this->client->createJob($job);

        if(!$jobOrder instanceof Job){
            throw new \Exception($jobOrder->error);
        }

        return $jobOrder;
    }

    /**
     * @param $publicKey
     * @param $privateKey
     */
    public function setApiKeys($publicKey, $privateKey){
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;

        $authenticator = new Authenticator($this->environment_url, $publicKey, $privateKey);

        $this->client = new Client(new HttpClient($authenticator));
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


    /**
     * @param $city
     * @return mixed
     * @throws \Exception
     */
    public function getSlots($city, $type, $date = null){

        $date = $date ?? new \DateTime();
        $date->add(new \DateInterval('PT15M'));

        if($type == "pickup") {
            $slots = $this->client->getSchedulingSlotsAtPickup($city, $date);
        } elseif ($type == "dropoff") {
            $slots = $this->client->getSchedulingSlotsAtDropoff($city, $date);
        } else {
            throw new \Exception("INVALID_TYPE");
        }

        if(!$slots instanceof SchedulingSlots){
            throw new \Exception($slots->error);
        }
        return $slots->getSlots();
    }


    public function validateJob(Job $job){
        $jobOrder = $this->client->validateJob($job);

        if($jobOrder !== true){
            throw new \Exception($jobOrder->error);
        }

        return $jobOrder;
    }

    public function priceJob(Job $job, $validate = false){

        if($validate){
            $this->validateJob($job);
        }

        return $this->client->getPricing($job);
    }

    public function createJobObjectFromRequest(Request $request){
        $pickupAt = $request->request->get("pickupDate");

        if(empty($pickupAt))
        {
            throw new \Exception("NO_PICKUP_DATE");
        }

        if(!$pickupAt instanceof \DateTime){
            $pickupAt = \DateTime::createFromFormat('d/m/Y H:i', $pickupAt);
        }

        $pickupAddress = $request->request->get('pickupAddress');
        $dropOffAddress = $request->request->get('dropOffAddress');
        $packageType = $request->request->get('packageType');
        $transportType = $request->request->get('transportType');

        $job = new Job();

        $job->addPickup($pickupAddress)
            ->setPickupAt($pickupAt);

        $job->addDropOff($dropOffAddress);

        $job->setTransportType($transportType);

        return $job;
    }

    /**
     * @param $pickupAddress
     * @param $dropoffAddress
     * @param $transport_type
     * @param null $assignmentCode
     * @param null|\DateTime $pickupDate
     * @param null|\DateTime $dropoffDate
     * @param null $pickupCompany
     * @param null $pickupFirstname
     * @param null $pickupLastname
     * @param null $pickupPhone
     * @param null $pickupEmail
     * @param null $client_reference
     * @param null $dropoffCompany
     * @param null $dropoffFirstname
     * @param null $dropoffLastname
     * @param null $dropoffPhone
     * @param null $dropoffEmail
     */
    public function createJobObject(
        $pickupAddress,
        $dropoffAddress,
        $transport_type,
        $assignmentCode = null,
        $pickupDate = null,
        $dropoffDate = null,
        $pickupCompany = null,
        $pickupFirstname = null,
        $pickupLastname = null,
        $pickupPhone = null,
        $pickupEmail = null,
        $clientReference = null,
        $dropoffCompany = null,
        $dropoffFirstname = null,
        $dropoffLastname = null,
        $dropoffPhone = null,
        $dropoffEmail = null
    ) {
        $job = new Job();

        $job->addPickup($pickupAddress);

        $job->addDropOff($dropoffAddress);

        $pickups = $job->getPickups();
        $pickup = $pickups[0];

        $dropoffs = $job->getDropOffs();
        $dropoff = $dropoffs[0];

        if($pickupDate){
            $pickup->setPickupAt($pickupDate);
        }

        if($dropoffDate){
            $dropoff->setDropoffAt($dropoffDate);
        }

        $pickup->setContactCompany($pickupCompany);
        $pickup->setContactFirstName($pickupFirstname);
        $pickup->setContactLastName($pickupLastname);
        $pickup->setContactEmail($pickupEmail);
        $pickup->setContactPhone($pickupPhone);

        $dropoff->setContactCompany($dropoffCompany);
        $dropoff->setContactFirstName($dropoffFirstname);
        $dropoff->setContactLastName($dropoffLastname);
        $dropoff->setContactEmail($dropoffEmail);
        $dropoff->setContactPhone($dropoffPhone);

        $dropoff->setClientReference($clientReference);

        $job->setTransportType($transport_type);
        $job->setAssignmentCode($assignmentCode);

        return $job;
    }

    public function getVatRate() {
        return $this->vatRate;
    }

    public function getEnvironment() {
        return $this->environment;
    }

    public function getAuthorizedWebhookIps(){
        return $this->authorizedWebhookIps;
    }

    public function webHook(){

    }
}
