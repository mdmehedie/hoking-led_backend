# Redis API Documentation

## 🌐 API Endpoints

### **Base URL**
```
http://your-domain.com/admin/redis
```

### **Authentication**
All endpoints require:
- Authentication middleware
- `access admin` permission
- CSRF token for POST requests

---

## 📋 Endpoints

### **1. Get Redis Configuration**
```http
GET /admin/redis/config
```

**Response:**
```json
{
    "success": true,
    "config": {
        "client": "phpredis",
        "host": "127.0.0.1",
        "port": 6379,
        "password": "***",
        "database": 0,
        "cache_db": 1,
        "session_db": 2,
        "queue_db": 3,
        "prefix": "laravel_",
        "cache_ttl": 3600,
        "session_ttl": 120,
        "cache_enabled": true,
        "session_enabled": true,
        "queue_enabled": true
    }
}
```

**Example Usage:**
```javascript
fetch('/admin/redis/config')
    .then(response => response.json())
    .then(data => {
        console.log('Redis config:', data.config);
    });
```

---

### **2. Test Redis Connection**
```http
POST /admin/redis/test-connection
```

**Response:**
```json
{
    "success": true,
    "message": "Redis connection successful",
    "config": {...},
    "ping_result": "PONG",
    "cache_test": true
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Redis connection error: Connection refused",
    "config": {...},
    "error": "Connection refused"
}
```

**Example Usage:**
```javascript
fetch('/admin/redis/test-connection', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert('Redis is working!');
    } else {
        alert('Redis error: ' + data.message);
    }
});
```

---

### **3. Get Server Information**
```http
GET /admin/redis/server-info
```

**Response:**
```json
{
    "success": true,
    "info": {
        "redis_version": "7.0.0",
        "redis_mode": "standalone",
        "os": "Linux 5.4.0",
        "arch_bits": 64,
        "uptime_in_seconds": 86400,
        "connected_clients": 5,
        "used_memory": "2.50M",
        "used_memory_human": "2.50M",
        "used_memory_peak": "3.00M",
        "maxmemory": "256.00M",
        "keyspace_hits": 1500,
        "keyspace_misses": 100,
        "total_commands_processed": 2500,
        "instantaneous_ops_per_sec": 10
    },
    "version": "7.0.0",
    "uptime": 86400,
    "connected_clients": 5,
    "used_memory": "2.50M"
}
```

**Example Usage:**
```javascript
fetch('/admin/redis/server-info')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('redis-version').textContent = data.version;
            document.getElementById('redis-memory').textContent = data.used_memory;
            document.getElementById('redis-clients').textContent = data.connected_clients;
        }
    });
```

---

### **4. Clear Configuration Cache**
```http
POST /admin/redis/clear-cache
```

**Response:**
```json
{
    "success": true,
    "message": "Redis configuration cache cleared successfully"
}
```

**Example Usage:**
```javascript
fetch('/admin/redis/clear-cache', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert('Redis cache cleared!');
        location.reload();
    }
});
```

---

## 🔧 Frontend Integration

### **Vue.js Component Example**
```vue
<template>
    <div>
        <h3>Redis Status</h3>
        <button @click="testConnection">Test Connection</button>
        <button @click="getServerInfo">Refresh Info</button>
        <button @click="clearCache">Clear Cache</button>
        
        <div v-if="status">
            <p>Status: {{ status.success ? 'Connected' : 'Disconnected' }}</p>
            <p>Version: {{ serverInfo.version }}</p>
            <p>Memory: {{ serverInfo.used_memory }}</p>
            <p>Clients: {{ serverInfo.connected_clients }}</p>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            status: null,
            serverInfo: {}
        }
    },
    methods: {
        async testConnection() {
            try {
                const response = await fetch('/admin/redis/test-connection', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                this.status = await response.json();
            } catch (error) {
                console.error('Error testing Redis:', error);
            }
        },
        
        async getServerInfo() {
            try {
                const response = await fetch('/admin/redis/server-info');
                const data = await response.json();
                if (data.success) {
                    this.serverInfo = data;
                }
            } catch (error) {
                console.error('Error getting server info:', error);
            }
        },
        
        async clearCache() {
            try {
                const response = await fetch('/admin/redis/clear-cache', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                if (data.success) {
                    alert('Cache cleared successfully!');
                }
            } catch (error) {
                console.error('Error clearing cache:', error);
            }
        }
    },
    
    mounted() {
        this.testConnection();
        this.getServerInfo();
    }
}
</script>
```

