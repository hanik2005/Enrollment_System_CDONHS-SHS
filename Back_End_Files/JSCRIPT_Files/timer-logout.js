const idleTimeLimit = 60 * 1000; // 1 minute before showing modal
const logoutTimeLimit = 60 * 1000; // 1 minute countdown before logout
const storageKey = "portal_timer_logout_state_v1";
const actionKey = "portal_timer_logout_action_v1";
const channelName = "portal_timer_logout_channel_v1";
const actionPropagationWindowMs = 10 * 1000; // keep cross-tab actions fresh only for a short window

let modal = null;
let countdown = null;
let monitorInterval = null;
let lastActivityPublish = 0;
let lastActionTimestamp = 0;
let bc = null;

function nowMs() {
    return Date.now();
}

function createFreshState() {
    return {
        idleDeadline: nowMs() + idleTimeLimit,
        logoutDeadline: 0,
        forceLogoutAt: 0
    };
}

function readState() {
    try {
        const raw = localStorage.getItem(storageKey);
        if (!raw) return null;
        const parsed = JSON.parse(raw);
        if (!parsed || typeof parsed !== "object") return null;
        return {
            idleDeadline: Number(parsed.idleDeadline) || 0,
            logoutDeadline: Number(parsed.logoutDeadline) || 0,
            forceLogoutAt: Number(parsed.forceLogoutAt) || 0
        };
    } catch (_) {
        return null;
    }
}

function writeState(state) {
    localStorage.setItem(storageKey, JSON.stringify(state));
}

function emitAction(type) {
    const payload = {
        type: String(type || ""),
        at: nowMs()
    };
    localStorage.setItem(actionKey, JSON.stringify(payload));
    if (bc) {
        try {
            bc.postMessage(payload);
        } catch (_) {
            // Ignore channel failures
        }
    }
}

function handleRemoteAction(action) {
    if (!action || typeof action !== "object") {
        return;
    }

    const actionType = String(action.type || "");
    const at = Number(action.at) || 0;
    if (!at || at <= lastActionTimestamp) {
        return;
    }
    lastActionTimestamp = at;

    if (actionType === "stay") {
        closeModal();
        syncFromState();
        return;
    }

    if (actionType === "logout") {
        autoLogout();
    }
}

function initializeState() {
    const state = readState();
    if (state) {
        const now = nowMs();
        const isRecentForcedLogout =
            state.forceLogoutAt > 0 && now - state.forceLogoutAt <= actionPropagationWindowMs;
        const isRecentLogoutDeadline =
            state.logoutDeadline > 0 && now - state.logoutDeadline <= actionPropagationWindowMs;

        if (isRecentForcedLogout || isRecentLogoutDeadline) {
            return state;
        }

        if (state.forceLogoutAt > 0 || state.logoutDeadline > 0 || state.idleDeadline <= 0) {
            const refreshed = createFreshState();
            writeState(refreshed);
            return refreshed;
        }

        return state;
    }
    const initial = createFreshState();
    writeState(initial);
    return initial;
}

function closeModal() {
    if (modal) {
        modal.remove();
        modal = null;
        countdown = null;
    }
}

function autoLogout() {
    window.location.href = "../../Back_End_Files/PHP_Files/logout.php";
}

function publishActivity(force = false) {
    const now = nowMs();
    if (!force && now - lastActivityPublish < 600) {
        return;
    }
    const currentState = readState();
    if (!force && currentState && (currentState.forceLogoutAt > 0 || currentState.logoutDeadline > 0)) {
        // When warning modal is active, only explicit button actions should change state.
        return;
    }
    lastActivityPublish = now;
    closeModal();
    writeState(createFreshState());
}

function publishWarningIfNeeded(state) {
    if (state.logoutDeadline > 0) {
        return state;
    }
    const updated = {
        idleDeadline: state.idleDeadline,
        logoutDeadline: nowMs() + logoutTimeLimit,
        forceLogoutAt: 0
    };
    writeState(updated);
    return updated;
}

function publishLogoutAndExit() {
    const ts = nowMs();
    writeState({
        idleDeadline: 0,
        logoutDeadline: ts,
        forceLogoutAt: ts
    });
    autoLogout();
}

