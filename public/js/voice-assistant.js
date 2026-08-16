// public/js/voice-assistant.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Voice assistant script loaded');

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

    let currentLanguage = 'en'; // default
    let isListening = false;
    let conversationActive = false;
    let isSpeaking = false;
    let lastSpokenText = '';
    let heardSpeech = false;
    let lastRecognitionError = null;
    let recognitionStartedAt = 0;

    // ===== Load Voices (fix for getVoices empty array) =====
    let voicesLoaded = false;
    let availableVoices = [];

    function loadVoices() {
        return new Promise((resolve) => {
            if (!window.speechSynthesis) {
                resolve([]);
                return;
            }

            if (voicesLoaded) {
                resolve(availableVoices);
                return;
            }
            const voices = window.speechSynthesis.getVoices();
            if (voices.length > 0) {
                availableVoices = voices;
                voicesLoaded = true;
                resolve(voices);
            } else {
                window.speechSynthesis.onvoiceschanged = function() {
                    availableVoices = window.speechSynthesis.getVoices();
                    voicesLoaded = true;
                    resolve(availableVoices);
                };
            }
        });
    }

    // ===== Language Selector =====
    if (langSelect) {
        langSelect.addEventListener('change', function() {
            currentLanguage = this.value;
            const msg = currentLanguage === 'en' 
                ? '🌐 Language changed to English' 
                : '🌐 भाषा नेपालीमा परिवर्तन भयो';
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

    console.log('✅ Voice elements found');

    function setListeningUi(active, message = null) {
        isListening = active;
        window.isListening = active;
        toggleBtn.innerHTML = active
            ? '<i class="fas fa-stop"></i> Stop'
            : '<i class="fas fa-microphone"></i> Start';
        statusEl.textContent = message || (active ? 'Listening... speak now' : 'Click to speak or type below');
        toggleBtn.classList.toggle('listening', active);
    }

    // ===== TOGGLE CHAT WINDOW =====
    launchBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const isHidden = chatWindow.style.display === 'none' || chatWindow.style.display === '';
        chatWindow.style.display = isHidden ? 'flex' : 'none';
        launchBtn.style.transform = isHidden ? 'scale(1.1)' : 'scale(1)';
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
            window.speechSynthesis.cancel(); // Stop any speech
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

    // ===== SPEAK FUNCTION (bilingual, with voice loading) =====
    async function speak(text, lang = 'en') {
        if (!window.speechSynthesis || !text) return;
        if (text === lastSpokenText && isSpeaking) return;

        window.speechSynthesis.cancel();
        isSpeaking = true;
        lastSpokenText = text;

        try {
            await loadVoices();
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = lang === 'np' ? 'ne-NP' : 'en-US';
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            utterance.volume = 1;

            if (lang === 'np') {
                const nepaliVoice = availableVoices.find(v => v.lang.startsWith('ne'));
                if (nepaliVoice) utterance.voice = nepaliVoice;
            }

            utterance.onend = function() {
                isSpeaking = false;
                lastSpokenText = '';
                if (isListening) {
                    recognition.stop();
                    setListeningUi(false);
                }
            };

            utterance.onerror = function() {
                isSpeaking = false;
                lastSpokenText = '';
            };

            window.speechSynthesis.speak(utterance);
        } catch (error) {
            console.error('Speech synthesis error:', error);
            isSpeaking = false;
            lastSpokenText = '';
        }
    }

    // ===== ADD MESSAGE =====
    function addMessage(text, sender) {
        if (!messagesEl || !text) return;
        const div = document.createElement('div');
        div.className = sender === 'user' ? 'user-msg' : 'assistant-msg';
        const isNepali = currentLanguage === 'np';
        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble';
        bubble.textContent = text;
        bubble.style.cssText = `background: ${sender === 'user' ? '#f59e0b' : '#e5e7eb'}; color: ${sender === 'user' ? 'white' : '#1e293b'}; padding: 10px 14px; border-radius: 12px; margin: 4px 0; max-width: 80%; align-self: ${sender === 'user' ? 'flex-end' : 'flex-start'}; font-size: ${isNepali ? '16px' : '14px'}; white-space: pre-line;`;
        div.appendChild(bubble);
        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    // ===== SEND TO BACKEND (with better error handling) =====
    function sendToBackend(text) {
        addMessage(text, 'user');
        const language = currentLanguage;

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
            speak(responseMessage, language);

            // ===== HANDLE ACTIONS =====
            if ((data.action === 'open_page' || data.action === 'redirect') && data.url) {
                const params = new URLSearchParams(data.data || {});
                const targetUrl = params.toString() ? `${data.url}?${params.toString()}` : data.url;
                window.location.href = targetUrl;
                return;
            }

            if (data.action === 'submit') {
                const msg = language === 'np' 
                    ? '✅ कार्य सफलतापूर्वक पूरा भयो!' 
                    : '✅ Action completed successfully!';
                addMessage(msg, 'assistant');
                speak(msg, language);
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
                : 'Sorry, I encountered an error. Please try again.';
            addMessage(msg, 'assistant');
        })
        .finally(() => {
            if (textInput) textInput.disabled = false;
            if (sendBtn) sendBtn.disabled = false;
        });
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
            addMessage('Microphone permission is blocked or unavailable. Please allow microphone access in your browser, or type your request below.', 'assistant');
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
            window.speechSynthesis.cancel();
            sendToBackend(finalTranscript.trim());
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
            addMessage(messages[event.error], 'assistant');
        }

        if (isListening) {
            recognition.stop();
            setListeningUi(false);
            window.speechSynthesis.cancel();
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

            if (isSpeaking) {
                window.speechSynthesis.cancel();
                isSpeaking = false;
                lastSpokenText = '';
            }

            if (!isListening) {
                recognition.lang = currentLanguage === 'np' ? 'ne-NP' : 'en-US';
                window.speechSynthesis.cancel();
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
                window.speechSynthesis.cancel();
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
            sendToBackend(text);
        };

        sendBtn.addEventListener('click', sendTypedMessage);
        textInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                sendTypedMessage();
            }
        });
    }

    // Start with chat window hidden
    chatWindow.style.display = 'none';

    // Preload voices
    loadVoices();
});
