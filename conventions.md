Testing conventions & approach (vergepos)
========================================

Goal
- Use both Unit tests (fast, isolated) and Integration/Feature tests (migrations + DB).
- Write tests incrementally — add one focused test at a time.

Where tests live
- tests/
  - Unit/              <- unit tests that mirror app/ path (recommended)
    - Http/
      - Controllers/
        - InventoryAdjustmentControllerTest.php   <- unit tests for controller
    - Models/
      - InventoryAdjustmentTest.php
  - Feature/           <- integration tests (DB + HTTP level)
    - InventoryAdjustmentFlowTest.php

Unit test conventions
- Unit tests mirror source file path. Example:
  app/Http/Controllers/InventoryAdjustmentController.php
  => tests/Unit/Http/Controllers/InventoryAdjustmentControllerTest.php
- Unit tests should mock external dependencies (DB, GenericCreate, GenericRetrieve).
- Keep unit tests deterministic and fast (no DB).

Feature / Integration test conventions
- Use RefreshDatabase or DatabaseMigrations.
- Use sqlite :memory: by default for CI / local; also run a subset against a real MySQL test database (vergepos_api_gateway_test) in CI/optional pipeline to catch engine-specific issues.
- Use factories for models when needed — but add factories only as you need them.

Database strategy
- Default (fast): sqlite in-memory for local and PR runs.
- Additional (optional in CI): MySQL test DB named `vergepos_api_gateway_test` (same credentials as `vergepos_api_gateway`).
- Configure phpunit.xml to default to sqlite, and allow overriding environment variables in CI to use MySQL.

Testing workflow (small steps)
1. Add one unit test for small logic (e.g., client_uuid generation).
2. Run phpunit and fix production code if necessary.
3. Add integration test for the end-to-end behavior (insertOrIgnore dedup + recount).
4. If you need DB-specific checks, add CI job that runs subset of tests against MySQL.

Running tests
- Run all tests:
  vendor/bin/phpunit
- Run single file:
  vendor/bin/phpunit tests/Unit/Http/Controllers/InventoryAdjustmentControllerTest.php

Notes / tips
- Prefer mocking or partial-mocks in unit tests to avoid side-effects.
- Integration tests must use RefreshDatabase and factories (create only as needed).
- When adding new tests for controllers, mirror the controller folder path as above.

We’ll start small: next I added a unit test that asserts the controller generates deterministic client_uuid and strips remarks / unsets id/db_id before insert. Add more tests later incrementally.