function createModal() {
    if (modal) return;

    modal = document.createElement("div");
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100%";
    modal.style.height = "100%";
    modal.style.backgroundColor = "rgba(0, 0, 0, 0.6)";
    modal.style.display = "flex";
    modal.style.justifyContent = "center";
    modal.style.alignItems = "center";
    modal.style.zIndex = "1000";
    modal.style.animation = "fadeIn 0.3s ease";

    const content = document.createElement("div");
    content.style.backgroundColor = "#ffffff";
    content.style.padding = "30px 25px";
    content.style.borderRadius = "12px";
    content.style.textAlign = "center";
    content.style.minWidth = "350px";
    content.style.maxWidth = "90%";
    content.style.boxShadow = "0 8px 25px rgba(0,0,0,0.2)";
    content.style.fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";

    const title = document.createElement("h3");
    title.textContent = "Session Timeout Warning";
    title.style.marginBottom = "15px";
    title.style.color = "#333";

    const message = document.createElement("p");
    message.innerHTML = 'You have been inactive.<br>Your session will close in <span id="countdown">60</span> seconds.';
    message.style.fontSize = "16px";
    message.style.color = "#555";
    message.style.lineHeight = "1.5";

    countdown = message.querySelector("#countdown");
    countdown.style.fontWeight = "bold";
    countdown.style.color = "#d9534f";

    const btnContainer = document.createElement("div");
    btnContainer.style.marginTop = "20px";
    btnContainer.style.display = "flex";
    btnContainer.style.justifyContent = "center";
    btnContainer.style.gap = "15px";

    const stayBtn = document.createElement("button");
    stayBtn.textContent = "Stay Logged In";
    stayBtn.style.padding = "10px 18px";
    stayBtn.style.border = "none";
    stayBtn.style.borderRadius = "6px";
    stayBtn.style.backgroundColor = "#5cb85c";
    stayBtn.style.color = "#fff";
    stayBtn.style.cursor = "pointer";
    stayBtn.style.fontSize = "14px";
    stayBtn.style.transition = "background-color 0.3s ease";
    stayBtn.addEventListener("mouseenter", () => { stayBtn.style.backgroundColor = "#4cae4c"; });
    stayBtn.addEventListener("mouseleave", () => { stayBtn.style.backgroundColor = "#5cb85c"; });

    const logoutBtn = document.createElement("button");
    logoutBtn.textContent = "Logout";
    logoutBtn.style.padding = "10px 18px";
    logoutBtn.style.border = "none";
    logoutBtn.style.borderRadius = "6px";
    logoutBtn.style.backgroundColor = "#d9534f";
    logoutBtn.style.color = "#fff";
    logoutBtn.style.cursor = "pointer";
    logoutBtn.style.fontSize = "14px";
    logoutBtn.style.transition = "background-color 0.3s ease";
    logoutBtn.addEventListener("mouseenter", () => { logoutBtn.style.backgroundColor = "#c9302c"; });
    logoutBtn.addEventListener("mouseleave", () => { logoutBtn.style.backgroundColor = "#d9534f"; });

    btnContainer.appendChild(stayBtn);
    btnContainer.appendChild(logoutBtn);
    content.appendChild(title);
    content.appendChild(message);
    content.appendChild(btnContainer);
    modal.appendChild(content);
    document.body.appendChild(modal);

    stayBtn.addEventListener("click", () => {
        publishActivity(true);
        emitAction("stay");
    });
    logoutBtn.addEventListener("click", () => {
        emitAction("logout");
        publishLogoutAndExit();
    });

    if (!document.getElementById("timerLogoutFadeStyle")) {
        const style = document.createElement("style");
        style.id = "timerLogoutFadeStyle";
        style.innerHTML = `
            @keyframes fadeIn {
                from {opacity: 0;}
                to {opacity: 1;}
            }
        `;
        document.head.appendChild(style);
    }
}

function updateCountdownText(state) {
    if (!countdown || !state.logoutDeadline) return;
    const secondsLeft = Math.max(0, Math.ceil((state.logoutDeadline - nowMs()) / 1000));
    countdown.textContent = String(secondsLeft);
}

function syncFromState() {
    let state = readState();
    if (!state) {
        state = initializeState();
    }

    const now = nowMs();

    if (state.forceLogoutAt > 0) {
        if (now - state.forceLogoutAt > actionPropagationWindowMs) {
            const refreshed = createFreshState();
            writeState(refreshed);
            closeModal();
            return;
        }
        autoLogout();
        return;
    }

    if (state.logoutDeadline > 0 && now >= state.logoutDeadline) {
        autoLogout();
        return;
    }

    if (state.logoutDeadline > 0) {
        createModal();
        updateCountdownText(state);
        return;
    }

    if (state.idleDeadline > 0 && now >= state.idleDeadline) {
        state = publishWarningIfNeeded(state);
        createModal();
        updateCountdownText(state);
        return;
    }

    closeModal();
}

["mousemove", "keydown", "scroll", "touchstart", "click"].forEach((evt) => {
    document.addEventListener(evt, () => publishActivity(false), { passive: true });
});

window.addEventListener("storage", (event) => {
    if (event.key === storageKey) {
        syncFromState();
        return;
    }

    if (event.key === actionKey && event.newValue) {
        try {
            handleRemoteAction(JSON.parse(event.newValue));
        } catch (_) {
            // Ignore malformed storage payloads
        }
    }
});

if (typeof BroadcastChannel !== "undefined") {
    bc = new BroadcastChannel(channelName);
    bc.onmessage = (event) => {
        handleRemoteAction(event.data);
    };
}

initializeState();
syncFromState();
monitorInterval = window.setInterval(syncFromState, 250);
