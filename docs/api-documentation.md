# Workflow Management System API Documentation

## Overview

This API provides a complete workflow management system for handling leave requests, mission requests, user management, and approval workflows. The API follows RESTful principles and returns JSON responses with a consistent structure.

## Base URL

```
http://localhost:8000/api
```

## Authentication

All protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer <token>
```

## Response Format

All API responses follow this format:

```json
{
    "status": "success|error",
    "data": {},
    "message": "Optional message",
    "meta": {
        "pagination": {},
        "filters": {}
    }
}
```

## Authentication Endpoints

### Login

```
POST /auth/login
```

**Request Body:**

```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**

```json
{
    "status": "success",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com"
            // ... other user fields
        },
        "token": "1|abc123..."
    },
    "message": "Login successful"
}
```

### Register

```
POST /auth/register
```

**Request Body:**

```json
{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password",
    "employee_id": "EMP001",
    "phone": "1234567890"
}
```

**Response:**

```json
{
    "status": "success",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com"
            // ... other user fields
        },
        "token": "1|abc123..."
    },
    "message": "Registration successful"
}
```

### Logout

```
POST /auth/logout
```

**Response:**

```json
{
    "status": "success",
    "message": "Logout successful"
}
```

### Get Profile

```
GET /auth/profile
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "user@example.com",
    // ... other user fields
    "roles": [...],
    "departments": [...]
  },
  "message": "Profile retrieved successfully"
}
```

### Update Profile

```
PUT /auth/profile
```

**Request Body:**

```json
{
    "name": "John Smith",
    "email": "johnsmith@example.com",
    "phone": "0987654321"
    // password and password_confirmation (optional)
}
```

**Response:**

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "name": "John Smith",
        "email": "johnsmith@example.com"
        // ... other user fields
    },
    "message": "Profile updated successfully"
}
```

## Leave Request Endpoints

### Get Leave Requests

```
GET /leave-requests
```

**Query Parameters:**

-   `status` - Filter by status (pending, approved, rejected)
-   `user_id` - Filter by user ID
-   `start_date` - Filter by start date
-   `end_date` - Filter by end date
-   `per_page` - Number of items per page (default: 15)

**Response:**

```json
{
  "status": "success",
  "data": {
    "data": [...],
    "links": {...},
    "meta": {...}
  },
  "message": "Leave requests retrieved successfully"
}
```

### Create Leave Request

```
POST /leave-requests
```

**Request Body:**

```json
{
    "leave_type": "annual",
    "days_requested": 5,
    "start_date": "2023-06-01",
    "end_date": "2023-06-05",
    "reason": "Vacation",
    "emergency_contact": "1234567890",
    "medical_certificate": "path/to/certificate.pdf", // optional
    "is_paid": true
}
```

**Response:**

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "user_id": 1,
        "leave_type": "annual"
        // ... other fields
    },
    "message": "Leave request created successfully"
}
```

### Get Leave Request

```
GET /leave-requests/{id}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "user_id": 1,
    "leave_type": "annual",
    // ... other fields
    "user": {...},
    "approvals": [...]
  },
  "message": "Leave request retrieved successfully"
}
```

### Update Leave Request

```
PUT /leave-requests/{id}
```

**Request Body:**

```json
{
    "leave_type": "sick",
    "days_requested": 3,
    "start_date": "2023-06-01",
    "end_date": "2023-06-03",
    "reason": "Medical appointment"
}
```

**Response:**

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "user_id": 1,
        "leave_type": "sick"
        // ... other fields
    },
    "message": "Leave request updated successfully"
}
```

### Delete Leave Request

```
DELETE /leave-requests/{id}
```

**Response:**

```json
{
    "status": "success",
    "message": "Leave request deleted successfully"
}
```

## Mission Request Endpoints

### Get Mission Requests

```
GET /mission-requests
```

**Query Parameters:**

-   `status` - Filter by status (pending, approved, rejected)
-   `user_id` - Filter by user ID
-   `start_date` - Filter by start date
-   `end_date` - Filter by end date
-   `per_page` - Number of items per page (default: 15)

**Response:**

```json
{
  "status": "success",
  "data": {
    "data": [...],
    "links": {...},
    "meta": {...}
  },
  "message": "Mission requests retrieved successfully"
}
```

### Create Mission Request

```
POST /mission-requests
```

**Request Body:**

```json
{
    "destination": "New York",
    "purpose": "Business meeting",
    "estimated_cost": 2500.0,
    "transportation_mode": "Flight",
    "accommodation_details": "Hotel XYZ",
    "start_date": "2023-06-10",
    "end_date": "2023-06-15",
    "reason": "Client meeting"
}
```

**Response:**

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "user_id": 1,
        "destination": "New York"
        // ... other fields
    },
    "message": "Mission request created successfully"
}
```

### Get Mission Request

```
GET /mission-requests/{id}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "user_id": 1,
    "destination": "New York",
    // ... other fields
    "user": {...},
    "approvals": [...]
  },
  "message": "Mission request retrieved successfully"
}
```

### Update Mission Request

```
PUT /mission-requests/{id}
```

**Request Body:**

