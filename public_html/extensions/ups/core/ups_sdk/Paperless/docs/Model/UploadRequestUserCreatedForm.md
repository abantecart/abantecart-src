# UploadRequestUserCreatedForm

## Properties
Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**user_created_form_file_name** | **string** | The name of the file. | 
**user_created_form_file** | **string** | The user created form file.  The maximum allowable size of each file is restricted to 10 MB. Should be a base64 encoded string.  Note: The maximum allowable size of each file is restriced to 1MB in CIE (Customer Integration Environment). | 
**user_created_form_file_format** | **string** | The UserCreatedForm file format.  The allowed file formats are bmp, doc, gif, jpg, pdf, png, rtf, tif, txt and xls. The only exceptions for having file format of length 4 character are docx and xlsx. All other file formats needs to be of length 3. | 
**user_created_form_document_type** | **string** | The type of documents in UserCreatedForm file.  The allowed document types are 001 - Authorization Form, 002 - Commercial Invoice, 003 - Certificate of Origin, 004 - Export Accompanying Document, 005 - Export License, 006 - Import Permit, 007 - One Time NAFTA, 008 - Other Document, 009 - Power of Attorney, 010 - Packing List, 011 - SED Document, 012 - Shipper&#x27;s Letter of Instruction, 013 - Declaration. The total number of documents allowed per file or per shipment is 13. Each document type needs to be three digits. | 

[[Back to Model list]](../../README.md#documentation-for-models) [[Back to API list]](../../README.md#documentation-for-api-endpoints) [[Back to README]](../../README.md)

