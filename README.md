# ⚔️ Cyber Quest — Incident Response Tabletop Exercise Framework

A cyber security tabletop exercise simulation styled as a Dungeons & Dragons campaign. Built with PHP and Bootstrap.

## 🏰 Overview

Cyber Quest is a framework for running incident response tabletop exercises using a D&D-inspired interface. Facilitators build campaigns from modular JSON scenario templates, and dice rolls determine outcomes, complications, and plot twists throughout the exercise.

## 🎲 Features

- **Modular JSON Scenario Templates** — Each scenario (ransomware, insider threat, supply chain compromise) is a self-contained JSON file with injects, facilitator prompts, dice events, and random complications.
- **Campaign Builder** — String multiple scenarios together into a full tabletop session. Drag and reorder quests.
- **D20 Dice Mechanics** — Roll dice at critical decision points. Natural 20 = Critical Success. Natural 1 = Critical Failure. Outcomes shape the narrative.
- **Random Event Generator** — Environmental events (D6), plot twists (D20), and NPC actions add unpredictability.
- **D&D Themed Interface** — Dark medieval parchment styling with Cinzel/Spectral typography, gold accents, and fantasy-flavoured role names.
- **Session Notes (Scribe's Log)** — Record observations and decisions throughout the exercise.
- **Debrief & Gap Analysis** — Structured debrief with evaluation forms, gap identification tables, and printable reports.
- **API Endpoints** — JSON API for scenarios and dice rolls, enabling future integrations.

## 📋 Included Scenarios

| Scenario | Theme | Severity | Duration |
|---|---|---|---|
| 🔴 The Crypt of Encrypted Shadows | Ransomware Attack | P1 — Critical | 60 min |
| 🟠 The Traitor Within the Keep | Insider Threat / Data Theft | P2 — High | 60 min |
| 🔵 The Poisoned Alliance | Supply Chain Compromise | P1 — Critical | 45 min |

## 🗂️ Project Structure

```
├── index.php                 # Landing page / Campaign Builder (The Tavern)
├── scenarios.php             # Scenario browser (Quest Board)
├── session.php               # Main exercise runner (War Room)
├── debrief.php               # Post-exercise debrief & gap analysis
├── api/
│   ├── dice.php              # Dice roll API endpoint
│   └── scenarios.php         # Scenarios list/detail API
├── assets/
│   ├── css/style.css         # D&D themed styling
│   └── js/app.js             # Frontend dice rolling & campaign builder
├── includes/
│   ├── functions.php         # Core PHP functions (dice, scenarios, sessions)
│   ├── header.php            # Page header with navigation
│   └── footer.php            # Page footer
├── templates/
│   ├── ransomware.json       # Ransomware attack scenario
│   ├── insider_threat.json   # Insider threat scenario
│   ├── supply_chain.json     # Supply chain compromise scenario
│   └── random_events.json    # Random events, plot twists, NPC actions
└── IR_Tabletop_Exercise.md   # Reference document
```

## 🚀 Getting Started

### Requirements
- PHP 7.4+ (with `session` and `json` extensions)
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

### With Apache
Point your document root to the repository directory and ensure `mod_rewrite` and `mod_php` are enabled.

## 🎮 How to Run an Exercise

1. **Visit the Tavern** (`index.php`) — Review available quests and build your campaign by selecting and ordering scenarios.
2. **Assign Roles** — Each participant takes a role (War Chief, Shadow Watcher, Keeper of the Codex, etc.).
3. **Enter the War Room** (`session.php`) — The facilitator reads each inject aloud, then guides discussion using the prompts.
4. **Roll the Dice** — At key moments, click the dice buttons. Outcomes range from Critical Failure (💀) to Critical Success (👑).
5. **Draw Complications** — Use the random complication cards and event generators to add unpredictability.
6. **Record Notes** — Use the Scribe's Log to capture key decisions and observations.
7. **Debrief** (`debrief.php`) — Review structured debrief questions, capture gaps and remediation actions, and complete evaluation forms.

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

- `id` — Unique identifier
- `title` / `subtitle` / `description` — Flavour text
- `severity`, `theme_color`, `icon` — Display properties
- `roles[]` — Required and optional participant roles
- `injects[]` — Sequential scenario phases, each with:
  - `narrative` — The inject text read by the facilitator
  - `facilitator_prompts[]` — Discussion questions
  - `dice_events[]` — Dice rolls with outcome tables
  - `random_complications[]` — Possible complication cards
- `debrief` — Post-scenario review questions

## 📄 Licence

This project is provided as-is for educational and training purposes.
