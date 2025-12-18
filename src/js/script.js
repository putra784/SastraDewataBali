// Cursor effect
    const cursor = document.getElementById("cursor");
    const trailContainer = document.getElementById("trail");

    let mouseX = 0,
      mouseY = 0;
    let currentX = 0,
      currentY = 0;

    const trailLength = 12;
    const trailDots = [];

    // generate trail dots
    for (let i = 0; i < trailLength; i++) {
      const dot = document.createElement("div");
      dot.className = "pointer-events-none fixed w-2 h-2 rounded-full bg-blue-500/40 blur-sm";
      trailContainer.appendChild(dot);
      trailDots.push({
        element: dot,
        x: 0,
        y: 0
      });
    }

    document.addEventListener("mousemove", (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;
    });

    function animate() {
      currentX += (mouseX - currentX) * 0.1;
      currentY += (mouseY - currentY) * 0.1;

      cursor.style.transform = `translate(${currentX}px, ${currentY}px) translate(-50%, -50%)`;

      let prevX = currentX,
        prevY = currentY;

      trailDots.forEach((dot, i) => {
        dot.x += (prevX - dot.x) * 0.1;
        dot.y += (prevY - dot.y) * 0.1;
        dot.element.style.transform = `translate(${dot.x}px, ${dot.y}px) translate(-50%, -50%)`;

        dot.element.style.opacity = 1 - i / trailLength;

        prevX = dot.x;
        prevY = dot.y;
      });

      requestAnimationFrame(animate);
    }

    animate();

    // MOBILE MENU
    const menuBtn = document.getElementById("menuBtn");
    const mobileMenu = document.getElementById("mobileMenu");

    menuBtn.addEventListener("click", (event) => {
      event.stopPropagation();
      mobileMenu.classList.toggle("hidden");
    });

    document.addEventListener("click", (event) => {
      if (!mobileMenu.contains(event.target) && !menuBtn.contains(event.target)) {
        mobileMenu.classList.add("hidden");
      }
    });

    // DESKTOP CATEGORY DROPDOWN
    const categoryMenu = document.getElementById("categoryMenu");
    const categoryWrapper = document.getElementById("categoryWrapper");
    const dropIcon = document.getElementById("drop-icon");
    const categoryBtn = document.getElementById("categoryBtn");

    // Klik tombol → toggle dropdown
    categoryWrapper.addEventListener("click", (event) => {
      event.stopPropagation();
      categoryMenu.classList.toggle("hidden");
      dropIcon.classList.toggle("-rotate-90");
      categoryBtn.classList.toggle("after:w-full");
    });

    // Klik di luar → tutup dropdown
    document.addEventListener("click", (event) => {
      const clickInside = categoryWrapper.contains(event.target);
      if (!clickInside) {
        categoryMenu.classList.add("hidden");
        dropIcon.classList.remove("-rotate-90");
        categoryBtn.classList.remove("after:w-full");
      }
    });

    const navbar = document.querySelector('header');
    const mainSection = document.querySelector('section');

    function updatePadding() {
      mainSection.style.paddingTop = navbar.offsetHeight + 'px';
    }

    // SLIDER
    window.addEventListener('load', updatePadding);
    window.addEventListener('resize', updatePadding);

    document.addEventListener("DOMContentLoaded", () => {

      const track = document.getElementById("carouselTrack");
      const slides = Array.from(track.children);

      const prevBtn = document.getElementById("prevBtn");
      const nextBtn = document.getElementById("nextBtn");

      const dotsContainer = document.getElementById("dots");

      const carouselViewport = document.getElementById("carouselViewport");

      let index = 0;
      const slideCount = slides.length;

      // BUAT DOT INDICATOR
      function createDots() {
        for (let i = 0; i < slideCount; i++) {
          const dot = document.createElement("button");
          dot.className =
            "w-3 h-3 rounded-full bg-gray-300 hover:bg-gray-400 transition-all";
          dot.addEventListener("click", () => goTo(i));
          dotsContainer.appendChild(dot);
        }
        updateDots();
      }

      function updateDots() {
        const dots = dotsContainer.children;
        for (let i = 0; i < dots.length; i++) {
          dots[i].classList.remove("bg-yellow-900");
          dots[i].classList.add("bg-gray-300");
        }
        dots[index].classList.remove("bg-gray-300");
        dots[index].classList.add("bg-yellow-900");
      }

      // FUNGSI PINDAH SLIDE
      function goTo(i) {
        index = (i + slideCount) % slideCount;
        track.style.transform = `translateX(-${index * 100}%)`;
        updateDots();
      }

      function next() {
        goTo(index + 1);
      }

      function prev() {
        goTo(index - 1);
      }

      // SUPPORT GESTURE (SWIPE MOBILE)
      let startX = 0;
      let isDragging = false;

      carouselViewport.addEventListener(
        "touchstart",
        (e) => {
          startX = e.touches[0].clientX;
          isDragging = true;
        }, {
          passive: true
        }
      );

      carouselViewport.addEventListener(
        "touchmove",
        (e) => {
          if (!isDragging) return;
        }, {
          passive: true
        }
      );

      carouselViewport.addEventListener(
        "touchend",
        (e) => {
          isDragging = false;
          const endX = e.changedTouches[0].clientX;
          const diff = endX - startX;

          if (diff > 80) prev();
          else if (diff < -80) next();
        }, {
          passive: true
        }
      );

      // EVENT LISTENER BUTTON
      prevBtn.addEventListener("click", prev);
      nextBtn.addEventListener("click", next);

      document.addEventListener("keydown", (e) => {
        if (e.key === "ArrowLeft") prev();
        if (e.key === "ArrowRight") next();
      });

      // INISIALISASI
      createDots();
      goTo(0);
    });


    // CREATE ACTIVE NAVBAR
    const navLinks = document.querySelectorAll("nav a[href]");
    const currentPage = window.location.pathname.split("/").pop();

    navLinks.forEach((link) => {
      const linkPage = link.getAttribute("href");

      // Underline aktif
      if (linkPage === currentPage) {
        link.classList.add("after:w-full");
      }

      // delete if NonActive
      link.addEventListener("click", () => {
        navLinks.forEach((l) => l.classList.remove("after:w-full"));
        link.classList.add("after:w-full");
      })
    });

    // DROPDOWN
    document.addEventListener("DOMContentLoaded", () => {
      const dropdownBtn = document.getElementById('dropdownButton');
      const dropdownMenu = document.getElementById('dropdownMenu');

      dropdownBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // supaya klik tombol tidak langsung tertangkap oleh document
        dropdownMenu.classList.toggle('hidden');
      });

      // Tutup dropdown jika klik di luar
      document.addEventListener('click', (event) => {
        if (!dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
          dropdownMenu.classList.add('hidden');
        }
      });
    });