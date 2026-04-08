/**
 * Cyber Quest — Frontend JavaScript
 * Handles dice rolling, campaign building, and session interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    initCampaignBuilder();
    initSetupPage();
    initDiceRollers();
    initRandomEventGenerators();
    initComplicationCards();
    initQuickDiceRoller();
});

/* ════════════════════════════════════════════
   CSRF Token Helper
   ════════════════════════════════════════════ */
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

/* ════════════════════════════════════════════
   Campaign Builder (Index Page)
   ════════════════════════════════════════════ */
function initCampaignBuilder() {
    const addButtons = document.querySelectorAll('.add-scenario-btn');
    const selectedContainer = document.getElementById('selectedScenarios');
    const orderInput = document.getElementById('scenarioOrder');
    const startBtn = document.getElementById('startCampaign');
    const durationBadge = document.getElementById('campaignDuration');

    if (!selectedContainer || !orderInput) return;

    let selectedScenarios = [];

    addButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (selectedScenarios.indexOf(id) !== -1) return;

            selectedScenarios.push(id);
            updateCampaignDisplay();
        });
    });

    function updateCampaignDisplay() {
        // Clear placeholder
        const placeholder = selectedContainer.querySelector('.dropzone-text');
        if (placeholder) placeholder.remove();

        // Rebuild the list
        selectedContainer.innerHTML = '';

        selectedScenarios.forEach(function(id, index) {
            var scenario = window.scenarioData && window.scenarioData[id];
            if (!scenario) return;

            var item = document.createElement('div');
            item.className = 'scenario-card-mini campaign-item';
            item.dataset.id = id;
            item.innerHTML =
                '<div class="d-flex align-items-center w-100">' +
                    '<span class="me-2" style="cursor: grab; color: var(--gold);">☰</span>' +
                    '<span class="scenario-icon me-2">' + scenario.icon + '</span>' +
                    '<div class="flex-grow-1">' +
                        '<h6 class="mb-0">' + escapeHtml(scenario.title) + '</h6>' +
                        '<small class="text-muted">' + scenario.estimated_duration_minutes + ' min — DC ' + scenario.difficulty_class + '</small>' +
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger remove-scenario-btn ms-2" data-id="' + escapeHtml(id) + '">' +
                        '<i class="bi bi-x"></i>' +
                    '</button>' +
                '</div>';

            selectedContainer.appendChild(item);
        });

        // Add remove handlers
        selectedContainer.querySelectorAll('.remove-scenario-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var removeId = this.dataset.id;
                selectedScenarios = selectedScenarios.filter(function(s) { return s !== removeId; });
                updateCampaignDisplay();
            });
        });

        // Update hidden input
        orderInput.value = selectedScenarios.join(',');

        // Update start button
        if (startBtn) {
            startBtn.disabled = selectedScenarios.length === 0;
        }

        // Update duration
        if (durationBadge && window.scenarioData) {
            var totalDuration = 0;
            selectedScenarios.forEach(function(id) {
                if (window.scenarioData[id]) {
                    totalDuration += window.scenarioData[id].estimated_duration_minutes;
                }
            });
            durationBadge.textContent = totalDuration + ' min';
        }

        // Show placeholder if empty
        if (selectedScenarios.length === 0) {
            selectedContainer.innerHTML = '<p class="dropzone-text text-muted text-center py-4"><i class="bi bi-arrow-left"></i> Add quests from the available list</p>';
        }
    }
}

/* ════════════════════════════════════════════
   Setup Page — Event Configuration
   ════════════════════════════════════════════ */
