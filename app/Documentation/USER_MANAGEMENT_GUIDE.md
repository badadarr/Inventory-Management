# 👥 User Management Guide

## Overview
User Management telah ditambahkan dengan akses terbatas hanya untuk **Super Admin** dan **Admin**.

## 🔐 Access Control

### Who Can Access?
- ✅ **Super Admin** - Full access to all users
- ✅ **Admin** - Full access to users in their company
- ❌ **Sales** - No access
- ❌ **Warehouse** - No access
- ❌ **Finance** - No access

## 📍 Menu Location

Menu **Users** dapat ditemukan di:
```
Sidebar → Others → Users
```

Menu ini hanya akan muncul untuk Super Admin dan Admin.

## ✨ Features

### 1. View Users
- List semua users dengan pagination
- Filter by name dan email
- Sort by various fields
- Display: Name, Email, Role, Company

### 2. Create User
- Click tombol **"Add User"**
- Fill form:
  - Name (required)
  - Email (required, unique)
  - Password (required, min 8 characters)
  - Role (required, dropdown)
  - Company Name (optional)
  - Company ID (optional)
- Click **"Create"**

### 3. Edit User
- Click icon **Edit** (pencil) pada user
- Update informasi user
- Password optional (kosongkan jika tidak ingin mengubah)
- Click **"Update"**

### 4. Delete User
- Click icon **Delete** (trash) pada user
- Confirm deletion
- User akan dihapus dari sistem

## 🎯 Role Options

Saat membuat/edit user, tersedia 5 role:

| Role | Value | Description |
|------|-------|-------------|
| Super Admin | super_admin | Full access to all |
| Admin | admin | Full access per company |
| Sales | sales | Sales related features |
| Warehouse | warehouse | Warehouse operations |
| Finance | finance | Financial operations |

## 🔒 Security Rules

### 1. Self-Deletion Prevention
- User tidak bisa menghapus akun mereka sendiri
- Error message akan muncul jika mencoba

### 2. Email Uniqueness
- Email harus unique di sistem
- Validation error jika email sudah digunakan

### 3. Password Security
- Minimum 8 characters
- Automatically hashed sebelum disimpan
- Saat edit, password optional (tidak wajib diisi)

### 4. Role-Based Access
- Route protected dengan middleware `role:super_admin,admin`
- Frontend menu hidden untuk role lain
- 403 Forbidden jika unauthorized access

## 📋 Usage Examples

### Example 1: Create Sales User
```
Name: John Doe
Email: john.sales@company.com
Password: password123
Role: Sales
Company Name: PT. Company A
Company ID: 1
```

### Example 2: Create Admin for New Company
```
Name: Jane Admin
Email: jane.admin@companyb.com
Password: securepass456
Role: Admin
Company Name: PT. Company B
Company ID: 2
```

### Example 3: Update User Role
```
1. Click Edit on user
2. Change Role from "Sales" to "Warehouse"
3. Update Company ID if needed
4. Click Update
```

## 🧪 Testing

### Test as Super Admin
```bash
# Login: superadmin@example.com / password
1. Navigate to Others → Users
2. Verify you can see all users
3. Create a new user
4. Edit existing user
5. Delete a user (not yourself)
```

### Test as Admin
```bash
# Login: admin.pta@example.com / password
1. Navigate to Others → Users
2. Verify you can see users
3. Create a new user for your company
4. Edit users
5. Delete users
```

### Test as Sales (Should Fail)
```bash
# Login: sales@example.com / password
1. Check sidebar - "Users" menu should NOT appear
2. Try to access /users directly - Should get 403 Forbidden
```

## 🎨 UI Components

### User List Table
- **Columns**: #, Name (with photo), Email, Role (badge), Company, Actions
- **Actions**: Edit (blue), Delete (red)
- **Pagination**: Bottom of table
- **Filters**: Name, Email, Sort By, Sort Order

### Create/Edit Modal
- **Modal Title**: "Create User" or "Edit User"
- **Form Fields**: All user fields
- **Buttons**: Create/Update (green), Cancel (gray)
- **Validation**: Real-time error messages

## 🔧 Technical Details

### Backend
- **Controller**: `UserController`
- **Routes**: 
  - GET `/users` - Index
  - POST `/users` - Store
  - PUT `/users/{user}` - Update
  - DELETE `/users/{user}` - Destroy
- **Middleware**: `role:super_admin,admin`
- **Validation**: Laravel validation rules

### Frontend
- **Page**: `resources/js/Pages/User/Index.vue`
- **Components**: CardTable, TableData, Modal
- **Form**: Inertia useForm
- **Permissions**: usePermissions composable

## 📝 Notes

### Company ID
- Super Admin biasanya tidak memiliki company_id (null)
- Admin dan role lain harus memiliki company_id
- Company ID digunakan untuk data isolation (future feature)

### Password Management
- Saat create: Password wajib diisi
- Saat edit: Password optional (kosongkan untuk tidak mengubah)
- Password di-hash dengan bcrypt

### Role Assignment
- Hati-hati saat assign role Super Admin
- Pastikan company_id sesuai dengan role
- Admin harus memiliki company_id

## 🚀 Future Enhancements

1. **Company Data Isolation**: Filter users by company_id for Admin
2. **Bulk Actions**: Delete multiple users at once
3. **User Status**: Active/Inactive toggle
4. **Last Login**: Track last login timestamp
5. **Profile Photo**: Upload user photo
6. **Email Verification**: Send verification email
7. **Password Reset**: Reset password functionality
8. **Audit Log**: Track user changes

## ✅ Checklist

- ✅ User Management menu added to sidebar
- ✅ Only Super Admin & Admin can access
- ✅ CRUD operations implemented
- ✅ Role dropdown with all 5 roles
- ✅ Password hashing
- ✅ Email uniqueness validation
- ✅ Self-deletion prevention
- ✅ Modal for create/edit
- ✅ Confirmation for delete
- ✅ Flash messages for success/error

## 🎉 Ready to Use!

User Management sudah siap digunakan. Login sebagai Super Admin atau Admin untuk mulai mengelola users!
