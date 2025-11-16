# V3 Infrastructure Setup Documentation
## OS Visuals AI Chatbot Assistant - Phase 1

**Date:** November 10, 2025
**Session:** 2
**Status:** ✅ COMPLETE

---

## Overview

This document details the infrastructure setup for v3, the development version of OS Visuals that includes AI chatbot integration. v3 is an exact copy of v2 with modifications to support the Hydra GPT API integration while keeping v2 as a stable backup.

---

## What Was Accomplished in Phase 1

### 1. Created v3 Folder Structure ✅

**Action:** Copied entire v2 directory to v3 as development environment

```bash
cp -r /var/www/projects/f25-01/html/v2 /var/www/projects/f25-01/html/v3
```

**Result:**
- All v2 files and subdirectories copied to v3
- v2 remains unchanged and production-ready
- v3 is isolated for chatbot development

**Folder Structure:**
```
/var/www/projects/f25-01/html/
├── v2/                           (stable production version, untouched)
│   ├── core/
│   ├── core-a/
│   ├── core-e/
│   ├── core-s/
│   ├── api/
│   ├── config/
│   └── ... (all other v2 files)
│
└── v3/                           (new development version with chatbot)
    ├── core/                     (from v2, unchanged)
    ├── core-a/                   (from v2, unchanged)
    ├── core-e/                   (from v2, unchanged)
    ├── core-s/                   (from v2, unchanged)
    ├── api/                      (from v2, plus NEW: chatbot-stream.php)
    ├── config/                   (from v2, unchanged)
    ├── system.php                (MODIFIED: version_path = '/v3')
    ├── navbar.php                (will add: chatbot-loader.php include)
    ├── chatbot/                  (NEW: chatbot directory for Phase 2)
    └── ... (all other v2 files)
```

### 2. Updated Configuration Files ✅

#### system.php - Version Path Update

**File:** `/var/www/projects/f25-01/html/v3/system.php`

**Change Made:**
```php
// OLD (line 19):
$version_path = "/v2";

// NEW (line 19):
$version_path = "/v3";
```

**Impact:**
- v3 now references itself as `/v3` instead of `/v2`
- All internal links and resource paths correctly point to v3 version
- Navbar, stylesheets, and navigation work correctly in v3

### 3. Added Hydra GPT Configuration ✅

#### .env File - Added Hydra Configuration

**File:** `/var/www/projects/f25-01/.env` (root directory)

**New Configuration Added (lines 6-11):**
```env
# Hydra GPT Configuration for AI Chatbot
HYDRA_API_KEY=sk-4b3666be67d840b2b1010ebb9dbc001e
HYDRA_API_URL=https://gpt.hydra.newpaltz.edu/api
HYDRA_MODEL=gemma3:12b
HYDRA_TEMPERATURE=0.7
HYDRA_MAX_TOKENS=500
```

**Details:**
- **HYDRA_API_KEY:** Provided by user from Hydra dashboard
- **HYDRA_API_URL:** School's Hydra GPT service endpoint
- **HYDRA_MODEL:** Gemma 3 12B (free model available on Hydra)
- **HYDRA_TEMPERATURE:** 0.7 (balanced between deterministic and creative responses)
- **HYDRA_MAX_TOKENS:** 500 (reasonable response length limit)

**Security Note:**
- .env file is in .gitignore and NOT committed to repository
- API key stored securely server-side only, never exposed to client
- Both v2 and v3 share the same .env (shared infrastructure)

### 4. Created .env.template ✅

**File:** `/var/www/projects/f25-01/.env.template` (new reference file)

**Purpose:** Provides template for future deployments and team members

**Content:** Placeholder values with documentation on where to find credentials

```env
# ... (see file for full content)
HYDRA_API_KEY=sk-YOUR_API_KEY_HERE
# Generate API key from: https://gpt.hydra.newpaltz.edu/
# In Hydra dashboard: Settings → Account → API Keys → Create New Secret Key
```

### 5. Created Backend API Endpoint ✅

**File:** `/var/www/projects/f25-01/html/v3/api/chatbot-stream.php` (new)

**Purpose:** Proxy endpoint between v3 frontend and Hydra GPT API

**Key Features:**
- Validates API key is configured
- Constructs 3-layer system prompt (global + category + mechanism-specific)
- Makes curl requests to Hydra API
- Handles errors gracefully
- Returns JSON responses to frontend

**System Prompt Architecture:**

The endpoint constructs smart prompts using three layers:

1. **Layer 1 - Global Instructions**
   - Explains chatbot is OS education assistant
   - Sets tone (friendly, educational)
   - Provides out-of-scope response template

2. **Layer 2 - Category Knowledge**
   - Detects mechanism category from ID (CPU, Memory, Page, Disk, File)
   - Provides category-specific terminology and concepts
   - Example: CPU Scheduling category includes arrival time, burst time, convoy effect, etc.

