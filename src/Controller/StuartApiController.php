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

    /**
     * @param $city
     * @return JsonResponse
     */
    public function nextPickupSlot($city){

        if($city == null){
            return new JsonResponse([
                'message' => $this->translator->trans('CITY_NOT_FOUND', [], 'Errors')
            ], 500);
        }

        try{
            $slot = $this->api->getNextPickupSlot($city);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $this->translator->trans($exception->getMessage(), [], 'Errors')
            ], 500);
        }
        return new JsonResponse($slot);
    }

    /**
     * @param Request $request
     * @param $pickupAddress
     * @param $dropOffAddress
     * @param string $packageType
     * @return JsonResponse
     */
    public function simpleJob(Request $request, $pickupAddress, $dropOffAddress, $packageType = 'small')
    {
        $pickupAt = $request->request->get("pickupAt");
        try {
            $job = $this->api->addSimpleJob($pickupAddress, $dropOffAddress, $pickupAt, $packageType);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $this->translator->trans($exception->getMessage(), [], 'Errors')
            ], 500);
        }
        return JsonResponse::fromJsonString($this->serializer->serialize($job, 'json'));
    }
}