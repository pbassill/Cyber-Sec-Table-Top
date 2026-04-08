# Incident Response Playbook — Tabletop Exercise

> **Classification:** CONFIDENTIAL — Internal Use Only
>
> **Document Owner:** Chief Information Security Officer
> **Version:** 1.0
> **Date:** [DD/MM/YYYY]
> **Review Date:** [DD/MM/YYYY]
> **Approved By:** [Name / Title]

---

## Table of Contents

- [1. Introduction and Purpose](#1-introduction-and-purpose)
  - [1.1 Objectives](#11-objectives)
  - [1.2 Regulatory Context](#12-regulatory-context)
  - [1.3 Scope and Limitations](#13-scope-and-limitations)
- [2. Pre-Session Preparation](#2-pre-session-preparation)
  - [2.1 Facilitator Preparation (2 Weeks Prior)](#21-facilitator-preparation-2-weeks-prior)
  - [2.2 Required Reference Materials](#22-required-reference-materials)
  - [2.3 Participant Briefing Pack](#23-participant-briefing-pack)
- [3. Roles and Participants](#3-roles-and-participants)
- [4. Exercise Agenda](#4-exercise-agenda)
- [5. Exercise Ground Rules](#5-exercise-ground-rules)
- [6. Scenario A — Ransomware Attack](#6-scenario-a--ransomware-attack)
- [7. Scenario B — Insider Threat: Privileged Data Theft](#7-scenario-b--insider-threat-privileged-data-theft)
- [8. Scenario C — Third-Party Supply Chain Compromise](#8-scenario-c--third-party-supply-chain-compromise)
- [9. Phase 4 — Hot Wash and Debrief](#9-phase-4--hot-wash-and-debrief)
  - [9.1 Structured Debrief Questions](#91-structured-debrief-questions)
  - [9.2 Gap Identification and Action Capture](#92-gap-identification-and-action-capture)
- [Appendix A — Regulatory Notification Quick Reference](#appendix-a--regulatory-notification-quick-reference)
- [Appendix B — Incident Severity Classification Matrix](#appendix-b--incident-severity-classification-matrix)
- [Appendix C — Participant Evaluation Form](#appendix-c--participant-evaluation-form)
- [Appendix D — Key Contact Directory Template](#appendix-d--key-contact-directory-template)

---

## 1. Introduction and Purpose

This document provides the complete facilitator's guide for a four-hour Incident Response Tabletop Exercise designed for [Bank Name] Private Banking. The exercise is intended to test, validate, and improve the bank's incident response capabilities across multiple threat scenarios that are directly relevant to the private banking sector.

The tabletop exercise simulates realistic cyber security incidents in a structured, discussion-based format. No live systems will be affected. All scenarios are fictitious but grounded in real-world threat intelligence applicable to financial services institutions.

### 1.1 Objectives

1. Validate the bank's Incident Response Plan (IRP) against realistic, multi-stage attack scenarios.
2. Test decision-making processes under pressure, including escalation pathways and regulatory notification obligations.
3. Evaluate cross-functional coordination between IT Security, Operations, Compliance, Legal, Senior Management, and Client Relations.
4. Identify gaps in technical controls, communication protocols, and recovery procedures.
5. Satisfy regulatory expectations for periodic incident response testing (FCA, PRA, GDPR, DORA).
6. Build institutional muscle memory for crisis situations and improve mean time to containment.

### 1.2 Regulatory Context

Private banks operating in the United Kingdom and the European Economic Area are subject to a range of regulatory requirements mandating incident response preparedness. This exercise has been designed to address obligations under the following frameworks:

- **Financial Conduct Authority (FCA)** — Operational Resilience requirements (PS21/3) and Senior Managers & Certification Regime (SM&CR) accountability.
- **Prudential Regulation Authority (PRA)** — SS1/21 Operational Resilience and supervisory expectations for cyber resilience.
- **General Data Protection Regulation (UK GDPR)** — Article 33/34 breach notification obligations (72-hour reporting window to the ICO).
- **Digital Operational Resilience Act (DORA)** — ICT incident management, classification, and reporting requirements for financial entities.
- **NIS2 Directive** — Network and information systems security obligations for essential and important entities.
- **SWIFT Customer Security Programme (CSP)** — Mandatory and advisory security controls for SWIFT-connected institutions.

### 1.3 Scope and Limitations

This exercise is a facilitated discussion only. It does not involve any penetration testing, red team activity, or interaction with live production systems. Participants should respond as they would in a genuine incident, referencing existing policies and procedures wherever possible. Where gaps are identified, these should be noted for remediation rather than resolved in real time.

---

## 2. Pre-Session Preparation

### 2.1 Facilitator Preparation (2 Weeks Prior)

- Confirm exercise date, venue, and participant availability.
- Distribute the participant briefing pack (Section 2.3) no fewer than five working days before the exercise.
- Verify that all referenced documents (IRP, BCP, communications templates) are current and accessible.
- Prepare printed scenario inject cards and any supporting materials (network diagrams, asset registers).
- Arrange a dedicated, secure meeting room with no external attendees. Ensure recording equipment is available if minutes are to be taken verbally.
- Prepare evaluation forms (Appendix C) for distribution at the close of the session.
- Brief any observers (auditors, regulators) on ground rules and their non-participant status.

### 2.2 Required Reference Materials

The following documents should be available to participants throughout the exercise, either in print or on secure devices:

- Incident Response Plan (current approved version)
- Business Continuity Plan and IT Disaster Recovery Plan
- Crisis Communications Plan and pre-approved client notification templates
- Regulatory notification checklists (ICO, FCA, PRA, SWIFT)
- Network architecture diagrams and critical asset register
- Third-party vendor and service provider contact directory
- Cyber insurance policy summary (coverage, notification requirements, panel firms)
- Senior Management escalation matrix and out-of-hours contact procedures

### 2.3 Participant Briefing Pack

Each participant should receive a briefing pack containing: an overview of the exercise objectives, ground rules, their assigned role, a summary of the bank's current incident response framework, and a reminder that all discussions are conducted under Chatham House rules. Participants should be reminded not to disclose scenario details to colleagues who are not participating.

---

## 3. Roles and Participants

The following roles should be represented during the exercise. Where an individual holds multiple responsibilities, a deputy should attend for the secondary role to ensure full coverage.

| Role | Suggested Participant | Exercise Responsibilities |
|---|---|---|
| **Exercise Facilitator** | CISO or External Consultant | Delivers scenario injects, manages timing, guides discussion, captures observations |
| **Incident Commander** | Head of IT Security | Leads the response team, makes containment and escalation decisions |
| **IT Operations Lead** | Infrastructure Manager | Provides technical context, assesses system impact, proposes containment actions |
| **SOC Analyst** | Senior SOC Analyst | Interprets alerts, provides forensic indicators, monitors detection tooling response |
| **Compliance / DPO** | Data Protection Officer | Assesses regulatory notification obligations, advises on data breach classification |
| **Legal Counsel** | Head of Legal / External Counsel | Advises on legal exposure, privilege, law enforcement engagement, contractual obligations |
| **Client Relations** | Head of Private Client Services | Manages client communication strategy, assesses reputational impact |
| **Senior Management** | COO or Deputy CEO | Provides executive-level decision authority, approves external communications |
| **Communications Lead** | Head of Corporate Communications | Drafts holding statements, manages media and social media response |
| **Third-Party / Vendor Mgmt** | Procurement / Vendor Manager | Assesses third-party exposure, coordinates vendor incident response |
| **Observer(s)** | Internal Audit / External Auditor | Silently observes, notes process adherence, provides independent assessment |
| **Scribe** | Governance / Risk Analyst | Documents all decisions, actions, timelines, and identified gaps in real time |

All participants must treat exercise materials and discussions as Confidential. Mobile phones should be set to silent. Participants may step out only during scheduled breaks.

---

## 4. Exercise Agenda

The exercise is divided into four phases across four hours. Each scenario phase follows a structured pattern of inject delivery, team discussion, facilitator prompts, and a brief summary before progressing.

| Time (UTC) | Phase | Description |
|---|---|---|
| 09:00 – 09:15 | **Opening** | Welcome, objectives, ground rules, role confirmation, classification reminder |
| 09:15 – 10:15 | **Phase 1: Scenario A** | Ransomware attack on core banking infrastructure with data exfiltration threat |
| 10:15 – 10:30 | **Break** | Refreshments. Facilitator resets materials for Phase 2 |
| 10:30 – 11:30 | **Phase 2: Scenario B** | Insider threat — privileged employee data theft targeting UHNW client portfolios |
| 11:30 – 11:45 | **Break** | Refreshments. Facilitator resets materials for Phase 3 |
| 11:45 – 12:30 | **Phase 3: Scenario C** | Third-party supply chain compromise via managed service provider |
| 12:30 – 13:00 | **Phase 4: Hot Wash** | Structured debrief, gap analysis, remediation action capture, evaluation forms |

---

## 5. Exercise Ground Rules

1. This is a no-fault exercise. The purpose is to identify improvements, not to assign blame.
2. There are no wrong answers. All contributions are valuable and should be voiced without hesitation.
3. Respond as you would in a real incident. Reference actual policies, tools, and contacts.
4. The facilitator controls the pace. Scenario injects are delivered at set intervals; do not skip ahead.
5. All discussions are governed by Chatham House rules. Observations may be shared; attribution may not.
6. Observers may not participate in discussion unless invited by the facilitator.
7. The scribe will record all key decisions, actions, and identified gaps. Participants should flag items they wish to be captured.
8. If a genuine incident occurs during the exercise, the facilitator will pause the session immediately. Real incidents always take priority.

---

## 6. Scenario A — Ransomware Attack

**Duration: 60 minutes (09:15 – 10:15 UTC)**

### Inject A-1: Initial Detection (T+0 Minutes)

> 🔴 **INJECT**
>
> It is 07:42 UTC on a Tuesday morning. The Security Operations Centre (SOC) receives a Priority 1 alert from the Endpoint Detection and Response (EDR) platform. Multiple workstations on the Private Client Services floor are exhibiting anomalous behaviour: rapid file encryption across mapped network drives, creation of ransom note text files, and outbound connections to a known command-and-control IP address.
>
> Simultaneously, the IT Service Desk receives three calls from relationship managers reporting that they cannot open client portfolio documents. One manager reports seeing a pop-up window demanding payment in cryptocurrency.
>
> The EDR dashboard indicates 14 endpoints are affected and the count is rising. The affected file shares include the `S:\PrivateClients\` and `S:\Compliance\` directories.

**Facilitator Discussion Prompts — Inject A-1:**

- Who is responsible for declaring this a security incident and at what severity level?
- What is the immediate containment strategy? Should affected endpoints be isolated from the network?
- What is the impact on client-facing services? Can relationship managers continue to service clients?
- Has the SOC confirmed whether the EDR agent is blocking the encryption or merely alerting?
- At what point do we escalate to the Incident Commander and convene the Crisis Management Team?
- Do we have offline backups of the affected file shares? When were they last tested?

---

### Inject A-2: Escalation and Scope Expansion (T+15 Minutes)

> 🔴 **INJECT**
>
> The SOC has completed initial triage. The attack vector has been identified as a phishing email received by a senior relationship manager at 07:12 UTC. The email contained a macro-enabled Excel attachment purporting to be a client portfolio rebalancing report. The malware achieved initial execution via a VBA macro and subsequently moved laterally using harvested domain credentials.
>
> Critically, the SOC has identified outbound data transfer to an external IP address. Approximately 2.3 GB of data from the `S:\PrivateClients\` directory was exfiltrated between 07:20 and 07:41 UTC before the EDR alert triggered. The directory contains client identification documents, account statements, tax records, and portfolio valuations for approximately 340 ultra-high-net-worth (UHNW) clients.
>
> A ransom note has been found demanding 50 BTC (approximately £2.1 million at current rates), with a 72-hour payment deadline. The note threatens publication of stolen client data on a dark web leak site if payment is not made.

**Facilitator Discussion Prompts — Inject A-2:**

- This is now a confirmed data breach involving personal data of UHNW clients. What are our GDPR Article 33 obligations and what is the notification timeline?
- Do we engage the ICO proactively or wait until we have completed our internal assessment?
- What is our position on ransom payment? Who has the authority to make this decision?
- Should we engage our cyber insurance provider and their nominated incident response panel firm?
- How do we preserve forensic evidence whilst also containing the spread?
- What are the FCA/PRA notification requirements for a material operational incident?
- Do we need to notify SWIFT if any SWIFT-related infrastructure is potentially compromised?

---

### Inject A-3: Client and Media Pressure (T+30 Minutes)

> 🔴 **INJECT**
>
> A financial journalist from a national broadsheet has contacted the Communications team, stating they have received a tip-off about a cyber attack at the bank. They are requesting comment before a 14:00 UTC publication deadline.
>
> Separately, two UHNW clients have contacted their relationship managers directly, stating they have received emails from an unknown party claiming to hold their financial records and threatening to release them unless the clients themselves pay a personal ransom of £50,000 each.
>
> The Board Chairman has been informed and is requesting an immediate briefing. The bank's largest institutional client, a family office with £800 million under management, has scheduled an urgent call with the CEO to discuss the security of their assets and data.

**Facilitator Discussion Prompts — Inject A-3:**

- What is our media response strategy? Do we issue a holding statement or a full press release?
- How do we communicate with affected clients? Who approves the client notification and what does it contain?
- Clients are being directly extorted. Do we advise them to contact law enforcement? Do we coordinate this centrally?
- Should we engage the National Cyber Security Centre (NCSC) and/or Action Fraud?
- How do we manage the Board's expectations whilst the investigation is ongoing?
- What is the reputational risk assessment? Could we lose the family office account?

---

### Inject A-4: Recovery Decisions (T+45 Minutes)

> 🔴 **INJECT**
>
> The containment team has successfully isolated all affected endpoints and halted lateral movement. The threat actor's C2 channel has been blocked at the perimeter firewall and DNS sinkhole.
>
> However, the IT Operations team reports that backup restoration for the encrypted file shares will take approximately 18 hours due to the volume of data and the need to verify backup integrity. During this period, relationship managers will have no access to client portfolio documents.
>
> The CISO has confirmed that the threat actor likely maintained persistent access for up to 11 days before deploying ransomware, based on initial forensic analysis of authentication logs. The full extent of the compromise is not yet known.
>
> The 72-hour ransom deadline is now at T-70 hours.

**Facilitator Discussion Prompts — Inject A-4:**

- With 18 hours of downtime, what is our business continuity strategy for client servicing?
- 11 days of persistent access suggests a deeper compromise. What additional systems need forensic review?
- Do we engage an external digital forensics firm, or rely on internal capability?
- What is the decision-making framework for ransom payment, and who chairs that decision?
- How do we ensure the threat actor has not left additional backdoors or persistence mechanisms?
- What is our timeline for completing the ICO notification given the evolving scope of the breach?

---

## 7. Scenario B — Insider Threat: Privileged Data Theft

**Duration: 60 minutes (10:30 – 11:30 UTC)**

### Inject B-1: Anomalous Activity Detected (T+0 Minutes)

> 🟠 **INJECT**
>
> The Data Loss Prevention (DLP) system has generated an alert. A senior portfolio analyst with privileged access to the Client Relationship Management (CRM) system has downloaded an unusually large dataset overnight. The download occurred at 02:17 UTC and comprised the full client database extract, including: names, addresses, dates of birth, passport numbers, net worth assessments, source of funds documentation, and investment holdings for all 1,247 private banking clients.
>
> The analyst's role requires access to individual client records but not bulk exports. The DLP policy was configured to alert on downloads exceeding 500 records. The analyst's workstation shows the data was saved to an encrypted USB device.
>
> HR records indicate the analyst submitted their resignation three days ago, with a four-week notice period. Their LinkedIn profile, updated yesterday, lists a new role at a competing private bank starting next month.

**Facilitator Discussion Prompts — Inject B-1:**

- What is the appropriate initial response? Do we immediately revoke the analyst's access?
- If we revoke access, do we risk alerting the analyst and giving them time to destroy evidence or move data?
- What are the legal considerations around monitoring an employee's activity without their knowledge?
- Should we involve HR and Legal before taking any technical action?
- Is this a data breach under GDPR if the data has not yet left the organisation?
- What is the bank's policy on USB device usage? Should this have been blocked by endpoint controls?

---

### Inject B-2: Investigation Findings (T+20 Minutes)

> 🟠 **INJECT**
>
> A covert review of the analyst's email (authorised by Legal and the DPO under the bank's Acceptable Use Policy) reveals the following:
>
> The analyst sent an encrypted ZIP file to a personal email address at 03:04 UTC on the same night as the download. The file size is consistent with the extracted dataset.
>
> Additionally, a review of the analyst's access logs over the past 30 days shows a pattern of querying individual UHNW client records that fall outside their assigned portfolio. Eleven of the queried clients are known to be prospects of the competing bank the analyst is joining.
>
> The analyst is scheduled to be in the office today and is currently at their desk.

**Facilitator Discussion Prompts — Inject B-2:**

- Data has now left the organisation via personal email. What are the regulatory notification obligations?
- How do we handle the analyst? Do we confront them, conduct a formal interview, or escalate to law enforcement?
- What evidence preservation steps are required before any confrontation?
- Is this a matter for the police under the Computer Misuse Act 1990 or the Data Protection Act 2018?
- Do we have grounds for an injunction against the competing bank to prevent use of the stolen data?
- Which UHNW clients need to be notified and what do we tell them?

---

### Inject B-3: Complications (T+35 Minutes)

> 🟠 **INJECT**
>
> During a discreet meeting with HR and Legal, the analyst becomes confrontational and claims they were acting on verbal instructions from their line manager (a Managing Director) to prepare a 'client transition plan' for the competing bank as part of an informal arrangement.
>
> The Managing Director denies any such instruction. However, the analyst produces a text message thread (on a personal device) that contains ambiguous language from the MD, including: 'Let's make sure we have everything ready for the move' and 'Keep this between us for now.'
>
> The MD is a Senior Manager under the SM&CR regime.

**Facilitator Discussion Prompts — Inject B-3:**

- How does the involvement of a Senior Manager change the response and escalation pathway?
- What are the SM&CR implications? Do we need to notify the FCA about potential conduct issues?
- Should both individuals be suspended pending investigation?
- How do we preserve the text message evidence on the analyst's personal device?
- Does this change the nature of the offence from a lone actor to potential conspiracy?
- What independent investigation capability do we engage to avoid conflicts of interest?

---

## 8. Scenario C — Third-Party Supply Chain Compromise

**Duration: 45 minutes (11:45 – 12:30 UTC)**

### Inject C-1: Vendor Notification (T+0 Minutes)

> 🔵 **INJECT**
>
> The bank's managed IT services provider, 'SecureOps Ltd' (fictitious), has issued an urgent notification to all clients. SecureOps provides privileged remote access for system administration, patch management, and monitoring of the bank's on-premises infrastructure.
>
> SecureOps reports that their internal environment was compromised approximately 14 days ago. The attacker gained access to SecureOps' remote management tooling, which holds administrative credentials for client environments, including [Bank Name]. SecureOps cannot yet confirm whether the attacker used these credentials to access client systems.
>
> SecureOps has engaged a third-party forensics firm, but initial findings will not be available for 48–72 hours.

**Facilitator Discussion Prompts — Inject C-1:**

- What is our immediate response to this notification? Do we sever all SecureOps remote access immediately?
- If we cut SecureOps access, what operational services do we lose, and do we have the internal capability to cover them?
- What credentials does SecureOps hold for our environment, and can we rotate them unilaterally?
- Do our contractual arrangements with SecureOps include mandatory breach notification timelines and indemnities?
- Should we treat our own environment as compromised until proven otherwise?
- What threat hunting activities should we initiate on our own systems?

---

### Inject C-2: Evidence of Access (T+20 Minutes)

> 🔵 **INJECT**
>
> Internal threat hunting has identified suspicious activity. Authentication logs show that a SecureOps service account authenticated to three of the bank's domain controllers at 04:30 UTC, six days ago. The session duration was approximately 90 minutes.
>
> During this session, the account queried Active Directory for all service accounts, exported the NTDS.dit file (the Active Directory database containing password hashes), and accessed the SWIFT Alliance Lite2 gateway server.
>
> The SWIFT gateway server audit logs show a query of the transaction log archive but no evidence of fraudulent payment instructions being submitted. However, the integrity of the logs themselves cannot be assured if the threat actor had administrative access.

**Facilitator Discussion Prompts — Inject C-2:**

- The NTDS.dit file has been exfiltrated. What is the scope of credential compromise, and what is the remediation plan?
- SWIFT infrastructure has been accessed. What are our obligations under the SWIFT CSP, and do we notify SWIFT directly?
- Do we halt all SWIFT payment processing until the integrity of the gateway can be assured?
- What is the financial exposure if fraudulent SWIFT messages were submitted but the logs were tampered with?
- How do we communicate this to the PRA given the systemic risk implications?
- What are the contractual and legal remedies available to us against SecureOps?

---

## 9. Phase 4 — Hot Wash and Debrief

**Duration: 30 minutes (12:30 – 13:00 UTC)**

The facilitator should guide the debrief through the following structured discussion areas, capturing all observations, identified gaps, and remediation actions.

### 9.1 Structured Debrief Questions

#### Process and Procedures

1. Were the Incident Response Plan and supporting procedures adequate for the scenarios presented?
2. Were escalation pathways clear and followed correctly? Were there any points of confusion?
3. Were regulatory notification obligations well understood? Did the team know the timelines and recipients?
4. Were roles and responsibilities clearly defined, or were there overlaps and gaps?

#### Technical Controls

1. Did the detection and monitoring tools perform as expected in each scenario?
2. Were containment options adequate and timely? What additional controls would have helped?
3. Were backup and recovery capabilities sufficient? Were RTOs and RPOs achievable?
4. Are privileged access management controls adequate for both internal users and third parties?

#### Communication and Coordination

- Were internal communications effective across all functions?
- Were client communication templates adequate and pre-approved?
- Was the media response strategy effective and timely?
- Were third-party and vendor management communications handled appropriately?

### 9.2 Gap Identification and Action Capture

The scribe should capture all identified gaps and remediation actions in the following format:

| # | Identified Gap | Remediation Action | Owner | Target Date |
|---|---|---|---|---|
| 1 | | | | |
| 2 | | | | |
| 3 | | | | |
| 4 | | | | |
| 5 | | | | |
| 6 | | | | |

---

## Appendix A — Regulatory Notification Quick Reference

| Regulator / Body | Trigger | Timeline | Method |
|---|---|---|---|
| **ICO (UK GDPR)** | Personal data breach likely to result in risk to individuals | Within 72 hours of becoming aware | ICO online portal or telephone |
| **FCA** | Material operational incident or cyber attack affecting firm's ability to operate | As soon as reasonably practicable | FCA Connect portal or supervisory contact |
| **PRA** | Significant operational incident with prudential implications | As soon as reasonably practicable | PRA supervisory team contact |
| **SWIFT** | Confirmed or suspected compromise of SWIFT-related infrastructure | Immediately upon detection | SWIFT ISAC reporting and CSP channels |
| **NCSC** | Significant cyber incident affecting UK financial infrastructure | As soon as reasonably practicable | NCSC incident reporting portal |
| **Action Fraud / NCA** | Criminal cyber offence (ransomware, data theft, fraud) | As soon as practical after containment priorities addressed | Action Fraud online or NCA referral |
| **Affected Individuals** | High risk to rights and freedoms of data subjects (Art. 34) | Without undue delay | Direct written notification |

---

## Appendix B — Incident Severity Classification Matrix

| Severity | Description | Example | Response Expectation |
|---|---|---|---|
| **P1 — Critical** | Active compromise with confirmed data loss, client impact, or systemic threat to operations | Ransomware with data exfiltration; SWIFT infrastructure compromise | Immediate: Crisis Management Team convened within 30 minutes; all-hands response |
| **P2 — High** | Confirmed security incident with significant potential for data loss or operational disruption | Insider data theft; compromised privileged account with lateral movement | Urgent: Incident Commander engaged within 1 hour; dedicated response team |
| **P3 — Medium** | Suspected incident requiring investigation; limited evidence of impact | Anomalous DLP alert; single phishing compromise without lateral movement | Prompt: SOC-led investigation within 4 hours; escalation if scope expands |
| **P4 — Low** | Security event with no confirmed malicious activity; precautionary investigation | Failed brute force attempts; blocked malware at perimeter | Standard: SOC triage within 24 hours; log and monitor |

---

## Appendix C — Participant Evaluation Form

Please complete this form at the close of the exercise and return it to the facilitator.

| Question | Rating (1–5) / Comments |
|---|---|
| Were the exercise objectives clearly communicated? | |
| Were the scenarios realistic and relevant to our organisation? | |
| Did you feel your role and responsibilities were clear during the exercise? | |
| Were escalation pathways and decision-making processes effective? | |
| Do you feel the Incident Response Plan is adequate for the scenarios presented? | |
| Were regulatory notification obligations well understood by the team? | |
| Were communication strategies (internal, client, media) effective? | |
| Were technical containment and recovery options adequate? | |
| What was the most valuable learning from today's exercise? | |
| What is the single most important improvement the bank should make? | |

| Field | |
|---|---|
| **Name (optional):** | |
| **Role:** | |
| **Date:** | |

---

## Appendix D — Key Contact Directory Template

Maintain this directory as a living document. Review and update quarterly.

| Function / Entity | Contact Name | Office Phone | Mobile | Email |
|---|---|---|---|---|
| CISO | | | | |
| IT Operations (On-Call) | | | | |
| Legal Counsel | | | | |
| Data Protection Officer | | | | |
| Head of Compliance | | | | |
| Head of Client Relations | | | | |
| Corporate Communications | | | | |
| CEO / COO | | | | |
| Board Chairman | | | | |
| Cyber Insurance Broker | | | | |
| External Forensics Firm | | | | |
| External Legal (IR Panel) | | | | |
| SecureOps Ltd (MSP) | | | | |
| ICO Reporting Line | | | | |
| FCA Supervisory Contact | | | | |
| PRA Supervisory Contact | | | | |
| NCSC Incident Reporting | | | | |
| SWIFT ISAC | | | | |

---

> **CONFIDENTIAL — Internal Use Only**
> **[Bank Name] Private Banking — Incident Response Tabletop Exercise**
