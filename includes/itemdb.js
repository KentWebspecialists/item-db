document.addEventListener("DOMContentLoaded", function () {
  var searchInput = document.getElementById("itemdb-search");
  var gridItems = document.querySelectorAll(".itemdb-item");

  searchInput.addEventListener("input", function () {
    var searchTerm = searchInput.value.toLowerCase();

    gridItems.forEach(function (item) {
      var itemTitle = item.querySelector("h3").textContent.toLowerCase();

      if (itemTitle.indexOf(searchTerm) !== -1) {
        item.style.display = "block";
      } else {
        item.style.display = "none";
      }
    });
  });
});
