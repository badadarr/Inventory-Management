# POS / Cart Feature - TODO

## Status
🔴 **NOT AVAILABLE** - Membutuhkan diskusi business flow terlebih dahulu

## Alasan Disabled
1. Membutuhkan analisis kebutuhan bisnis yang lebih detail
2. Flow POS berbeda dengan Order Create (manual)
3. Perlu diskusi payment flow, stock management, dan receipt printing
4. Integration dengan hardware (barcode scanner, printer) perlu direncanakan

## Current Implementation
- Controller: `app/Http/Controllers/CartController.php` - **DISABLED**
- View: `resources/js/Pages/Cart/NotAvailable.vue` - Temporary placeholder
- Old View: `resources/js/Pages/Cart/Pos.vue` - Available for reference

## Technical Requirements (To Be Discussed)

### 1. Database Schema
- [ ] `carts` table (temporary shopping cart)
  ```sql
  - id
  - user_id (FK to users)
  - product_id (FK to products)
  - quantity
  - timestamps
  ```

### 2. Business Flow Questions

#### Stock Management
- [ ] Apakah stock di-hold saat item di cart?
- [ ] Atau stock baru decrement saat checkout?
- [ ] Bagaimana handle concurrent users yang cart same product?

#### Payment Flow
- [ ] Support partial payment?
- [ ] Support split payment (multiple payment methods)?
- [ ] Bagaimana handle change (kembalian)?
- [ ] Need payment confirmation?

#### Cart Persistence
- [ ] Cart expire after berapa lama?
- [ ] Support "Hold Order" untuk process later?
- [ ] User bisa punya multiple carts?

#### Customer Management
- [ ] Wajib pilih customer atau bisa anonymous?
- [ ] Support walk-in customer vs registered customer?
- [ ] Apply customer-specific pricing automatically?

#### Receipt & Printing
- [ ] Receipt format: thermal (58mm/80mm) atau A4?
- [ ] Auto-print atau manual?
- [ ] Email receipt support?
- [ ] Reprint receipt history?

#### Discount & Pricing
- [ ] Override product price di POS?
- [ ] Discount per item atau per transaction?
- [ ] Custom discount authorization required?

### 3. UI/UX Considerations
- [ ] Keyboard shortcuts untuk kasir speed
- [ ] Touch-screen friendly untuk tablet
- [ ] Barcode scanner integration
- [ ] Product search & filter strategy
- [ ] Cart item quick edit/remove

### 4. Integration Points
- [ ] Hardware: Barcode scanner
- [ ] Hardware: Receipt printer
- [ ] Hardware: Cash drawer
- [ ] Software: Accounting system
- [ ] Software: Inventory sync

### 5. Performance Requirements
- [ ] Target transaction time per order
- [ ] Offline mode needed?
- [ ] Concurrent cashier support
- [ ] Real-time stock update visibility

## Existing Code References

### Backend Services
- `app/Services/CartService.php` - Cart CRUD operations
- `app/Services/OrderService.php::createForUser()` - Convert cart to order
- `app/Repositories/CartRepository.php` - Database queries

### Frontend Components
- `resources/js/Pages/Cart/Pos.vue` - Full POS interface (currently disabled)
- Features already implemented in old code:
  - Product grid with images & stock
  - Add/remove cart items
  - Increment/decrement quantity
  - Custom discount (fixed & percentage)
  - Tax calculation
  - Payment method selection
  - Customer selection

### Existing Features Can Be Reused
✅ Product listing & search
✅ Cart management (add/update/delete)
✅ Customer selection
✅ Payment method enum (7 methods: Cash, Bank Transfer, Credit Card, Debit Card, E-Wallet, QRIS, Gift Card)
✅ Tax & discount calculation
✅ Order creation from cart

## Implementation Plan (When Ready)

### Phase 1: Core POS (1-2 weeks)
1. Enable CartController
2. Update database migration (create carts table)
3. Test cart CRUD operations
4. Update Pos.vue with new payment methods
5. Basic receipt printing

### Phase 2: UX Improvements (1 week)
6. Keyboard shortcuts
7. Product search & filters
8. Better cart display
9. Mobile responsive

### Phase 3: Advanced Features (2-3 weeks)
10. Barcode scanner
11. Hold orders
12. Custom pricing integration
13. Split payment
14. Receipt customization

### Phase 4: Testing & Deployment (1 week)
15. Load testing
16. User acceptance testing
17. Training documentation
18. Production deployment

## Decision Log
| Date | Decision | Reason | By |
|------|----------|--------|-----|
| 2025-10-08 | Feature disabled | Need business flow discussion | Development Team |

## Next Steps
1. ✅ Create "Not Available" placeholder page
2. ⏳ Schedule meeting with stakeholders
3. ⏳ Document business requirements
4. ⏳ Create detailed technical specification
5. ⏳ Get approval for implementation
6. ⏳ Start Phase 1 development

## Contact
For questions or discussion about POS/Cart feature:
- Discuss with: Development Team
- When: TBD after requirements gathering

---
Last Updated: 2025-10-08
