# JunkTracker Alpha 0.1.0

Internal tracking app for Jimmy's Junk (Coventry, RI).

## Release: alpha 0.1.0

### Overview

- Custom PHP 8.x MVC skeleton.
- Bootstrap 5, mobile-first design.
- Auth, dashboard shell, and stubbed modules for clients, jobs, sales, users, and search.

### Features in this alpha

- **Authentication**
  - Login/logout using existing `users` table (`email` + `password_hash`).
  - Session-based protection for app routes.

- **Dashboard (stubbed metrics)**
  - Layout for:
    - MTD/YTD income cards (Shop, eBay, Scrap, Jobs).
    - Jobs snapshot (quoted, active, completed MTD/YTD).
    - Pipeline (prospects, quotes waiting, estimate/invoice follow-ups).
    - Recent activity feed.

- **Modules (wired but not fully implemented yet)**
  - Clients
  - Jobs
  - Sales
  - Users (admin-only)
  - Global search

### Next steps

- Implement real queries for dashboard metrics.
- Build full CRUD for clients, jobs, and sales.
- Add per-job P&L view (payments, labor, dumps, expenses).
- Add activity logging and global search.

This version is for internal testing of structure, routing, and basic login before building out the full functionality.