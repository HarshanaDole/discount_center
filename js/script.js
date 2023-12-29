const searchBox = document.getElementById("search-box");
const emailBox = document.getElementById("email");

searchBox.addEventListener("focus", function () {
  if (searchBox.placeholder === "Search for products") {
    searchBox.placeholder = "";
  }
});

searchBox.addEventListener("blur", function () {
  if (searchBox.placeholder === "") {
    searchBox.placeholder = "Search for products";
  }
});

emailBox.addEventListener("focus", function () {
  if (emailBox.value === "Your email address") {
    emailBox.value = "";
  }
});

emailBox.addEventListener("blur", function () {
  if (emailBox.value === "") {
    emailBox.value = "Your email address";
  }
});

function closeAllCategories() {
  var mainCategoryElements = document.querySelectorAll(
    ".expand .sub-subcat, .expand .sub-sub-subcat"
  );
  mainCategoryElements.forEach(function (category) {
    category.classList.remove("expanded");
  });
}

function closeAllSubCaregories() {
  var subCategoryElements = document.querySelectorAll(
    ".expand .sub-sub-subcat"
  );
  subCategoryElements.forEach(function (subcategory) {
    subcategory.classList.remove("expanded");
  });
}

function toggleCategories(element) {
  closeAllCategories();
  var nextSibling = element.parentNode.nextElementSibling;

  if (nextSibling && nextSibling.classList.contains("subcategories")) {
    var subcatElements = nextSibling.querySelectorAll(".subcat");
    subcatElements.forEach(function (subcat) {
      if (subcat.classList.contains("expanded")) {
        subcat.classList.remove("expanded");
      } else {
        subcat.classList.add("expanded");
      }
    });
  } else {
    console.error("Next sibling ul.subcategories not found!");
  }
}

function toggleSubCategories(element, categoryId) {
  closeAllSubCaregories();
  var subSubcategories = document.querySelector(
    '.sub-subcategories[data-category-id="' + categoryId + '"]'
  );

  if (subSubcategories) {
    var subSubcatElements = subSubcategories.querySelectorAll(".sub-subcat");
    subSubcatElements.forEach(function (subSubcat) {
      if (subSubcat.classList.contains("expanded")) {
        subSubcat.classList.remove("expanded");
      } else {
        subSubcat.classList.add("expanded");
      }
    });
  } else {
    console.error("Sub-subcategories not found!");
  }
}

function toggleSubSubCategories(element, subcategoryId) {
  var subSubSubcategories = document.querySelector(
    '.sub-sub-subcategories[data-category-id="' + subcategoryId + '"]'
  );

  if (subSubSubcategories) {
    var subSubSubcatElements =
      subSubSubcategories.querySelectorAll(".sub-sub-subcat");
    subSubSubcatElements.forEach(function (subSubSubcat) {
      subSubSubcat.classList.toggle("expanded");
    });
  } else {
    console.error("Sub-sub-subcategories not found!");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const carousel = document.querySelector(".product-carousel");
  const prevButton = document.getElementById("prevButton");
  const nextButton = document.getElementById("nextButton");
  const slides = document.querySelectorAll(".product-slide");
  const slideWidth = carousel.offsetWidth;
  let currentIndex = 0;
  let autoScrollInterval;
  let isAnimating = false;

  function updateCarousel() {
    const offset = currentIndex * slideWidth;
    carousel.scrollTo({
      left: offset,
      behavior: "smooth",
    });
  }

  function startAutoScroll() {
    autoScrollInterval = setInterval(function () {
      currentIndex = (currentIndex + 1) % 3; // Assuming you have 3 slides
      updateCarousel();
    }, 12000); // Auto-scroll every 12 seconds
  }

  // Start auto-scrolling
  startAutoScroll();

  // Stop auto-scrolling when the user interacts with the carousel
  carousel.addEventListener("scroll", function () {
    clearInterval(autoScrollInterval);
  });

  // Previous button click event
  prevButton.addEventListener("click", function () {
    currentIndex = (currentIndex - 1 + 3) % 3; // Assuming you have 3 slides
    updateCarousel();
    clearInterval(autoScrollInterval);
    startAutoScroll(); // Restart auto-scrolling
  });

  // Next button click event
  nextButton.addEventListener("click", function () {
    currentIndex = (currentIndex + 1) % 3; // Assuming you have 3 slides
    updateCarousel();
    clearInterval(autoScrollInterval);
    startAutoScroll(); // Restart auto-scrolling
  });

  // Ensure initial auto-scroll after a delay (adjust as needed)
  setTimeout(startAutoScroll, 500);

  function resetTextAnimations() {
    const currentSlide = slides[currentIndex];
    const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
    const prevSlide = slides[prevIndex];

    [currentSlide, prevSlide].forEach((slide) => {
      const textElements = slide.querySelectorAll(".product-text p");
      const textElements2 = slide.querySelectorAll(".product-text-2 p");
      textElements.forEach((text) => {
        text.style.animation = "none";
        void text.offsetWidth; // Trigger reflow
        text.style.animation = null;
      });

      textElements2.forEach((text) => {
        text.style.animation = "none";
        void text.offsetWidth; // Trigger reflow
        text.style.animation = null;
      });
    });
  }

  function resetLogoAnimations() {
    const logoElements = document.querySelectorAll(".product-logo");
    const logoElements2 = document.querySelectorAll(".product-logo-2");
    logoElements.forEach((logo) => {
      logo.style.animation = "none";
      void logo.offsetWidth; // Trigger reflow
      logo.style.animation = null;
    });

    logoElements2.forEach((logo) => {
      logo.style.animation = "none";
      void logo.offsetWidth; // Trigger reflow
      logo.style.animation = null;
    });
  }

  function resetTextAndLogoAnimations() {
    resetTextAnimations();
    resetLogoAnimations();
  }

  function updateCarousel() {
    const slideWidth = carousel.offsetWidth;
    const offset = currentIndex * slideWidth;
    carousel.scrollTo({
      left: offset,
      behavior: "smooth",
    });

    // Reset animations for the current and previous slides
    resetTextAndLogoAnimations();

    // Add "animate-slide" class to the current slide
    setTimeout(() => {
      const currentSlide = slides[currentIndex];
      currentSlide.classList.add("animate-slide");
      isAnimating = false;
    }, 10); // Adjust this delay as needed to ensure animations complete
  }

  // Initially trigger animations for the first slide
  slides[currentIndex].classList.add("animate-slide");
});

