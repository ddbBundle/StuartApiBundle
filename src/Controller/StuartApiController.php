<?php

namespace DdB\StuartApiBundle\Controller;

use DdB\StuartApiBundle\StuartApi;
use Stuart\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;

class StuartApiController extends AbstractController
{
    /**
     * @var StuartApi
     */
    private $api;
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(StuartApi $api, Serializer $serializer)
    {
        $this->api = $api;
        $this->serializer = $serializer;
    }

    public function index()
    {
        
    }

    public function nextPickupSlot($city = "Bordeaux"){
        $slot = $this->api->getNextPickupSlot($city);
        if($slot !== null){
            return new JsonResponse($slot);
        } else {
            return new JsonResponse("No slots found", 404);
        }
    }

    public function simpleJob(Request $request, $pickupAddress, $dropOffAddress, $packageType = 'small')
    {
        $pickupAt = $request->request->get("pickupAt");
        $job = $this->api->addSimpleJob($pickupAddress, $dropOffAddress, $pickupAt, $packageType);
        if($job instanceof Job){
            return $this->json($this->serializer->serialize($job, 'json'));
        }
        return $this->json($this->serializer->encode($job, 'json'));
    }
}