function initSetupPage() {
    var addBtn = document.getElementById('addParticipantBtn');
    var participantList = document.getElementById('participantList');
    var copyUrlBtn = document.getElementById('copyUrlBtn');

    if (!participantList) return;

    // Department options HTML
    var deptData = window.departmentData || {};
    var deptOptionsHtml = '';
    for (var key in deptData) {
        deptOptionsHtml += '<option value="' + escapeHtml(key) + '">' + escapeHtml(deptData[key]) + '</option>';
    }

    // Add participant button
    if (addBtn) {
        addBtn.addEventListener('click', function() {
            addParticipantRow('', '', deptOptionsHtml);
            updateDeptSummary();
        });
    }

    // Initialise remove handlers for existing rows
    attachRemoveHandlers();

    // Update department summary on load and on changes
    participantList.addEventListener('change', updateDeptSummary);
    participantList.addEventListener('input', updateDeptSummary);
    updateDeptSummary();

    // Copy URL button
    if (copyUrlBtn) {
        copyUrlBtn.addEventListener('click', function() {
            var urlInput = document.getElementById('playerUrl');
            if (urlInput) {
                navigator.clipboard.writeText(urlInput.value).then(function() {
                    copyUrlBtn.innerHTML = '<i class="bi bi-check"></i>';
                    setTimeout(function() {
                        copyUrlBtn.innerHTML = '<i class="bi bi-clipboard"></i>';
                    }, 2000);
                });
            }
        });
    }

    // Restore preselected scenarios on setup page
    if (window.preselectedScenarios && window.preselectedScenarios.length > 0) {
        var selectedContainer = document.getElementById('selectedScenarios');
        var orderInput = document.getElementById('scenarioOrder');
        if (selectedContainer && orderInput && window.scenarioData) {
            // Remove placeholder
            var placeholder = selectedContainer.querySelector('.dropzone-text');
            if (placeholder) placeholder.remove();

            selectedContainer.innerHTML = '';

            window.preselectedScenarios.forEach(function(id) {
                var scenario = window.scenarioData[id];
                if (!scenario) return;

                var item = document.createElement('div');
                item.className = 'scenario-card-mini campaign-item';
                item.dataset.id = id;
                item.innerHTML =
                    '<div class="d-flex align-items-center w-100">' +
                        '<span class="me-2" style="cursor: grab; color: var(--gold);">☰</span>' +
                        '<span class="scenario-icon me-2">' + scenario.icon + '</span>' +
                        '<div class="flex-grow-1">' +
                            '<h6 class="mb-0">' + escapeHtml(scenario.title) + '</h6>' +
                            '<small class="text-muted">' + scenario.estimated_duration_minutes + ' min — DC ' + scenario.difficulty_class + '</small>' +
                        '</div>' +
                        '<button type="button" class="btn btn-sm btn-outline-danger remove-scenario-btn ms-2" data-id="' + escapeHtml(id) + '">' +
                            '<i class="bi bi-x"></i>' +
                        '</button>' +
                    '</div>';

                selectedContainer.appendChild(item);
            });

            // Set up scenario removal and recount
            rebindSetupScenarioHandlers();
            updateSetupDuration();
        }
    }

    function addParticipantRow(name, dept, deptOptions) {
        var row = document.createElement('div');
        row.className = 'participant-row mb-2';
        row.innerHTML =
            '<div class="row g-2 align-items-center">' +
                '<div class="col-md-5">' +
                    '<input type="text" class="form-control dnd-input" name="participant_name[]" ' +
                        'value="' + escapeHtml(name) + '" placeholder="Participant name" maxlength="100">' +
                '</div>' +
                '<div class="col-md-5">' +
                    '<select class="form-select dnd-input" name="participant_dept[]">' +
                        deptOptions +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<button type="button" class="btn btn-outline-danger btn-sm w-100 remove-participant-btn">' +
                        '<i class="bi bi-trash"></i>' +
                    '</button>' +
                '</div>' +
            '</div>';

        // Set selected department
        if (dept) {
            var select = row.querySelector('select');
            if (select) select.value = dept;
        }

        participantList.appendChild(row);
        attachRemoveHandlers();
    }

    function attachRemoveHandlers() {
        participantList.querySelectorAll('.remove-participant-btn').forEach(function(btn) {
            btn.onclick = function() {
                this.closest('.participant-row').remove();
                updateDeptSummary();
            };
        });
    }

    function updateDeptSummary() {
        var summaryDiv = document.getElementById('deptSummary');
        var cardsDiv = document.getElementById('deptSummaryCards');
        if (!summaryDiv || !cardsDiv) return;

        var rows = participantList.querySelectorAll('.participant-row');
        var deptMap = {};

        rows.forEach(function(row) {
            var nameInput = row.querySelector('input[name="participant_name[]"]');
            var deptSelect = row.querySelector('select[name="participant_dept[]"]');
            if (!nameInput || !deptSelect) return;

            var name = nameInput.value.trim();
            var dept = deptSelect.value;
            if (name === '') return;

            if (!deptMap[dept]) deptMap[dept] = [];
            deptMap[dept].push(name);
        });

        var hasParticipants = Object.keys(deptMap).length > 0;
        summaryDiv.style.display = hasParticipants ? 'block' : 'none';

        if (!hasParticipants) return;

        var html = '';
        for (var deptKey in deptMap) {
            var label = deptData[deptKey] || deptKey;
            html += '<div class="col-md-4"><div class="dept-card">';
            html += '<h6 class="dept-card-title">' + escapeHtml(label) + ' <span class="badge bg-secondary">' + deptMap[deptKey].length + '</span></h6>';
            html += '<ul class="dept-member-list">';
            deptMap[deptKey].forEach(function(name) {
                html += '<li>' + escapeHtml(name) + '</li>';
            });
            html += '</ul></div></div>';
        }
        cardsDiv.innerHTML = html;
    }

    function rebindSetupScenarioHandlers() {
        var selectedContainer = document.getElementById('selectedScenarios');
        if (!selectedContainer) return;

        selectedContainer.querySelectorAll('.remove-scenario-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var removeId = this.dataset.id;
                var items = selectedContainer.querySelectorAll('.campaign-item');
                items.forEach(function(item) {
                    if (item.dataset.id === removeId) item.remove();
                });
                updateSetupOrderInput();
                updateSetupDuration();

                if (selectedContainer.querySelectorAll('.campaign-item').length === 0) {
                    selectedContainer.innerHTML = '<p class="dropzone-text text-muted text-center py-4"><i class="bi bi-arrow-left"></i> Add quests from the available list</p>';
                }
            });
        });
    }

    function updateSetupOrderInput() {
        var orderInput = document.getElementById('scenarioOrder');
        var selectedContainer = document.getElementById('selectedScenarios');
        if (!orderInput || !selectedContainer) return;

        var ids = [];
        selectedContainer.querySelectorAll('.campaign-item').forEach(function(item) {
            ids.push(item.dataset.id);
        });
        orderInput.value = ids.join(',');
    }

    function updateSetupDuration() {
        var durationBadge = document.getElementById('campaignDuration');
        var selectedContainer = document.getElementById('selectedScenarios');
        if (!durationBadge || !selectedContainer || !window.scenarioData) return;

        var total = 0;
        selectedContainer.querySelectorAll('.campaign-item').forEach(function(item) {
            var s = window.scenarioData[item.dataset.id];
            if (s) total += s.estimated_duration_minutes;
        });
        durationBadge.textContent = total + ' min';
    }
}

