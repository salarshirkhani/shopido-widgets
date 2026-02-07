(function () {
  function initFAQ(root) {
    if (!root) return;

    const allowMultiple = root.getAttribute("data-allow-multiple") === "1";
    const items = Array.from(root.querySelectorAll(".shopido-faq-item"));

    function setPanelHeight(item, open) {
      const panel = item.querySelector(".shopido-faq-panel");
      if (!panel) return;

      if (open) {
        panel.hidden = false;
        panel.style.maxHeight = panel.scrollHeight + "px";
      } else {
        panel.style.maxHeight = panel.scrollHeight + "px";
        requestAnimationFrame(() => {
          panel.style.maxHeight = "0px";
          panel.hidden = true;
        });
      }
    }

    // initial heights
    items.forEach((item) => {
      const btn = item.querySelector(".shopido-faq-header");
      const panel = item.querySelector(".shopido-faq-panel");
      if (!btn || !panel) return;

      const open = btn.getAttribute("aria-expanded") === "true";
      if (open) {
        item.classList.add("is-open");
        panel.hidden = false;
        panel.style.maxHeight = panel.scrollHeight + "px";
      } else {
        item.classList.remove("is-open");
        panel.hidden = true;
        panel.style.maxHeight = "0px";
      }
    });

    items.forEach((item) => {
      const btn = item.querySelector(".shopido-faq-header");
      const panel = item.querySelector(".shopido-faq-panel");
      if (!btn || !panel) return;

      btn.addEventListener("click", () => {
        const isOpen = btn.getAttribute("aria-expanded") === "true";
        const nextOpen = !isOpen;

        if (!allowMultiple && nextOpen) {
          items.forEach((other) => {
            if (other === item) return;
            const b = other.querySelector(".shopido-faq-header");
            if (!b) return;
            if (b.getAttribute("aria-expanded") === "true") {
              b.setAttribute("aria-expanded", "false");
              other.classList.remove("is-open");
              setPanelHeight(other, false);
            }
          });
        }

        btn.setAttribute("aria-expanded", nextOpen ? "true" : "false");
        if (nextOpen) {
          item.classList.add("is-open");
          setPanelHeight(item, true);
        } else {
          item.classList.remove("is-open");
          setPanelHeight(item, false);
        }
      });
    });

    window.addEventListener("resize", () => {
      items.forEach((item) => {
        const btn = item.querySelector(".shopido-faq-header");
        const panel = item.querySelector(".shopido-faq-panel");
        if (!btn || !panel) return;
        const open = btn.getAttribute("aria-expanded") === "true";
        if (open) panel.style.maxHeight = panel.scrollHeight + "px";
      });
    });
  }

  function boot() {
    document.querySelectorAll(".shopido-faq").forEach(initFAQ);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }

  // برای Elementor editor
  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    window.elementorFrontend.hooks.addAction(
      "frontend/element_ready/shopido_faq_accordion.default",
      function ($scope) {
        initFAQ($scope[0].querySelector(".shopido-faq"));
      }
    );
  }
})();
