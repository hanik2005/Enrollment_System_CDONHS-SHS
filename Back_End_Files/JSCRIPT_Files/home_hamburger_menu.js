document.addEventListener("DOMContentLoaded", () => {
    const menuItems = [];
    const defaultIcon = "../../Assets/LOGO.png";

    function getRoleLabel() {
        const path = window.location.pathname.toLowerCase();
        if (path.includes("/student_files/")) return "Student";
        if (path.includes("/teacher_files/")) return "Teacher";
        if (path.includes("/admin_files/")) return "Administrator";
        return "User";
    }

    function getMenuIcon(href, labelText) {
        const hrefText = (href || "").toLowerCase();
        const label = (labelText || "").toLowerCase();
        const checks = [
            { key: "logout", icon: "../../Assets/NotVisible.png" },
            { key: "profile", icon: "../../Assets/profile_button.png" },
            { key: "student_enlistment", icon: "../../Assets/enlistment_button.png" },
            { key: "class_list", icon: "../../Assets/class_list_button.png" },
            { key: "student_progress", icon: "../../Assets/progress_button.png" },
            { key: "enrollment_summary", icon: "../../Assets/teacher_enrollment_image.png" },
            { key: "teacher_advisory_notes", icon: "../../Assets/grades_button.png" },
            { key: "admin_student_application_list", icon: "../../Assets/application_button.jpg" },
            { key: "sensitive_information", icon: "../../Assets/Visible.png" },
            { key: "activation_page", icon: "../../Assets/activation_button.png" },
            { key: "enlistment_validation", icon: "../../Assets/enlistment_validation.png" },
            { key: "teacher_advisory_page", icon: "../../Assets/teacher_application_image.png" },
            { key: "document_compliance", icon: "../../Assets/Visible.png" },
            { key: "reports_dashboard", icon: "../../Assets/validation_button.png" },
            { key: "document_correction", icon: "../../Assets/application_button.jpg" },
            { key: "audit_trail", icon: "../../Assets/NotVisible.png" },
            { key: "student_progress_validation", icon: "../../Assets/progress_button.png" },
            { key: "home.php", icon: "../../Assets/LOGO.png" },
            { key: "dashboard", icon: "../../Assets/LOGO.png" }
        ];

        const combined = `${hrefText} ${label}`;
        for (const entry of checks) {
            if (combined.includes(entry.key)) {
                return entry.icon;
            }
        }

        return defaultIcon;
    }

    function addIconToLink(link) {
        if (!link || link.dataset.iconReady === "1") {
            return;
        }

        const label = link.textContent.trim();
        const iconSrc = getMenuIcon(link.getAttribute("href"), label);
        link.textContent = "";

        const wrap = document.createElement("span");
        wrap.className = "home-menu-link-content";

        const icon = document.createElement("img");
        icon.className = "home-menu-link-icon";
        icon.src = iconSrc;
        icon.alt = "";
        icon.setAttribute("aria-hidden", "true");

        const text = document.createElement("span");
        text.className = "home-menu-link-text";
        text.textContent = label;

        wrap.appendChild(icon);
        wrap.appendChild(text);
        link.appendChild(wrap);
        link.dataset.iconReady = "1";
    }

    function addIconToDisabledItem(item) {
        if (!item || item.dataset.iconReady === "1") {
            return;
        }

        const label = item.textContent.trim();
        item.textContent = "";

        const wrap = document.createElement("span");
        wrap.className = "home-menu-link-content";

        const icon = document.createElement("img");
        icon.className = "home-menu-link-icon";
        icon.src = defaultIcon;
        icon.alt = "";
        icon.setAttribute("aria-hidden", "true");

        const text = document.createElement("span");
        text.className = "home-menu-link-text";
        text.textContent = label;

        wrap.appendChild(icon);
        wrap.appendChild(text);
        item.appendChild(wrap);
        item.dataset.iconReady = "1";
    }

    function decorateMenuLinks(container) {
        if (!container) {
            return;
        }

        container.querySelectorAll("a").forEach(addIconToLink);
        container.querySelectorAll(".menu-link-disabled").forEach(addIconToDisabledItem);
    }

    function closeMenu(item) {
        item.overlay.classList.remove("open");
        item.toggle.setAttribute("aria-expanded", "false");
        document.body.classList.remove("home-menu-open");
        window.setTimeout(() => {
            if (!item.overlay.classList.contains("open")) {
                item.overlay.hidden = true;
            }
        }, 220);
    }

    function closeAllMenus(exceptItem = null) {
        menuItems.forEach((item) => {
            if (item !== exceptItem && item.toggle.getAttribute("aria-expanded") === "true") {
                closeMenu(item);
            }
        });
    }

    function openMenu(item) {
        closeAllMenus(item);
        item.overlay.hidden = false;
        requestAnimationFrame(() => {
            item.overlay.classList.add("open");
            item.toggle.setAttribute("aria-expanded", "true");
            document.body.classList.add("home-menu-open");
            const focusTarget = item.overlay.querySelector(".home-menu-close");
            if (focusTarget) {
                focusTarget.focus();
            }
        });
    }

    function registerMenu(toggle, overlay) {
        if (!toggle || !overlay || toggle.dataset.menuRegistered === "1") {
            return;
        }

        const item = { toggle, overlay };
        menuItems.push(item);
        toggle.dataset.menuRegistered = "1";

        const closeButton = overlay.querySelector(".home-menu-close");
        const menuLinks = overlay.querySelectorAll(".home-menu-links a");

        toggle.addEventListener("click", () => {
            const isOpen = toggle.getAttribute("aria-expanded") === "true";
            if (isOpen) {
                closeMenu(item);
            } else {
                openMenu(item);
            }
        });

        if (closeButton) {
            closeButton.addEventListener("click", () => {
                closeMenu(item);
                toggle.focus();
            });
        }

        overlay.addEventListener("click", (event) => {
            if (event.target === overlay) {
                closeMenu(item);
            }
        });

        overlay.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                closeMenu(item);
                toggle.focus();
            }
        });

        menuLinks.forEach((link) => {
            link.addEventListener("click", () => {
                closeMenu(item);
            });
        });
    }

    function toHamburgerButton(toggle, menuId) {
        toggle.classList.add("home-menu-toggle");
        toggle.setAttribute("aria-label", "Open navigation menu");
        toggle.setAttribute("aria-expanded", "false");
        toggle.setAttribute("aria-controls", menuId);
        toggle.setAttribute("type", "button");
        toggle.innerHTML = "";

        const icon = document.createElement("span");
        icon.className = "menu-icon";
        icon.setAttribute("aria-hidden", "true");
        icon.innerHTML = "<span></span><span></span><span></span>";

        const label = document.createElement("span");
        label.className = "menu-label";
        label.textContent = "Menu";

        toggle.appendChild(icon);
        toggle.appendChild(label);
    }

    function buildLegacyOverlay(toggle, dropdown, index) {
        const role = getRoleLabel();
        const image = toggle.querySelector("img");
        const imageSrc = toggle.dataset.profileSrc || (image ? image.getAttribute("src") : "../../Assets/profile_button.png");
        const imageAlt = toggle.dataset.profileAlt || (image ? (image.getAttribute("alt") || `${role} profile`) : `${role} profile`);
        const menuId = `auto-home-menu-${index + 1}`;

        toHamburgerButton(toggle, menuId);

        const overlay = document.createElement("div");
        overlay.className = "home-menu-overlay";
        overlay.id = menuId;
        overlay.hidden = true;

        const panel = document.createElement("aside");
        panel.className = "home-menu-panel";
        panel.setAttribute("role", "dialog");
        panel.setAttribute("aria-modal", "true");
        panel.setAttribute("aria-label", `${role} navigation menu`);

        const top = document.createElement("div");
        top.className = "home-menu-top";

        const closeButton = document.createElement("button");
        closeButton.className = "home-menu-close";
        closeButton.type = "button";
        closeButton.setAttribute("aria-label", "Close navigation menu");
        closeButton.textContent = "Close";
        top.appendChild(closeButton);

        const profile = document.createElement("div");
        profile.className = "home-menu-profile";

        const profileImg = document.createElement("img");
        profileImg.src = imageSrc || "../../Assets/profile_button.png";
        profileImg.alt = imageAlt;

        const profileMeta = document.createElement("div");
        const title = document.createElement("h3");
        title.textContent = "Navigation";
        const subtitle = document.createElement("p");
        subtitle.textContent = role;

        profileMeta.appendChild(title);
        profileMeta.appendChild(subtitle);
        profile.appendChild(profileImg);
        profile.appendChild(profileMeta);

        const nav = document.createElement("nav");
        nav.className = "home-menu-links";
        nav.setAttribute("aria-label", `${role} page links`);

        dropdown.querySelectorAll("a").forEach((link) => {
            nav.appendChild(link.cloneNode(true));
        });

        panel.appendChild(top);
        panel.appendChild(profile);
        panel.appendChild(nav);
        overlay.appendChild(panel);
        document.body.appendChild(overlay);

        dropdown.remove();
        decorateMenuLinks(nav);
    }

    const legacyButtons = Array.from(
        document.querySelectorAll(".legacy-menu-trigger, .home-menu-toggle:not([aria-controls])")
    );
    legacyButtons.forEach((button, index) => {
        const root = button.closest(".right") || button.parentElement;
        const dropdown = root ? root.querySelector(".legacy-nav-links") : null;
        if (!dropdown) {
            return;
        }
        buildLegacyOverlay(button, dropdown, index);
    });

    document.querySelectorAll(".home-menu-links").forEach(decorateMenuLinks);

    const toggles = Array.from(document.querySelectorAll(".home-menu-toggle[aria-controls]"));
    toggles.forEach((toggle) => {
        const menuId = toggle.getAttribute("aria-controls");
        if (!menuId) {
            return;
        }
        const overlay = document.getElementById(menuId);
        if (!overlay) {
            return;
        }
        registerMenu(toggle, overlay);
    });

    if (!menuItems.length) {
        return;
    }

    document.addEventListener("keydown", (event) => {
        if (event.key !== "Escape") {
            return;
        }
        closeAllMenus();
    });
});
