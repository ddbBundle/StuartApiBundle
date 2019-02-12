<?php

namespace DdB\StuartApiBundle\Controller;

use DdB\StuartApiBundle\StuartApi;
use Stuart\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(StuartApi $api, Serializer $serializer, TranslatorInterface $translator)
    {
        $this->api = $api;
        $this->serializer = $serializer;
        $this->translator = $translator;
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
            return JsonResponse::fromJsonString($this->serializer->serialize($job, 'json'));
        }
        //If it's not an instance of Job that means an error occurred
        $error = $job->error;
        return new JsonResponse([
            'error' => $error,
            'message' => $this->translator->trans($error, [], 'Errors')
        ], 500);
    }
}