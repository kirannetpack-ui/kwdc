// public/js/voice-assistant.js
document.addEventListener('DOMContentLoaded', function() {
    // ===== DOM Elements =====
    const launchBtn = document.getElementById('voiceLaunchBtn');
    const chatWindow = document.getElementById('voiceChatWindow');
    const closeBtn = document.getElementById('voiceCloseBtn');
    const toggleBtn = document.getElementById('voiceToggleBtn');
    const statusEl = document.getElementById('voiceStatus');
    const messagesEl = document.getElementById('voiceMessages');
    const langSelect = document.getElementById('voiceLangSelect');
    const textInput = document.getElementById('voiceTextInput');
    const sendBtn = document.getElementById('voiceSendBtn');
    const clearBtn = document.getElementById('assistantClearBtn');

    let currentLanguage = 'en'; // default
    let isListening = false;
    let conversationActive = false;
    let heardSpeech = false;
    let lastRecognitionError = null;
    let recognitionStartedAt = 0;
    let lastAssistantNotice = '';
    const historyKey = 'kwdcAssistantHistory';
    const openKey = 'kwdcAssistantOpen';
    const maxHistoryItems = 60;

    function readHistory() {
        try {
            return JSON.parse(localStorage.getItem(historyKey) || '[]').filter(item => item?.text && item?.sender);
        } catch (error) {
            console.warn('Assistant history could not be read.', error);
            return [];
        }
    }

    function writeHistory(items) {
        try {
            localStorage.setItem(historyKey, JSON.stringify(items.slice(-maxHistoryItems)));
        } catch (error) {
            console.warn('Assistant history could not be saved.', error);
        }
    }

    function clearHistory() {
        try {
            localStorage.removeItem(historyKey);
        } catch (error) {
            console.warn('Assistant history could not be cleared.', error);
        }
    }

    function rememberMessage(text, sender) {
        const items = readHistory();
        items.push({ text, sender, at: new Date().toISOString() });
        writeHistory(items);
    }

    function setAssistantOpen(open) {
        try {
            sessionStorage.setItem(openKey, open ? '1' : '0');
        } catch (error) {
            console.warn('Assistant open state could not be saved.', error);
        }
    }

    function shouldRestoreOpen() {
        try {
            return sessionStorage.getItem(openKey) === '1';
        } catch (error) {
            return false;
        }
    }

    // ===== Language Selector =====
    if (langSelect) {
        langSelect.addEventListener('change', function() {
            currentLanguage = this.value;
            const msg = currentLanguage === 'en' 
                ? 'Language changed to English' 
                : 'भाषा नेपालीमा परिवर्तन भयो';
            addMessage(msg, 'assistant');
        });
    }

    // ===== Safety Checks =====
    if (!launchBtn) {
        console.error('❌ Voice launch button not found!');
        return;
    }
    if (!chatWindow) {
        console.error('❌ Voice chat window not found!');
        return;
    }

    const initialMessagesHtml = messagesEl.innerHTML;

    function setListeningUi(active, message = null) {
        isListening = active;
        window.isListening = active;
        toggleBtn.innerHTML = active
            ? '<i class="fas fa-stop"></i> Stop'
            : '<i class="fas fa-microphone"></i> Start';
        statusEl.textContent = message || (active ? 'Listening... speak now' : 'Speak or type a request');
        toggleBtn.classList.toggle('listening', active);
    }

    // ===== TOGGLE CHAT WINDOW =====
    launchBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const isHidden = chatWindow.style.display === 'none' || chatWindow.style.display === '';
        chatWindow.style.display = isHidden ? 'flex' : 'none';
        launchBtn.style.transform = isHidden ? 'scale(1.1)' : 'scale(1)';
        setAssistantOpen(isHidden);
    });

    // ===== CLOSE BUTTON =====
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            chatWindow.style.display = 'none';
            launchBtn.style.transform = 'scale(1)';
            if (isListening) {
                recognition?.stop();
                setListeningUi(false);
            }
            stopSpeaking();
            setAssistantOpen(false);
        });
    }

    // ===== SPEECH RECOGNITION =====
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition = null;

    if (!SpeechRecognition) {
        statusEl.textContent = 'Mic not supported here. Type below.';
        toggleBtn.disabled = true;
        toggleBtn.style.opacity = '0.65';
        console.warn('SpeechRecognition not supported; typed assistant fallback enabled.');
    } else {
        recognition = new SpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'en-US';
        recognition.maxAlternatives = 1;

        window.recognition = recognition;
    }
    window.isListening = false;

    function stopSpeaking() {
        if (window.speechSynthesis) {
            window.speechSynthesis.cancel();
        }
    }

    // ===== ADD MESSAGE =====
    function renderMessage(text, sender) {
        if (!messagesEl || !text) return;
        const div = document.createElement('div');
        div.className = sender === 'user'
            ? 'kwdc-assistant-msg user-msg'
            : 'kwdc-assistant-msg assistant-msg';
        const isNepali = currentLanguage === 'np';
        const bubble = document.createElement('div');
        bubble.className = 'kwdc-assistant-bubble';
        bubble.textContent = text;
        if (isNepali) {
            bubble.style.fontSize = '15px';
        }
        div.appendChild(bubble);
        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function addMessage(text, sender) {
        renderMessage(text, sender);
        rememberMessage(text, sender);
    }

    function restoreHistory() {
        const items = readHistory();
        if (items.length === 0) {
            return;
        }

        messagesEl.innerHTML = '';
        items.forEach(item => renderMessage(item.text, item.sender));
    }

    function resetAssistantMessages() {
        clearHistory();
        messagesEl.innerHTML = initialMessagesHtml;
        lastAssistantNotice = '';
        statusEl.textContent = 'Ready';
    }

    function addAssistantNotice(text) {
        if (!text || text === lastAssistantNotice) {
            return;
        }

        lastAssistantNotice = text;
        addMessage(text, 'assistant');
    }

    function makeCssSafe(value) {
        if (window.CSS?.escape) {
            return CSS.escape(value);
        }

        return String(value).replace(/"/g, '\\"');
    }

    function prettifyFieldName(value) {
        return String(value || '')
            .replace(/\[[^\]]*\]/g, ' ')
            .replace(/[_-]+/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .replace(/\b\w/g, letter => letter.toUpperCase());
    }

    function displayFieldName(field, fallback = 'that field') {
        const name = String(field.name || field.id || '').toLowerCase();

        if (name.includes('delivery_stops') && name.includes('address')) return 'Delivery Address';
        if (name.includes('delivery_stops') && name.includes('recipient_name')) return 'Recipient Name';
        if (name.includes('delivery_stops') && name.includes('recipient_phone')) return 'Recipient Phone';
        if (name.includes('pickup_contact_person')) return 'Pickup Contact Person';
        if (name.includes('pickup_contact_phone')) return 'Pickup Contact Phone';
        if (name.includes('pickup_address')) return 'Pickup Address';
        if (name.includes('total_distance')) return 'Total Distance';
        if (name.includes('total_price')) return 'Total Price';

        const readable = field.placeholder || fieldTextParts(field).find(part => !part.includes('[')) || fallback;
        return prettifyFieldName(readable);
    }

    function normalizeFieldText(value) {
        return String(value || '')
            .toLowerCase()
            .replace(/[_-]+/g, ' ')
            .replace(/[^a-z0-9\s]/g, ' ')
            .replace(/\b(the|a|an|field|input|box|please|current|this|page|request|form|value)\b/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function fieldTextParts(field) {
        const id = field.id || '';
        const parts = [
            field.name,
            id,
            field.placeholder,
            field.getAttribute('aria-label'),
            field.dataset.label,
        ];

        if (id) {
            const label = document.querySelector(`label[for="${makeCssSafe(id)}"]`);
            if (label) parts.push(label.textContent);
        }

        const wrappedLabel = field.closest('label');
        if (wrappedLabel) parts.push(wrappedLabel.textContent);

        const nearbyLabel = field.closest('.form-group, .mb-3, .mb-4, .col, .col-md-6, .row')?.querySelector('label');
        if (nearbyLabel) parts.push(nearbyLabel.textContent);

        return parts.filter(Boolean).map(part => String(part).trim()).filter(Boolean);
    }

    function visibleAssistantFields() {
        return Array.from(document.querySelectorAll('input, textarea, select'))
            .filter(field => {
                const type = (field.getAttribute('type') || '').toLowerCase();
                if (field.disabled || field.readOnly) return false;
                if (['hidden', 'password', 'file', 'submit', 'button', 'reset'].includes(type)) return false;
                if (field.name && ['_token', '_method'].includes(field.name)) return false;

                const style = window.getComputedStyle(field);
                const rect = field.getBoundingClientRect();
                return style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.height > 0;
            });
    }

    function fieldAliasesForQuery(query) {
        const normalized = normalizeFieldText(query);
        const aliases = [];

        if (/\b(contact person|contact name|pickup contact|person name)\b/.test(normalized)) {
            aliases.push('pickup contact person', 'contact person', 'contact name', 'pickup_contact_person');
        }
        if (/\b(phone|mobile|number|contact phone|contact number)\b/.test(normalized)) {
            aliases.push('pickup contact phone', 'contact phone', 'recipient phone', 'phone', 'mobile');
        }
        if (/\b(email|mail)\b/.test(normalized)) {
            aliases.push('email', 'email address');
        }
        if (/\b(pickup|from|origin|collection)\b/.test(normalized) && /\b(address|location|place)\b/.test(normalized)) {
            aliases.push('pickup address', 'from address', 'origin', 'pickup_address');
        }
        if (/\b(delivery|drop|destination|to|recipient)\b/.test(normalized) && /\b(address|location|place)\b/.test(normalized)) {
            aliases.push('delivery address', 'destination address', 'drop address', 'delivery_address');
        }
        if (/\b(recipient|receiver|customer)\b/.test(normalized) && /\b(name|person)\b/.test(normalized)) {
            aliases.push('recipient name', 'receiver name');
        }
        if (/\b(item|items|cargo|goods|description|details)\b/.test(normalized)) {
            aliases.push('items description', 'cargo description', 'description');
        }
        if (/\b(price|amount|cost|total|rate)\b/.test(normalized)) {
            aliases.push('total price', 'amount', 'price');
        }
        if (/\b(distance|km|kilometer)\b/.test(normalized)) {
            aliases.push('total distance', 'distance', 'km');
        }
        if (/\b(date|day)\b/.test(normalized)) {
            aliases.push('scheduled date', 'date');
        }
        if (/\b(time|hour)\b/.test(normalized)) {
            aliases.push('scheduled time', 'time');
        }
        if (/\b(vehicle|truck|van|bike)\b/.test(normalized)) {
            aliases.push('vehicle type', 'vehicle');
        }

        aliases.push(normalized);
        return aliases.filter(Boolean);
    }

    function scoreField(field, requestedLabel) {
        const needles = fieldAliasesForQuery(requestedLabel).map(normalizeFieldText).filter(Boolean);
        const haystackParts = fieldTextParts(field).map(normalizeFieldText).filter(Boolean);
        const haystack = haystackParts.join(' ');

        return needles.reduce((best, needle) => {
            if (!needle) return best;
            if (haystackParts.includes(needle)) return Math.max(best, 100);
            if (haystack.includes(needle)) return Math.max(best, 80);

            const words = needle.split(' ').filter(word => word.length > 1);
            const matches = words.filter(word => haystack.includes(word)).length;
            if (matches === words.length && words.length > 0) return Math.max(best, 70);
            if (matches > 0) return Math.max(best, matches * 18);
            return best;
        }, 0);
    }

    function findVisibleField(requestedLabel) {
        return visibleAssistantFields()
            .map(field => ({ field, score: scoreField(field, requestedLabel) }))
            .filter(item => item.score >= 35)
            .sort((a, b) => b.score - a.score)[0]?.field || null;
    }

    function currentFieldValue(field) {
        if (field.tagName === 'SELECT') {
            return field.selectedOptions?.[0]?.textContent || field.value || '';
        }

        return field.value || '';
    }

    function findVisibleFieldByValue(value) {
        const needle = normalizeFieldText(value);
        if (!needle) return null;

        return visibleAssistantFields()
            .map(field => {
                const rawValue = currentFieldValue(field);
                const normalizedValue = normalizeFieldText(rawValue);
                let score = 0;

                if (normalizedValue === needle) {
                    score = 100;
                } else if (normalizedValue.includes(needle)) {
                    score = 75;
                }

                return { field, score };
            })
            .filter(item => item.score > 0)
            .sort((a, b) => b.score - a.score)[0]?.field || null;
    }

    function fillAssistantField(field, value) {
        const type = (field.getAttribute('type') || '').toLowerCase();
        const cleanValue = String(value).trim();

        if (field.tagName === 'SELECT') {
            const normalizedValue = normalizeFieldText(cleanValue);
            const match = Array.from(field.options).find(option => {
                return normalizeFieldText(option.textContent) === normalizedValue
                    || normalizeFieldText(option.value) === normalizedValue
                    || normalizeFieldText(option.textContent).includes(normalizedValue);
            });
            if (match) field.value = match.value;
        } else if (type === 'checkbox' || type === 'radio') {
            field.checked = /^(yes|true|on|checked|enable|enabled|1)$/i.test(cleanValue);
        } else {
            field.value = cleanValue;
        }

        field.dispatchEvent(new Event('input', { bubbles: true }));
        field.dispatchEvent(new Event('change', { bubbles: true }));
        field.focus({ preventScroll: true });
        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function replaceAssistantFieldValue(field, oldValue, newValue) {
        const currentValue = currentFieldValue(field);
        const escapedOldValue = oldValue.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const nextValue = currentValue.match(new RegExp(escapedOldValue, 'i'))
            ? currentValue.replace(new RegExp(escapedOldValue, 'ig'), newValue)
            : newValue;

        fillAssistantField(field, nextValue);
    }

    function parseCurrentPageReplacement(text) {
        const clean = text.trim().replace(/\s+/g, ' ');
        const patterns = [
            /^(?:please\s+)?instead\s+of\s+(.+?)\s+(?:put|use|make\s+it|set\s+it\s+to)\s+(.+?)$/i,
            /^(?:please\s+)?replace\s+(.+?)\s+with\s+(.+?)$/i,
            /^(?:please\s+)?(?:change|switch)\s+(.+?)\s+(?:to|into)\s+(.+?)$/i,
        ];

        for (const pattern of patterns) {
            const match = clean.match(pattern);
            if (match) {
                return {
                    oldValue: match[1].trim(),
                    newValue: match[2].trim(),
                };
            }
        }

        return null;
    }

    function parseCurrentPageFill(text) {
        const clean = text.trim().replace(/\s+/g, ' ');
        const patterns = [
            /^(?:please\s+)?(?:put|set|change|fill|add|make)\s+(?:the\s+)?(.+?)\s+(?:as|to|=)\s+(.+?)(?=\s+(?:on|for|in|at)\s+.+$|$)/i,
            /^(?:please\s+)?(?:enter|type)\s+(.+?)\s+(?:in|into|for)\s+(?:the\s+)?(.+?)$/i,
        ];

        for (const pattern of patterns) {
            const match = clean.match(pattern);
            if (!match) continue;

            if (/^(?:please\s+)?(?:enter|type)\b/i.test(clean)) {
                return { label: match[2], value: match[1] };
            }

            return { label: match[1], value: match[2] };
        }

        return null;
    }

    function tryCurrentPageFill(text) {
        const replacement = parseCurrentPageReplacement(text);
        if (replacement) {
            const replacementField = findVisibleFieldByValue(replacement.oldValue);
            if (replacementField) {
                addMessage(text, 'user');
                lastAssistantNotice = '';
                replaceAssistantFieldValue(replacementField, replacement.oldValue, replacement.newValue);
                addAssistantNotice(`Done. I replaced ${replacement.oldValue} with ${replacement.newValue} in ${displayFieldName(replacementField)}. Review it before saving.`);
                statusEl.textContent = 'Updated current page';
                return true;
            }
        }

        const command = parseCurrentPageFill(text);
        if (!command) {
            return false;
        }

        const field = findVisibleField(command.label);
        if (!field) {
            return false;
        }

        addMessage(text, 'user');
        lastAssistantNotice = '';
        fillAssistantField(field, command.value);
        addAssistantNotice(`Done. I set ${displayFieldName(field, command.label)} to ${command.value}. Review it before saving.`);
        statusEl.textContent = 'Filled current page';
        return true;
    }

    function applyDataToCurrentPage(data) {
        if (!data || typeof data !== 'object') {
            return false;
        }

        const fieldAliases = {
            pickup_contact_person: ['pickup_contact_person', 'contact_person', 'contact_name'],
            pickup_contact_phone: ['pickup_contact_phone', 'contact_phone', 'phone'],
            recipient_name: ['recipient_name', 'delivery_stops[0][recipient_name]'],
            recipient_phone: ['recipient_phone', 'delivery_stops[0][recipient_phone]'],
        };

        let filled = false;
        Object.entries(data).forEach(([key, value]) => {
            if (value === null || value === undefined || value === '') {
                return;
            }

            const names = fieldAliases[key] || [key];
            const field = names
                .map((name) => document.querySelector(`[name="${makeCssSafe(name)}"], #${makeCssSafe(name)}`))
                .find(Boolean);

            if (!field) {
                return;
            }

            fillAssistantField(field, value);
            filled = true;
        });

        return filled;
    }

    function handleAssistantAction(data) {
        if (!((data.action === 'open_page' || data.action === 'redirect') && data.url)) {
            return false;
        }

        const params = new URLSearchParams(data.data || {});
        const targetUrl = params.toString() ? `${data.url}?${params.toString()}` : data.url;
        const target = new URL(targetUrl, window.location.origin);

        setAssistantOpen(true);

        if (target.pathname === window.location.pathname) {
            if (params.toString()) {
                window.history.pushState({}, '', target.pathname + target.search);
            }

            const filled = applyDataToCurrentPage(data.data || {});
            statusEl.textContent = filled ? 'Filled this page' : 'Ready';
            if (filled) {
                addAssistantNotice('I filled what I could on this page. Please review before saving.');
            }
            return true;
        }

        addAssistantNotice('Opening the right page and keeping this chat here.');
        window.location.href = targetUrl;
        return true;
    }

    // ===== SEND TO BACKEND (with better error handling) =====
    function sendToBackend(text) {
        addMessage(text, 'user');
        lastAssistantNotice = '';
        const language = currentLanguage;
        statusEl.textContent = 'Working on it...';

        fetch('/ai/voice-assistant', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ message: text, language: language })
        })
        .then(async res => {
            if (!res.ok) {
                const body = await res.text();
                throw new Error(`Request failed with ${res.status}: ${body.substring(0, 150)}`);
            }

            // Check if response is JSON
            const contentType = res.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // If not JSON, read as text and throw
                const text = await res.text();
                throw new Error(`Expected JSON, got ${contentType}: ${text.substring(0, 100)}`);
            }
            return res.json();
        })
        .then(data => {
            const responseMessage = data.message || 'I didn\'t understand that.';
            addMessage(responseMessage, 'assistant');
            statusEl.textContent = 'Ready';

            // ===== HANDLE ACTIONS =====
            if (handleAssistantAction(data)) {
                return;
            }

            if (data.action === 'submit') {
                const msg = language === 'np' 
                    ? 'कार्य सफलतापूर्वक पूरा भयो!' 
                    : 'Action completed successfully.';
                addMessage(msg, 'assistant');
            }

            if (data.done) {
                toggleBtn.textContent = 'New Session';
                toggleBtn.disabled = false;
            }
        })
        .catch(err => {
            console.error('❌ Backend error:', err);
            const msg = currentLanguage === 'np' 
                ? 'क्षमा गर्नुहोस्, मैले एउटा त्रुटि भेटाएँ। कृपया फेरि प्रयास गर्नुहोस्।' 
                : 'Sorry, I could not complete that. You can try again or use the form directly.';
            addAssistantNotice(msg);
            statusEl.textContent = 'Could not complete request';
        })
        .finally(() => {
            if (textInput) textInput.disabled = false;
            if (sendBtn) sendBtn.disabled = false;
        });
    }

    function handleAssistantInput(text) {
        if (tryCurrentPageFill(text)) {
            if (textInput) textInput.disabled = false;
            if (sendBtn) sendBtn.disabled = false;
            return;
        }

        sendToBackend(text);
    }

    // ===== RECOGNITION EVENTS =====
    async function ensureMicrophoneAccess() {
        if (!navigator.mediaDevices?.getUserMedia) {
            return true;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            stream.getTracks().forEach(track => track.stop());
            return true;
        } catch (error) {
            console.warn('Microphone access failed:', error);
            statusEl.textContent = 'Mic permission blocked. Type below.';
            addAssistantNotice('Microphone permission is blocked or unavailable. Please allow microphone access in your browser, or type your request below.');
            return false;
        }
    }

    if (recognition) recognition.onresult = function(event) {
        let finalTranscript = '';
        for (let i = event.resultIndex; i < event.results.length; i++) {
            const transcript = event.results[i][0].transcript;
            if (transcript.trim() !== '') {
                heardSpeech = true;
            }
            if (event.results[i].isFinal) {
                finalTranscript += transcript;
            }
        }
        if (finalTranscript.trim() !== '') {
            recognition.stop();
            setListeningUi(false, 'Processing...');
            stopSpeaking();
            handleAssistantInput(finalTranscript.trim());
        }
    };

    if (recognition) recognition.onstart = function() {
        heardSpeech = false;
        lastRecognitionError = null;
        recognitionStartedAt = Date.now();
        setListeningUi(true, 'Listening... speak now');
    };

    if (recognition) recognition.onspeechstart = function() {
        heardSpeech = true;
        statusEl.textContent = 'Hearing you...';
    };

    if (recognition) recognition.onspeechend = function() {
        statusEl.textContent = 'Processing...';
    };

    if (recognition) recognition.onend = function() {
        if (!isListening) {
            return;
        }

        const listenedForMs = Date.now() - recognitionStartedAt;
        setListeningUi(false);

        if (lastRecognitionError === 'not-allowed' || lastRecognitionError === 'service-not-allowed') {
            statusEl.textContent = 'Mic permission blocked. Type below.';
            return;
        }

        if (!heardSpeech || listenedForMs < 1200) {
            statusEl.textContent = 'I did not hear anything. Try again or type below.';
        }
    };

    if (recognition) recognition.onerror = function(event) {
        console.error('❌ Recognition error:', event.error);
        lastRecognitionError = event.error;

        const messages = {
            'not-allowed': 'Microphone permission is blocked. Please allow mic access in your browser, or type below.',
            'service-not-allowed': 'Speech recognition is blocked by this browser. Type below or try Chrome.',
            'no-speech': 'I did not hear anything. Click Start and speak after the listening message appears.',
            'audio-capture': 'No microphone was found. Check your mic, or type below.',
            'network': 'Browser speech service could not connect. Type below or try again.',
        };

        if (messages[event.error]) {
            statusEl.textContent = event.error === 'no-speech' ? 'I did not hear anything. Try again.' : 'Mic unavailable. Type below.';
            addAssistantNotice(messages[event.error]);
        }

        if (isListening) {
            recognition.stop();
            setListeningUi(false);
            stopSpeaking();
        }
    };

    // ===== TOGGLE LISTENING =====
    if (toggleBtn) {
        toggleBtn.addEventListener('click', async function() {
            if (!recognition) {
                statusEl.textContent = 'Mic not supported here. Type below.';
                textInput?.focus();
                return;
            }

            if (!isListening) {
                recognition.lang = currentLanguage === 'np' ? 'ne-NP' : 'en-US';
                stopSpeaking();
                toggleBtn.disabled = true;
                statusEl.textContent = 'Checking microphone...';

                const hasMicAccess = await ensureMicrophoneAccess();
                toggleBtn.disabled = false;
                if (!hasMicAccess) {
                    textInput?.focus();
                    return;
                }

                try {
                    recognition.start();
                } catch (error) {
                    console.error('Recognition start failed:', error);
                    statusEl.textContent = 'Could not start mic. Type below.';
                }
            } else {
                recognition.stop();
                stopSpeaking();
                setListeningUi(false);
            }
        });
    }

    if (sendBtn && textInput) {
        const sendTypedMessage = function() {
            const text = textInput.value.trim();
            if (!text) {
                textInput.focus();
                return;
            }

            textInput.value = '';
            textInput.disabled = true;
            sendBtn.disabled = true;
            statusEl.textContent = 'Processing...';
            handleAssistantInput(text);
        };

        sendBtn.addEventListener('click', sendTypedMessage);
        textInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                sendTypedMessage();
            }
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            resetAssistantMessages();
            textInput?.focus();
        });
    }

    if (messagesEl) {
        messagesEl.addEventListener('click', function(event) {
            const button = event.target.closest('[data-prompt]');
            if (!button) return;

            const prompt = button.dataset.prompt;
            if (!prompt) return;

            if (textInput) {
                textInput.value = prompt;
                textInput.focus();
            }

            handleAssistantInput(prompt);
        });
    }

    restoreHistory();
    if (shouldRestoreOpen()) {
        chatWindow.style.display = 'flex';
        launchBtn.style.transform = 'scale(1.1)';
    } else {
        chatWindow.style.display = 'none';
    }
});
