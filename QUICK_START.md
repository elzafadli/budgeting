# Quick Start Guide

## 🚀 Getting Started

The application is **already set up and ready to use!**

### Start the Application

```bash
php artisan serve
```

Then visit: **http://localhost:8000**

---

## 👥 Test Users

| Role | Email | Password |
|------|-------|----------|
| **Admin** | admin@example.com | password |
| **Project Manager** | pm@example.com | password |
| **Finance** | finance@example.com | password |

---

## 📋 Testing the Complete Workflow

### Step 1: Create Budget Request (Admin)
1. Login as **admin@example.com**
2. Click **"Create Budget"** in navigation
3. Fill in:
   - Title: "Office Equipment Purchase"
   - Description: "Computers and furniture for new office"
   - Add items:
     - Item: "Laptop", Qty: 5, Price: 15000000
     - Item: "Desk", Qty: 10, Price: 2000000
4. Click **"Create Budget Request"**
5. On detail page, click **"Submit for Approval"**

### Step 2: PM Approval (Project Manager)
1. Logout and login as **pm@example.com**
2. Click **"Approvals"** in navigation
3. Review the budget request
4. Add optional note: "Approved for procurement"
5. Click **"Approve"**

### Step 3: Finance Approval (Finance)
1. Logout and login as **finance@example.com**
2. Click **"Approvals"** in navigation
3. Review the PM-approved budget
4. Add optional note: "Budget allocated"
5. Click **"Approve"**

### Step 4: Create Realization (Finance)
1. Still logged in as finance
2. Go to **"Budgets"** → View the approved budget
3. Click **"Create Realization"**
4. Fill in:
   - Realization Date: Today's date
   - Add items:
     - Description: "Purchased 5 laptops", Amount: 75000000
     - Description: "Purchased 10 desks", Amount: 20000000
   - Upload proof files (optional)
5. Click **"Create Realization"**

---

## 🎨 Key Features to Test

### Dashboard
- **Admin**: See total budgets, drafts, submitted, approved
- **PM/Finance**: See pending approvals, approved, rejected counts
- View recent budget requests

### Budget Management
- ✅ Create multiple line items
- ✅ Auto-calculate totals
- ✅ Dynamic item rows (add/remove)
- ✅ Submit for approval
- ✅ Delete draft budgets

### Approval System
- ✅ Two-level approval (PM → Finance)
- ✅ Approve with notes
- ✅ Reject with mandatory reason
- ✅ Approval history tracking

### Realization
- ✅ Track actual disbursements
- ✅ Upload proof documents (PDF/images)
- ✅ Compare approved vs realized amounts
- ✅ Mark budget as completed

---

## 📱 UI Features

### Bootstrap 5 Components
- Responsive navigation with role-based menu items
- Color-coded status badges
- Interactive forms with validation
- Modal dialogs for confirmations
- Alert messages for success/error
- Responsive tables

### Status Colors
- 🔵 **Submitted** - Primary blue
- 🔵 **PM Approved** - Info cyan
- 🟢 **Finance Approved** - Success green
- 🔴 **Rejected** - Danger red
- ⚪ **Draft** - Secondary gray
- ⚫ **Completed** - Dark

---

## 🔐 Role Permissions

### Admin
- ✅ Create budget requests
- ✅ Submit for approval
- ✅ View own budgets
- ✅ Delete draft budgets
- ❌ Cannot approve

### Project Manager
- ✅ View all budgets
- ✅ Approve/reject submitted budgets
- ✅ Add approval notes
- ❌ Cannot create budgets
- ❌ Cannot create realizations

### Finance
- ✅ View all budgets
- ✅ Final approval of PM-approved budgets
- ✅ Create realizations
- ✅ Upload proof documents
- ❌ Cannot create budget requests

---

## 📂 File Upload

Supported formats: **PDF, JPG, JPEG, PNG**  
Max size: **2MB**  
Storage location: `storage/app/public/proof_files/`

---

## 🗄️ Database

Database name: `budgeting` (or as configured in `.env`)

### Tables Created
- users
- budgets
- budget_items
- budget_approvals
- budget_realizations
- budget_realization_items
- cache, jobs, sessions (Laravel default)

---

## 🛠️ Troubleshooting

### Database Not Found
```bash
# Create database manually in MySQL:
CREATE DATABASE budgeting;

# Then run:
php artisan migrate:fresh --seed
```

### Storage Link Missing
```bash
php artisan storage:link
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📊 Sample Test Scenarios

### Scenario 1: Reject Budget
1. Admin creates and submits budget
2. PM rejects with reason: "Insufficient justification"
3. Budget status changes to "Rejected"
4. Admin can view rejection reason in approval history

### Scenario 2: Multiple Items
1. Create budget with 5+ line items
2. Total auto-calculates
3. View itemized breakdown in detail page

### Scenario 3: Finance Actions
1. After PM approval, only Finance can see in approvals
2. Finance approves budget
3. "Create Realization" button appears
4. Complete realization → Budget marked as "Completed"

---

## 🎯 Application Flow

```
┌─────────┐      ┌────────────┐      ┌─────────┐
│  Admin  │ ───> │    PM      │ ───> │ Finance │
│ Creates │      │  Approves  │      │Approves │
│  Draft  │      │  Level 1   │      │ Level 2 │
└─────────┘      └────────────┘      └─────────┘
                                            │
                                            ▼
                                     ┌──────────────┐
                                     │ Finance      │
                                     │ Creates      │
                                     │ Realization  │
                                     └──────────────┘
```

---

## ✅ System is Ready!

Everything is configured and working. Just run `php artisan serve` and start testing!
