# OAuth2 Grant Types

There are 4 different ways to obtain tokens from the token endpoint.

1. Authorization Code
2. Password
3. Client Credentials
4. Refresh Token

## Authorization Code Grant Type

[!!] This is the usual grant type .. 99% of apps should use this.

When using an Authorization header ..

    POST /oauth2/token HTTP/1.1
    Host: example.com
    Authorization: Basic XXX
    Content-Type: application/x-www-form-urlencoded
    Content-Length: 120

    grant_type=authorization_code&code=86e9bdcc-0a7c-4638-8115-b5aad307813d&redirect_uri=http%3A%2F%2Fexample.net%2Fcallback

Without using an Authorization header ..

    POST /oauth2/token HTTP/1.1
    Host: example.com
    Content-Type: application/x-www-form-urlencoded
    Content-Length: 218

    grant_type=authorization_code&code=86e9bdcc-0a7c-4638-8115-b5aad307813d&redirect_uri=http%3A%2F%2Fexample.net%2Fcallback&client_id=113ee767-e7f8-4294-a972-80a97a7f9926&client_secret=36e79816-8ee1-4e4a-9f2a-8cf670861f05


## Password Grant Type

[!!] This grant type allows the client to access the end users username/password. Avoid if you can!

When using an Authorization header ..

    POST /oauth2/token HTTP/1.1
    Host: example.com
    Authorization: Basic XXX
    Content-Type: application/x-www-form-urlencoded
    Content-Length: 49

    grant_type=password&username=kiall&password=kiall

Without using an Authorization header ..

    POST /oauth2/token HTTP/1.1
    Host: example.com
    Content-Type: application/x-www-form-urlencoded
    Content-Length: 147

    grant_type=password&username=kiall&password=kiall&client_id=113ee767-e7f8-4294-a972-80a97a7f9926&client_secret=36e79816-8ee1-4e4a-9f2a-8cf670861f05

## Client Credentials Grant Type

[!!] This grant type is not associated with any user, ie it should only be used for either "public data only" API calls, or SOA style use cases (eg backend service accessing a protected API)

When using an Authorization header ..

    POST /oauth2/token HTTP/1.1
    Host: example.com
    Authorization: Basic XXX
    Content-Type: application/x-www-form-urlencoded
    Content-Length: 29

    grant_type=client_credentials

Without using an Authorization header ..

    POST /oauth2/token HTTP/1.1
    Host: example.com
    Content-Type: application/x-www-form-urlencoded
    Content-Length: 127

    grant_type=client_credentials&client_id=113ee767-e7f8-4294-a972-80a97a7f9926&client_secret=36e79816-8ee1-4e4a-9f2a-8cf670861f05

## Refresh Token Grant Type

[!!] This grant type required the client to already have a refresh token from one of the other grant types.

When using an Authorization header ..

    POST /oauth2/token HTTP/1.1
    Host: example.com
    Authorization: Basic XXX
    Content-Type: application/x-www-form-urlencoded
    Content-Length: 75

    grant_type=refresh_token&refresh_token=e9a35af8-8236-4c50-912f-d257c2ece888

Without using an Authorization header ..

    POST /oauth2/token HTTP/1.1
    Host: example.com
    Content-Type: application/x-www-form-urlencoded
    Content-Length: 173

    grant_type=refresh_token&refresh_token=e9a35af8-8236-4c50-912f-d257c2ece888&client_id=113ee767-e7f8-4294-a972-80a97a7f9926&client_secret=36e79816-8ee1-4e4a-9f2a-8cf670861f05
