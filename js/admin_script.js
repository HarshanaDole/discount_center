let navbar = document.querySelector(".header .flex .navbar");
let profile = document.querySelector(".header .flex .profile");

document.querySelector("#menu-btn").onclick = () => {
  navbar.classList.toggle("active");
  profile.classList.remove("active");
};

document.querySelector("#user-btn").onclick = () => {
  profile.classList.toggle("active");
  navbar.classList.remove("active");
};

window.onscroll = () => {
  navbar.classList.remove("active");
  profile.classList.remove("active");
};

let mainImage = document.querySelector(
  ".update-product .image-container .main-image img"
);
let subImages = document.querySelectorAll(
  ".update-product .image-container .sub-image img"
);

subImages.forEach((images) => {
  images.onclick = () => {
    src = images.getAttribute("src");
    mainImage.src = src;
  };
});

let previousOrderCount =
  parseInt(localStorage.getItem("previousOrderCount")) || 0;

function fetchNewOrderCount() {
  const audioElement = document.getElementById("notificationSound");

  fetch("../components/get_new_order_count.php")
    .then((response) => response.json())
    .then((data) => {
      const notificationBadge = document.querySelector(".notification");
      const notificationBadge3 = document.querySelector(
        "#menu-btn .notification"
      );

      if (data.newOrderCount > 0) {
        notificationBadge.textContent = data.newOrderCount;
        notificationBadge.style.display = "inline";
        notificationBadge3.style.display = "inline";

        if (data.newOrderCount > previousOrderCount) {
          audioElement.play();
        }

        previousOrderCount = data.newOrderCount;
        localStorage.setItem("previousOrderCount", previousOrderCount);
      } else {
        notificationBadge.style.display = "none";
        notificationBadge3.style.display = "none";
        previousOrderCount = 0;
        localStorage.setItem("previousOrderCount", 0);
      }
    })
    .catch((error) => {
      console.error("Error fetching new order count:", error);
    });
}

fetchNewOrderCount();

setInterval(fetchNewOrderCount, 3000);

// JavaScript to maintain scroll position
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

// Call saveScrollPosition() before form submission
document.querySelectorAll("form").forEach((form) => {
  form.addEventListener("submit", saveScrollPosition);
});