```json
{
    "destination": "Los Angeles",
    "purpose": "Conference",
    "estimated_cost": 3000.0
}
```

**Response:**

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "user_id": 1,
        "destination": "Los Angeles"
        // ... other fields
    },
    "message": "Mission request updated successfully"
}
```

### Delete Mission Request

```
DELETE /mission-requests/{id}
```

**Response:**

```json
{
    "status": "success",
    "message": "Mission request deleted successfully"
}
```

## Approval Endpoints

### Get Pending Approvals

```
GET /approvals/pending
```

**Response:**

```json
{
  "status": "success",
  "data": [...],
  "message": "Pending requests retrieved successfully"
}
```

### Approve Request

```
POST /approvals/approve
```

**Request Body:**

```json
{
    "request_id": 1,
    "request_type": "leave",
    "comments": "Approved for vacation"
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "request": {...},
    "approval": {...}
  },
  "message": "Request approved successfully"
}
```

### Reject Request

```
POST /approvals/reject
```

**Request Body:**

```json
{
    "request_id": 1,
    "request_type": "leave",
    "comments": "Insufficient notice"
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "request": {...},
    "approval": {...}
  },
  "message": "Request rejected successfully"
}
```

### Get Request Details

```
GET /requests/{type}/{id}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 1,
    "request_type": "leave",
    // ... other fields
    "user": {...},
    "approvals": [...]
  },
  "message": "Request retrieved successfully"
}
```

## User Management Endpoints (Admin Only)

### Get Users

```
GET /users
```

**Query Parameters:**

-   `search` - Search by name, email, or employee_id
-   `is_active` - Filter by active status
-   `per_page` - Number of items per page (default: 15)

**Response:**

```json
{
  "status": "success",
  "data": {
    "data": [...],
    "links": {...},
    "meta": {...}
  },
  "message": "Users retrieved successfully"
}
```

### Create User

```
POST /users
```

**Request Body:**

```json
{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "password",
    "password_confirmation": "password",
    "employee_id": "EMP002",
    "phone": "0987654321",
    "is_active": true,
    "department_ids": [1, 2],
    "role_ids": [1, 3]
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 2,
    "name": "Jane Smith",
    "email": "jane@example.com",
    // ... other fields
    "roles": [...],
    "departments": [...]
  },
  "message": "User created successfully"
}
```

### Update User

```
PUT /users/{id}
```

**Request Body:**

```json
{
    "name": "Jane Doe",
    "email": "janedoe@example.com",
    "phone": "1112223333",
    "is_active": true,
    "department_ids": [1],
    "role_ids": [1, 2]
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 2,
    "name": "Jane Doe",
    "email": "janedoe@example.com",
    // ... other fields
    "roles": [...],
    "departments": [...]
  },
  "message": "User updated successfully"
}
```

### Delete User

```
DELETE /users/{id}
```

**Response:**

```json
{
    "status": "success",
    "message": "User deleted successfully"
}
```

### Assign Role to User

```
POST /users/assign-role
```

**Request Body:**

```json
{
    "user_id": 2,
    "role_id": 3
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 2,
    "name": "Jane Doe",
    // ... other fields
    "roles": [...],
    "departments": [...]
  },
  "message": "Role assigned successfully"
}
```

### Get Roles

```
GET /roles
```

**Response:**

```json
{
  "status": "success",
  "data": [...],
  "message": "Roles retrieved successfully"
}
```

## Department Management Endpoints

### Get Departments

```
GET /departments
```

**Query Parameters:**

-   `search` - Search by name, code, or description
-   `is_active` - Filter by active status
-   `per_page` - Number of items per page (default: 15)

**Response:**

```json
{
  "status": "success",
  "data": {
    "data": [...],
    "links": {...},
    "meta": {...}
  },
  "message": "Departments retrieved successfully"
}
```

### Create Department

```
POST /departments
```

**Request Body:**

```json
{
    "name": "Finance Department",
    "description": "Financial management and accounting",
    "code": "FIN",
    "manager_id": 1,
    "is_active": true
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 3,
    "name": "Finance Department",
    "code": "FIN",
    // ... other fields
    "manager": {...}
  },
  "message": "Department created successfully"
}
```

### Update Department

```
PUT /departments/{id}
```

**Request Body:**

```json
{
    "name": "Accounting Department",
    "description": "Accounting and financial reporting",
    "code": "ACC",
    "manager_id": 2,
    "is_active": true
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 3,
    "name": "Accounting Department",
    "code": "ACC",
    // ... other fields
    "manager": {...}
  },
  "message": "Department updated successfully"
}
```

### Delete Department

```
DELETE /departments/{id}
```

**Response:**

```json
{
    "status": "success",
    "message": "Department deleted successfully"
}
```

## Workflow Management Endpoints

### Get Workflows

```
GET /workflows
```

**Query Parameters:**

-   `type` - Filter by workflow type (leave, mission, other)
-   `department_id` - Filter by department ID
-   `is_active` - Filter by active status
-   `per_page` - Number of items per page (default: 15)

**Response:**

```json
{
  "status": "success",
  "data": {
    "data": [...],
    "links": {...},
    "meta": {...}
  },
  "message": "Workflows retrieved successfully"
}
```

### Create Workflow

```
POST /workflows
```

**Request Body:**

```json
{
    "name": "Finance Leave Workflow",
    "type": "leave",
    "department_id": 3,
    "approval_steps": "[{\"role\": \"Team Leader\", \"order\": 1}, {\"role\": \"CFO\", \"order\": 2}]",
    "conditions": "{}",
    "is_active": true
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 5,
    "name": "Finance Leave Workflow",
    "type": "leave",
    // ... other fields
    "department": {...},
    "creator": {...}
  },
  "message": "Workflow created successfully"
}
```

### Update Workflow

```
PUT /workflows/{id}
```

**Request Body:**

```json
{
    "name": "Finance Mission Workflow",
    "type": "mission",
    "department_id": 3,
    "approval_steps": "[{\"role\": \"Team Leader\", \"order\": 1}, {\"role\": \"CFO\", \"order\": 2}, {\"role\": \"CEO\", \"order\": 3}]",
    "is_active": true
}
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "id": 5,
    "name": "Finance Mission Workflow",
    "type": "mission",
    // ... other fields
    "department": {...},
    "creator": {...}
  },
  "message": "Workflow updated successfully"
}
```

## Dashboard & Analytics Endpoints

### Get Dashboard Statistics

```
GET /dashboard/stats
```

**Response:**

```json
{
    "status": "success",
    "data": {
        "user_requests": {
            "pending": 2,
            "approved": 5,
            "rejected": 1,
            "total": 8
        },
        "pending_approvals": 3,
        "unread_notifications": 2,
        "department_stats": {
            "total_users": 15,
            "active_requests": 5
        }
    },
    "message": "Dashboard statistics retrieved successfully"
}
```

### Get Analytics Data

```
GET /analytics
```

**Response:**

```json
{
  "status": "success",
  "data": {
    "request_trends": {...},
    "approval_statistics": {
      "approved": 15,
      "rejected": 3,
      "pending": 5
    },
    "department_performance": {
      "average_processing_time": 2.5,
      "approval_rate": 83.3
    }
  },
  "message": "Analytics data retrieved successfully"
}
```

## Notification Endpoints

### Get Notifications

```
GET /notifications
```

**Query Parameters:**

-   `is_read` - Filter by read status
-   `type` - Filter by notification type
-   `per_page` - Number of items per page (default: 15)

**Response:**

```json
{
  "status": "success",
  "data": {
    "data": [...],
    "links": {...},
    "meta": {...}
  },
  "message": "Notifications retrieved successfully"
}
```

### Mark Notification as Read

```
POST /notifications/{id}/read
```

**Response:**

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "is_read": true,
        "read_at": "2023-05-20T10:30:00.000000Z"
        // ... other fields
    },
    "message": "Notification marked as read"
}
```

