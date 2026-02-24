# CRM Integration Examples

This guide provides ready-to-use webhook configurations for popular CRM and automation platforms.

## HubSpot

### Contact Creation
```bash
php artisan webhook:add 1 https://api.hubapi.com/contacts/v1/contact \
  --headers='{"Authorization":"Bearer YOUR_ACCESS_TOKEN","Content-Type":"application/json"}'
```

### Deal Creation
```bash
php artisan webhook:add 1 https://api.hubapi.com/deals/v1/deal \
  --headers='{"Authorization":"Bearer YOUR_ACCESS_TOKEN","Content-Type":"application/json"}'
```

### Custom Object Creation
```bash
php artisan webhook:add 1 https://api.hubapi.com/crm/v3/objects/custom_object_name \
  --headers='{"Authorization":"Bearer YOUR_ACCESS_TOKEN","Content-Type":"application/json"}'
```

**Note:** Map your form fields to HubSpot properties. Use HubSpot's property names as keys.

---

## Zoho CRM

### Lead Creation
```bash
php artisan webhook:add 1 https://crm.zoho.com/crm/private/json/Leads/insertRecords \
  --headers='{"Authorization":"Zoho-oauthtoken YOUR_ACCESS_TOKEN","Content-Type":"application/json"}'
```

### Contact Creation
```bash
php artisan webhook:add 1 https://crm.zoho.com/crm/private/json/Contacts/insertRecords \
  --headers='{"Authorization":"Zoho-oauthtoken YOUR_ACCESS_TOKEN","Content-Type":"application/json"}'
```

### Custom Module
```bash
php artisan webhook:add 1 https://crm.zoho.com/crm/private/json/YOUR_MODULE/insertRecords \
  --headers='{"Authorization":"Zoho-oauthtoken YOUR_ACCESS_TOKEN","Content-Type":"application/json"}'
```

---

## Salesforce

### Lead Creation
```bash
php artisan webhook:add 1 https://yourinstance.salesforce.com/services/data/v58.0/sobjects/Lead \
  --headers='{"Authorization":"Bearer YOUR_ACCESS_TOKEN","Content-Type":"application/json"}'
```

### Contact Creation
```bash
php artisan webhook:add 1 https://yourinstance.salesforce.com/services/data/v58.0/sobjects/Contact \
  --headers='{"Authorization":"Bearer YOUR_ACCESS_TOKEN","Content-Type":"application/json"}'
```

### Custom Object
```bash
php artisan webhook:add 1 https://yourinstance.salesforce.com/services/data/v58.0/sobjects/Your_Custom_Object__c \
  --headers='{"Authorization":"Bearer YOUR_ACCESS_TOKEN","Content-Type":"application/json"}'
```

---

## Zapier

### Webhook Trigger
```bash
php artisan webhook:add 1 https://hooks.zapier.com/hooks/catch/YOUR_ZAP_ID/
```

**Zapier Configuration:**
1. Create a new Zap
2. Choose "Webhooks by Zapier" as trigger
3. Copy the webhook URL
4. Use the URL in the command above

---

## Make.com (Integromat)

### Webhook Trigger
```bash
php artisan webhook:add 1 https://hook.eu1.make.com/YOUR_WEBHOOK_ID
```

**Make.com Configuration:**
1. Create a new scenario
2. Add "Webhook" trigger
3. Copy the webhook URL
4. Use the URL in the command above

---

## Pipedrive

### Person Creation
```bash
php artisan webhook:add 1 https://api.pipedrive.com/v1/persons \
  --headers='{"Authorization":"Bearer YOUR_API_TOKEN"}'
```

### Deal Creation
```bash
php artisan webhook:add 1 https://api.pipedrive.com/v1/deals \
  --headers='{"Authorization":"Bearer YOUR_API_TOKEN"}'
```

---

## ActiveCampaign

### Contact Sync
```bash
php artisan webhook:add 1 https://youraccount.api-us1.com/api/3/contacts \
  --headers='{"Api-Token":"YOUR_API_TOKEN","Content-Type":"application/json"}'
```

### Deal Creation
```bash
php artisan webhook:add 1 https://youraccount.api-us1.com/api/3/deals \
  --headers='{"Api-Token":"YOUR_API_TOKEN","Content-Type":"application/json"}'
```

---

## Monday.com

### Item Creation
```bash
php artisan webhook:add 1 https://api.monday.com/v2 \
  --headers='{"Authorization":"Bearer YOUR_API_TOKEN","Content-Type":"application/json"}'
```

**Note:** Requires GraphQL mutation in your Monday.com automation.

---

## Custom Webhook Endpoints

### Generic REST API
```bash
php artisan webhook:add 1 https://your-api.com/endpoint \
  --headers='{"Authorization":"Bearer token","Content-Type":"application/json","X-API-Key":"your-key"}'
```

### PUT Method
```bash
php artisan webhook:add 1 https://your-api.com/endpoint --method=PUT \
  --headers='{"Authorization":"Bearer token"}'
```

---

## Data Mapping Examples

### HubSpot Contact Mapping
Your form data should map to HubSpot properties:
```json
{
  "firstname": "John",
  "lastname": "Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "company": "Acme Corp"
}
```

### Salesforce Lead Mapping
```json
{
  "FirstName": "John",
  "LastName": "Doe",
  "Email": "john@example.com",
  "Phone": "+1234567890",
  "Company": "Acme Corp"
}
```

### Zoho CRM Lead Mapping
```json
{
  "First Name": "John",
  "Last Name": "Doe",
  "Email": "john@example.com",
  "Phone": "+1234567890"
}
```

---

## Troubleshooting CRM Integrations

### HubSpot
- **401 Unauthorized**: Check API key is valid
- **400 Bad Request**: Verify property names exist in HubSpot
- **Rate Limited**: HubSpot has rate limits (100 requests/10 seconds)

### Zoho CRM
- **INVALID_TOKEN**: Refresh your access token
- **INVALID_DATA**: Check required fields are included
- **DUPLICATE_DATA**: Zoho prevents duplicate records

### Salesforce
- **INVALID_SESSION_ID**: Refresh access token
- **REQUIRED_FIELD_MISSING**: Check required fields
- **DUPLICATE_VALUE**: Check for duplicate records

### General Issues
- **SSL Errors**: Ensure webhook URL uses HTTPS
- **Timeout**: Webhooks have 30-second timeout
- **Large Payload**: Some CRMs limit payload size

---

## Testing CRM Integrations

### 1. Use Staging/Test Accounts
```bash
# Test with staging CRM first
php artisan webhook:add 1 https://test-hubspot.com/contacts/v1/contact \
  --headers='{"Authorization":"Bearer TEST_TOKEN"}'
```

### 2. Monitor Logs
```bash
tail -f storage/logs/laravel.log | grep webhook
```

### 3. Check Failed Jobs
```bash
php artisan queue:failed
```

### 4. Validate Data Format
Test with sample data before going live:
```bash
curl -X POST http://localhost:8000/api/v1/forms/1/submit \
  -H "Content-Type: application/json" \
  -d '{"firstname":"Test","lastname":"User","email":"test@example.com"}'
```

---

## Security Best Practices

- **Use HTTPS**: Always use HTTPS URLs for webhooks
- **API Keys**: Store keys securely, rotate regularly
- **Rate Limiting**: Implement rate limiting on your webhook endpoints
- **Validation**: Validate incoming webhook data
- **IP Whitelisting**: Consider IP restrictions if supported by CRM

---

## Support

For CRM-specific integration help:
- Check the CRM's API documentation
- Verify API credentials and permissions
- Test with small data sets first
- Monitor error logs for specific error messages