// JavaScript code to initialize custom scrollbar
const sidebar = document.querySelector(".sidebar");
const isScrollable = sidebar.scrollHeight > sidebar.clientHeight;

if (isScrollable) {
  sidebar.classList.add("custom-scrollbar");
} else {
  sidebar.classList.remove("custom-scrollbar");
}

document.querySelector(".menu-btn").addEventListener("click", function () {
  event.stopPropagation();
  const sidebar = document.querySelector(".sidebar");
  const pageContent = document.querySelector(".page-content");

  sidebar.classList.toggle("open");
  pageContent.classList.toggle("sidebar-open");
  if (sidebar.classList.contains("open")) {
    pageContent.classList.add("dark-overlay");
  } else {
    pageContent.classList.remove("dark-overlay");
  }
});

document.querySelector(".fa-xmark").addEventListener("click", function () {
  event.stopPropagation();
  const sidebar = document.querySelector(".sidebar");
  const pageContent = document.querySelector(".page-content");

  sidebar.classList.remove("open");
  pageContent.classList.remove("sidebar-open");
  pageContent.classList.remove("dark-overlay");
});

document.addEventListener("click", function (event) {
  const sidebar = document.querySelector(".sidebar");
  const pageContent = document.querySelector(".page-content");

  if (sidebar.classList.contains("open") && !event.target.closest(".sidebar")) {
    sidebar.classList.remove("open");
    pageContent.classList.remove("sidebar-open");
    pageContent.classList.remove("dark-overlay"); // Remove the dark overlay
  }
});

document.addEventListener("DOMContentLoaded", function () {
  // Get the current scroll position from the session storage
  var scrollPosition = sessionStorage.getItem("scrollPosition");

  if (scrollPosition !== null) {
    window.scrollTo(0, scrollPosition);
  }
});

// Save scroll position before performing actions
function saveScrollPosition() {
  var scrollPosition = window.scrollY;
  sessionStorage.setItem("scrollPosition", scrollPosition);
}

// Save scroll position before clicking on specific buttons
document
  .querySelectorAll(".option-btn, .heart-icon, .btn, .delete-btn")
  .forEach((button) => {
    button.addEventListener("click", saveScrollPosition);
  });

// Remove the scroll position for all other elements
document.body.addEventListener("click", function (event) {
  var targetElement = event.target;

  if (
    !targetElement.matches(".option-btn, .heart-icon, .btn, .delete-btn") &&
    !targetElement.closest(".option-btn, .heart-icon, .btn, .delete-btn")
  ) {
    sessionStorage.removeItem("scrollPosition");
  }
});

document.addEventListener("DOMContentLoaded", function() {
  var parallaxImage = document.getElementById("parallax-image");

  function handleScroll() {
    if (window.innerWidth >= 641) {
      var scrollPosition = window.scrollY;
      parallaxImage.style.transform = "translateX(-50%) translateY(" + scrollPosition * 0.3 + "px)";
    }
  }

  document.addEventListener("scroll", handleScroll);
  window.addEventListener("resize", handleScroll);
});
