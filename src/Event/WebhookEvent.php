<?php
namespace DdB\StuartApiBundle\Event;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class WebhookEvent extends Event
{
    protected $request;

    protected $data;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->data = json_decode($request->getContent(), true);
    }

    public function getRequest(){
        return $this->request;
    }

    public function getData() {
        return $this->data;
    }
}
