
# FormatD.HubSpot

A HubSpot form finisher that sends neos form builder forms to hubspot


## What does it do?

This package provides a form finisher for neos form builder and a php service to send data to hubspot forms.
Several forms with field mappings can be configured via settings and selected in the backend form finisher.


## Kompatiblität

Versioning scheme:

     1.0.0 
     | | |
     | | Bugfix Releases (non breaking)
     | Neos Compatibility Releases (non breaking except framework dependencies)
     Feature Releases (breaking)

Releases und compatibility:

| Package-Version | Neos CMS Version  |
|-----------------|-------------------|
| 1.0.x           | 8.x               |


## API-Credentials

Create API-Credentials in HubSpot and add them to neos settings:

```YAML
FormatD:
  HubSpot:
    api:
      portalId: 'my-hubspot-portal-id'
      accessToken: 'my-secret-access-token'
```

## Field Mappings

The package comes with the following default mapping. This mapping can be modified or additional mappings can be configured.
The objectTypeId is a sort of field-type defined by HubSpot.

```YAML
FormatD:
  HubSpot:
    formFinisherMappings:
      default:
        firstname:
          objectTypeId: '0-1'
          name: 'firstname'
          value: '${fieldValue}'
        lastname:
          objectTypeId: '0-1'
          name: 'lastname'
          value: '${fieldValue}'
        company:
          objectTypeId: '0-1'
          name: 'company'
          value: '${fieldValue}'
        email:
          objectTypeId: '0-1'
          name: 'email'
          value: '${fieldValue}'
        phone:
          objectTypeId: '0-1'
          name: 'phone'
          value: '${fieldValue}'
        subject:
          objectTypeId: '0-5'
          name: 'subject'
          value: "${'Request from ' + formValues.company}"
        content:
          objectTypeId: '0-5'
          name: 'content'
          value: '${fieldValue}'
```

## EEL-Helper for file upload

If you want to use a file upload field in your form you need to use the EEL-Helper `HFile.uploadFileAndGetUrl`. This
helper uploads the file to the file manager of HubSpot and returns the new URL as a field value, before the form is
submitted. For the file upload you need to define the folder ID of the folder in HubSpot. You do it either in the
setting:

```YAML
FormatD:
  HubSpot:
    api:
      defaultUploadFolderId: '111111111111'
```

or you set the optional parameter in the helper: `${HFile.uploadFileAndGetUrl(fieldValue, 111111111111)}`.

Here is how you can include the file upload field in your finisher mapping:

```YAML
FormatD:
  HubSpot:
    formFinisherMappings:
      default:
        fileUpload:
          objectTypeId: '0-5'
          name: 'fileUpload'
          value: '${HFile.uploadFileAndGetUrl(fieldValue, 111111111111)}'
```

## Extending

Eel expressions can be used in the mapping configuration. These variables form the context can be used in these eel expressions per default: `formValues`, `fieldValue`.
If you need more your best bet would be to extend the finisher class and provide more context by overriding the `collectMappingContextVariables()` method in the HubSpotFormFinisher class.
This way it would be possible to provide additional data form session or other sources.
