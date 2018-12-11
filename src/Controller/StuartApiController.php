<?php

namespace DdB\StuartApiBundle\Controller;

use DdB\StuartApiBundle\StuartApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function simpleJob($pickupAddress, $dropOffAddress, $packageType = 'small')
    {
        $job = $this->api->addSimpleJob($pickupAddress, $dropOffAddress, $packageType);
        return $this->json($this->serializer->encode($job, 'json'));
    }
}