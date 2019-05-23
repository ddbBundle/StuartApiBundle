<?php


namespace DdB\StuartApiBundle\Event;


final class StuartApiEvents
{
    /**
     * Listener can use this event to set delivery states when the webhook is triggered
     *
     * @Event("DdB\StuartApiBundle\Event\WebhookEvent")
     */
    const WEBHOOK_API = "stuart.api.webhook";
}