3. **Layer 3 - Mechanism-Specific Context**
   - Provides specific algorithm definition
   - Lists pros/cons
   - Includes examples
   - Pre-populated for 7 key mechanisms (001, 005, 011, 012, 013, 021, 041)

**Tested Successfully:**
```bash
curl -X POST http://localhost/p/f25-01/v3/api/chatbot-stream.php \
  -H "Content-Type: application/json" \
  -d '{"message":"What is FCFS?","mechanism":"001",...}'
```

**Response:** ✅ Full explanation of FCFS with examples returned from Hydra GPT

### 6. Updated Database Schema ✅

#### Added `version` Column to `submissions` Table

**File:** MySQL database: `p_f25_01_db`

**Change Made:**
```sql
ALTER TABLE submissions ADD COLUMN version VARCHAR(10) DEFAULT 'v2';
```

**Details:**
- Column type: VARCHAR(10)
- Default value: 'v2' (all existing submissions marked as v2)
- New v3 submissions will be marked: 'v3'
- Allows easy filtering/querying by version
- Non-breaking change (existing data unchanged)

**Verification:**
```
Field       Type        Null  Key  Default  Extra
submission_id ...
...
version     varchar(10)  YES        v2
```

#### Added `version` Column to `experiments` Table

**File:** MySQL database: `p_f25_01_db`

**Change Made:**
```sql
ALTER TABLE experiments ADD COLUMN version VARCHAR(10) DEFAULT 'v2';
```

**Same details as submissions table**

**Verification:**
```
Field       Type        Null  Key  Default  Extra
experiment_id ...
...
version     varchar(10)  YES        v2
```

**Data Isolation Strategy:**

With these schema changes, queries can now isolate data by version:
```php
// Get only v3 submissions
$sql = "SELECT * FROM submissions WHERE version = 'v3'";

// Get only v2 submissions
$sql = "SELECT * FROM submissions WHERE version = 'v2'";

// Get all (default for queries not specifying version)
$sql = "SELECT * FROM submissions";
```

### 7. Fixed File Permissions ✅

**Issue:** v3 folder copied with user pacchiao1 ownership, Apache runs as nobody

**Fix Applied:**
```bash
chmod 755 /var/www/projects/f25-01/html/v3          # Directory executable
find /var/www/projects/f25-01/html/v3 -name "*.php" -exec chmod 644 {} \;
find /var/www/projects/f25-01/html/v3 -type d -exec chmod 755 {} \;
chmod 644 /var/www/projects/f25-01/.env             # Make .env readable
```

**Result:** ✅ Apache can now read and execute all v3 files

### 8. Verified v3 Functionality ✅

**Tests Performed:**

1. **Homepage Load Test**
   ```bash
   curl http://localhost/p/f25-01/v3/index.php
   ```
   ✅ **Result:** Homepage loads successfully, shows `/v3/` in icon path

2. **Mechanism Page Test (m-001 FCFS)**
   ```bash
   curl http://localhost/p/f25-01/v3/core/m-001/index.php
   ```
   ✅ **Result:** Mechanism page loads with correct styling and structure

3. **API Endpoint Test**
   ```bash
   curl -X POST http://localhost/p/f25-01/v3/api/chatbot-stream.php \
     -H "Content-Type: application/json" \
     -d '{"message":"What is FCFS?","mechanism":"001"}'
   ```
   ✅ **Result:** Full explanation of FCFS returned from Hydra GPT

4. **v2 Compatibility Test**
   ```bash
   curl http://localhost/p/f25-01/v2/index.php
   ```
   ✅ **Result:** v2 still works, not affected by v3 changes

---

## Technical Details

### API Communication Flow

```
Client (JavaScript)
    ↓ (POST JSON)
v3/api/chatbot-stream.php
    ↓ (construct system prompt)
Hydra GPT API (gpt.hydra.newpaltz.edu)
    ↓ (process with Gemma 3 12B)
Hydra Response
    ↓ (parse and return JSON)
Client (JavaScript renders response)
```

### System Prompt Example

For m-001 (FCFS CPU Scheduling), the system prompt includes:

**Layer 1:**
> "You are an educational assistant for OS Visuals... Help students understand how OS scheduling algorithms work..."

**Layer 2:**
> "The student is learning about CPU Scheduling algorithms. These include: FCFS, SJF, Priority Scheduling, Round Robin... Key concepts include: processes, arrival time, burst time, waiting time, convoy effect, starvation..."

**Layer 3:**
> "Mechanism: FCFS (First Come First Served). Processes execute in the order they arrive. Non-preemptive, simple, unfair. Pros: Simple to understand. Cons: Convoy effect, poor average wait time. Example: If P1 takes 24 units, P2 and P3 must wait for P1 to complete."

Result: Contextual, accurate, mechanism-aware responses from Hydra GPT.

