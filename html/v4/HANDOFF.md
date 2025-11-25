# VisOS AI Chatbot v3b - Team Handoff Guide

**Welcome to the VisOS AI Chatbot v3b project!** This document will help you understand and maintain the chatbot system.

---

## Quick Start

The chatbot is a context-aware AI assistant that runs on every mechanism page in the OS Visuals platform.

**How to use:**
1. Visit any mechanism page: `/v3b/core/m-001/` or any other mechanism
2. Click the blue chat bubble in the bottom-right corner
3. Ask a question about the algorithm
4. Get an AI response that understands the current algorithm

---

## Current Configuration

| Component | Status | Details |
|-----------|--------|---------|
| Primary AI Service | Groq Cloud API | Free, fast, reliable |
| Fallback Service | Hydra (school server) | Automatic if Groq fails |
| Team Email Account | visosofficial@gmail.com | For accessing Groq console |
| API Keys | Configured in .env | Stored securely, NOT in code |
| Database Logging | Optional | Currently disabled |

---

## Critical Information

### Groq Account Access

**Email:** visosofficial@gmail.com
**Password:** Stored in team password manager
**Console:** https://console.groq.com

**What to do here:**
- Monitor API usage (should be <100 requests/day for 40 users)
- Check API key health
- Rotate API key yearly (good security practice)
- View rate limits

### How to Rotate Groq API Key

1. Go to https://console.groq.com
2. Sign in with visosofficial@gmail.com
3. Navigate to: Settings → API Keys
4. Click "Rotate Key" on current key
5. Copy new key: `gsk_...`
6. Update `.env` file:
   ```
   GROQ_API_KEY=gsk_[NEW_KEY_HERE]
   ```
7. Restart web server or reload page to test
8. Verify chatbot works

**When to rotate:**
- Yearly (security best practice)
- If key is exposed
- After team member with access leaves
- Before/after semester breaks

### API Key Location

Primary (Production):
```
/var/www/projects/f25-01/.env (GROQ_API_KEY)
```

Backup/Reference:
```
/.env.template (has instructions)
/v3b/.env (if different from main)
```

**IMPORTANT:** Never commit .env to git!

---

## Project Structure

```
/var/www/projects/f25-01/

├── html/
│   ├── v2c/                          ← Production site (DO NOT TOUCH!)
│   ├── v3/                           ← Phase 3 Hydra version (Backup)
│   └── v3b/                          ← Current version (Work here)
│       ├── chatbot/                  ← UI components (don't modify)
│       │   ├── chatbot-ui.js
│       │   ├── chatbot-api.js
│       │   ├── chatbot-context.js
│       │   ├── chatbot-config.js
│       │   ├── chatbot-styles.css
│       │   └── chatbot-loader.php
│       ├── api/
│       │   ├── chatbot-stream.php    ← Main API endpoint
│       │   ├── services/             ← Service implementations
│       │   │   ├── BaseAIService.php
│       │   │   ├── GroqService.php
│       │   │   └── HydraService.php
│       │   ├── config.php            ← Service factory
│       │   └── logs/                 ← Service logs (auto-created)
│       ├── system.php                ← Version path config
│       ├── navbar.php                ← Chatbot loader include
│       ├── core/                     ← Mechanism pages (324 total)
│       └── docs/
│           ├── PHASE4_MIGRATION.md   ← Architecture docs
│           ├── CHATBOT_UI.md         ← Phase 2 docs
│           ├── CONTEXT_EXTRACTION.md ← Phase 3 docs
│           └── V3_SETUP.md           ← Phase 1 docs
│
└── .env                              ← API keys (GITIGNORED)
    ├── GROQ_API_KEY=...
    ├── HYDRA_API_KEY=...
    └── [other config]
```

---

## What Was Done (Phase 4)

**Three-phase project completion:**

✅ **Phase 1:** Backend API with Hydra GPT
✅ **Phase 2:** Frontend UI widget
✅ **Phase 3:** Context extraction (mechanism-aware)
✅ **Phase 4 (Current):** Cloud service migration

**Phase 4 Changes:**
- Migrated from Hydra (unreliable) to Groq (99.99% uptime)
- Implemented service abstraction layer
- Added automatic fallback mechanism
- Zero changes to UI or context extraction layers
- Created new service classes (GroqService, HydraService)
- Team-owned email for sustainability

