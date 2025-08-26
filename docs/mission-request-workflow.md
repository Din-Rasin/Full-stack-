# Mission Request Workflow

```mermaid
graph TD
    %% Start Process
    START([User Submits Mission Request])

    %% Form Validation
    VALIDATE{Validate Mission Details}
    INVALID[Show Validation Errors]

    %% Department Check
    DEPT_CHECK{Check User Department}

    %% IT Department Mission Flow
    IT_TL{IT: Send to Team Leader}
    IT_TL_DECISION{Team Leader Decision}
    IT_CEO{Send to CEO}
    IT_CEO_DECISION{CEO Final Decision}

    %% Sales Department Mission Flow
    SALES_TL{Sales: Send to Team Leader}
    SALES_TL_DECISION{Team Leader Decision}
    SALES_CFO{Send to CFO}
    SALES_CFO_DECISION{CFO Decision}
    SALES_HR{Send to HR Manager}
    SALES_HR_DECISION{HR Manager Decision}
    SALES_CEO{Send to CEO}
    SALES_CEO_DECISION{CEO Final Decision}

    %% Final States
    APPROVED([Mission Approved])
    REJECTED([Mission Rejected])
    PENDING([Pending Approval])

    %% Additional Processes
    BUDGET_CHECK{Budget Approval Required?}
    BUDGET_APPROVE[Budget Department Approval]

    %% Notifications and Updates
    NOTIFY_USER[Notify User]
    NOTIFY_NEXT[Notify Next Approver]
    UPDATE_DB[(Update Database)]
    CALENDAR_UPDATE[Update Mission Calendar]

    %% Flow Start
    START --> VALIDATE
    VALIDATE -->|Valid| DEPT_CHECK
    VALIDATE -->|Invalid| INVALID
    INVALID --> START

    %% Department Routing
    DEPT_CHECK -->|IT Department| IT_TL
    DEPT_CHECK -->|Sales Department| SALES_TL

    %% IT Department Mission Process
    IT_TL --> IT_TL_DECISION
    IT_TL_DECISION -->|Approved| BUDGET_CHECK
    IT_TL_DECISION -->|Rejected| REJECTED

    BUDGET_CHECK -->|Yes| BUDGET_APPROVE
    BUDGET_CHECK -->|No| IT_CEO
    BUDGET_APPROVE -->|Approved| IT_CEO
    BUDGET_APPROVE -->|Rejected| REJECTED

    IT_CEO --> IT_CEO_DECISION
    IT_CEO_DECISION -->|Approved| APPROVED
    IT_CEO_DECISION -->|Rejected| REJECTED

    %% Sales Department Mission Process
    SALES_TL --> SALES_TL_DECISION
    SALES_TL_DECISION -->|Approved| SALES_CFO
    SALES_TL_DECISION -->|Rejected| REJECTED

    SALES_CFO --> SALES_CFO_DECISION
    SALES_CFO_DECISION -->|Approved| SALES_HR
    SALES_CFO_DECISION -->|Rejected| REJECTED

    SALES_HR --> SALES_HR_DECISION
    SALES_HR_DECISION -->|Approved| SALES_CEO
    SALES_HR_DECISION -->|Rejected| REJECTED

    SALES_CEO --> SALES_CEO_DECISION
    SALES_CEO_DECISION -->|Approved| APPROVED
    SALES_CEO_DECISION -->|Rejected| REJECTED

    %% Pending States
    IT_TL --> PENDING
    SALES_TL --> PENDING
    SALES_CFO --> PENDING
    SALES_HR --> PENDING
    BUDGET_APPROVE --> PENDING

    %% Database and Notifications
    APPROVED --> UPDATE_DB
    REJECTED --> UPDATE_DB
    PENDING --> UPDATE_DB

    UPDATE_DB --> NOTIFY_USER
    APPROVED --> CALENDAR_UPDATE
    PENDING --> NOTIFY_NEXT

    %% Styling
    style START fill:#4caf50,color:#fff
    style APPROVED fill:#4caf50,color:#fff
    style REJECTED fill:#f44336,color:#fff
    style PENDING fill:#ff9800,color:#fff
    style UPDATE_DB fill:#2196f3,color:#fff
    style CALENDAR_UPDATE fill:#9c27b0,color:#fff
```
