# OauthTokenBody

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**grant_type** | **string** | Valid values: authorization_code | [default to 'authorization_code']
**code** | **string** | Authorization code from the UPS login system. | 
**redirect_uri** | **string** | Callback URL for the requesting application. | 
**code_verifier** | **string** | **Only required for PKCE flow**. A randomly generated secret created by the calling application that can be verified by the authorization server. | [optional] 
**client_id** | **string** | **Only required for PKCE flow**. The public identifier for your application, obtained when you, the developer first registered the application. | [optional] 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

