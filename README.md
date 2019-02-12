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

- nextPickupSlot($city = "Bordeaux") : Get the next available pickup slot for the city
- public function simpleJob(Request $request, $pickupAddress, $dropOffAddress, $packageType = 'small') : Returns a Job object if succesfull

## Translations

All the errors returned by the Stuart API can be translated using the Errors.(lang).yaml files inside Resources/translations