# Workflow Management System - Documentation

This directory contains comprehensive documentation for the Laravel Workflow Management System, including Mermaid diagrams and implementation guidelines.

## Documentation Files

1. [System Overview](system-overview.md) - High-level architecture and components
2. [Leave Request Workflow](leave-request-workflow.md) - Detailed workflow for leave requests
3. [Mission Request Workflow](mission-request-workflow.md) - Detailed workflow for mission requests
4. [User Role Management](user-role-management.md) - Role-based access control system
5. [Database Schema](database-schema.md) - Entity relationship diagram for the database
6. [Laravel System Architecture](laravel-system-architecture.md) - Laravel framework architecture
7. [Implementation Guidelines](implementation-guidelines.md) - Step-by-step implementation instructions
8. [Cloud System Architecture](cloud-system-architecture.md) - Comprehensive cloud-based system architecture
9. [Cloud System Architecture Explanation](cloud-system-architecture-explanation.md) - Detailed explanation of the cloud architecture

## System Overview

The Workflow Management System is a comprehensive solution for managing employee leave and mission requests with department-specific approval workflows. The system supports multiple departments with different approval chains:

-   **IT Department**:

    -   Leave: Team Leader → HR Manager
    -   Mission: Team Leader → CEO

-   **Sales Department**:
    -   Leave: Team Leader → CFO → HR Manager
    -   Mission: Team Leader → CFO → HR Manager → CEO

## Key Features

-   Role-based access control (RBAC)
-   Department-specific workflow configurations
-   Email notifications and alerts
-   Status tracking and reporting
-   Audit logging for compliance
-   Responsive UI with Bootstrap

## Technology Stack

-   **Backend**: Laravel 10.x, PHP 8.1+
-   **Database**: MySQL 8.0
-   **Frontend**: Blade Templates, Bootstrap 5, JavaScript
-   **Queue**: Redis or Database driver
-   **Cache**: Redis or File cache
-   **Email**: SMTP or SendGrid
-   **Storage**: Local or AWS S3

Each documentation file contains detailed Mermaid diagrams that visualize the system architecture, workflows, and database schema. These diagrams serve as both documentation and implementation guides for building the complete system.
