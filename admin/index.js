// DROPDOWN PROFILE
const profileBtn = document.getElementById("profileBtn");
const dropdown = document.getElementById("dropdownProfile");

// Toggle dropdown
profileBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    dropdown.classList.toggle("hidden");
});

// Close dropdown when clicking outside
document.addEventListener("click", () => {
    dropdown.classList.add("hidden");
});

// DROPDOWN POSTS
const postDropdownBtn = document.getElementById("postDropdownBtn");
const postDropdown = document.getElementById("postDropdown");
const arrowIcon = document.getElementById("arrowIcon");

let isOpen = false;

postDropdownBtn.addEventListener("click", () => {
  if (!isOpen) {
    // OPEN (fade + slide)
    postDropdown.classList.remove("hidden");
    postDropdown.classList.add("dropdown-enter");

    requestAnimationFrame(() => {
      postDropdown.classList.add("dropdown-enter-active");
      postDropdown.classList.remove("dropdown-enter");
    });

    arrowIcon.classList.add("rotate-180");
    isOpen = true;

  } else {
    // CLOSE (fade-out + slide-up)
    postDropdown.classList.add("dropdown-exit");

    requestAnimationFrame(() => {
      postDropdown.classList.add("dropdown-exit-active");
      postDropdown.classList.remove("dropdown-exit");
    });

    // Hapus elemen setelah animasi selesai
    setTimeout(() => {
      postDropdown.classList.add("hidden");
      postDropdown.classList.remove("dropdown-exit-active");
    }, 200);

    arrowIcon.classList.remove("rotate-180");
    isOpen = false;
  }
});

document.addEventListener("click", (event) => {
  const isClickInside =
    postDropdown.contains(event.target) ||
    postDropdownBtn.contains(event.target);

  if (!isClickInside && isOpen) {
    // CLOSE ANIMATED
    postDropdown.classList.add("dropdown-exit");

    requestAnimationFrame(() => {
      postDropdown.classList.add("dropdown-exit-active");
      postDropdown.classList.remove("dropdown-exit");
    });

    setTimeout(() => {
      postDropdown.classList.add("hidden");
      postDropdown.classList.remove("dropdown-exit-active");
    }, 200);

    arrowIcon.classList.remove("rotate-180");
    isOpen = false;
  }
});

// Auto generate slug
document.querySelector("input[name='title']").addEventListener("input", function () {
    document.getElementById("slug").value =
        this.value.toLowerCase().replace(/ /g, "-").replace(/[^a-z0-9-]/g, "");
});

// Show schedule time
document.getElementById("scheduledCheck").addEventListener("change", function () {
    document.getElementById("scheduleTime").classList.toggle("hidden", !this.checked);
});

const menuBtn = document.querySelector(".w-full .flex button"); // button menu
const sidebar = document.getElementById("sidebar");

menuBtn.addEventListener("click", () => {
  sidebar.classList.toggle("sidebar-collapsed");
});