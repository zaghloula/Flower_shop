function navigateTo(section) {
  window.location.hash = section;
}
const form = document.getElementById("contactForm");
form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const data = new FormData(form);
  const res = await fetch("contact.php", {
    method: "POST",
    body: data,
  });
  const text = await res.text();
  console.log(text); // log PHP die() output or success message
});
// order.js
// document.addEventListener("DOMContentLoaded", function () {
//   const form = document.querySelector("form");

//   form.addEventListener("submit", function (event) {
//     const name = document.getElementById("name").value.trim();
//     const email = document.getElementById("email").value.trim();
//     const amount = document.getElementById("amount").value.trim();
//     const product = document.getElementById("product").value.trim();

//     if (!name || !email || !amount || !product) {
//       event.preventDefault();
//       alert("Please fill in all fields.");
//     }
//   });
// });