---

## How to Maintain

### Daily
- Users can access chatbot normally
- No action needed from your team

### Weekly
- Monitor error logs if issues reported
- Check `/v3b/api/logs/chatbot.log` for errors

### Monthly
- Review chatbot usage patterns
- Verify both services are responding
- Test fallback mechanism
  ```bash
  # Temporarily disable Groq in .env:
  GROQ_API_KEY=invalid
  # Test chatbot - should fallback to Hydra
  # Then restore correct key
  ```

### Yearly
- Rotate Groq API key
- Update mechanism metadata if needed
- Review and test all service configurations

---

## If Something Breaks

### Chatbot not responding at all

1. **Check if page loads:**
   - Open `/v3b/core/m-001/`
   - See if blue chat bubble appears

2. **If bubble not visible:**
   - Check browser console for JavaScript errors
   - Verify chatbot files loaded (network tab)
   - Check if navbar.php is included

3. **If bubble visible but not working:**
   - Open browser console
   - Send a test message
   - Look for error messages
   - Check `/v3b/api/logs/chatbot.log`

4. **Common causes:**
   - API key invalid → Update in .env
   - Service down → Check Groq/Hydra status
   - Network error → Check connectivity
   - Permission issue → Check file permissions

### Getting slow responses

- Normal if using Hydra (school server is CPU-bound)
- Check if Groq is up: https://status.groq.com
- If both services slow, Groq might be rate-limited

### Errors in logs

Check `/v3b/api/logs/chatbot.log` for:
- API key errors → Regenerate and update .env
- Service unavailable → Check service status pages
- Timeout errors → May be network issue

---

## Useful Commands

### Check service logs
```bash
tail -f /var/www/projects/f25-01/html/v3b/api/logs/chatbot.log
```

### Test API endpoint manually
```bash
curl -X POST http://localhost/p/f25-01/v3b/api/chatbot-stream.php \
  -H "Content-Type: application/json" \
  -d '{"message":"What is FCFS?","mechanism":"001","mode":"core"}'
```

### Verify .env is correct
```bash
grep "GROQ_API_KEY" /var/www/projects/f25-01/.env
```

### Check file permissions
```bash
ls -la /var/www/projects/f25-01/html/v3b/api/
```

---

## Contact & Escalation

**For API issues:**
- Groq: https://console.groq.com/docs/help
- Hydra: Contact CS department IT

**For code issues:**
- Check PHASE4_MIGRATION.md (architecture docs)
- Check chatbot-stream.php comments (implementation)
- Review git history for recent changes

**For future team:**
- Keep this document updated
- Add notes about any changes made
- Document any issues encountered

---

## Dos and Don'ts

✅ **DO:**
- Rotate API keys yearly
- Monitor service logs
- Test both services periodically
- Update team email password in manager
- Document changes in git commits

❌ **DON'T:**
- Commit .env to git (gitignore is set)
- Share API keys in plain text
- Modify v3 or v2c directories
- Change chatbot UI without testing
- Delete logs without archiving

---

## Future Improvements

Potential enhancements for next semester:

1. **Expand Metadata:** Add more algorithm descriptions
2. **Add Caching:** Reduce API calls for common questions
3. **Analytics:** Track which algorithms students ask about most
4. **Ollama Integration:** Support local AI model as fallback
5. **Multi-language:** Translate responses to other languages
6. **Streaming:** Show response as it generates (better UX)

See PHASE4_MIGRATION.md "Future Enhancements" section for details.

---

## Key Takeaways

🔑 **Remember:**
1. Groq is primary, Hydra is fallback
2. API keys in .env, NEVER in code or git
3. v3b is current, v3 is backup, v2c is NEVER touched
4. Rotate Groq key yearly
5. Monitor logs for issues
6. Test fallover quarterly

---

## Questions?

Refer to:
- **Architecture:** PHASE4_MIGRATION.md
- **Security:** API_KEY_MANAGEMENT.md
- **UI/Context:** CHATBOT_UI.md & CONTEXT_EXTRACTION.md
- **Setup:** V3_SETUP.md

Or check the git history for previous team's notes.

---

**Welcome to the team! Good luck maintaining the chatbot!** 🚀

Last Updated: November 17, 2025
Previous Team Lead: [Your Name]
