# Apollo SDK 6.5.0 - Suite Client Management

**Release Date**: August 12, 2026  
**Package**: `ometra/apollo-sdk`  
**PHP**: 8.4+

## Summary

Feature release adding full CRUD support for clients, contacts, and users management through the Suite module. No breaking changes.

## New Resources

### ClientsResource
```php
Apollo::suite()->clients()->index()
Apollo::suite()->clients()->show($id)
Apollo::suite()->clients()->create($data)
Apollo::suite()->clients()->update($id, $data)
Apollo::suite()->clients()->delete($id)
```

### ContactsResource
```php
Apollo::suite()->clients($id)->contacts()->index()
Apollo::suite()->clients($id)->contacts()->show($contactId)
Apollo::suite()->clients($id)->contacts()->create($data)
Apollo::suite()->clients($id)->contacts()->update($contactId, $data)
Apollo::suite()->clients($id)->contacts()->delete($contactId)
```

### ClientUsersResource
```php
Apollo::suite()->clients($id)->users()->index()
Apollo::suite()->clients($id)->users()->show($uri)
Apollo::suite()->clients($id)->users()->create($data)
Apollo::suite()->clients($id)->users()->update($uri, $data)
Apollo::suite()->clients($id)->users()->delete($uri)
```

## API Endpoints

- `GET|POST /api/clients`
- `GET|PUT|DELETE /api/clients/{clientId}`
- `GET|POST /api/clients/{clientId}/contacts`
- `GET|PUT|DELETE /api/clients/{clientId}/contacts/{contactId}`
- `GET|POST /api/clients/{clientId}/users`
- `GET|PUT|DELETE /api/clients/{clientId}/users/{uriUser}`

## Requirements

- PHP 8.4+
- `SUITE_BASE_URL` environment variable configured
- Valid user token in Caronte context

## Backward Compatible

✅ No migration required  
✅ No breaking changes  
✅ Fully type-hinted
