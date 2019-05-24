<?php

namespace DdB\StuartApiBundle\Controller;

use DdB\StuartApiBundle\Event\StuartApiEvents;
use DdB\StuartApiBundle\Event\WebhookEvent;
use DdB\StuartApiBundle\StuartApi;
use Stuart\Job;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    private $eventDispatcher;

    public function __construct(StuartApi $api, Serializer $serializer, TranslatorInterface $translator, EventDispatcherInterface $eventDispatcher = null)
    {
        $this->api = $api;
        $this->serializer = $serializer;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
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

    public function validateJob(Request $request){
        try {
            $job = $this->api->createJobObjectFromRequest($request);
            $response = $this->api->validateJob($job);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $this->translator->trans($exception->getMessage(), [], 'Errors')
            ], 500);
        }
        return new JsonResponse(['message' => 'delivery.valid']);
    }

    public function priceJob(Request $request){
        try {
            $job = $this->api->createJobObjectFromRequest($request);
            $response = $this->api->priceJob($job, true);
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $this->translator->trans($exception->getMessage(), [], 'Errors')
            ], 500);
        }

        if($request->request->get('addVat')){
            $priceTaxExcluded = $response->amount;
            $priceTaxIncluded = (float)sprintf('%.2f',$priceTaxExcluded +$priceTaxExcluded * ($this->api->getVatRate()/ 100));
            $response->amount = $priceTaxIncluded;
        }
        return new JsonResponse($response);
    }

    public function webhook(Request $request){

        $env = $this->api->getEnvironment();

        if(in_array($_SERVER['REMOTE_ADDR'], $this->api->getuthorizedWebhookIps()[strtolower($env)]))
        {
            if($this->eventDispatcher){
                $event = new WebhookEvent($request);
                $this->eventDispatcher->dispatch(StuartApiEvents::WEBHOOK_API, $event);
            }
        }
        return new Response();
    }
}
