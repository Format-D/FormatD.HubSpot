
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

```
FormatD:
  HubSpot:
    api:
      portalId: 'my-hubspot-portal-id'
      accessToken: 'my-secret-access-token'
```

## Field Mappings

The package comes with the following default mapping. This mapping can be modified or additional mappings can be configured.
The objectTypeId is a sort of field-type defined by HubSpot.

```
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