/* ════════════════════════════════════════════
   Dice Rolling (Session Page)
   ════════════════════════════════════════════ */
function initDiceRollers() {
    document.querySelectorAll('.roll-dice-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var dice = this.dataset.dice;
            var trigger = this.dataset.trigger;
            var outcomes = JSON.parse(this.dataset.outcomes || '{}');
            var resultDiv = document.getElementById('result-' + trigger);
            var button = this;

            // Disable button during roll
            button.disabled = true;
            button.innerHTML = '<i class="bi bi-hourglass-split"></i> Rolling...';

            // Perform local dice roll for instant feedback
            var sides = parseInt(dice.replace('d', ''));
            var roll = Math.floor(Math.random() * sides) + 1;

            // Find matching outcome
            var outcome = null;
            var outcomeKey = null;
            for (var key in outcomes) {
                if (outcomes[key].range && roll >= outcomes[key].range[0] && roll <= outcomes[key].range[1]) {
                    outcome = outcomes[key];
                    outcomeKey = key;
                    break;
                }
            }

            // Animate and display
            setTimeout(function() {
                if (resultDiv) {
                    resultDiv.style.display = 'block';
                    var outcomeClass = getOutcomeClass(outcomeKey);
                    var outcomeIcon = getOutcomeIcon(outcomeKey);

                    resultDiv.className = 'dice-result mt-3 ' + outcomeClass;
                    resultDiv.innerHTML =
                        '<div class="d-flex align-items-center mb-2">' +
                            '<span class="dice-roll-display me-3">' +
                                '<strong class="fs-3">' + outcomeIcon + ' ' + roll + '</strong>' +
                                '<small class="ms-1 text-muted">/' + sides + '</small>' +
                            '</span>' +
                            '<div>' +
                                '<h6 class="mb-0">' + escapeHtml(outcome ? outcome.title : 'Unknown') + '</h6>' +
                            '</div>' +
                        '</div>' +
                        '<p class="mb-1">' + escapeHtml(outcome ? outcome.description : '') + '</p>' +
                        (outcome && outcome.modifier ? '<p class="mb-0"><strong>Effect:</strong> ' + escapeHtml(outcome.modifier) + '</p>' : '');
                }

                button.disabled = false;
                button.innerHTML = '<i class="bi bi-dice-5"></i> Roll Again';
            }, 800);
        });
    });
}