---

## Database Schema Changes Summary

| Table | Change | Purpose |
|-------|--------|---------|
| submissions | Added `version` column (VARCHAR 10) | Isolate v2 vs v3 data |
| experiments | Added `version` column (VARCHAR 10) | Isolate v2 vs v3 data |

**No Breaking Changes:**
- All existing queries still work (version defaults to 'v2')
- New code can filter by version if needed
- Easy to migrate data between versions in future

---

## File Locations Reference

### Key New Files Created

| Path | Purpose |
|------|---------|
| `/var/www/projects/f25-01/html/v3/` | Development version folder |
| `/var/www/projects/f25-01/html/v3/system.php` | Modified: version_path = '/v3' |
| `/var/www/projects/f25-01/html/v3/api/chatbot-stream.php` | NEW: Backend API endpoint |
| `/var/www/projects/f25-01/.env.template` | NEW: Configuration template |
| `/var/www/projects/f25-01/.env` | MODIFIED: Added Hydra config |

### Key Directories Shared

| Path | Used By | Status |
|------|---------|--------|
| `/var/www/projects/f25-01/html/cgi-bin/` | v2, v3 | Shared (Java backend) |
| `/var/www/projects/f25-01/html/files/` | v2, v3 | Shared (I/O data) |

---

## Verification Checklist

- [x] v3 folder created as exact copy of v2
- [x] v3/system.php updated with version_path = '/v3'
- [x] .env file includes HYDRA_API_KEY and other Hydra config
- [x] .env.template created with placeholders
- [x] chatbot-stream.php created and functional
- [x] Database schema updated (submissions + experiments version columns)
- [x] File permissions fixed for Apache
- [x] v3 homepage loads successfully
- [x] v3 mechanism page (m-001) loads successfully
- [x] Chatbot API endpoint responds with valid JSON
- [x] Chatbot generates mechanism-aware responses
- [x] v2 remains unchanged and functional
- [x] Database supports data isolation by version

---

## What Happens Next (Phase 2)

In Phase 2, we will:

1. **Create Chatbot UI Component** (`chatbot-ui.js`)
   - JavaScript class that creates UI elements
   - Toggle button in bottom-right corner
   - Chat window with message display and input form
   - Styling via `chatbot-styles.css`

2. **Create Context Extraction Module** (`chatbot-context.js`)
   - Extracts mechanism ID, animation state, input/output data
   - Makes chatbot aware of which algorithm is being viewed
   - Handles different mechanism types (CPU vs Memory vs Page, etc.)

3. **Load Chatbot on All Pages** (`chatbot-loader.php`)
   - Include in navbar.php so chatbot appears everywhere
   - Initializes UI component on page load
   - Works on all 5 modes (View, Advanced, Research, Submission, Compare)

**Estimated Duration:** 3-4 days

---

## Troubleshooting Notes

### If v3 Shows 403 Forbidden
- Check folder permissions: `chmod 755 /var/www/projects/f25-01/html/v3`
- Check PHP file permissions: `find /path -name "*.php" -exec chmod 644 {} \;`
- Check Apache error log: `/var/log/httpd/error_log`

### If Chatbot API Returns "API Key Not Configured"
- Verify .env file exists at `/var/www/projects/f25-01/.env`
- Verify .env is readable: `chmod 644 .env`
- Verify HYDRA_API_KEY is set in .env (not placeholder value)
- Check .env is actually loaded: Add temporary debug to chatbot-stream.php

### If v2 Breaks After Phase 1 Changes
- Verify v2 was not modified (only v3 was created)
- Revert any accidental changes: `git checkout v2/`
- Database changes (version column) are backwards compatible

---

## Committing Changes

**Files to Commit:**

```bash
git add html/v3/
git add html/v3/api/chatbot-stream.php
git add .env.template
# NOTE: Do NOT commit .env file (contains API key) - it's in .gitignore
```

**Suggested Commit Message:**
```
feat(v3): Initialize v3 development version with Hydra GPT integration

- Copy v2 to v3 as development environment
- Update v3 system.php version_path to '/v3'
- Add Hydra GPT configuration to .env
- Create .env.template for reference
- Create chatbot-stream.php backend API endpoint
- Add version column to submissions and experiments tables for data isolation
- Test v3 and confirm v2 still functional

This establishes the foundation for Phase 2 (UI) and Phase 3 (context extraction).
```

---

## Summary

**Phase 1 Successfully Completed** ✅

- v3 infrastructure fully set up and tested
- Hydra API authentication working
- Backend endpoint responding with AI-generated content
- Database ready for v3-specific data isolation
- v2 remains stable and unchanged

**Ready for Phase 2:** Frontend chatbot UI component development

---

**Documentation Created:** November 10, 2025
**By:** Claude Code (AI Assistant)
**Next Step:** Phase 2 - Frontend Chatbot Widget
