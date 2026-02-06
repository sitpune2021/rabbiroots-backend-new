# 🛠️ RabbiRoots Q-Commerce – Admin Panel

**System Administration & Operations Dashboard**

> **IMPORTANT DISCLAIMER**
> This is a working technical document. All features, flows, and controls described here are subject to refinement based on mutual agreement between **RabbiRoots** and **Satara IT Solutions**.
> This README defines the **minimum functional and non-functional requirements** for the Admin Panel.

---

## 📌 Purpose of Admin Panel

The Admin Panel is the **central command system** for RabbiRoots Q-Commerce operations.
It enables **real-time control, monitoring, analytics, inventory, logistics, financial reconciliation, and security enforcement** across stores, delivery agents, and customers.

---

## 🧱 System Scope

The Admin Panel supports:

* Master Admin (Company-level control)
* Store Managers (Store-level operations)
* Delivery & Logistics Managers
* Finance & Compliance Teams
* Support & Staff Admins

---

## 🔐 Role-Based Access Control (RBAC)

### Supported Roles

| Role             | Access Scope                                   |
| ---------------- | ---------------------------------------------- |
| Super Admin      | Full system access                             |
| Store Manager    | Store-level orders, inventory, staff           |
| Delivery Manager | Agent assignment & delivery tracking           |
| Finance Manager  | Payouts, reconciliation (read-only financials) |
| Picker           | Assigned orders only                           |
| Staff Admin      | Agent onboarding, penalties                    |
| Support Agent    | Customer complaints resolution                 |
| Sub-Admin        | Multi-store (future franchise model)           |

### RBAC Rules

* Permissions apply **immediately**
* Session refresh required after permission updates
* **All actions logged** in immutable audit logs

---

## 📊 Dashboard Overview (Real-Time)

**Auto-refresh every 30 seconds**

### Key Metrics

* Today’s Orders vs Target
* Today’s Revenue vs Target
* Average Order Value (trend)
* On-time Delivery %
* Average Delivery Time
* Picker Efficiency (time per item)
* Low Inventory Alerts
* Delayed Orders (>30 mins)

### Visualizations

* Hourly order trend (with next-hour forecast)
* Order pipeline:

  * Preparing
  * Packed
  * Out for Delivery
  * Completed
* Top:

  * Products
  * Delivery Agents
  * Promo Codes

### Export Options

* CSV
* Excel

---

## 📦 Inventory Management (Critical)

### Inventory Model

Inventory follows a **3-tier system**:

* **Available** – Can be ordered immediately
* **Reserved** – In cart (non-blocking)
* **Committed** – Paid & confirmed

### Key Rules

* Inventory **does NOT decrement** on add-to-cart
* Inventory decrements **ONLY after payment**
* FIFO logic with **millisecond timestamps**
* POS & App share a **single synchronized inventory source**

### Admin Capabilities

* Receive stock (e.g., “+50 Budhani Chips”)
* Manual correction (with reason logging)
* Expiry tracking & discounting
* Shelf/section transfers
* Realtime sync to customer app

---

## 🧾 Order Management

### Order States

1. Confirmed
2. Preparing / Picking
3. Packed
4. Out for Delivery
5. Delivered

### Admin Controls

* Real-time order queue
* Manual or auto assignment to pickers
* Reassignment if overload detected
* Delivery agent assignment via map view
* Lock orders after store close

---

## 🚚 Delivery & Agent Management

### Agent Monitoring

* Live agent map
* Battery level status
* Idle detection (Green / Yellow / Red)
* Auto logout after prolonged idle

### Commission Engine (Configurable)

* Base commission
* Distance-based payout
* Rush hour bonuses
* Weekend & festival multipliers
* Weather-based bonuses
* Joining & performance incentives

> **All values editable from Admin Panel**
> Changes apply from **next business day**

---

## 💰 Finance & Payments

### Phase 1: COD

* Cash collected logged per order
* Agent blocked from new orders until cash deposited
* Daily reconciliation by Finance Manager
* Discrepancy tracking with audit trail

### Ledger System

Each order generates a **complete financial record**:

* Gross revenue
* Deductions
* Commissions
* Net revenue
* Payment method
* Ratings & timestamps

### Reports

* Daily
* Weekly
* Monthly
* Agent-wise
* Store-wise

---

## 🎟️ Promo Codes & Pricing Control

### Promo Engine

* Rule-based applicability filters
* Time-based activation
* Store-specific & device-specific campaigns
* Single promo per order (no stacking)

### Dynamic Pricing

* Rush-hour pricing windows
* Admin-controlled schedules
* Promo impact **does NOT affect search ranking**

---

## 🧑‍🍳 Food Safety & Compliance

### Tampering Detection

* QR-linked order bags
* Mandatory agent scan at delivery
* Photo & GPS evidence logging
* Pattern analysis (agent/store level)

### Incident Handling

* Auto refund + replacement
* Customer notified within 5 minutes
* Regulatory logs maintained (FSSAI)

---

## 🔔 Notifications & Communication

* Push notifications (all order stages)
* SMS / WhatsApp (opt-in only)
* Real-time in-app updates
* Masked calling (agent ↔ customer)
* In-app chat fallback

---

## 🔒 Security & Compliance

* TLS 1.2+ (in transit)
* AES-256 (at rest)
* No card data stored (Razorpay)
* DPDP Act 2023 compliant
* Quarterly security audits
* Bi-annual penetration testing
* 24-hour breach notification SLA

---

## 🖥️ Infrastructure & Access Control

### Ownership Rules

* **All servers, databases, and cloud accounts are owned by RabbiRoots**
* Satara IT Solutions gets **developer access only**
* No vendor lock-in
* Full portability guaranteed

### Backup & DR

* Automated daily backups
* Tested restoration plan
* Defined RTO & RPO
* Disaster recovery drills scheduled

---

## 🚀 Deployment & Environments

* Environments:

  * Local
  * Staging
  * Production
* CI/CD with approval gates
* Rollback strategy mandatory

---

## 📅 Project Governance

* Weekly standups
* Weekly demos
* Formal milestone sign-off
* Code pushed to production **only after approval**
* All commits auditable

---

## 📌 Pending Finalization

* Cancelled orders after dispatch (delivery fee ownership)
* Third-party chat/ticketing integration

---

## ✅ Acceptance Criteria

* <2s load time on 4G
* API P95 <500ms
* Zero critical bugs at launch
* All admin workflows tested end-to-end

