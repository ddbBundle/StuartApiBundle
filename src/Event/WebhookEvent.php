<?php
namespace DdB\StuartApiBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class WebhookEvent extends Event
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(){
        return $this->request;
    }
}