/* ════════════════════════════════════════════
   Quick Dice Roller (Sidebar)
   ════════════════════════════════════════════ */
function initQuickDiceRoller() {
    var display = document.getElementById('quickDiceDisplay');
    var history = document.getElementById('rollHistory');

    if (!display) return;

    document.querySelectorAll('.quick-roll').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var sides = parseInt(this.dataset.sides);
            var roll = Math.floor(Math.random() * sides) + 1;
            var valueEl = display.querySelector('.dice-value');

            // Animate
            display.classList.add('dice-rolling');
            valueEl.textContent = '...';

            setTimeout(function() {
                display.classList.remove('dice-rolling');
                valueEl.textContent = roll;

                // Apply critical styling
                if (sides === 20) {
                    if (roll === 20) {
                        valueEl.style.color = 'var(--gold-bright)';
                        valueEl.style.textShadow = '0 0 20px rgba(232, 200, 74, 0.8)';
                    } else if (roll === 1) {
                        valueEl.style.color = '#ff6b6b';
                        valueEl.style.textShadow = '0 0 20px rgba(255, 107, 107, 0.8)';
                    } else {
                        valueEl.style.color = 'var(--gold)';
                        valueEl.style.textShadow = '0 0 10px rgba(201, 169, 78, 0.5)';
                    }
                } else {
                    valueEl.style.color = 'var(--gold)';
                    valueEl.style.textShadow = '0 0 10px rgba(201, 169, 78, 0.5)';
                }

                // Add to history
                if (history) {
                    var label = '';
                    if (sides === 20 && roll === 20) label = ' 👑';
                    if (sides === 20 && roll === 1) label = ' 💀';

                    var historyItem = document.createElement('div');
                    historyItem.className = 'roll-history-item';
                    historyItem.innerHTML =
                        '<span>D' + sides + '</span>' +
                        '<span class="roll-value">' + roll + label + '</span>';
                    history.insertBefore(historyItem, history.firstChild);

                    // Limit history
                    while (history.children.length > 20) {
                        history.removeChild(history.lastChild);
                    }
                }
            }, 600);
        });
    });
}

/* ════════════════════════════════════════════
   Random Event Generators (Sidebar)
   ════════════════════════════════════════════ */
