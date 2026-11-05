

(function () {
  "use strict";

  var reduceMotion =
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function ready(fn) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", fn);
    } else {
      fn();
    }
  }

  ready(function () {
    initGlitchText();
    initParticles();
    initTyping();
    initHeaderScroll();
    initNavigation();
    initSectionReveal();
    initMemberStage();
    initPortfolioFilters();
    initSkillBars();
    initWallet();
    initMusic();
    initContactForms();
  });

  // glitch title text
  function initGlitchText() {
    document.querySelectorAll(".glitch-title").forEach(function (title) {
      if (title.getAttribute("data-text")) {
        return;
      }

      if (title.textContent.trim()) {
        title.setAttribute("data-text", title.textContent.trim());
      }
    });
  }

  // particle background js
  function initParticles() {
    if (typeof window.particlesJS !== "function") {
      return;
    }

    var container = document.getElementById("particles-js");

    if (!container) {
      return;
    }

    window.particlesJS("particles-js", {
      particles: {
        number: { value: 80, density: { enable: true, value_area: 800 } },
        color: { value: "#ff7f00" },
        shape: { type: "circle" },
        opacity: {
          value: 0.5,
          random: true,
          anim: { enable: true, speed: 1, opacity_min: 0.1, sync: false },
        },
        size: {
          value: 3,
          random: true,
          anim: { enable: true, speed: 2, size_min: 0.1, sync: false },
        },
        line_linked: {
          enable: true,
          distance: 150,
          color: "#ff7f00",
          opacity: 0.4,
          width: 1,
        },
        move: {
          enable: true,
          speed: 1,
          direction: "none",
          random: true,
          straight: false,
          out_mode: "out",
          bounce: false,
        },
      },
      interactivity: {
        detect_on: "canvas",
        events: {
          onhover: { enable: true, mode: "grab" },
          onclick: { enable: true, mode: "push" },
          resize: true,
        },
        modes: {
          grab: { distance: 140, line_linked: { opacity: 1 } },
          push: { particles_nb: 4 },
        },
      },
      retina_detect: true,
    });
  }

  // typing text effects
  function initTyping() {
    if (reduceMotion || typeof window.TypeIt !== "function") {
      return;
    }

    document.querySelectorAll(".typing-subtitle").forEach(function (element) {
      if (!element.textContent.trim()) {
        return;
      }

      new window.TypeIt(element, {
        speed: 50,
        startDelay: 900,
        cursor: true,
        afterComplete: function (instance) {
          instance.destroy();
        },
      }).go();
    });

    document.querySelectorAll(".typing-text").forEach(function (element) {
      if (!element.textContent.trim()) {
        return;
      }

      new window.TypeIt(element, {
        speed: 12,
        startDelay: 1200,
        cursor: true,
        afterComplete: function (instance) {
          instance.destroy();
        },
      }).go();
    });
  }

  // sticky header state
  function initHeaderScroll() {
    var headers = document.querySelectorAll(".site-header.devenci-header");

    if (!headers.length) {
      return;
    }

    var update = function () {
      headers.forEach(function (header) {
        header.classList.toggle("scrolled", window.scrollY > 50);
      });
    };

    update();
    window.addEventListener("scroll", update, { passive: true });
  }

  // Scroll spy and mobile menu close behavior for the core Navigation block
  function initNavigation() {
    if (window.devenciData && window.devenciData.homeUrl) {
      document.querySelectorAll('a[href="/"]').forEach(function (link) {
        link.setAttribute("href", window.devenciData.homeUrl);
      });
    }

    var links = Array.prototype.slice.call(
      document.querySelectorAll('.main-nav a[href^="#"]'),
    );
    var sections = Array.prototype.slice.call(
      document.querySelectorAll("section[id]"),
    );

    if (links.length && sections.length) {
      var updateActive = function () {
        var current = "";

        sections.forEach(function (section) {
          if (window.scrollY >= section.offsetTop - 220) {
            current = section.id;
          }
        });

        links.forEach(function (link) {
          link.classList.toggle(
            "active",
            link.getAttribute("href") === "#" + current,
          );
        });
      };

      updateActive();
      window.addEventListener("scroll", updateActive, { passive: true });
    }

    document
      .querySelectorAll('.main-nav a[href^="#"]')
      .forEach(function (link) {
        link.addEventListener("click", function () {
          var container = document.querySelector(
            ".wp-block-navigation__responsive-container.is-menu-open",
          );
          var close = document.querySelector(
            ".wp-block-navigation__responsive-container-close",
          );

          if (container && close) {
            close.click();
          }
        });
      });
  }

  // Fade sections in as they enter the viewport, preserving the original
  function initSectionReveal() {
    if (reduceMotion || !("IntersectionObserver" in window)) {
      return;
    }

    var sections = document.querySelectorAll(".devenci-section");

    sections.forEach(function (section) {
      section.classList.add("devenci-reveal");
    });

    var observer = new IntersectionObserver(
      function (entries, obs) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.05 },
    );

    sections.forEach(function (section) {
      if (section.id === "home") {
        section.classList.add("is-visible");
      } else {
        observer.observe(section);
      }
    });
  }

  // Pixel-accurate member hover detection on the group homepage
  function initMemberStage() {
    var stage = document.querySelector(".members");

    if (!stage) {
      return;
    }

    var members = Array.prototype.slice.call(stage.querySelectorAll(".member"));
    var alphaMaps = new Map();
    var activeMember = null;

    function getAlphaMap(img) {
      if (alphaMaps.has(img)) {
        return alphaMaps.get(img);
      }

      if (!img.complete || !img.naturalWidth) {
        return null;
      }

      var canvas = document.createElement("canvas");
      canvas.width = img.naturalWidth;
      canvas.height = img.naturalHeight;

      var ctx = canvas.getContext("2d", { willReadFrequently: true });

      if (!ctx) {
        return null;
      }

      ctx.drawImage(img, 0, 0);

      try {
        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var map = {
          pixels: imageData.data,
          width: canvas.width,
          height: canvas.height,
        };
        alphaMaps.set(img, map);
        return map;
      } catch (error) {
        return null;
      }
    }

    // The member image fills a fixed 3:4 box with object-fit: contain, so the

    
    function getDrawnBox(img) {
      var rect = img.getBoundingClientRect();

      if (!img.naturalWidth || !img.naturalHeight) {
        return rect;
      }

      var scale = Math.min(
        rect.width / img.naturalWidth,
        rect.height / img.naturalHeight,
      );
      var width = img.naturalWidth * scale;
      var height = img.naturalHeight * scale;
      var left = rect.left + (rect.width - width) / 2;
      var top = rect.top + (rect.height - height) / 2;

      return {
        left: left,
        top: top,
        width: width,
        height: height,
        right: left + width,
        bottom: top + height,
      };
    }

    function isInsideCharacter(member, mouseX, mouseY) {
      var img = member.querySelector("img");

      if (!img || !img.complete || !img.naturalWidth) {
        return false;
      }

      var rect = getDrawnBox(img);

      if (
        mouseX < rect.left ||
        mouseX > rect.right ||
        mouseY < rect.top ||
        mouseY > rect.bottom
      ) {
        return false;
      }

      var map = getAlphaMap(img);

      if (!map) {
        return true;
      }

      var sourceX = Math.floor(((mouseX - rect.left) / rect.width) * map.width);
      var sourceY = Math.floor(
        ((mouseY - rect.top) / rect.height) * map.height,
      );

      if (
        sourceX < 0 ||
        sourceY < 0 ||
        sourceX >= map.width ||
        sourceY >= map.height
      ) {
        return false;
      }

      var alpha = map.pixels[(sourceY * map.width + sourceX) * 4 + 3];
      return alpha > 20;
    }

    function findBestMember(mouseX, mouseY) {
      var candidates = [];

      members.forEach(function (member, index) {
        if (!isInsideCharacter(member, mouseX, mouseY)) {
          return;
        }

        var img = member.querySelector("img");
        var rect = getDrawnBox(img);
        var centerX = rect.left + rect.width / 2;
        var centerY = rect.top + rect.height / 2;
        var dx = (mouseX - centerX) / rect.width;
        var dy = (mouseY - centerY) / rect.height;
        var distance = Math.sqrt(dx * dx + dy * dy);
        var zIndex = Number(window.getComputedStyle(member).zIndex) || 0;

        candidates.push({
          member: member,
          distance: distance,
          zIndex: zIndex,
          index: index,
        });
      });

      if (!candidates.length) {
        return null;
      }

      candidates.sort(function (a, b) {
        if (Math.abs(a.distance - b.distance) > 0.05) {
          return a.distance - b.distance;
        }

        return b.zIndex - a.zIndex || b.index - a.index;
      });

      return candidates[0].member;
    }

    function updateHover(mouseX, mouseY) {
      var selected = findBestMember(mouseX, mouseY);

      if (selected === activeMember) {
        return selected;
      }

      activeMember = selected;

      members.forEach(function (member) {
        member.classList.toggle("is-hovered", member === selected);
      });

      stage.style.cursor = selected ? "pointer" : "default";
      return selected;
    }

    stage.addEventListener("mousemove", function (event) {
      updateHover(event.clientX, event.clientY);
    });

    stage.addEventListener("mouseleave", function () {
      activeMember = null;
      members.forEach(function (member) {
        member.classList.remove("is-hovered");
      });
      stage.style.cursor = "default";
    });

    stage.addEventListener("click", function (event) {
      var selected = findBestMember(event.clientX, event.clientY);

      if (!selected) {
        return;
      }

      var destination = selected.getAttribute("href");

      if (destination && destination !== "#") {
        event.preventDefault();
        window.location.assign(destination);
      }
    });

    stage.addEventListener(
      "touchstart",
      function (event) {
        var touch = event.touches[0];

        if (touch) {
          updateHover(touch.clientX, touch.clientY);
        }
      },
      { passive: true },
    );

    members.forEach(function (member) {
      member.addEventListener("focus", function () {
        activeMember = member;
        members.forEach(function (item) {
          item.classList.toggle("is-hovered", item === member);
        });
      });

      member.addEventListener("blur", function () {
        member.classList.remove("is-hovered");
        activeMember = null;
      });

      var img = member.querySelector("img");

      if (img) {
        img.addEventListener("load", function () {
          alphaMaps.delete(img);
        });
      }
    });

    if (!reduceMotion && typeof window.gsap !== "undefined") {
      var images = stage.querySelectorAll(".member img");

      window.gsap.set(images, { opacity: 0, y: 60 });
      window.gsap.to(images, {
        opacity: 1,
        y: 0,
        duration: 1,
        stagger: 0.2,
        delay: 0.3,
        ease: "power3.out",
      });
    }
  }

  // portfolio category filters
  function initPortfolioFilters() {
    document
      .querySelectorAll(".devenci-portfolio")
      .forEach(function (portfolio) {
        var buttons = portfolio.querySelectorAll(".filter-btn");
        var items = portfolio.querySelectorAll(".portfolio-item");

        if (!buttons.length || !items.length) {
          return;
        }

        buttons.forEach(function (button) {
          button.addEventListener("click", function () {
            buttons.forEach(function (item) {
              item.classList.remove("active");
            });
            button.classList.add("active");

            var filter = button.getAttribute("data-filter");

            items.forEach(function (item) {
              if (
                filter === "all" ||
                item.getAttribute("data-category") === filter
              ) {
                item.style.display = "";
                window.setTimeout(function () {
                  item.style.opacity = "1";
                  item.style.transform = "scale(1)";
                }, 30);
              } else {
                item.style.opacity = "0";
                item.style.transform = "scale(0.8)";
                window.setTimeout(function () {
                  item.style.display = "none";
                }, 300);
              }
            });
          });
        });
      });
  }

  // skill bar animation
  function initSkillBars() {
    var bars = document.querySelectorAll(".skill-progress");

    if (!bars.length) {
      return;
    }

    bars.forEach(function (bar) {
      bar.style.width = "0";
    });

    var observer = new IntersectionObserver(
      function (entries, obs) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }

          var bar = entry.target;
          var target = parseInt(bar.getAttribute("data-width") || "0", 10);

          window.setTimeout(function () {
            bar.style.width = target + "%";
          }, 100);

          obs.unobserve(bar);
        });
      },
      { threshold: 0.1 },
    );

    bars.forEach(function (bar) {
      observer.observe(bar);
    });
  }

  // Shared toast helper
  function showToast(title, message) {
    var toast = document.getElementById("wallet-toast");

    if (!toast) {
      return;
    }

    var titleNode = toast.querySelector(".toast-title");
    var messageNode = toast.querySelector(".toast-message");

    if (titleNode) {
      titleNode.textContent = title;
    }

    if (messageNode) {
      messageNode.textContent = message;
    }

    toast.classList.add("show");

    window.setTimeout(function () {
      toast.classList.remove("show");
    }, 5000);
  }

  // wallet button and modal
  function initWallet() {
    var wrapper = document.querySelector(".devenci-wallet-btn");
    var toast = document.getElementById("wallet-toast");

    if (!wrapper) {
      return;
    }

    var link = wrapper.querySelector(".wp-block-button__link");

    if (link) {
      link.addEventListener("click", function (event) {
        event.preventDefault();

        window.setTimeout(function () {
          showToast(
            "Wallet Connected",
            "Your wallet has been successfully connected!",
          );
          link.textContent = "Connected";
          wrapper.classList.add("connected");
        }, 600);
      });
    }

    if (toast) {
      var close = toast.querySelector(".toast-close");

      if (close) {
        close.addEventListener("click", function () {
          toast.classList.remove("show");
        });
      }
    }
  }

  // background music player
  function initMusic() {
    var music = document.getElementById("background-music");
    var toggle = document.getElementById("music-toggle");
    var icon = document.getElementById("volume-icon");

    if (!music || !toggle || !icon) {
      return;
    }

    music.volume = 0.35;
    var userPaused = false;

    function setPlaying() {
      toggle.classList.remove("muted");
      toggle.setAttribute("aria-pressed", "true");
      icon.className = "fa-solid fa-volume-high";
    }

    function setMuted() {
      toggle.classList.add("muted");
      toggle.setAttribute("aria-pressed", "false");
      icon.className = "fa-solid fa-volume-xmark";
    }

    music.play().then(setPlaying).catch(setMuted);

    toggle.addEventListener("click", function (event) {
      event.stopPropagation();

      if (music.paused) {
        userPaused = false;
        music.play().then(setPlaying).catch(setMuted);
      } else {
        userPaused = true;
        music.pause();
        setMuted();
      }
    });

    document.addEventListener(
      "click",
      function () {
        if (music.paused && !userPaused) {
          music
            .play()
            .then(setPlaying)
            .catch(function () {});
        }
      },
      { once: true },
    );
  }

  // contact form handling
  function initContactForms() {
    document.querySelectorAll(".devenci-contact-form").forEach(function (form) {
      form.addEventListener("submit", function (event) {
        event.preventDefault();

        var status = form.querySelector(".devenci-form-status");
        var submit = form.querySelector('button[type="submit"]');
        var name = form.querySelector('[name="name"]');
        var email = form.querySelector('[name="email"]');
        var message = form.querySelector('[name="message"]');

        if (status) {
          status.textContent = "";
          status.classList.remove("is-error", "is-success");
        }

        if (
          !name.value.trim() ||
          !email.value.trim() ||
          !message.value.trim()
        ) {
          if (status) {
            status.textContent =
              "Please complete your name, email address, and message.";
            status.classList.add("is-error");
          }
          return;
        }

        var originalText = submit ? submit.textContent : "";
        if (submit) {
          submit.disabled = true;
          submit.textContent = "Sending…";
        }

        window
          .fetch(window.devenciData.ajaxUrl, {
            method: "POST",
            credentials: "same-origin",
            body: new FormData(form),
          })
          .then(function (response) {
            return response.json();
          })
          .then(function (result) {
            if (result && result.success) {
              if (status) {
                status.textContent = result.data.message;
                status.classList.add("is-success");
              }
              form.reset();
              showToast("Message Sent!", result.data.message);
            } else {
              var text =
                result && result.data && result.data.message
                  ? result.data.message
                  : "The message could not be sent.";
              if (status) {
                status.textContent = text;
                status.classList.add("is-error");
              }
            }
          })
          .catch(function () {
            if (status) {
              status.textContent =
                "The message could not be sent. Please try again later.";
              status.classList.add("is-error");
            }
          })
          .finally(function () {
            if (submit) {
              submit.disabled = false;
              submit.textContent = originalText;
            }
          });
      });
    });
  }
})();
