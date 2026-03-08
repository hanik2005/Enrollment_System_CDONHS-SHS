document.addEventListener("DOMContentLoaded", () => {
    const mainTarget = document.querySelector(
        "main, [role='main'], .dashboard, .enlistment-container, .page-container, .content, .table-container"
    );

    if (mainTarget) {
        if (!mainTarget.id) {
            mainTarget.id = "main-content";
        }

        if (
            mainTarget.tagName.toLowerCase() !== "main" &&
            !mainTarget.hasAttribute("role")
        ) {
            mainTarget.setAttribute("role", "main");
        }

        if (!document.querySelector(".skip-link")) {
            const skip = document.createElement("a");
            skip.className = "skip-link";
            skip.href = `#${mainTarget.id}`;
            skip.textContent = "Skip to main content";
            document.body.prepend(skip);
        }
    }

    const logo = document.querySelector(".header .left img");
    if (logo && !logo.getAttribute("alt")) {
        logo.setAttribute("alt", "CDONHS-SHS logo");
    }

    const profileButtons = document.querySelectorAll(".profile-btn");
    if (!profileButtons.length) {
        return;
    }

    const menus = [];

    function closeMenu(item) {
        item.dropdown.style.display = "none";
        item.button.setAttribute("aria-expanded", "false");
        item.links.forEach((link) => link.setAttribute("tabindex", "-1"));
    }

    function closeAllMenus(except = null) {
        menus.forEach((item) => {
            if (item !== except) {
                closeMenu(item);
            }
        });
    }

    function openMenu(item, focusFirst = false) {
        closeAllMenus(item);
        item.dropdown.style.display = "flex";
        item.button.setAttribute("aria-expanded", "true");
        item.links.forEach((link) => link.setAttribute("tabindex", "0"));
        if (focusFirst && item.links.length) {
            item.links[0].focus();
        }
    }

    profileButtons.forEach((button, index) => {
        const root = button.closest(".right") || button.parentElement;
        const dropdown = root ? root.querySelector(".profile-dropdown") : null;
        if (!dropdown) {
            return;
        }

        const profileImg = button.querySelector("img");
        if (profileImg && !profileImg.getAttribute("alt")) {
            profileImg.setAttribute("alt", "User profile");
        }

        const menuId = dropdown.id || `profile-dropdown-menu-${index + 1}`;
        dropdown.id = menuId;

        button.setAttribute("aria-haspopup", "menu");
        button.setAttribute("aria-controls", menuId);
        button.setAttribute("aria-expanded", "false");
        if (!button.getAttribute("aria-label")) {
            button.setAttribute("aria-label", "Open profile menu");
        }

        dropdown.setAttribute("role", "menu");
        const links = Array.from(dropdown.querySelectorAll("a"));
        links.forEach((link) => {
            link.setAttribute("role", "menuitem");
            link.setAttribute("tabindex", "-1");
        });

        const item = { button, dropdown, links };
        menus.push(item);

        button.addEventListener("click", (event) => {
            event.stopPropagation();
            const expanded = button.getAttribute("aria-expanded") === "true";
            if (expanded) {
                closeMenu(item);
            } else {
                openMenu(item, false);
            }
        });

        button.addEventListener("keydown", (event) => {
            if (event.key === "ArrowDown" || event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                openMenu(item, true);
            } else if (event.key === "Escape") {
                closeMenu(item);
            }
        });

        dropdown.addEventListener("keydown", (event) => {
            const activeIndex = links.indexOf(document.activeElement);

            if (event.key === "Escape") {
                event.preventDefault();
                closeMenu(item);
                button.focus();
                return;
            }

            if (!links.length) {
                return;
            }

            if (event.key === "ArrowDown") {
                event.preventDefault();
                const next = activeIndex < 0 ? 0 : (activeIndex + 1) % links.length;
                links[next].focus();
            }

            if (event.key === "ArrowUp") {
                event.preventDefault();
                const prev = activeIndex <= 0 ? links.length - 1 : activeIndex - 1;
                links[prev].focus();
            }
        });
    });

    document.addEventListener("click", (event) => {
        menus.forEach((item) => {
            const clickedInsideButton = item.button.contains(event.target);
            const clickedInsideMenu = item.dropdown.contains(event.target);
            if (!clickedInsideButton && !clickedInsideMenu) {
                closeMenu(item);
            }
        });
    });
});
