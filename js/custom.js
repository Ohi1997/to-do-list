jQuery(document).ready(function ($) {
  $("form").on("submit", function (event) {
    // Check if the user is online
    if (!navigator.onLine) {
      event.preventDefault();
      alert("Please connect to the internet to proceed.");
    }
  });
});