function initRandomEventGenerators() {
    var resultDiv = document.getElementById('randomEventResult');
    var data = window.randomEventsData;

    if (!resultDiv || !data) return;

    // Environmental Event
    var envBtn = document.getElementById('envEventBtn');
    if (envBtn) {
        envBtn.addEventListener('click', function() {
            var events = data.random_events && data.random_events.environmental;
            if (!events) return;

            var roll = Math.floor(Math.random() * 6) + 1;
            var event = null;
            for (var i = 0; i < events.length; i++) {
                if (events[i].trigger_on.indexOf(roll) !== -1) {
                    event = events[i];
                    break;
                }
            }

            if (event) {
                resultDiv.style.display = 'block';
                resultDiv.innerHTML =
                    '<div class="event-card event-negative">' +
                        '<div class="d-flex align-items-center mb-2">' +
                            '<span class="me-2 fs-5">🎲</span>' +
                            '<strong>D6 = ' + roll + '</strong>' +
                        '</div>' +
                        '<h6>' + escapeHtml(event.name) + '</h6>' +
                        '<p class="mb-0">' + escapeHtml(event.description) + '</p>' +
                    '</div>';
            }
        });
    }

    // Plot Twist
    var plotBtn = document.getElementById('plotTwistBtn');
    if (plotBtn) {
        plotBtn.addEventListener('click', function() {
            var twists = data.random_events && data.random_events.plot_twists;
            if (!twists) return;

            var roll = Math.floor(Math.random() * 20) + 1;
            var twist = null;
            for (var i = 0; i < twists.length; i++) {
                if (twists[i].trigger_on.indexOf(roll) !== -1) {
                    twist = twists[i];
                    break;
                }
            }

            if (twist) {
                resultDiv.style.display = 'block';
                resultDiv.innerHTML =
                    '<div class="event-card event-neutral">' +
                        '<div class="d-flex align-items-center mb-2">' +
                            '<span class="me-2 fs-5">🎲</span>' +
                            '<strong>D20 = ' + roll + '</strong>' +
                        '</div>' +
                        '<h6>' + escapeHtml(twist.name) + '</h6>' +
                        '<p class="mb-0">' + escapeHtml(twist.description) + '</p>' +
                    '</div>';
            } else {
                resultDiv.style.display = 'block';
                resultDiv.innerHTML =
                    '<div class="event-card event-positive">' +
                        '<div class="d-flex align-items-center mb-2">' +
                            '<span class="me-2 fs-5">🎲</span>' +
                            '<strong>D20 = ' + roll + '</strong>' +
                        '</div>' +
                        '<h6>No Plot Twist</h6>' +
                        '<p class="mb-0">The fates are quiet... for now.</p>' +
                    '</div>';
            }
        });
    }

    // NPC Action
    var npcBtn = document.getElementById('npcActionBtn');
    if (npcBtn) {
        npcBtn.addEventListener('click', function() {
            var npcs = data.random_events && data.random_events.npc_actions;
            if (!npcs) return;

            var index = Math.floor(Math.random() * npcs.length);
            var npc = npcs[index];

            if (npc) {
                var cardClass = npc.positive ? 'event-positive' : 'event-negative';
                resultDiv.style.display = 'block';
                resultDiv.innerHTML =
                    '<div class="event-card ' + cardClass + '">' +
                        '<h6>' + (npc.positive ? '✅ ' : '❌ ') + escapeHtml(npc.name) + '</h6>' +
                        '<p class="mb-0">' + escapeHtml(npc.description) + '</p>' +
                    '</div>';
            }
        });
    }
}

/* ════════════════════════════════════════════
   Complication Cards
   ════════════════════════════════════════════ */
function initComplicationCards() {
    document.querySelectorAll('.draw-complication-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var complications = JSON.parse(this.dataset.complications || '[]');
            if (complications.length === 0) return;

            var index = Math.floor(Math.random() * complications.length);
            var complication = complications[index];

            var resultDiv = this.parentElement.querySelector('.complication-result');
            if (resultDiv) {
                resultDiv.style.display = 'block';
                resultDiv.innerHTML =
                    '<div class="complication-card">' +
                        '<h6>⚡ Complication!</h6>' +
                        '<p class="mb-0">' + escapeHtml(complication) + '</p>' +
                    '</div>';
            }
        });
    });
}

/* ════════════════════════════════════════════
   Utility Functions
   ════════════════════════════════════════════ */
function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

function getOutcomeClass(key) {
    var classes = {
        'critical_fail': 'outcome-critical-fail',
        'fail': 'outcome-fail',
        'partial': 'outcome-partial',
        'success': 'outcome-success',
        'critical_success': 'outcome-critical-success'
    };
    return classes[key] || 'outcome-partial';
}

function getOutcomeIcon(key) {
    var icons = {
        'critical_fail': '💀',
        'fail': '⚔️',
        'partial': '🛡️',
        'success': '⚡',
        'critical_success': '👑'
    };
    return icons[key] || '🎲';
}