### Mark All Notifications as Read

```
POST /notifications/read-all
```

**Response:**

```json
{
    "status": "success",
    "message": "All notifications marked as read"
}
```

## File Upload Endpoints

### Upload File

```
POST /upload
```

**Form Data:**

-   `file` - The file to upload
-   `type` - Type of file (document, image, medical_certificate)

**Response:**

```json
{
    "status": "success",
    "data": {
        "original_name": "certificate.pdf",
        "filename": "1684567890_certificate.pdf",
        "path": "medical_certificates/1684567890_certificate.pdf",
        "url": "/storage/medical_certificates/1684567890_certificate.pdf",
        "size": 102400,
        "mime_type": "application/pdf",
        "type": "medical_certificate"
    },
    "message": "File uploaded successfully"
}
```

## Search Endpoints

### Search Requests

```
GET /search/requests
```

**Query Parameters:**

-   `query` - Search query (required)
-   `type` - Filter by request type (leave, mission)
-   `status` - Filter by status
-   `per_page` - Number of items per page (default: 15)

**Response:**

```json
{
  "status": "success",
  "data": {
    "data": [...],
    "links": {...},
    "meta": {...}
  },
  "message": "Search results retrieved successfully"
}
```

### Get Request History

```
GET /requests/history
```

**Query Parameters:**

-   `type` - Filter by request type (leave, mission)
-   `status` - Filter by status
-   `start_date` - Filter by start date
-   `end_date` - Filter by end date
-   `per_page` - Number of items per page (default: 15)

**Response:**

```json
{
  "status": "success",
  "data": {
    "data": [...],
    "links": {...},
    "meta": {...}
  },
  "message": "Request history retrieved successfully"
}
```

## Error Responses

The API returns appropriate HTTP status codes for different error conditions:

-   `400` - Bad Request (validation errors)
-   `401` - Unauthorized (authentication required)
-   `403` - Forbidden (insufficient permissions)
-   `404` - Not Found (resource not found)
-   `422` - Unprocessable Entity (validation failed)
-   `500` - Internal Server Error (server error)

**Validation Error Response:**

```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

**General Error Response:**

```json
{
    "status": "error",
    "message": "Failed to process request",
    "errors": ["Database connection failed"]
}
```
