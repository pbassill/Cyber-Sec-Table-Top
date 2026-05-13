# ⚔️ Cyber Quest — Incident Response Tabletop Exercise Framework

A cyber security tabletop exercise simulation styled as a Dungeons & Dragons campaign. Built with PHP, Bootstrap, and SQLite. Designed for corporate incident response teams, GRC professionals, and security leadership.

## 🏰 Overview

Cyber Quest is a framework for running incident response tabletop exercises using a D&D-inspired interface. Facilitators build campaigns from modular JSON scenario templates, and dice rolls determine outcomes, complications, and plot twists throughout the exercise. All exercise data is persisted to a SQLite database for audit trails, regulatory evidence, and trend analysis.

## 🎲 Features

### Core Exercise Engine
- **Modular JSON Scenario Templates** — 11 scenarios across all major incident categories, each with injects, facilitator prompts, dice events, and random complications.
- **Campaign Builder** — String multiple scenarios together into a full tabletop session. Drag and reorder quests.
- **D20 Dice Mechanics** — Roll dice at critical decision points. Natural 20 = Critical Success. Natural 1 = Critical Failure.
- **Random Event Generator** — Environmental events (D6), plot twists (D20), and NPC actions add unpredictability.
- **Session Notes (Scribe's Log)** — Record observations and decisions throughout the exercise.
- **Real-Time Player View** — Participants join via session code or QR code and follow along live with auto-refresh.

### Corporate & Enterprise Features
- **Persistent Data Storage (SQLite)** — All exercises, action items, evaluations, and timeline events are stored in a SQLite database for exercise history and audit trails.
- **CSV Export** — Export full reports, gap analysis, session notes, evaluations, and timeline as CSV files for board reporting and regulatory evidence.
- **Compliance Framework Tags** — Scenarios and injects are tagged with regulatory frameworks (GDPR, NIST CSF, ISO 27001, DORA, FCA, PRA, PCI-DSS) for audit mapping.
- **Exercise History Dashboard** — Browse all past exercises with statistics, action item tracking, and export capabilities.
- **Action Item Tracking** — Save gap analysis and remediation actions with owners and target dates to the database. Track open/closed status.
- **Participant Evaluations** — Star ratings for exercise effectiveness, saved to database for trend analysis.
- **Session Timeline** — Automatic recording of inject progression with timestamps for session replay.

### Facilitator Tools
- **Inject Timer/Countdown** — Configurable countdown timer (5–30 min) keeps exercises on schedule.
- **Facilitator Private Notes** — DM-only hints and expected answers hidden from the player view.
- **Printable Facilitator Pack** — Auto-generated print-friendly briefing pack with all scenarios, injects, outcome tables, and private notes.
- **Calendar Integration** — Download .ics calendar invites with session code and player URL for exercise scheduling.
- **QR Code for Player Join** — Participants scan a QR code to join from mobile devices.

### UI & Deployment
- **Dark/Light Theme Toggle** — Switch between D&D dark theme and professional "boardroom mode" light theme.
- **D&D Themed Interface** — Dark medieval parchment styling with Cinzel/Spectral typography, gold accents, and fantasy-flavoured role names.
- **Docker Deployment** — One-command deployment with Docker and docker-compose.
- **API Endpoints** — JSON API for scenarios, dice rolls, session state, debrief data, and exports.

## 📋 Included Scenarios (11)

| Scenario | Campaign Category | Severity | Duration |
|---|---|---|---|
| 🔴 The Crypt of Encrypted Shadows | Cyber Attack — Ransomware | P1 — Critical | 60 min |
| 🟣 The Enchanted Scroll | Cyber Attack — Phishing | P2 — High | 45 min |
| ⚫ The Siege of Shadows | Cyber Attack — DDoS | P2 — High | 45 min |
| 🟠 The Traitor Within the Keep | Insider Threat | P2 — High | 60 min |
| 🔵 The Poisoned Alliance | Third-Party / Supply Chain | P1 — Critical | 45 min |
| ☁️ The Shattered Spire | Digital / Operational Resilience | P1 — Critical | 60 min |
| 🟢 The Scattered Fellowship | Cyber Attack — Remote Working | P2 — High | 45 min |
| 🏢 The Siege of the Iron Vault | Physical Security | P2 — High | 45 min |
| 📋 The Tribunal of the Elder Council | Regulatory / Compliance | P1 — Critical | 60 min |
| 💰 The Gilded Deception | Fraud & Financial Crime | P1 — Critical | 60 min |
| 🌪️ The Great Storm of the Iron Citadel | Business Continuity / DR | P1 — Critical | 60 min |
| 📂 The Shattered Vault of Secrets | Data Breach / Data Loss | P1 — Critical | 50 min |

### Compliance Framework Coverage

Scenarios are tagged with regulatory frameworks for audit mapping:
- **GDPR** Art.33-34, UK DPA 2018
- **NIST CSF** (DE, PR, RS, RC categories)
- **ISO 27001** (A.6–A.18 controls)
- **DORA** Art.11, 13, 28-30
- **FCA** SYSC, PRIN, SUP, COCON
- **PRA** SS1/21, Fundamental Rules
- **PCI-DSS** Requirements
- **SM&CR** accountability
- **ICO** Breach Reporting Guidance
- **NCSC** Guidance documents

## 🗂️ Project Structure

```
├── index.php                 # Landing page / Campaign Builder (The Tavern)
├── setup.php                 # Event setup (DM / Facilitator configuration)
├── scenarios.php             # Scenario browser (Quest Board)
├── session.php               # Main exercise runner (War Room)
├── player.php                # Read-only participant view
├── debrief.php               # Post-exercise debrief & gap analysis
├── history.php               # Exercise history dashboard
├── api/
│   ├── dice.php              # Dice roll API endpoint
│   ├── scenarios.php         # Scenarios list/detail API
│   ├── session_state.php     # Player polling API
│   ├── debrief.php           # Save debrief data API
│   ├── export.php            # CSV export API (gaps, notes, evaluations, full)
│   ├── facilitator_pack.php  # Printable facilitator briefing pack
│   └── calendar.php          # .ics calendar invite generator
├── assets/
│   ├── css/style.css         # D&D themed styling + light theme
│   └── js/app.js             # Frontend dice rolling & campaign builder
├── includes/
│   ├── functions.php         # Core PHP functions (dice, scenarios, sessions)
│   ├── database.php          # SQLite database layer & migrations
│   ├── header.php            # Page header with navigation & theme toggle
│   └── footer.php            # Page footer with theme persistence
├── templates/
│   ├── campaigns.json        # 11 campaign category definitions
│   ├── ransomware.json       # Ransomware attack scenario
│   ├── phishing.json         # Phishing attack scenario
│   ├── ddos.json             # DDoS attack scenario
│   ├── insider_threat.json   # Insider threat scenario
│   ├── supply_chain.json     # Supply chain compromise scenario
│   ├── cloud_services.json   # Cloud services outage scenario
│   ├── remote_working.json   # Remote working security scenario
│   ├── physical_security.json# Physical security breach scenario
│   ├── regulatory_compliance.json # Regulatory investigation scenario
│   ├── fraud_financial_crime.json # Financial crime scenario
│   ├── business_continuity.json   # BCP / disaster recovery scenario
│   ├── data_breach.json      # Data breach / data loss scenario
│   └── random_events.json    # Random events, plot twists, NPC actions
├── data/
│   ├── sessions/             # Shared session JSON files
│   └── cyberquest.sqlite     # SQLite database (auto-created)
├── Dockerfile                # Docker container configuration
├── docker-compose.yml        # Docker Compose for one-command deployment
└── IR_Tabletop_Exercise.md   # Reference document
```

## 🚀 Getting Started

### Requirements
- PHP 8.0+ (with `pdo_sqlite`, `session`, and `json` extensions)
- A web server (Apache, Nginx, or PHP's built-in server)

### Quick Start

```bash
# Clone the repository
git clone https://github.com/pbassill/Cyber-Sec-Table-Top.git
cd Cyber-Sec-Table-Top

# Start PHP's built-in development server
php -S localhost:8000

# Open in your browser
open http://localhost:8000
```

### With Docker (Recommended)

```bash
# Clone and start
git clone https://github.com/pbassill/Cyber-Sec-Table-Top.git
cd Cyber-Sec-Table-Top

# Build and run
docker-compose up -d

# Open in your browser
open http://localhost:8080
```

### With Apache
Point your document root to the repository directory and ensure `mod_rewrite` and `mod_php` are enabled.

## 🎮 How to Run an Exercise

1. **Event Setup** (`setup.php`) — Name your event, choose the **industry vertical** for your players (banking, healthcare, retail, etc.), select campaign category, choose scenarios, add participants, and generate a session code. The vertical you pick controls which adventures the DM is offered — see *Industry Verticals* below.
2. **Share the Link** — Share the session code or QR code with participants. They join via `player.php`.
3. **Enter the War Room** (`session.php`) — The facilitator reads each inject aloud, uses the timer, and guides discussion using the prompts.
4. **Roll the Dice** — At key moments, click the dice buttons. Outcomes range from Critical Failure (💀) to Critical Success (👑).
5. **Use DM Tools** — Toggle private facilitator notes, draw complication cards, generate random events.
6. **Record Notes** — Use the Scribe's Log to capture key decisions and observations.
7. **Debrief** (`debrief.php`) — Complete gap analysis, capture action items, rate the exercise, and save everything to the database.
8. **Export** — Download CSV reports for board packs, auditors, and regulatory evidence.
9. **Review History** (`history.php`) — Browse past exercises, track action item closure, and analyse trends.

## 🎯 Dice Outcome Scale

| Roll | Outcome | Icon |
|---|---|---|
| Natural 1 | Critical Failure | 💀 |
| 2–7 | Failure | ⚔️ |
| 8–14 | Partial Success | 🛡️ |
| 15–19 | Success | ⚡ |
| Natural 20 | Critical Success | 👑 |

## 📝 Creating Custom Scenarios

Add new JSON files to the `templates/` directory following the existing schema. Each scenario needs:

- `id` — Unique identifier (matches filename)
- `campaign` — Campaign category ID (from `campaigns.json`)
- `verticals` — Array of industry vertical IDs (from `verticals.json`) the scenario is appropriate for. Use the literal `["all"]` to make the scenario available regardless of which vertical the DM picks. A scenario without a `verticals` field is treated as `["all"]` for backwards compatibility.
- `title` / `subtitle` / `description` — Flavour text
- `severity`, `theme_color`, `icon` — Display properties
- `compliance_frameworks[]` — Regulatory framework tags (e.g., `["GDPR Art.33", "NIST CSF RS.RP"]`)
- `roles[]` — Required and optional participant roles with D&D titles
- `injects[]` — Sequential scenario phases, each with:
  - `narrative` — The inject text read by the facilitator
  - `facilitator_prompts[]` — Discussion questions
  - `facilitator_notes[]` — Private DM hints (not shown to players)
  - `dice_events[]` — Dice rolls with outcome tables
  - `random_complications[]` — Possible complication cards
  - `compliance_frameworks[]` — Per-inject regulatory tags
- `debrief` — Post-scenario review questions

### Industry Verticals

Verticals are defined in `templates/verticals.json` (one entry per vertical with `id`, `title`, `icon`, `theme_color`, and `description`). The shipped list covers banking, healthcare, retail, manufacturing, government, technology/SaaS, energy & utilities, education, legal & professional services, plus a `generic` "any industry" catch-all.

When the DM picks a vertical at event setup, the campaign category cards and the available-adventures list are filtered to scenarios whose `verticals` array contains either that vertical's ID or the literal sentinel `"all"`. Picking `generic` shows every scenario. To add a new industry, add an entry to `verticals.json`; to make an existing scenario available there, add the new ID to that scenario's `verticals` array.

## 🏢 Corporate Deployment Guide

### For CISOs and GRC Teams

1. **Deploy** using Docker or your existing PHP infrastructure.
2. **Customise** scenarios by editing JSON templates or creating new ones.
3. **Run exercises** with the facilitator guide and printable pack.
4. **Export evidence** as CSV for board reporting and regulatory compliance.
5. **Track remediation** via the gap analysis and action item system.
6. **Review history** to demonstrate continuous improvement to auditors.

### Compliance Evidence

Each exercise generates:
- Full CSV report with all session data
- Gap analysis with owners and target dates
- Participant evaluation ratings
- Session timeline with timestamps
- Compliance framework mapping per scenario and inject

## 📄 Licence

This project is provided as-is for educational and training purposes.
