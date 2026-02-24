# Forms API Documentation

## Overview
The Forms API provides endpoints for retrieving form configurations and submitting form data. This API supports dynamic forms created through the admin panel with customizable fields, email notifications, and lead storage.

## Base URL
```
http://localhost:8000/api/v1
```

## Endpoints

### 1. Get Forms List
Retrieve all active forms that have email notifications or lead storage enabled.

**Endpoint:** `GET /forms`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Response:**
```json
{
  "success": true,
  "message": "Forms retrieved successfully",
  "data": {
    "forms": [
      {
        "id": 1,
        "name": "Contact Form",
        "fields": [
          {
            "type": "text",
            "label": "Name",
            "placeholder": "Enter your name",
            "required": true
          },
          {
            "type": "email",
            "label": "Email",
            "placeholder": "Enter your email",
            "required": true
          },
          {
            "type": "textarea",
            "label": "Message",
            "placeholder": "Enter your message",
            "required": false
          }
        ],
        "success_message": "Thank you for contacting us!"
      }
    ]
  }
}
```

### 2. Submit Form
Submit form data to a specific form.

**Endpoint:** `POST /forms/{formId}/submit`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "message": "This is a test message"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Form submitted successfully",
  "data": {
    "message": "Thank you for contacting us!"
  }
}
```

## Error Responses

### 404 Not Found
```json
{
  "success": false,
  "message": "Form not found",
  "data": []
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "data": {
    "errors": {
      "email": ["The email field is required."]
    }
  }
}
```

## Form Field Types

### Text Field
```json
{
  "type": "text",
  "label": "Name",
  "placeholder": "Enter your name",
  "required": true
}
```

### Email Field
```json
{
  "type": "email",
  "label": "Email",
  "placeholder": "Enter your email",
  "required": true
}
```

### Textarea Field
```json
{
  "type": "textarea",
  "label": "Message",
  "placeholder": "Enter your message",
  "required": false
}
```

### Select Field
```json
{
  "type": "select",
  "label": "Category",
  "options": ["General", "Support", "Sales"],
  "required": true
}
```

### Checkbox Field
```json
{
  "type": "checkbox",
  "label": "Subscribe to newsletter",
  "required": false
}
```

## Usage Examples

### JavaScript (Fetch API)
```javascript
// Get forms list
fetch('http://localhost:8000/api/v1/forms')
  .then(response => response.json())
  .then(data => {
    console.log('Forms:', data.data.forms);
  });

// Submit form
fetch('http://localhost:8000/api/v1/forms/1/submit', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    name: 'John Doe',
    email: 'john@example.com',
    message: 'This is a test message'
  })
})
.then(response => response.json())
.then(data => {
  console.log('Success:', data.data.message);
});
```

### cURL Examples
```bash
# Get forms list
curl -X GET "http://localhost:8000/api/v1/forms" \
  -H "Accept: application/json"

# Submit form
curl -X POST "http://localhost:8000/api/v1/forms/1/submit" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "message": "This is a test message"
  }'
```

## Notes

- Only forms with `email_notifications` or `store_leads` enabled are returned
- Form submission automatically handles lead storage and email notifications based on form settings
- All form data is stored as JSON in the leads table
- Email notifications are sent to addresses configured in the form settings
- The API follows RESTful conventions and returns consistent response formats
