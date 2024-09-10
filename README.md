# Payment gateway example

## Running tests

You can run the unit tests for this application with or without coverage. HTML coverage reports
will be generated in the untracked test-reports driectory.

```bash
docker compose run web composer tests-unit
```

```bash
docker compose run web composer tests-unit-coverage
```

## Start web server

The web server will run on `http://localhost:8080/`

```bash
docker compose up web
```

## Solution to the problem

This took a lot longer than anticipated ,and I'm not 100% happy with the solution. In retrospect this
needed more modelling with a domain expert beforehand.

The solution included the wo providers as requests and new providers can be composed in with ease.

## Authorisation

```http request
POST /v1/authorise HTTP/1.1
Host: localhost:8080
Content-Type: application/json
Accept: application/json

{
    "card_number": "4111111111111111",
    "expiry_date": "12/23",
    "cvv": "123",
    "amount": 100.00
}
```

Provider A will validate all cards that begin with a 4 and will invalidate everything else
(including those beginning with 5 as requested). As the spec left a lot of grey are it seems prudent to
allow as little as possible.

The tokens issued use OpenSSL encrypted JWTs for two reasons. Firstly, the internals of the payment provider
must never be exposed to the end consumer. Secondly, the token can now independently encapsulate much
of the important ID and validation information we require. As we sign the tokens we can ensure they are not spoofed.

## Capture

```http request
POST /v1/capture HTTP/1.1
Host: localhost:8080
Content-Type: application/json
Accept: application/json

{
    "auth_token": "02j9h98hd09j23d8h23f08n08jd2903hd23gd98h309dj203h82hfubuhksndfjkh8ho2nuh5645sfuh",
    "amount": 100.00
}
```

The capture endpoint will take the issued token and complete the transaction so long as the hold is still in place.
If the cpature was already processed then the operation is ignored.

The spec calls for an error response when a capture has already occurred. However, this violates the 
idempotency of the HTTP call, where identical requests generate different behaviours. It may be better
to simply acknowledge the success of a repeat call without reprocessing.

Passing the amount again here also seems redundant, as the token either contains (in this case) or refers to (in the spec)
a valid hold which includes the amount.

## Refund

```http request
POST /v1/refund HTTP/1.1
Host: localhost:8080
Content-Type: application/json
Accept: application/json

{
    "transaction_id": "tx123456789",
    "amount": 100.00
}
```

This is where things get more difficult. We have now leaked a potentially internal value from our payment
gateway (the transaction ID) and it does not map back to a 3rd party unless we can either parse the ID for a
routing value or keep our own record of transactions, which we may not want to do.

An improvement would be to move the capture and refund endpoints to also use tokens which we sign. This wasy
we can act as a secure proxy service rather than maintaining our own state.

Passing the amount again here also seems redundant, as the transaction must refer to a valid transaction which
includes the amount.

## Improvements

This took a lot longer than 2 hours and there are some things left to be improved here with the addition of time:
- PhpStan is setup but issues remain unaddressed
- End-to-end tests must be supplied and the application does not run perfectly yet
- The requirement to flag providers on and off requires implementation through either ENV_VARS for the container or a configuration system
- Logging is minimal, but a lot more information should be passed to STDOUT so the container can be monitored by CloudWatch
- A CI test pipeline needs to be created
- The application boostrap can be optimised
- More detailed docs are needed
- The cangelog needs populating

All of that said, I hope it gives a favour of the engineering approach in a limited timeframe. I will fox these issues
in time if I can so the example is more robust.