### **React Component Example**
```jsx
import React, { useState, useEffect } from 'react';

const RedisMonitor = () => {
    const [status, setStatus] = useState(null);
    const [serverInfo, setServerInfo] = useState({});
    const [loading, setLoading] = useState(false);

    const getCsrfToken = () => {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    };

    const testConnection = async () => {
        setLoading(true);
        try {
            const response = await fetch('/admin/redis/test-connection', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });
            const data = await response.json();
            setStatus(data);
        } catch (error) {
            console.error('Error testing Redis:', error);
        } finally {
            setLoading(false);
        }
    };

    const getServerInfo = async () => {
        try {
            const response = await fetch('/admin/redis/server-info');
            const data = await response.json();
            if (data.success) {
                setServerInfo(data);
            }
        } catch (error) {
            console.error('Error getting server info:', error);
        }
    };

    const clearCache = async () => {
        try {
            const response = await fetch('/admin/redis/clear-cache', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            });
            const data = await response.json();
            if (data.success) {
                alert('Cache cleared successfully!');
                getServerInfo();
            }
        } catch (error) {
            console.error('Error clearing cache:', error);
        }
    };

    useEffect(() => {
        testConnection();
        getServerInfo();
    }, []);

    return (
        <div className="redis-monitor">
            <h3>Redis Status</h3>
            <button onClick={testConnection} disabled={loading}>
                {loading ? 'Testing...' : 'Test Connection'}
            </button>
            <button onClick={getServerInfo}>Refresh Info</button>
            <button onClick={clearCache}>Clear Cache</button>

            {status && (
                <div className="status">
                    <p>Status: {status.success ? 'Connected' : 'Disconnected'}</p>
                    {!status.success && <p>Error: {status.message}</p>}
                </div>
            )}

            {serverInfo.success && (
                <div className="server-info">
                    <p>Version: {serverInfo.version}</p>
                    <p>Memory: {serverInfo.used_memory}</p>
                    <p>Clients: {serverInfo.connected_clients}</p>
                    <p>Uptime: {serverInfo.uptime} seconds</p>
                </div>
            )}
        </div>
    );
};

export default RedisMonitor;
```

---

## 🚨 Error Handling

### **Common Error Responses**

#### **Authentication Error**
```json
{
    "success": false,
    "message": "Unauthenticated"
}
```

#### **Permission Error**
```json
{
    "success": false,
    "message": "Forbidden"
}
```

#### **Connection Error**
```json
{
    "success": false,
    "message": "Redis connection error: Connection refused",
    "error": "Connection refused"
}
```

#### **Server Error**
```json
{
    "success": false,
    "message": "Internal server error"
}
```

### **JavaScript Error Handling**
```javascript
async function safeRedisRequest(url, options = {}) {
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Request failed');
        }
        
        return data;
    } catch (error) {
        console.error('Redis API error:', error);
        throw error;
    }
}

// Usage
try {
    const config = await safeRedisRequest('/admin/redis/config');
    console.log('Redis config:', config.config);
} catch (error) {
    alert('Failed to get Redis config: ' + error.message);
}
```

---

## 📝 Rate Limiting

Consider implementing rate limiting for Redis API endpoints:

```php
// In routes/redis.php
Route::middleware(['auth', 'can:access admin', 'throttle:60,1'])->group(function () {
    // Redis routes here
});
```

---

## 🔒 Security Considerations

1. **Authentication**: All endpoints require authentication and admin permissions
2. **CSRF Protection**: POST requests require valid CSRF token
3. **Rate Limiting**: Consider implementing rate limiting for API endpoints
4. **Input Validation**: Validate all inputs in controller methods
5. **Error Handling**: Don't expose sensitive Redis configuration in error messages
6. **Logging**: Log all Redis API access for audit purposes

---

## 📊 Monitoring & Analytics

### **API Usage Tracking**
```javascript
// Track API calls
function trackRedisApiCall(endpoint, success, duration) {
    // Send to analytics service
    gtag('event', 'redis_api_call', {
        endpoint: endpoint,
        success: success,
        duration: duration
    });
}

// Usage with timing
const startTime = performance.now();
fetch('/admin/redis/test-connection', { method: 'POST' })
    .then(response => response.json())
    .then(data => {
        const duration = performance.now() - startTime;
        trackRedisApiCall('test-connection', data.success, duration);
    });
```

---

**Last Updated**: March 7, 2026  
**Version**: 1.0.0
