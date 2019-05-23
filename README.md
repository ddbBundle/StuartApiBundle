# StuartApiBundle

Simple bundle using the stuart php library to integrate with symfony

## Configuration

```yaml 
stuart_api:
  private_key: "stuart super private key"
  public_key: "stuart public key"
  environment: "SANDBOX" // OR "PRODUCTION"
```

## Functions

### StuartApi

Used to create and dispatch jobs

- public function createJobObjectFromRequest(Request $request)
- public function createJobObject(
- public function addJob(Job $job)

### StuartApiController

These function have routes exposed and return a JSONResponse

- nextPickupSlot($city = "Bordeaux") : Get the next available pickup slot for the city
- validateJob(Request $request)
- priceJob(Request $request)

### Events

StuartApiEvents.php defines constants for the event names

#### WebhookEvent.php

This event redispatches any requests on the /webhook route. You can register an EventSubscriberInterface in your application to listen to these events and update your orders accordingly.

```php
namespace App\EventSubscriber;

use DdB\StuartApiBundle\Event\StuartApiEvents;
use DdB\StuartApiBundle\Event\WebhookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StuartWebhookSubscriber implements EventSubscriberInterface
{
    public function onStuartApiWebhook(WebhookEvent $event)
    {
        $request = $event->getRequest();
        //Do something with the request
    }

    public static function getSubscribedEvents()
    {
        return [
           StuartApiEvents::WEBHOOK_API => 'onStuartApiWebhook',
        ];
    }
}
```
## Translations

All the errors returned by the Stuart API can be translated using the Errors.(lang).yaml files inside Resources/translations