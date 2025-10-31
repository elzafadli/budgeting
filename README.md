# Budget Request and Realization Management System

A fullstack Laravel 11 application for managing budget requests with multi-level approval workflow and financial realization tracking.

## Tech Stack

- **Framework**: Laravel 11
- **Frontend**: Blade Templates + Bootstrap 5
- **Database**: MySQL
- **Authentication**: Laravel Breeze

## Features

### Role-Based Access Control
- **Admin**: Create and submit budget requests
- **Project Manager**: Review and approve/reject budget requests (Level 1)
- **Finance**: Final approval and create budget realizations (Level 2)

### Budget Management
- Create budget requests with multiple line items
- Submit for multi-level approval workflow
- Track approval status and history
- View detailed budget breakdowns

### Approval Workflow
1. Admin creates budget → Status: `draft`
2. Admin submits budget → Status: `submitted`
3. Project Manager approves → Status: `pm_approved`
4. Finance approves → Status: `finance_approved`
5. Finance creates realization → Status: `completed`

### Realization Tracking
- Record actual disbursement amounts
- Upload proof documents (PDF, images)
- Track variance between approved and realized amounts

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL
- Node.js & NPM

### Setup Instructions

1. **Clone or navigate to the project directory**
   ```bash
   cd d:\xampp\htdocs\budgeting
   ```

2. **Configure Database**
   Update `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=budgeting
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Run Migrations and Seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

5. **Start Development Server**
   ```bash
   php artisan serve
   ```

6. **Access the Application**
   Visit: `http://localhost:8000`

## Default Users

The system comes with three pre-configured users:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Project Manager | pm@example.com | password |
| Finance | finance@example.com | password |

## Database Schema

### Tables
- **users**: User accounts with roles
- **budgets**: Budget request headers
- **budget_items**: Line items for each budget
- **budget_approvals**: Approval records (PM & Finance)
- **budget_realizations**: Actual disbursement records
- **budget_realization_items**: Line items for realizations

## Usage Guide

### For Admin Users
1. Login with admin credentials
2. Navigate to "Create Budget"
3. Fill in budget details and add line items
4. Submit for approval
5. Track approval status in dashboard

### For Project Manager
1. Login with PM credentials
2. Go to "Approvals" page
3. Review pending budget requests
4. Approve or reject with notes

### For Finance Users
1. Login with finance credentials
2. Review PM-approved budgets in "Approvals"
3. Provide final approval
4. Create realizations for approved budgets
5. Upload proof documents for disbursements

## Key Features

### Bootstrap 5 UI
- Responsive design
- Role-based navigation
- Status badges with color coding
- Interactive forms with validation
- Modal dialogs for confirmations

### Status Badges
- 🟦 `draft` - Draft (Gray)
- 🟦 `submitted` - Submitted (Blue)
- 🟦 `pm_approved` - PM Approved (Cyan)
- 🟩 `finance_approved` - Finance Approved (Green)
- 🟥 `rejected` - Rejected (Red)
- ⬛ `completed` - Completed (Dark)

### File Upload
- Supports PDF and image formats
- Max file size: 2MB
- Files stored in `storage/app/public/proof_files`

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── BudgetController.php
│   │   ├── ApprovalController.php
│   │   ├── RealizationController.php
│   │   └── DashboardController.php
│   └── Middleware/
│       └── CheckRole.php
├── Models/
│   ├── Budget.php
│   ├── BudgetItem.php
│   ├── BudgetApproval.php
│   ├── BudgetRealization.php
│   └── BudgetRealizationItem.php
resources/
└── views/
    ├── layouts/
    ├── budgets/
    ├── approvals/
    └── realizations/
```

## License

This project is open-sourced software built with Laravel framework under the [MIT license](https://opensource.org/licenses/MIT).
