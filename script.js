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
  form.reset(); // Clear the form fields
});